<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                 'title' => 'Welcome to the New Academic Year',
                'content' => 'We are excited to welcome all students to the new academic year. Classes will begin on January 15, 2024. Please ensure you have completed your enrollment and course registration.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Library Hours Update',
                'content' => 'The university library will now be open from 7:00 AM to 10:00 PM on weekdays and 9:00 AM to 8:00 PM on weekends. Extended hours are available during exam periods.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Using the database builder to insert data
        $this->db->table('announcements')->insertBatch($data);
    }
}