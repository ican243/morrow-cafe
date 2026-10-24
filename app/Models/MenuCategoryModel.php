<?php

namespace App\Models;

class MenuCategoryModel extends SortableModel
{
    protected $table         = 'menu_categories';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name_ko', 'name_en', 'sort_order', 'is_visible'];

    protected $validationRules = [
        'name_ko' => 'required|max_length[50]',
        'name_en' => 'required|max_length[50]',
    ];
    protected $validationMessages = [
        'name_ko' => ['required' => '한글 이름을 입력해 주세요.'],
        'name_en' => ['required' => '영문 이름을 입력해 주세요.'],
    ];

    public function ordered(bool $visibleOnly = false): array
    {
        if ($visibleOnly) {
            $this->where('is_visible', 1);
        }

        return $this->orderBy('sort_order')->orderBy('id')->findAll();
    }
}
