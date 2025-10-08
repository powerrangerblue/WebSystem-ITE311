<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'course_code',
        'course_name',
        'description'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'course_code' => 'required|min_length[3]|max_length[20]|is_unique[courses.course_code]',
        'course_name' => 'required|min_length[3]|max_length[150]',
        'description' => 'permit_empty|max_length[1000]'
    ];

    protected $validationMessages = [
        'course_code' => [
            'required' => 'Course code is required.',
            'min_length' => 'Course code must be at least 3 characters long.',
            'max_length' => 'Course code cannot exceed 20 characters.',
            'is_unique' => 'This course code already exists.'
        ],
        'course_name' => [
            'required' => 'Course name is required.',
            'min_length' => 'Course name must be at least 3 characters long.',
            'max_length' => 'Course name cannot exceed 150 characters.'
        ],
        'description' => [
            'max_length' => 'Description cannot exceed 1000 characters.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Get all available courses (not enrolled by user)
     * 
     * @param int $user_id User ID to check enrollments
     * @return array Array of available courses
     */
    public function getAvailableCourses($user_id = null)
    {
        $builder = $this->db->table('courses');
        
        if ($user_id !== null) {
            // Get courses that the user is not enrolled in
            $builder->whereNotIn('id', function($query) use ($user_id) {
                return $query->select('course_id')
                           ->from('enrollments')
                           ->where('user_id', $user_id);
            });
        }
        
        return $builder->orderBy('course_name', 'ASC')->get()->getResultArray();
    }

    /**
     * Get course by ID with enrollment count
     * 
     * @param int $course_id Course ID
     * @return array|null Course data with enrollment count
     */
    public function getCourseWithEnrollmentCount($course_id)
    {
        $course = $this->find($course_id);
        
        if ($course) {
            $enrollmentModel = new EnrollmentModel();
            $course['enrollment_count'] = $enrollmentModel->getCourseEnrollmentCount($course_id);
        }
        
        return $course;
    }

    /**
     * Get all courses with enrollment counts
     * 
     * @return array Array of courses with enrollment counts
     */
    public function getAllCoursesWithEnrollmentCount()
    {
        $courses = $this->orderBy('course_name', 'ASC')->findAll();
        
        $enrollmentModel = new EnrollmentModel();
        foreach ($courses as &$course) {
            $course['enrollment_count'] = $enrollmentModel->getCourseEnrollmentCount($course['id']);
        }
        
        return $courses;
    }
}
