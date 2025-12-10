<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentFieldsToEnrollmentsTable extends Migration
{
    public function up()
    {
        // Add fields for student management in the enrollments table
        $fields = [
            'student_id' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'after' => 'course_id',
            ],
            'program' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'student_id',
            ],
            'year_level' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'program',
            ],
            'section' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'year_level',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Active', 'Inactive', 'Dropped'],
                'default' => 'Active',
                'after' => 'section',
            ],
            'enrollment_status' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
                'after' => 'status',
                'comment' => 'Graduated, Withdraw, etc.'
            ],
        ];

        $this->forge->addColumn('enrollments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', [
            'student_id',
            'program',
            'year_level',
            'section',
            'status',
            'enrollment_status',
        ]);
    }
}
