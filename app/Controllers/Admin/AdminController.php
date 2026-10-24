<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ImageService;
use App\Models\MediaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * 관리자 화면 공통 (로그인 확인은 라우트의 adminAuth 필터에서)
 */
abstract class AdminController extends BaseController
{
    protected function render(string $view, array $data = []): string
    {
        $data += ['title' => '', 'nav' => ''];
        $data['adminName'] = session('admin_name');

        return view('admin/' . $view, $data);
    }

    /** 없는 번호로 들어오면 404 */
    protected function findOr404($model, int $id): array
    {
        $row = $model->find($id);
        if (! $row) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    /**
     * 폼에 사진이 첨부됐으면 저장하고 media id 를 돌려준다.
     *  - 첨부 안 함: null (기존 사진 유지)
     *  - 문제 있음: 한국어 오류 문구(string)
     *
     * @return int|string|null
     */
    protected function uploadImage(string $field, string $altFallback = '')
    {
        $file = $this->request->getFile($field);
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $images = new ImageService();
        if ($error = $images->validate($file)) {
            return $error;
        }

        try {
            $data = $images->store($file->getTempName(), $file->getClientName());
        } catch (\Throwable $e) {
            log_message('error', '사진 저장 실패: {msg}', ['msg' => $e->getMessage()]);

            return '사진을 저장하지 못했습니다. 다른 사진으로 다시 시도해 주세요.';
        }

        $alt = trim((string) $this->request->getPost($field . '_alt')) ?: $altFallback;

        return (int) model(MediaModel::class)->insert($data + [
            'alt_text' => mb_substr($alt, 0, 200),
            'source'   => '직접 업로드',
        ]);
    }

    /** 입력값 되돌려주며 이전 화면으로 */
    protected function backWithErrors(array|string $errors)
    {
        return redirect()->back()->withInput()->with('errors', (array) $errors);
    }

    /** 체크박스 값 (체크 안 하면 아예 전송되지 않으므로 0 처리) */
    protected function flag(string $name): int
    {
        return $this->request->getPost($name) ? 1 : 0;
    }

    /** datetime-local 입력값 → DB 형식, 비었으면 null */
    protected function dt(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : date('Y-m-d H:i:s', strtotime($value));
    }
}
