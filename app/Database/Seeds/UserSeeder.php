<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'     => 'Admin User',
                'email'    => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Teacher One',
                'email'    => 'teacher1@example.com',
                'password' => password_hash('teacher123', PASSWORD_DEFAULT),
                'role'     => 'teacher',
            ],
            [
                'name'     => 'Student One',
                'email'    => 'student1@example.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role'     => 'student',
            ],
        ];

        $builder = $this->db->table('users');
        foreach ($data as $row) {
            $exists = $builder->where('email', $row['email'])->countAllResults();
            if ($exists === 0) {
                $builder->insert($row);
            }
            // reset builder for next loop
            $builder = $this->db->table('users');
        }
    }
}
