<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'course_id',
        'student_id',
        'program',
        'year_level',
        'section',
        'status',
        'enrollment_status',
        'enrollment_date'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'enrollment_date';
    protected $updatedField = '';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'course_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required.',
            'integer' => 'User ID must be an integer.'
        ],
        'course_id' => [
            'required' => 'Course ID is required.',
            'integer' => 'Course ID must be an integer.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setEnrollmentDate'];

    /**
     * Set enrollment date before insert
     */
    protected function setEnrollmentDate(array $data)
    {
        if (!isset($data['data']['enrollment_date'])) {
            $data['data']['enrollment_date'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Enroll a user in a course
     * 
     * @param array $data Enrollment data
     * @return bool|int Insert ID on success, false on failure
     */
    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    /**
     * Get all courses a user is enrolled in
     * 
     * @param int $user_id User ID
     * @return array Array of enrolled courses
     */
    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.course_code, courses.course_name, courses.description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return bool True if enrolled, false otherwise
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        $enrollment = $this->where('user_id', $user_id)
                          ->where('course_id', $course_id)
                          ->first();
        
        return $enrollment !== null;
    }

    /**
     * Get enrollment count for a specific course
     * 
     * @param int $course_id Course ID
     * @return int Number of enrollments
     */
    public function getCourseEnrollmentCount($course_id)
    {
        return $this->where('course_id', $course_id)->countAllResults();
    }

    /**
     * Get all enrollments for a specific course
     * 
     * @param int $course_id Course ID
     * @return array Array of enrollments with user data
     */
    public function getCourseEnrollments($course_id)
    {
        return $this->select('enrollments.*, users.name as user_name, users.email as user_email')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->where('enrollments.course_id', $course_id)
                    ->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }
}
