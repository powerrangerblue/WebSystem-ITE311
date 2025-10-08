<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'course_code' => 'CS101',
                'course_name' => 'Introduction to Computer Science',
                'description' => 'Fundamental concepts of computer science including programming basics, algorithms, and data structures.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_code' => 'MATH201',
                'course_name' => 'Calculus I',
                'description' => 'Introduction to differential and integral calculus with applications.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_code' => 'ENG101',
                'course_name' => 'English Composition',
                'description' => 'Development of writing skills through various forms of composition.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_code' => 'PHYS101',
                'course_name' => 'General Physics',
                'description' => 'Introduction to mechanics, thermodynamics, and wave motion.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_code' => 'HIST101',
                'course_name' => 'World History',
                'description' => 'Survey of world history from ancient civilizations to modern times.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'course_code' => 'CHEM101',
                'course_name' => 'General Chemistry',
                'description' => 'Introduction to chemical principles, atomic structure, and chemical bonding.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert courses
        $this->db->table('courses')->insertBatch($data);
    }
}
