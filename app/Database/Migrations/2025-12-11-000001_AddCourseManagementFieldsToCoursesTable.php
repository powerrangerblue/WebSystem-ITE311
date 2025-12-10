<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseManagementFieldsToCoursesTable extends Migration
{
    public function up()
    {
        // Add columns only if they don't exist
        if (!$this->db->fieldExists('school_year', 'courses')) {
            $this->forge->addColumn('courses', [
                'school_year' => [
                    'type' => 'VARCHAR',
                    'constraint' => '9', // e.g., "2024-2025"
                    'after' => 'description',
                ],
            ]);
        }

        if (!$this->db->fieldExists('semester', 'courses')) {
            $this->forge->addColumn('courses', [
                'semester' => [
                    'type' => 'ENUM',
                    'constraint' => ['1st Semester', '2nd Semester', 'Summer'],
                    'after' => 'school_year',
                ],
            ]);
        }

        if (!$this->db->fieldExists('schedule', 'courses')) {
            $this->forge->addColumn('courses', [
                'schedule' => [
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'after' => 'semester',
                ],
            ]);
        }

        if (!$this->db->fieldExists('teacher_id', 'courses')) {
            $this->forge->addColumn('courses', [
                'teacher_id' => [
                    'type' => 'INT',
                    'unsigned' => true,
                    'after' => 'schedule',
                ],
            ]);

            // Add foreign key constraint for teacher_id only if column was added
            $this->db->query('ALTER TABLE courses ADD CONSTRAINT fk_courses_teacher_id FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL');
        }

        if (!$this->db->fieldExists('status', 'courses')) {
            $this->forge->addColumn('courses', [
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['Active', 'Inactive'],
                    'default' => 'Active',
                    'after' => 'teacher_id',
                ],
            ]);
        }

        if (!$this->db->fieldExists('start_date', 'courses')) {
            $this->forge->addColumn('courses', [
                'start_date' => [
                    'type' => 'DATE',
                    'after' => 'status',
                ],
            ]);
        }

        if (!$this->db->fieldExists('end_date', 'courses')) {
            $this->forge->addColumn('courses', [
                'end_date' => [
                    'type' => 'DATE',
                    'after' => 'start_date',
                ],
            ]);
        }
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE courses DROP FOREIGN KEY fk_courses_teacher_id');

        $this->forge->dropColumn('courses', [
            'school_year',
            'semester',
            'schedule',
            'teacher_id',
            'status',
            'start_date',
            'end_date',
        ]);
    }
}
