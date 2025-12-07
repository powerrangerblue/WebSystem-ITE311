<?php

namespace App\Models;

use CodeIgniter\Model;

class GradesModel extends Model
{
    protected $table            = 'grades';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'student_id',
        'course_id',
        'grade',
        'updated_at'
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
        'student_id' => 'required|integer',
        'course_id'  => 'required|integer',
        'grade'      => 'permit_empty|max_length[10]'
    ];

    protected $validationMessages = [
        'student_id' => [
            'required' => 'Student ID is required.',
            'integer'  => 'Student ID must be an integer.'
        ],
        'course_id' => [
            'required' => 'Course ID is required.',
            'integer'  => 'Course ID must be an integer.'
        ],
        'grade' => [
            'max_length' => 'Grade cannot exceed 10 characters.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = false;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get grade for a specific student and course
     */
    public function getGrade($studentId, $courseId)
    {
        return $this->where('student_id', $studentId)
                    ->where('course_id', $courseId)
                    ->first();
    }

    /**
     * Update or create grade for a student in a course
     */
    public function updateGrade($studentId, $courseId, $grade)
    {
        $existing = $this->getGrade($studentId, $courseId);

        if ($existing) {
            // Update existing grade
            return $this->update($existing['id'], ['grade' => $grade]);
        } else {
            // Create new grade record
            return $this->insert([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'grade' => $grade
            ]);
        }
    }

    /**
     * Calculate course grade based on assignment grades
     */
    public function calculateCourseGrade($studentId, $courseId)
    {
        // Get all graded assignments for this course and student
        $db = \Config\Database::connect();
        $assignments = $db->table('assignments')
                          ->select('assignments.id, assignments.title')
                          ->where('assignments.course_id', $courseId)
                          ->get()
                          ->getResultArray();

        if (empty($assignments)) {
            return null; // No assignments in course
        }

        $totalGrade = 0;
        $gradedCount = 0;

        foreach ($assignments as $assignment) {
            $submission = $db->table('submissions')
                            ->where('assignment_id', $assignment['id'])
                            ->where('student_id', $studentId)
                            ->where('status', 'graded')
                            ->get()
                            ->getRowArray();

            if ($submission && !is_null($submission['grade'])) {
                $totalGrade += (float) $submission['grade'];
                $gradedCount++;
            }
        }

        if ($gradedCount === 0) {
            return null; // No graded assignments yet
        }

        // Calculate average grade
        $averageGrade = $totalGrade / $gradedCount;

        // Convert to letter grade or keep as numeric
        return round($averageGrade, 2);
    }
}
