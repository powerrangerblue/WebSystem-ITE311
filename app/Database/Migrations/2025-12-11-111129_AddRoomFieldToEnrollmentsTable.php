<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoomFieldToEnrollmentsTable extends Migration
{
    public function up()
    {
        // Add room field to enrollments table
        $fields = [
            'room' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'section',
            ],
        ];

        $this->forge->addColumn('enrollments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', 'room');
    }
}
