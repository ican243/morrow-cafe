<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// HOME 맨 위 큰 사진 + 문구. 여러 개 등록해두고 노출 기간으로 시즌 배너를 바꿔 끼울 수 있다.
class CreateBannersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'media_id'   => ['type' => 'INT', 'unsigned' => true],
            'eyebrow'    => ['type' => 'VARCHAR', 'constraint' => 60, 'default' => '', 'comment' => '제목 위 작은 영문'],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'subtitle'   => ['type' => 'VARCHAR', 'constraint' => 200, 'default' => ''],
            'link_label' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => ''],
            'link_url'   => ['type' => 'VARCHAR', 'constraint' => 300, 'default' => ''],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'starts_at'  => ['type' => 'DATETIME', 'null' => true],
            'ends_at'    => ['type' => 'DATETIME', 'null' => true],
            'sort_order' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['is_active', 'sort_order']);
        $this->forge->addForeignKey('media_id', 'media', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('banners', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('banners');
    }
}
