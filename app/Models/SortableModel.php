<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 노출 순서(sort_order)를 위/아래 버튼으로 바꾸는 기능 공통.
 * 메뉴처럼 "카테고리 안에서만" 순서를 바꾸는 경우 $sortScope 에 기준 컬럼을 적는다.
 */
abstract class SortableModel extends Model
{
    protected ?string $sortScope = null;

    public function nextSortOrder(?int $scopeValue = null): int
    {
        $builder = $this->builder()->selectMax('sort_order', 'm');
        if ($this->sortScope !== null) {
            $builder->where($this->sortScope, $scopeValue);
        }
        $row = $builder->get()->getRowArray();

        return (int) ($row['m'] ?? 0) + 1;
    }

    /**
     * 바로 위(또는 아래) 항목과 순서를 맞바꾼다.
     * 순서 값이 겹쳐 있으면 먼저 1,2,3… 으로 다시 매긴 뒤 바꾼다.
     */
    public function move(int $id, string $direction): bool
    {
        $row = $this->find($id);
        if (! $row) {
            return false;
        }

        $builder = $this->builder();
        if ($this->sortScope !== null) {
            $builder->where($this->sortScope, $row[$this->sortScope]);
        }
        $siblings = $builder->select('id')->orderBy('sort_order')->orderBy('id')->get()->getResultArray();
        $ids      = array_map('intval', array_column($siblings, 'id'));

        $pos  = array_search($id, $ids, true);
        $swap = $direction === 'up' ? $pos - 1 : $pos + 1;
        if ($pos === false || ! isset($ids[$swap])) {
            return false;
        }

        [$ids[$pos], $ids[$swap]] = [$ids[$swap], $ids[$pos]];

        $this->db->transStart();
        foreach ($ids as $i => $rowId) {
            $this->builder()->where('id', $rowId)->update(['sort_order' => $i + 1]);
        }
        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * 드래그로 바꾼 순서를 한 번에 저장.
     * 같은 묶음(예: 같은 카테고리)의 번호가 하나도 빠짐없이 와야만 저장한다
     * → 다른 화면에서 그사이 추가·삭제됐거나 다른 카테고리 번호가 섞이면 저장 안 함.
     */
    public function reorder(array $ids): bool
    {
        $ids = array_values(array_map('intval', $ids));
        if ($ids === [] || count($ids) !== count(array_unique($ids))) {
            return false;
        }

        $first = $this->builder()->where('id', $ids[0])->get()->getRowArray();
        if (! $first) {
            return false;
        }

        $builder = $this->builder();
        if ($this->sortScope !== null) {
            $builder->where($this->sortScope, $first[$this->sortScope]);
        }
        $siblings = array_map('intval', array_column($builder->select('id')->get()->getResultArray(), 'id'));

        $sorted = $ids;
        sort($sorted);
        sort($siblings);
        if ($sorted !== $siblings) {
            return false;
        }

        $this->db->transStart();
        foreach ($ids as $i => $rowId) {
            $this->builder()->where('id', $rowId)->update(['sort_order' => $i + 1]);
        }
        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
