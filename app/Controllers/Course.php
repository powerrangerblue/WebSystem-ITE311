<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;

class Course extends BaseController
{
    /**
     * Handle course enrollment via AJAX
     */
    public function enroll()
    {
        // Check if the user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to enroll in courses.'
            ]);
        }

        // Validate CSRF token
        if (!$this->request->isValidCsrf()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid security token. Please refresh the page and try again.'
            ]);
        }

        // Get user ID from session
        $user_id = session()->get('user_id');
        
        // Receive the course_id from the POST request
        $course_id = $this->request->getPost('course_id');
        
        // Validate course_id
        if (empty($course_id) || !is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID provided.'
            ]);
        }

        $course_id = (int) $course_id;

        // Check if course exists
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $course_id)->get()->getRowArray();
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ]);
        }

        // Check if the user is already enrolled
        $enrollmentModel = new EnrollmentModel();
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // If not, insert the new enrollment record with the current timestamp
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        $enrollmentId = $enrollmentModel->enrollUser($enrollmentData);

        // Return a JSON response indicating success or failure
        if ($enrollmentId) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in ' . esc($course['course_name']) . '!',
                'course' => [
                    'id' => $course['id'],
                    'course_code' => $course['course_code'],
                    'course_name' => $course['course_name']
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in course. Please try again.'
            ]);
        }
    }
}
