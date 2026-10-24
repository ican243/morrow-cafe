<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBusinessHoursTable extends Migration
{
    public function up()
    {
        // 요일당 한 줄 (0=일 ~ 6=토). 요일이 PK라 같은 요일이 두 번 들어갈 수 없다.
        $this->forge->addField([
            'day_of_week' => ['type' => 'TINYINT', 'unsigned' => true],
            'is_closed'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'open_time'   => ['type' => 'TIME', 'null' => true],
            'close_time'  => ['type' => 'TIME', 'null' => true],
            'last_order'  => ['type' => 'TIME', 'null' => true],
            'break_start' => ['type' => 'TIME', 'null' => true],
            'break_end'   => ['type' => 'TIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('day_of_week', true);
        $this->forge->createTable('business_hours', false, ['ENGINE' => 'InnoDB']);

        // 정기 휴무 외에 하루 이틀 쉬는 날 (명절, 내부 공사 등)
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'closed_on'  => ['type' => 'DATE'],
            'reason'     => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => ''],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('closed_on');
        $this->forge->createTable('store_closures', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('store_closures');
        $this->forge->dropTable('business_hours');
    }
}
