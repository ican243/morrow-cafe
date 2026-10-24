<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'type'         => ['type' => 'ENUM', 'constraint' => ['notice', 'event'], 'default' => 'notice'],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'summary'      => ['type' => 'VARCHAR', 'constraint' => 300, 'default' => ''],
            // 본문은 HTML 편집기 없이 일반 텍스트로 저장하고, 출력할 때 줄바꿈만 살린다 (스크립트 삽입 원천 차단)
            'body'         => ['type' => 'TEXT'],
            'media_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'is_pinned'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'published_at' => ['type' => 'DATETIME'],
            'event_start'  => ['type' => 'DATE', 'null' => true],
            'event_end'    => ['type' => 'DATE', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['is_published', 'published_at']);
        $this->forge->addForeignKey('media_id', 'media', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('posts', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('posts');
    }
}
