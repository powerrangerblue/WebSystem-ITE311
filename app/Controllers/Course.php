<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Course extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Handle course enrollment via AJAX
     * 
     * @return ResponseInterface
     */
    public function enroll()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to enroll in courses.'
            ])->setStatusCode(401);
        }

        // Get user ID from session
        $user_id = session()->get('user_id');
        
        // Validate CSRF token
        if (!$this->request->isValidCsrf()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid security token. Please refresh the page and try again.'
            ])->setStatusCode(403);
        }

        // Get course_id from POST request
        $course_id = $this->request->getPost('course_id');
        
        // Validate course_id
        if (empty($course_id) || !is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID provided.'
            ])->setStatusCode(400);
        }

        $course_id = (int) $course_id;

        // Check if course exists
        $course = $this->courseModel->find($course_id);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ])->setStatusCode(404);
        }

        // Check if user is already enrolled
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ])->setStatusCode(409);
        }

        // Prepare enrollment data
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id
        ];

        // Insert enrollment record
        $enrollmentId = $this->enrollmentModel->enrollUser($enrollmentData);

        if ($enrollmentId) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in ' . esc($course['course_name']) . '!',
                'enrollment_id' => $enrollmentId,
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
            ])->setStatusCode(500);
        }
    }

    /**
     * Get available courses for a user
     * 
     * @return ResponseInterface
     */
    public function getAvailableCourses()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to view courses.'
            ])->setStatusCode(401);
        }

        $user_id = session()->get('user_id');
        $courses = $this->courseModel->getAvailableCourses($user_id);

        return $this->response->setJSON([
            'success' => true,
            'courses' => $courses
        ]);
    }

    /**
     * Get user's enrolled courses
     * 
     * @return ResponseInterface
     */
    public function getUserEnrollments()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to view enrollments.'
            ])->setStatusCode(401);
        }

        $user_id = session()->get('user_id');
        $enrollments = $this->enrollmentModel->getUserEnrollments($user_id);

        return $this->response->setJSON([
            'success' => true,
            'enrollments' => $enrollments
        ]);
    }

    /**
     * Display course details
     * 
     * @param int $id Course ID
     * @return string
     */
    public function view($id)
    {
        $course = $this->courseModel->getCourseWithEnrollmentCount($id);
        
        if (!$course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        $data = [
            'course' => $course,
            'title' => $course['course_name']
        ];

        return view('course/view', $data);
    }

    /**
     * List all courses
     * 
     * @return string
     */
    public function index()
    {
        $courses = $this->courseModel->getAllCoursesWithEnrollmentCount();
        
        $data = [
            'courses' => $courses,
            'title' => 'All Courses'
        ];

        return view('course/index', $data);
    }
}
