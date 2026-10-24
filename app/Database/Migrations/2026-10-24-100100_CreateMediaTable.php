<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// 사이트에 올라간 모든 사진의 대장.
// 메뉴·갤러리·배너·공지는 사진 파일을 직접 갖지 않고 이 테이블의 id만 가리킨다.
// → 사진 하나를 여러 곳에서 써도 파일은 하나, 출처·라이선스도 한 곳에서 관리.
class CreateMediaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            // 확장자·크기 접미사 없는 기본 경로 (예: 2026/10/a1b2c3d4e5f6). 실제 파일은 _lg/_md/_sm.webp
            'path'            => ['type' => 'VARCHAR', 'constraint' => 120],
            'original_name'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'width'           => ['type' => 'SMALLINT', 'unsigned' => true],
            'height'          => ['type' => 'SMALLINT', 'unsigned' => true],
            'file_size'       => ['type' => 'INT', 'unsigned' => true, 'comment' => '_lg 파일 기준 byte'],
            'alt_text'        => ['type' => 'VARCHAR', 'constraint' => 200, 'default' => ''],
            // 출처 기록 — 직접 촬영이면 source='직접 촬영'
            'source'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'source_url'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'author'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'license'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'license_url'     => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'is_ai_generated' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            // 시드(데모)로 들어간 사진 표시 — 납품 전 일괄 정리할 때 기준
            'is_demo'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('path');
        $this->forge->createTable('media', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('media');
    }
}
