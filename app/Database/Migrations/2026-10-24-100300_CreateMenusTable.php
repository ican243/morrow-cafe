<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenusTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category_id'  => ['type' => 'INT', 'unsigned' => true],
            'name_ko'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'name_en'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'price'        => ['type' => 'INT', 'unsigned' => true, 'comment' => '원 단위'],
            'description'  => ['type' => 'VARCHAR', 'constraint' => 300, 'default' => ''],
            'temperature'  => ['type' => 'ENUM', 'constraint' => ['hot', 'ice', 'both', 'none'], 'default' => 'none'],
            'media_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'is_signature' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_best'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_sold_out'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_visible'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order'   => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            // 알레르기 유발 성분 코드를 쉼표로 저장 (예: milk,wheat,egg) — 코드 목록은 Config\Cafe::$allergens
            'allergens'    => ['type' => 'VARCHAR', 'constraint' => 200, 'default' => ''],
            'ingredients'  => ['type' => 'VARCHAR', 'constraint' => 500, 'default' => ''],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['category_id', 'sort_order']);
        $this->forge->addKey('is_signature');
        // 메뉴가 남아 있는 카테고리는 지울 수 없게 막는다 (실수로 메뉴가 통째로 사라지는 것 방지)
        $this->forge->addForeignKey('category_id', 'menu_categories', 'id', 'CASCADE', 'RESTRICT');
        // 사용 중인 사진은 지울 수 없게 막는다 (화면에 깨진 이미지가 뜨는 것 방지)
        $this->forge->addForeignKey('media_id', 'media', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('menus', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('menus');
    }
}
