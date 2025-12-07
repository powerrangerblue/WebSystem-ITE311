<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGradingFieldsToSubmissionsTable extends Migration
{
    public function up()
    {
        // Rename user_id to student_id for consistency
        $this->forge->modifyColumn('submissions', [
            'user_id' => [
                'name' => 'student_id',
                'type' => 'INT',
                'unsigned' => true,
            ]
        ]);

        // Rename file_path to submission_file for consistency
        $this->forge->modifyColumn('submissions', [
            'file_path' => [
                'name' => 'submission_file',
                'type' => 'VARCHAR',
                'constraint' => '255',
            ]
        ]);

        // Add new grading fields
        $this->forge->addColumn('submissions', [
            'submission_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'grade' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['not_submitted', 'submitted', 'graded'],
                'default' => 'not_submitted',
            ],
            'graded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Remove added columns
        $this->forge->dropColumn('submissions', ['submission_notes', 'grade', 'feedback', 'status', 'graded_at', 'updated_at']);

        // Rename back to original names
        $this->forge->modifyColumn('submissions', [
            'student_id' => [
                'name' => 'user_id',
                'type' => 'INT',
                'unsigned' => true,
            ]
        ]);

        $this->forge->modifyColumn('submissions', [
            'submission_file' => [
                'name' => 'file_path',
                'type' => 'VARCHAR',
                'constraint' => '255',
            ]
        ]);
    }
}
