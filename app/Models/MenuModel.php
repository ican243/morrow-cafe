<?php

namespace App\Models;

class MenuModel extends SortableModel
{
    protected $table         = 'menus';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected ?string $sortScope = 'category_id';
    protected $allowedFields = [
        'category_id', 'name_ko', 'name_en', 'price', 'description', 'temperature', 'media_id',
        'is_signature', 'is_best', 'is_sold_out', 'is_visible', 'sort_order', 'allergens', 'ingredients',
    ];

    protected $validationRules = [
        'category_id' => 'required|is_natural_no_zero|is_not_unique[menu_categories.id]',
        'name_ko'     => 'required|max_length[60]',
        'name_en'     => 'permit_empty|max_length[60]',
        'price'       => 'required|is_natural|less_than[1000000]',
        'description' => 'permit_empty|max_length[300]',
        'temperature' => 'required|in_list[none,hot,ice,both]',
        'ingredients' => 'permit_empty|max_length[500]',
    ];
    protected $validationMessages = [
        'category_id' => ['required' => '카테고리를 선택해 주세요.', 'is_not_unique' => '없는 카테고리입니다.'],
        'name_ko'     => ['required' => '한글 메뉴명을 입력해 주세요.'],
        'price'       => ['required' => '가격을 입력해 주세요.', 'is_natural' => '가격은 숫자만 입력해 주세요.', 'less_than' => '가격이 너무 큽니다.'],
    ];

    /** 사진 정보까지 같이 붙여서 */
    public function withMedia(): static
    {
        $this->select('menus.*, media.path AS media_path, media.alt_text AS media_alt, media.width AS media_w, media.height AS media_h')
            ->join('media', 'media.id = menus.media_id', 'left');

        return $this;
    }

    public function signatures(int $limit = 3): array
    {
        return $this->withMedia()
            ->join('menu_categories c', 'c.id = menus.category_id')
            ->where(['menus.is_signature' => 1, 'menus.is_visible' => 1, 'c.is_visible' => 1])
            ->orderBy('c.sort_order')->orderBy('menus.sort_order')
            ->findAll($limit);
    }

    /** 카테고리별로 묶은 공개 메뉴 */
    public function publicByCategory(): array
    {
        $menus = $this->withMedia()
            ->where('menus.is_visible', 1)
            ->orderBy('menus.sort_order')->orderBy('menus.id')
            ->findAll();

        $grouped = [];
        foreach ($menus as $menu) {
            $grouped[$menu['category_id']][] = $menu;
        }

        return $grouped;
    }

    public function countByCategory(): array
    {
        $rows = $this->select('category_id, COUNT(*) AS c')->groupBy('category_id')->findAll();

        return array_column($rows, 'c', 'category_id');
    }
}
