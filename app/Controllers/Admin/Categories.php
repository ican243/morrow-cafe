<?php

namespace App\Controllers\Admin;

use App\Models\MenuCategoryModel;
use App\Models\MenuModel;

class Categories extends AdminController
{
    private MenuCategoryModel $model;

    public function __construct()
    {
        $this->model = model(MenuCategoryModel::class);
    }

    public function index()
    {
        return $this->render('categories/index', [
            'title'      => '메뉴 카테고리',
            'nav'        => 'categories',
            'categories' => $this->model->ordered(),
            'counts'     => model(MenuModel::class)->countByCategory(),
        ]);
    }

    public function new()
    {
        return $this->render('categories/form', ['title' => '카테고리 추가', 'nav' => 'categories', 'row' => null]);
    }

    public function create()
    {
        $data = $this->input() + ['sort_order' => $this->model->nextSortOrder()];
        if (! $this->model->insert($data)) {
            return $this->backWithErrors($this->model->errors());
        }

        return redirect()->to(site_url('admin/categories'))->with('message', '카테고리를 추가했습니다.');
    }

    public function edit(int $id)
    {
        return $this->render('categories/form', ['title' => '카테고리 수정', 'nav' => 'categories', 'row' => $this->findOr404($this->model, $id)]);
    }

    public function update(int $id)
    {
        $this->findOr404($this->model, $id);
        if (! $this->model->update($id, $this->input())) {
            return $this->backWithErrors($this->model->errors());
        }

        return redirect()->to(site_url('admin/categories'))->with('message', '저장했습니다.');
    }

    public function delete(int $id)
    {
        $row   = $this->findOr404($this->model, $id);
        $count = model(MenuModel::class)->where('category_id', $id)->countAllResults();

        // 메뉴가 남아 있으면 지우지 않는다 (DB 에서도 막혀 있음)
        if ($count > 0) {
            return redirect()->back()->with('error', "'{$row['name_ko']}' 카테고리에 메뉴가 {$count}개 있어 삭제할 수 없습니다. 메뉴를 다른 카테고리로 옮기거나 먼저 삭제해 주세요.");
        }

        $this->model->delete($id);

        return redirect()->to(site_url('admin/categories'))->with('message', "'{$row['name_ko']}' 카테고리를 삭제했습니다.");
    }

    public function sort()
    {
        return $this->saveOrder($this->model);
    }

    public function move(int $id, string $dir)
    {
        $this->model->move($id, $dir);

        return redirect()->to(site_url('admin/categories'));
    }

    private function input(): array
    {
        return [
            'name_ko'    => trim((string) $this->request->getPost('name_ko')),
            'name_en'    => trim((string) $this->request->getPost('name_en')),
            'is_visible' => $this->flag('is_visible'),
        ];
    }
}
