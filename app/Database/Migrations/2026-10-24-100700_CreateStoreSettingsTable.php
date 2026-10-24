<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// 주소·전화·SNS 같은 "값 하나짜리" 매장 정보. 항목이 늘어도 테이블을 고치지 않도록 key/value로 둔다.
// 어떤 key가 있는지는 Config\Cafe::$settingFields 에 정리.
class CreateStoreSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 64],
            'value'       => ['type' => 'TEXT', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('setting_key', true);
        $this->forge->createTable('store_settings', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('store_settings');
    }
}
