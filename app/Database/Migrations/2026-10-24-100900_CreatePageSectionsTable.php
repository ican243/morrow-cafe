<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// HOME 소개글, ABOUT 브랜드 스토리·커피 철학·공간 콘셉트 같은 "문단" 단위 글.
// 칸(section_key)은 정해져 있고, 관리자는 내용만 고친다 → 사장님이 실수로 화면 구조를 깨뜨릴 일이 없다.
class CreatePageSectionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'section_key' => ['type' => 'VARCHAR', 'constraint' => 40],
            'eyebrow'     => ['type' => 'VARCHAR', 'constraint' => 60, 'default' => ''],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'body'        => ['type' => 'TEXT'],
            'media_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('section_key');
        $this->forge->addForeignKey('media_id', 'media', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('page_sections', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('page_sections');
    }
}
