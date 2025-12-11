<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoomAndDayOfClassToCoursesTable extends Migration
{
    public function up()
    {
        // Add room field after schedule
        if (!$this->db->fieldExists('room', 'courses')) {
            $this->forge->addColumn('courses', [
                'room' => [
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                    'null' => true,
                    'after' => 'schedule',
                ],
            ]);
        }

        // Add day_of_class field after room
        if (!$this->db->fieldExists('day_of_class', 'courses')) {
            $this->forge->addColumn('courses', [
                'day_of_class' => [
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                    'null' => true,
                    'after' => 'room',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['room', 'day_of_class']);
    }
}
