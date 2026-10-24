<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Cafe;
use RuntimeException;

/**
 * 사진 저장 담당.
 *
 * 올라온 사진을 그대로 두지 않고 항상 새로 그려서(re-encode) 저장한다.
 *  - 사진 속 촬영 위치·기기 정보(EXIF)가 지워진다
 *  - 이미지로 위장한 파일이 섞여 들어와도 GD가 읽지 못하면 저장 자체가 안 된다
 *  - 원본은 저장하지 않고 WebP 3가지 크기(lg/md/sm)만 남긴다 → 디스크 절약, 화면 속도 향상
 */
class ImageService
{
    private Cafe $config;
    private string $root;

    public function __construct()
    {
        $this->config = config(Cafe::class);
        $this->root   = FCPATH . 'uploads/';
    }

    /**
     * 업로드 파일 검증. 문제가 있으면 사용자에게 보여줄 한국어 메시지, 없으면 null.
     */
    public function validate(?UploadedFile $file): ?string
    {
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return '사진을 선택해 주세요.';
        }

        if ($file->getError() === UPLOAD_ERR_INI_SIZE || $file->getError() === UPLOAD_ERR_FORM_SIZE) {
            return '사진 용량이 너무 큽니다. ' . ($this->config->uploadMaxKb / 1024) . 'MB 이하로 올려 주세요.';
        }

        if (! $file->isValid()) {
            return '사진 업로드에 실패했습니다. 다시 시도해 주세요.';
        }

        if ($file->getSizeByUnit('kb') > $this->config->uploadMaxKb) {
            return '사진 용량이 너무 큽니다. ' . ($this->config->uploadMaxKb / 1024) . 'MB 이하로 올려 주세요.';
        }

        // 확장자가 아니라 실제 파일 내용으로 형식을 확인
        if (! in_array($file->getMimeType(), $this->config->uploadMimes, true)) {
            return 'JPG, PNG, WEBP 형식의 사진만 올릴 수 있습니다.';
        }

        $info = @getimagesize($file->getTempName());
        if ($info === false) {
            return '사진 파일을 읽을 수 없습니다.';
        }

        $max = $this->config->uploadMaxDimension;
        if ($info[0] > $max || $info[1] > $max) {
            return "사진 크기가 너무 큽니다. 가로·세로 {$max}px 이하로 올려 주세요.";
        }

        if ($info[0] < 300 || $info[1] < 300) {
            return '사진이 너무 작습니다. 가로·세로 300px 이상으로 올려 주세요.';
        }

        return null;
    }

    /**
     * 사진 파일을 WebP로 변환해 저장하고 media 테이블에 넣을 값을 돌려준다.
     *
     * @param string $sourcePath 원본 파일 경로 (업로드 임시파일 또는 시드 이미지)
     */
    public function store(string $sourcePath, string $originalName = ''): array
    {
        $image = $this->load($sourcePath);

        $dir = date('Y/m');
        if (! is_dir($this->root . $dir) && ! mkdir($this->root . $dir, 0775, true) && ! is_dir($this->root . $dir)) {
            throw new RuntimeException('업로드 폴더를 만들 수 없습니다.');
        }

        // 파일명은 추측할 수 없는 랜덤 값으로 (원래 파일명은 DB에만 기록)
        $base = $dir . '/' . bin2hex(random_bytes(8));

        $width  = imagesx($image);
        $height = imagesy($image);
        $saved  = [];

        try {
            foreach ($this->config->imageSizes as $suffix => $maxWidth) {
                $target = $width > $maxWidth
                    ? imagescale($image, $maxWidth, (int) round($height * $maxWidth / $width), IMG_BICUBIC)
                    : $image;

                $file = $this->root . $base . '_' . $suffix . '.webp';
                if (! imagewebp($target, $file, $this->config->webpQuality)) {
                    throw new RuntimeException('사진 저장에 실패했습니다.');
                }
                $saved[] = $file;

                if ($target !== $image) {
                    imagedestroy($target);
                }
            }
        } catch (\Throwable $e) {
            foreach ($saved as $f) {
                @unlink($f);
            }
            throw $e;
        } finally {
            imagedestroy($image);
        }

        $lgWidth = min($width, $this->config->imageSizes['lg']);

        return [
            'path'          => $base,
            'original_name' => mb_substr($originalName, 0, 255),
            'width'         => $lgWidth,
            'height'        => (int) round($height * $lgWidth / $width),
            'file_size'     => filesize($this->root . $base . '_lg.webp'),
        ];
    }

    /** media.path 기준으로 저장된 파일 전부 삭제 */
    public function delete(string $path): void
    {
        foreach (array_keys($this->config->imageSizes) as $suffix) {
            $file = $this->root . $path . '_' . $suffix . '.webp';
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * GD 이미지로 읽고, 휴대폰 사진의 회전 정보(EXIF Orientation)를 실제 픽셀에 반영한다.
     * (안 하면 세로로 찍은 사진이 옆으로 누워서 저장됨)
     *
     * @return \GdImage
     */
    private function load(string $path)
    {
        $image = @imagecreatefromstring((string) file_get_contents($path));
        if ($image === false) {
            throw new RuntimeException('사진 파일을 읽을 수 없습니다.');
        }

        // PNG 투명 배경은 WebP로 옮길 때 검게 보이지 않도록 크림색으로 채운다
        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }
        $w      = imagesx($image);
        $h      = imagesy($image);
        $canvas = imagecreatetruecolor($w, $h);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 0xF5, 0xF0, 0xE8));
        imagecopy($canvas, $image, 0, 0, 0, 0, $w, $h);
        imagedestroy($image);
        $image = $canvas;

        $orientation = 1;
        if (function_exists('exif_read_data')) {
            $exif        = @exif_read_data($path);
            $orientation = (int) ($exif['Orientation'] ?? 1);
        }

        $rotated = match ($orientation) {
            3       => imagerotate($image, 180, 0),
            6       => imagerotate($image, -90, 0),
            8       => imagerotate($image, 90, 0),
            default => null,
        };

        if ($rotated) {
            imagedestroy($image);
            $image = $rotated;
        }

        return $image;
    }
}
