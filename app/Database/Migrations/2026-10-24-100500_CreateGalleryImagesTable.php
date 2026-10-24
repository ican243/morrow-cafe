<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGalleryImagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'media_id'   => ['type' => 'INT', 'unsigned' => true],
            'area'       => ['type' => 'ENUM', 'constraint' => ['inside', 'seat', 'outside'], 'default' => 'inside'],
            'caption'    => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => ''],
            'show_on_home' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_visible' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['area', 'sort_order']);
        $this->forge->addForeignKey('media_id', 'media', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('gallery_images', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('gallery_images');
    }
}
