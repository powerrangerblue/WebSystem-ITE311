<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table            = 'assignments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'course_id',
        'title',
        'description',
        'due_date',
        'attachment',
        'created_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'course_id'   => 'required|integer',
        'title'       => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
        'due_date'    => 'required|valid_date',
        'attachment'  => 'permit_empty|max_length[255]',
        'created_by'  => 'required|integer'
    ];

    protected $validationMessages = [
        'course_id' => [
            'required' => 'Course ID is required.',
            'integer'  => 'Course ID must be an integer.'
        ],
        'title' => [
            'required'   => 'Assignment title is required.',
            'min_length' => 'Title must be at least 3 characters.',
            'max_length' => 'Title cannot exceed 255 characters.'
        ],
        'description' => [
            'required'   => 'Assignment description is required.',
            'min_length' => 'Description must be at least 10 characters.'
        ],
        'due_date' => [
            'required'    => 'Due date is required.',
            'valid_date'  => 'Please provide a valid due date.'
        ],
        'created_by' => [
            'required' => 'Creator ID is required.',
            'integer'  => 'Creator ID must be an integer.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get assignments for a specific course
     */
    public function getAssignmentsByCourse($courseId)
    {
        return $this->select('assignments.*, users.name as creator_name')
                    ->join('users', 'users.id = assignments.created_by')
                    ->where('assignments.course_id', $courseId)
                    ->orderBy('assignments.due_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get assignments created by a teacher
     */
    public function getAssignmentsByTeacher($teacherId)
    {
        return $this->where('created_by', $teacherId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get a single assignment with course information
     */
    public function getAssignmentWithCourse($assignmentId)
    {
        return $this->select('assignments.*, courses.course_name, courses.course_code')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->where('assignments.id', $assignmentId)
                    ->first();
    }

    /**
     * Get all courses with their assignment counts
     */
    public function getAllCoursesWithAssignments()
    {
        try {
            // Get all courses with assignment counts
            $courses = $this->db->table('courses')
                ->select('courses.*, COUNT(assignments.id) as assignment_count')
                ->join('assignments', 'assignments.course_id = courses.id', 'left')
                ->groupBy('courses.id')
                ->orderBy('courses.course_name', 'ASC')
                ->get()
                ->getResultArray();

            return $courses ?: [];
        } catch (\Exception $e) {
            log_message('error', 'Error getting courses with assignments: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total courses count
     */
    public function getTotalCoursesCount()
    {
        try {
            return $this->db->table('courses')->countAll();
        } catch (\Exception $e) {
            log_message('error', 'Error getting total courses count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total active students count
     */
    public function getTotalActiveStudentsCount()
    {
        try {
            return $this->db->table('users')
                ->where('role', 'student')
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error getting total active students count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total assignments posted count
     */
    public function getAssignmentsPostedCount()
    {
        try {
            return $this->countAll();
        } catch (\Exception $e) {
            log_message('error', 'Error getting assignments posted count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get pending grades count for a teacher
     */
    public function getPendingGradesCount($teacherId)
    {
        try {
            return $this->db->table('submissions')
                ->join('assignments', 'assignments.id = submissions.assignment_id')
                ->where('assignments.created_by', $teacherId)
                ->where('submissions.status', 'submitted')
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error getting pending grades count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get upcoming assignments within specified days
     */
    public function getUpcomingAssignments($daysAhead = 7)
    {
        try {
            $futureDate = date('Y-m-d H:i:s', strtotime("+{$daysAhead} days"));
            $currentDate = date('Y-m-d H:i:s');

            return $this->select('assignments.*, courses.course_name, courses.course_code')
                ->join('courses', 'courses.id = assignments.course_id')
                ->where('assignments.due_date >', $currentDate)
                ->where('assignments.due_date <=', $futureDate)
                ->orderBy('assignments.due_date', 'ASC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error getting upcoming assignments: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get assignments needing grading for a teacher
     */
    public function getAssignmentsNeedingGrading($teacherId)
    {
        try {
            // Get assignments created by this teacher that have submitted but ungraded submissions
            $assignments = $this->db->table('assignments')
                ->select('assignments.*, courses.course_name, courses.course_code, COUNT(submissions.id) as pending_count')
                ->join('courses', 'courses.id = assignments.course_id')
                ->join('submissions', 'submissions.assignment_id = assignments.id AND submissions.status = "submitted"', 'left')
                ->where('assignments.created_by', $teacherId)
                ->groupBy('assignments.id')
                ->having('pending_count >', 0)
                ->orderBy('assignments.due_date', 'ASC')
                ->get()
                ->getResultArray();

            return $assignments ?: [];
        } catch (\Exception $e) {
            log_message('error', 'Error getting assignments needing grading: ' . $e->getMessage());
            return [];
        }
    }
}
