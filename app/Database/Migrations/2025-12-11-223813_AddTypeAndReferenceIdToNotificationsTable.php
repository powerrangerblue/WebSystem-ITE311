<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeAndReferenceIdToNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('notifications', [
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'message',
            ],
            'reference_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'type',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('notifications', ['type', 'reference_id']);
    }
}
