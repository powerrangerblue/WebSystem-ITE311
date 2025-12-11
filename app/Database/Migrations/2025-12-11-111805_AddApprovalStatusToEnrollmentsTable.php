<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovalStatusToEnrollmentsTable extends Migration
{
    public function up()
    {
        // Add approval_status field to enrollments table for enrollment approval workflow
        $fields = [
            'approval_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'declined'],
                'default' => 'pending',
                'after' => 'enrollment_status',
                'comment' => 'Enrollment approval status: pending, approved, declined'
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'approval_status',
                'comment' => 'User ID of the person who approved/declined the enrollment'
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_by',
                'comment' => 'Timestamp when enrollment was approved/declined'
            ],
            'approval_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'approved_at',
                'comment' => 'Notes from the approver about the decision'
            ],
        ];

        $this->forge->addColumn('enrollments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', [
            'approval_status',
            'approved_by',
            'approved_at',
            'approval_notes'
        ]);
    }
}
