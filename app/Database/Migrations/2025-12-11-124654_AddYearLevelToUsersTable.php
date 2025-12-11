<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddYearLevelToUsersTable extends Migration
{
    public function up()
    {
        // Year level feature removed - no longer adding year_level column
    }

    public function down()
    {
        if ($this->db->fieldExists('year_level', 'users')) {
            $this->forge->dropColumn('users', 'year_level');
        }
    }
}
