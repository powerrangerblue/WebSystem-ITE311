<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'course_code' => 'ITE311',
                'course_name' => 'Web Systems and Technologies',
                'description' => 'Comprehensive course covering modern web development technologies, frameworks, and best practices for building robust web applications.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'ITE312',
                'course_name' => 'Systems Analysis and Design',
                'description' => 'Learn methodologies for analyzing business requirements and designing effective information systems using modern approaches.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'ITE313',
                'course_name' => 'Database Management Systems',
                'description' => 'In-depth study of database design, implementation, and management using various database management systems and SQL.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'ITE314',
                'course_name' => 'Software Engineering',
                'description' => 'Principles and practices of software engineering including project management, quality assurance, and development methodologies.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'ITE315',
                'course_name' => 'Computer Networks',
                'description' => 'Understanding network architecture, protocols, and technologies for modern computer communication systems.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'ITE316',
                'course_name' => 'Information Security',
                'description' => 'Comprehensive overview of information security principles, threats, and countermeasures in modern computing environments.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'ITE317',
                'course_name' => 'Mobile Application Development',
                'description' => 'Design and development of mobile applications for iOS and Android platforms using modern development frameworks.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'ITE318',
                'course_name' => 'Cloud Computing',
                'description' => 'Introduction to cloud computing concepts, services, and deployment models with hands-on experience in cloud platforms.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Using the database builder to insert data
        $this->db->table('courses')->insertBatch($data);
    }
}
