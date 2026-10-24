<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessHourModel extends Model
{
    protected $table         = 'business_hours';
    protected $primaryKey    = 'day_of_week';
    protected $returnType    = 'array';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['day_of_week', 'is_closed', 'open_time', 'close_time', 'last_order', 'break_start', 'break_end', 'updated_at'];

    public const DAY_NAMES = ['일', '월', '화', '수', '목', '금', '토'];

    /** 월요일부터 일요일 순서로 (한국 달력 표기 습관) */
    public function week(): array
    {
        $rows = array_column($this->findAll(), null, 'day_of_week');
        $week = [];
        foreach ([1, 2, 3, 4, 5, 6, 0] as $d) {
            $week[$d] = ($rows[$d] ?? ['day_of_week' => $d, 'is_closed' => 1]) + ['name' => self::DAY_NAMES[$d]];
        }

        return $week;
    }
}
