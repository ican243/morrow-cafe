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
            'kakaoKey' => (string) env('kakao.jsKey', ''),
        ]);
    }

    /**
     * 대표님이 붙여 넣은 그대로를 저장할 주소로 정리한다.
     *  "[네이버 지도] 모로우커피 … https://naver.me/abc" → https://naver.me/abc  (공유 글에서 주소만)
     *  instagram.com/morrow → https://instagram.com/morrow                     (https:// 자동)
     *  morrow.coffee, @morrow.coffee (인스타그램 칸) → https://www.instagram.com/morrow.coffee
     * javascript: 같은 다른 형식은 손대지 않음 → 아래 검사에서 막힘
     */
    private function cleanLink(string $v, string $key): string
    {
        if ($v === '') {
            return $v;
        }
        if (preg_match('#https?://[^\s<>"\'()\[\]]+#i', $v, $m)) {
            $v = rtrim($m[0], '.,!?');
        } elseif ($key === 'instagram_url' && preg_match('/^@?([A-Za-z0-9._]{1,30})$/', $v, $m) && stripos($v, 'instagram.com') === false) {
            return 'https://www.instagram.com/' . $m[1];
        } elseif (preg_match('#(?<![\w.@-])(?:[a-z0-9-]+\.)+[a-z]{2,}(?:[/?\#][^\s<>"\']*)?#i', $v, $m)) {
            $v = 'https://' . rtrim($m[0], '.,!?');
        }

        // 인스타 공유 링크 끝의 ?igsh=… 같은 추적 꼬리는 뗀다
        if ($key === 'instagram_url' && preg_match('#^https?://#i', $v)) {
            $v = preg_replace('#[?\#].*$#', '', $v);
        }

        return $v;
    }

    /** 주소가 이 칸에 맞는 사이트인지 (예: 인스타 칸에 카카오 주소를 넣은 경우 막기) */
    private function hostAllowed(string $url, array $hosts): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        foreach ($hosts as $h) {
            if ($host === $h || str_ends_with($host, '.' . $h)) {
                return true;
            }
        }

        return false;
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
                    $v = $this->cleanLink($v, $key);
                }

                if (! empty($f['required']) && $v === '') {
                    $errors[] = "{$f['label']}: 꼭 입력해 주세요.";
                }
                if ($v !== '' && $f['type'] === 'url') {
                    if (! preg_match('#^https?://#i', $v)) {
                        $errors[] = "{$f['label']}: 주소를 찾지 못했습니다. 아래 안내대로 복사한 링크를 붙여 넣어 주세요.";
                    } elseif (! empty($f['hosts']) && ! $this->hostAllowed($v, $f['hosts'])) {
                        $errors[] = "{$f['label']}: {$f['label']} 주소가 아닙니다. 다른 칸의 주소를 넣지 않았는지 확인해 주세요.";
                    }
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

        $before = model(StoreSettingModel::class)->all();
        model(StoreSettingModel::class)->saveMany($values);

        // 주소만 바꾸고 지도 위치는 그대로면 지도·길찾기가 예전 자리로 안내하게 된다
        $redirect = redirect()->to(site_url($back))->with('message', '저장했습니다.');
        if (isset($values['address'], $values['map_lat'])
            && $values['address'] !== ($before['address'] ?? '')
            && $values['map_lat'] !== ''
            && $values['map_lat'] === ($before['map_lat'] ?? '')
            && $values['map_lng'] === ($before['map_lng'] ?? '')) {
            $redirect->with('notice', '주소는 바뀌었는데 지도 위치는 그대로입니다. 아래 지도에서 매장 위치가 맞는지 확인해 주세요. (주소 검색으로 고르면 자동으로 맞춰집니다)');
        }

        return $redirect;
    }
}
