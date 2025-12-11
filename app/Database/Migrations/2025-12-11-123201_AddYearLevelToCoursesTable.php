<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddYearLevelToCoursesTable extends Migration
{
    public function up()
    {
        // Year level feature removed - no longer adding year_level column
    }

    public function down()
    {
        if ($this->db->fieldExists('year_level', 'courses')) {
            $this->forge->dropColumn('courses', 'year_level');
        }
    }
}
