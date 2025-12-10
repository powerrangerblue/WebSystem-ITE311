<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Course extends BaseController
{
    protected CourseModel $courseModel;
    protected EnrollmentModel $enrollmentModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    public function index()
    {
        $searchTerm = trim((string) ($this->request->getGet('search_term') ?? ''));
        $courses = $this->getCourses($searchTerm);

        // Get enrolled course IDs for the current user
        $enrolledCourseIds = [];
        if (session()->get('isLoggedIn')) {
            $userId = session()->get('user_id');
            $enrolledCourses = $this->enrollmentModel->getUserEnrollments($userId);
            $enrolledCourseIds = array_column($enrolledCourses, 'course_id');
        }

        return view('courses/index', [
            'courses' => $courses,
            'searchTerm' => $searchTerm,
            'enrolledCourseIds' => $enrolledCourseIds,
        ]);
    }

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

        // Check if the user is a student (only students can enroll)
        $userRole = strtolower((string) session()->get('role'));
        if ($userRole !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can enroll in courses.'
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
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
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

        $enrollmentId = $this->enrollmentModel->enrollUser($enrollmentData);

        // Return a JSON response indicating success or failure
        if ($enrollmentId) {
            // Create notification for the user
            $notificationModel = new \App\Models\NotificationModel();
            $notificationModel->insert([
                'user_id' => $user_id,
                'message' => 'You have been enrolled in ' . $course['course_name'],
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

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

    public function search()
    {
        $searchTerm = trim((string) ($this->request->getVar('search_term') ?? ''));
        $courses = $this->getCourses($searchTerm);

        // Get enrolled course IDs for the current user (for AJAX responses)
        $enrolledCourseIds = [];
        if (session()->get('isLoggedIn')) {
            $userId = session()->get('user_id');
            $enrolledCourses = $this->enrollmentModel->getUserEnrollments($userId);
            $enrolledCourseIds = array_column($enrolledCourses, 'course_id');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'count' => count($courses),
                'courses' => $courses,
                'searchTerm' => $searchTerm,
                'enrolledCourseIds' => $enrolledCourseIds,
            ]);
        }

        return view('courses/index', [
            'courses' => $courses,
            'searchTerm' => $searchTerm,
            'enrolledCourseIds' => $enrolledCourseIds,
        ]);
    }

    /**
     * Get enrollment details for the current student
     */
    public function getEnrollmentDetails($courseId)
    {
        // Check if the user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to view enrollment details.'
            ]);
        }

        // Check if the user is a student
        $userRole = strtolower((string) session()->get('role'));
        if ($userRole !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can view their enrollment details.'
            ]);
        }

        $userId = session()->get('user_id');

        // Get enrollment details with course and user information, including teacher name
        $db = \Config\Database::connect();
        $enrollment = $db->table('enrollments')
            ->select('enrollments.*, users.name, users.email, courses.course_name, courses.course_code, courses.description, courses.school_year, courses.semester, courses.schedule, teacher.name as teacher_name')
            ->join('users', 'users.id = enrollments.user_id')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->join('users as teacher', 'teacher.id = courses.teacher_id', 'left')
            ->where('enrollments.user_id', $userId)
            ->where('enrollments.course_id', $courseId)
            ->get()
            ->getRowArray();

        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment not found.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'enrollment' => $enrollment
        ]);
    }

    /**
     * Apply LIKE filters to the courses table using Query Builder.
     */
    protected function getCourses(string $searchTerm): array
    {
        if ($searchTerm !== '') {
            $this->courseModel
                ->groupStart()
                ->like('course_name', $searchTerm)
                ->orLike('description', $searchTerm)
                ->groupEnd();
        }

        return $this->courseModel
            ->orderBy('course_name', 'ASC')
            ->findAll();
    }
}
