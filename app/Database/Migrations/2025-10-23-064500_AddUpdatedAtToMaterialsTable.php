<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToMaterialsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('materials', [
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('materials', 'updated_at');
    }
}
