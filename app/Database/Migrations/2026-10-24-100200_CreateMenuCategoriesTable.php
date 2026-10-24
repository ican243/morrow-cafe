<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenuCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name_ko'    => ['type' => 'VARCHAR', 'constraint' => 50],
            'name_en'    => ['type' => 'VARCHAR', 'constraint' => 50],
            'sort_order' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'is_visible' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('sort_order');
        $this->forge->createTable('menu_categories', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('menu_categories');
    }
}
