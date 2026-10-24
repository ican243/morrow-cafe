<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * 실제 운영(납품)용 최소 데이터: 매장 이름, 기본 영업시간.
 * 이미 값이 있으면 건드리지 않는다 → 여러 번 실행해도 안전.
 *
 *   php spark db:seed InitialSeeder
 */
class InitialSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $settings = $this->db->table('store_settings');
        foreach (['store_name' => 'MORROW COFFEE', 'store_name_en' => 'MORROW COFFEE'] as $key => $value) {
            if (! $settings->where('setting_key', $key)->countAllResults()) {
                $settings->insert(['setting_key' => $key, 'value' => $value, 'updated_at' => $now]);
            }
        }

        $hours = $this->db->table('business_hours');
        if (! $hours->countAllResults()) {
            foreach (range(0, 6) as $d) {
                $hours->insert([
                    'day_of_week' => $d,
                    'is_closed'   => 0,
                    'open_time'   => '10:00:00',
                    'close_time'  => '21:00:00',
                    'updated_at'  => $now,
                ]);
            }
        }

        cache()->delete('cafe_settings');
    }
}
