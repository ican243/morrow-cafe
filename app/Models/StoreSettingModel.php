<?php

namespace App\Models;

use CodeIgniter\Model;

class StoreSettingModel extends Model
{
    protected $table         = 'store_settings';
    protected $primaryKey    = 'setting_key';
    protected $returnType    = 'array';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['setting_key', 'value', 'updated_at'];

    private const CACHE_KEY = 'cafe_settings';

    /** key => value 배열. 모든 화면 머리말·바닥글에서 쓰여서 캐시해 둔다 */
    public function all(): array
    {
        $cached = cache(self::CACHE_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $settings = array_column($this->findAll(), 'value', 'setting_key');
        cache()->save(self::CACHE_KEY, $settings, 3600);

        return $settings;
    }

    public function saveMany(array $values): void
    {
        $now  = date('Y-m-d H:i:s');
        $rows = [];
        foreach ($values as $key => $value) {
            $rows[] = ['setting_key' => $key, 'value' => trim((string) $value), 'updated_at' => $now];
        }
        if ($rows !== []) {
            $this->builder()->upsertBatch($rows);
        }
        cache()->delete(self::CACHE_KEY);
    }
}
