<?php

namespace App\Controllers\Admin;

use App\Models\MediaModel;
use App\Models\MenuCategoryModel;
use App\Models\MenuModel;
use Config\Cafe;

class Menus extends AdminController
{
    private MenuModel $model;

    public function __construct()
    {
        $this->model = model(MenuModel::class);
    }

    public function index()
    {
        $categories = model(MenuCategoryModel::class)->ordered();
        $catId      = (int) $this->request->getGet('category');
        $soldOut    = (bool) $this->request->getGet('soldout');
        $keyword    = trim((string) $this->request->getGet('q'));

        $this->model->withMedia();
        if ($catId) {
            $this->model->where('menus.category_id', $catId);
        }
        if ($soldOut) {
            $this->model->where('menus.is_sold_out', 1);
        }
        if ($keyword !== '') {
            $this->model->groupStart()->like('menus.name_ko', $keyword)->orLike('menus.name_en', $keyword)->groupEnd();
        }
        $menus = $this->model->orderBy('menus.sort_order')->orderBy('menus.id')->findAll();

        $grouped = [];
        foreach ($menus as $m) {
            $grouped[$m['category_id']][] = $m;
        }

        return $this->render('menus/index', [
            'title'      => '메뉴 관리',
            'nav'        => 'menus',
            'categories' => $categories,
            'grouped'    => $grouped,
            'filter'     => ['category' => $catId, 'soldout' => $soldOut, 'q' => $keyword],
        ]);
    }

    public function new()
    {
        return $this->form(null);
    }

    public function edit(int $id)
    {
        return $this->form($this->model->withMedia()->where('menus.id', $id)->first() ?? $this->findOr404($this->model, $id));
    }

    public function create()
    {
        $data = $this->input();
        if (! $this->model->validate($data)) {
            return $this->backWithErrors($this->model->errors());
        }

        $mediaId = $this->uploadImage('image', $data['name_ko']);
        if (is_string($mediaId)) {
            return $this->backWithErrors($mediaId);
        }

        $data['media_id']   = $mediaId;
        $data['sort_order'] = $this->model->nextSortOrder($data['category_id']);
        $this->model->insert($data, false);

        return redirect()->to(site_url('admin/menus?category=' . $data['category_id']))->with('message', "'{$data['name_ko']}' 메뉴를 추가했습니다.");
    }

    public function update(int $id)
    {
        $old  = $this->findOr404($this->model, $id);
        $data = $this->input();
        if (! $this->model->validate($data)) {
            return $this->backWithErrors($this->model->errors());
        }

        $mediaId = $this->uploadImage('image', $data['name_ko']);
        if (is_string($mediaId)) {
            return $this->backWithErrors($mediaId);
        }
        if ($mediaId) {
            $data['media_id'] = $mediaId;
        } elseif ($this->request->getPost('remove_image')) {
            $data['media_id'] = null;
        }

        // 카테고리를 옮기면 새 카테고리의 맨 뒤로
        if ((int) $old['category_id'] !== (int) $data['category_id']) {
            $data['sort_order'] = $this->model->nextSortOrder($data['category_id']);
        }

        $this->model->update($id, $data);

        // 사진을 바꿨거나 뺐으면, 아무 데서도 안 쓰게 된 예전 사진 파일 정리
        if (array_key_exists('media_id', $data) && $old['media_id'] && (int) $old['media_id'] !== (int) $data['media_id']) {
            model(MediaModel::class)->removeIfOrphan((int) $old['media_id']);
        }

        return redirect()->to(site_url('admin/menus?category=' . $data['category_id']))->with('message', "'{$data['name_ko']}' 메뉴를 저장했습니다.");
    }

    /** 목록에서 바로 바꾸는 가격·품절·노출 */
    public function quick(int $id)
    {
        $row  = $this->findOr404($this->model, $id);
        $data = [];

        $field = $this->request->getPost('field');
        if (in_array($field, ['is_sold_out', 'is_visible'], true)) {
            $data[$field] = $row[$field] ? 0 : 1;
        } elseif ($field === 'price') {
            $price = $this->request->getPost('price');
            if (! ctype_digit((string) $price) || (int) $price >= 1000000) {
                return redirect()->back()->with('error', '가격은 숫자만 입력해 주세요.');
            }
            $data['price'] = (int) $price;
        }

        if ($data) {
            $this->model->skipValidation()->update($id, $data);
        }

        $msg = match (true) {
            isset($data['is_sold_out']) => "'{$row['name_ko']}' " . ($data['is_sold_out'] ? '품절 처리했습니다.' : '판매 중으로 바꿨습니다.'),
            isset($data['is_visible'])  => "'{$row['name_ko']}' " . ($data['is_visible'] ? '화면에 표시합니다.' : '화면에서 숨겼습니다.'),
            isset($data['price'])       => "'{$row['name_ko']}' 가격을 " . number_format($data['price']) . '원으로 바꿨습니다.',
            default                     => null,
        };

        return redirect()->to(previous_url() . '#menu-' . $id)->with('message', $msg);
    }

    public function delete(int $id)
    {
        $row = $this->findOr404($this->model, $id);
        $this->model->delete($id);
        model(MediaModel::class)->removeIfOrphan($row['media_id'] ? (int) $row['media_id'] : null);

        return redirect()->back()->with('message', "'{$row['name_ko']}' 메뉴를 삭제했습니다.");
    }

    public function sort()
    {
        return $this->saveOrder($this->model);
    }


    private function form(?array $row)
    {
        $config = config(Cafe::class);

        return $this->render('menus/form', [
            'title'      => $row ? '메뉴 수정' : '메뉴 추가',
            'nav'        => 'menus',
            'row'        => $row,
            'categories' => model(MenuCategoryModel::class)->ordered(),
            'allergens'  => $config->allergens,
            'temps'      => $config->temperatures,
            'ratio'      => $config->cropRatios['menu'],
            'preset'     => (int) $this->request->getGet('category'),
        ]);
    }

    private function input(): array
    {
        $allergens = array_intersect((array) $this->request->getPost('allergens'), array_keys(config(Cafe::class)->allergens));

        return [
            'category_id'  => (int) $this->request->getPost('category_id'),
            'name_ko'      => trim((string) $this->request->getPost('name_ko')),
            'name_en'      => trim((string) $this->request->getPost('name_en')),
            'price'        => preg_replace('/[^0-9]/', '', (string) $this->request->getPost('price')),
            'description'  => trim((string) $this->request->getPost('description')),
            'temperature'  => (string) $this->request->getPost('temperature'),
            'ingredients'  => trim((string) $this->request->getPost('ingredients')),
            'allergens'    => implode(',', $allergens),
            'is_signature' => $this->flag('is_signature'),
            'is_best'      => $this->flag('is_best'),
            'is_sold_out'  => $this->flag('is_sold_out'),
            'is_visible'   => $this->flag('is_visible'),
        ];
    }
}
