<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersRoleAddTeacher extends Migration
{
    public function up()
    {
        // Expand ENUM to include "teacher"
        $this->db->query('ALTER TABLE `users` MODIFY `role` ENUM("student","teacher","admin") NOT NULL DEFAULT "student"');
    }

    public function down()
    {
        // Revert to original ENUM without "teacher"
        $this->db->query('ALTER TABLE `users` MODIFY `role` ENUM("student","admin") NOT NULL DEFAULT "student"');
    }
}


