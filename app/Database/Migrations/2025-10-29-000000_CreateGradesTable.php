<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateGradesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'student_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'course_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'grade' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['student_id', 'course_id']);
        $this->forge->createTable('grades');
    }

    public function down()
    {
        $this->forge->dropTable('grades');
    }
}


