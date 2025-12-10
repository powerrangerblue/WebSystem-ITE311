<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends BaseController
{
    public function dashboard()
    {
        return view('admin_dashboard');
    }

    public function courses()
    {
        $db = \Config\Database::connect();

        // Get course statistics
        $totalCourses = $db->table('courses')->countAllResults();
        $activeCourses = $db->table('courses')->where('status', 'Active')->countAllResults();

        // Get courses with teacher names
        $courses = $db->table('courses')
            ->select('courses.*, users.name as teacher_name')
            ->join('users', 'users.id = courses.teacher_id', 'left')
            ->orderBy('courses.course_code', 'ASC')
            ->get()
            ->getResultArray();

        // Get list of active teachers for the dropdown
        $teachers = $db->table('users')
            ->where('role', 'teacher')
            ->where('status', 'active')
            ->select('id, name')
            ->get()
            ->getResultArray();

        return view('admin/courses', [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'activeCourses' => $activeCourses,
            'teachers' => $teachers
        ]);
    }

    public function updateCourse()
    {
        $db = \Config\Database::connect();
        $input = $this->request->getPost();

        // Validate dates: end_date should be after start_date
        $startDate = $input['start_date'] ?? null;
        $endDate = $input['end_date'] ?? null;

        if (!empty($startDate) && !empty($endDate)) {
            if (strtotime($endDate) <= strtotime($startDate)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'End date must be after the start date'
                ]);
            }
        }

        $data = [
            'school_year' => $input['school_year'] ?? null,
            'semester' => $input['semester'] ?? null,
            'start_date' => $input['start_date'] ?? null,
            'end_date' => $input['end_date'] ?? null,
            'course_name' => $input['course_name'] ?? null,
            'description' => $input['description'] ?? null,
            'teacher_id' => $input['teacher_id'] ?? null,
            'schedule' => $input['schedule'] ?? null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $courseId = $input['course_id'] ?? null;
        if ($courseId) {
            $db->table('courses')->where('id', $courseId)->update($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Course updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Course ID not provided']);
    }

    public function createCourse()
    {
        $db = \Config\Database::connect();
        $input = $this->request->getPost();

        // Validate required fields
        if (empty($input['course_code']) || empty($input['course_name'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course code and course name are required'
            ]);
        }

        // Check if course code already exists
        $existingCourse = $db->table('courses')->where('course_code', $input['course_code'])->get()->getRow();
        if ($existingCourse) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course code already exists'
            ]);
        }

        // Validate dates: end_date should be after start_date
        $startDate = $input['start_date'] ?? null;
        $endDate = $input['end_date'] ?? null;

        if (!empty($startDate) && !empty($endDate)) {
            if (strtotime($endDate) <= strtotime($startDate)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'End date must be after the start date'
                ]);
            }
        }

        $data = [
            'course_code' => $input['course_code'],
            'course_name' => $input['course_name'],
            'description' => $input['description'] ?? null,
            'school_year' => $input['school_year'] ?? null,
            'semester' => $input['semester'] ?? null,
            'schedule' => $input['schedule'] ?? null,
            'teacher_id' => $input['teacher_id'] ?? null,
            'status' => $input['status'] ?? 'Active', // Use provided status or default to Active
            'start_date' => $input['start_date'] ?? null,
            'end_date' => $input['end_date'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $result = $db->table('courses')->insert($data);
        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Course created successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create course']);
    }
}
