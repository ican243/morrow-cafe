<?php

namespace App\Models;

use CodeIgniter\Model;

class StoreClosureModel extends Model
{
    protected $table         = 'store_closures';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['closed_on', 'reason'];

    protected $validationRules = [
        'closed_on' => 'required|valid_date[Y-m-d]|is_unique[store_closures.closed_on,id,{id}]',
        'reason'    => 'permit_empty|max_length[100]',
    ];
    protected $validationMessages = [
        'closed_on' => ['required' => '날짜를 입력해 주세요.', 'is_unique' => '이미 등록된 날짜입니다.'],
    ];

    public function upcoming(int $limit = 10): array
    {
        return $this->where('closed_on >=', date('Y-m-d'))->orderBy('closed_on')->findAll($limit);
    }
}
