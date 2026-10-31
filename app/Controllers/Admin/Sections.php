<?php

namespace App\Controllers\Admin;

use App\Models\MediaModel;
use App\Models\PageSectionModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Sections extends AdminController
{
    public function index()
    {
        $defs = config('Cafe')->sections;

        return $this->render('sections/index', [
            'title'    => '브랜드 소개 글',
            'nav'      => 'sections',
            'defs'     => $defs,
            'sections' => model(PageSectionModel::class)->byKeys(array_keys($defs)),
        ]);
    }

    public function edit(string $key)
    {
        $def = $this->def($key);
        $row = model(PageSectionModel::class)->byKeys([$key])[$key] ?? null;

        return $this->render('sections/form', [
            'title' => $def['label'],
            'nav'   => 'sections',
            'key'   => $key,
            'def'   => $def,
            'row'   => $row,
        ]);
    }

    public function update(string $key)
    {
        $def   = $this->def($key);
        $model = model(PageSectionModel::class);
        $row   = $model->where('section_key', $key)->first();

        $data = [
            'section_key' => $key,
            'eyebrow'     => trim((string) $this->request->getPost('eyebrow')),
            'title'       => trim((string) $this->request->getPost('title')),
            'body'        => trim((string) $this->request->getPost('body')),
        ];
        if (! $model->validate($data)) {
            return $this->backWithErrors($model->errors());
        }

        $mediaId = $this->uploadImage('image', $data['title']);
        if (is_string($mediaId)) {
            return $this->backWithErrors($mediaId);
        }
        if ($mediaId) {
            $data['media_id'] = $mediaId;
        }

        if ($row) {
            $model->skipValidation()->update($row['id'], $data);
            if ($mediaId && $row['media_id']) {
                model(MediaModel::class)->removeIfOrphan((int) $row['media_id']);
            }
            if (! $mediaId) {
                $this->saveCurrentAlt($row['media_id']);
            }
        } else {
            $model->skipValidation()->insert($data);
        }

        return redirect()->to(site_url('admin/sections'))->with('message', "'{$def['label']}' 내용을 저장했습니다.");
    }

    private function def(string $key): array
    {
        $defs = config('Cafe')->sections;
        if (! isset($defs[$key])) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $defs[$key];
    }
}
