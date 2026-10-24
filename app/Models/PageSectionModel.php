<?php

namespace App\Models;

use CodeIgniter\Model;

class PageSectionModel extends Model
{
    protected $table         = 'page_sections';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['section_key', 'eyebrow', 'title', 'body', 'media_id'];

    protected $validationRules = [
        'eyebrow' => 'permit_empty|max_length[60]',
        'title'   => 'required|max_length[150]',
        'body'    => 'required|max_length[5000]',
    ];
    protected $validationMessages = [
        'title' => ['required' => '제목을 입력해 주세요.'],
        'body'  => ['required' => '내용을 입력해 주세요.'],
    ];

    /** section_key => 섹션 (사진 포함) */
    public function byKeys(array $keys): array
    {
        $rows = $this->select('page_sections.*, media.path AS media_path, media.alt_text AS media_alt, media.width AS media_w, media.height AS media_h')
            ->join('media', 'media.id = page_sections.media_id', 'left')
            ->whereIn('section_key', $keys)
            ->findAll();

        return array_column($rows, null, 'section_key');
    }
}
