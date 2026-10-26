<?php

namespace App\Controllers\Admin;

use App\Models\BusinessHourModel;
use App\Models\StoreClosureModel;
use App\Models\StoreSettingModel;

/**
 * 매장 정보 / 이용 안내 / SNS 링크 / 영업시간·휴무일
 */
class Store extends AdminController
{
    public function info()
    {
        return $this->settingsForm(['store', 'facility'], '매장 정보', 'store', 'admin/store');
    }

    public function saveInfo()
    {
        return $this->saveSettings(['store', 'facility'], 'admin/store');
    }

    public function sns()
    {
        return $this->settingsForm(['sns'], 'SNS·외부 링크', 'sns', 'admin/sns');
    }

    public function saveSns()
    {
        return $this->saveSettings(['sns'], 'admin/sns');
    }

    public function hours()
    {
        return $this->render('store/hours', [
            'title'    => '영업시간·휴무일',
            'nav'      => 'hours',
            'week'     => model(BusinessHourModel::class)->week(),
            'closures' => model(StoreClosureModel::class)->upcoming(50),
        ]);
    }

    public function saveHours()
    {
        $posted = (array) $this->request->getPost('days');
        $rows   = [];
        $errors = [];
        $time   = static fn ($v) => preg_match('/^\d{2}:\d{2}$/', (string) $v) ? $v . ':00' : null;

        foreach (range(0, 6) as $d) {
            $in     = $posted[$d] ?? [];
            $closed = ! empty($in['is_closed']) ? 1 : 0;
            $row    = [
                'day_of_week' => $d,
                'is_closed'   => $closed,
                'open_time'   => $time($in['open_time'] ?? ''),
                'close_time'  => $time($in['close_time'] ?? ''),
                'last_order'  => $time($in['last_order'] ?? ''),
                'break_start' => $time($in['break_start'] ?? ''),
                'break_end'   => $time($in['break_end'] ?? ''),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
            $name = BusinessHourModel::DAY_NAMES[$d];

            if (! $closed && (! $row['open_time'] || ! $row['close_time'])) {
                $errors[] = "{$name}요일 여는 시간·닫는 시간을 입력하거나 휴무로 체크해 주세요.";
            }
            if (($row['break_start'] === null) !== ($row['break_end'] === null)) {
                $errors[] = "{$name}요일 브레이크타임은 시작·끝을 모두 입력해 주세요.";
            }
            $rows[] = $row;
        }

        if ($errors) {
            return $this->backWithErrors($errors);
        }

        model(BusinessHourModel::class)->builder()->upsertBatch($rows);

        return redirect()->to(site_url('admin/hours'))->with('message', '영업시간을 저장했습니다.');
    }

    public function addClosure()
    {
        $model = model(StoreClosureModel::class);
        $data  = [
            'closed_on' => (string) $this->request->getPost('closed_on'),
            'reason'    => trim((string) $this->request->getPost('reason')),
        ];
        if (! $model->insert($data)) {
            return $this->backWithErrors($model->errors());
        }

        return redirect()->to(site_url('admin/hours'))->with('message', '휴무일을 추가했습니다.');
    }

    public function deleteClosure(int $id)
    {
        model(StoreClosureModel::class)->delete($id);

        return redirect()->to(site_url('admin/hours'))->with('message', '휴무일을 삭제했습니다.');
    }

    private function settingsForm(array $groups, string $title, string $nav, string $action)
    {
        $fields = array_intersect_key(config('Cafe')->settingFields, array_flip($groups));

        return $this->render('store/settings', [
            'title'    => $title,
            'nav'      => $nav,
            'groups'   => $fields,
            'values'   => model(StoreSettingModel::class)->all(),
            'action'   => $action,
        ]);
    }

    /**
     * 주소 앞의 https:// 를 빼먹어도 저장되게 붙여 준다.
     *  instagram.com/morrow → https://instagram.com/morrow
     *  @morrow (인스타그램 칸)  → https://www.instagram.com/morrow
     * javascript: 같은 다른 형식은 손대지 않음 → 아래 검사에서 막힘
     */
    private function withScheme(string $v, string $key): string
    {
        if ($v === '' || preg_match('#^https?://#i', $v)) {
            return $v;
        }
        if ($key === 'instagram_url' && preg_match('/^@([A-Za-z0-9._]{1,30})$/', $v, $m)) {
            return 'https://www.instagram.com/' . $m[1];
        }
        if (preg_match('#^[a-z0-9-]+(\.[a-z0-9-]+)+([/?\#]\S*)?$#i', $v)) {
            return 'https://' . $v;
        }

        return $v;
    }

    private function saveSettings(array $groups, string $back)
    {
        $fields      = array_intersect_key(config('Cafe')->settingFields, array_flip($groups));
        $values      = [];
        $errors      = [];

        foreach ($fields as $group) {
            foreach ($group as $key => $f) {
                $v = trim((string) $this->request->getPost($key));
                if ($f['type'] === 'url') {
                    $v = $this->withScheme($v, $key);
                }

                if (! empty($f['required']) && $v === '') {
                    $errors[] = "{$f['label']}: 꼭 입력해 주세요.";
                }
                if ($v !== '' && $f['type'] === 'url' && ! preg_match('#^https?://#i', $v)) {
                    $errors[] = "{$f['label']}: 인터넷 주소 형식이 아닙니다." . (! empty($f['placeholder']) ? " (예: {$f['placeholder']})" : '');
                }
                if ($v !== '' && $f['type'] === 'number' && ! is_numeric($v)) {
                    $errors[] = "{$f['label']}: 숫자로 입력해 주세요.";
                }
                if (mb_strlen($v) > 2000) {
                    $errors[] = "{$f['label']}: 내용이 너무 깁니다.";
                }
                $values[$key] = $v;
            }
        }

        if ($errors) {
            return $this->backWithErrors($errors);
        }

        model(StoreSettingModel::class)->saveMany($values);

        return redirect()->to(site_url($back))->with('message', '저장했습니다.');
    }
}
