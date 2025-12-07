<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentSubmissionModel extends Model
{
    protected $table            = 'submissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'assignment_id',
        'student_id',
        'submission_file',
        'submission_notes',
        'grade',
        'feedback',
        'status',
        'submitted_at',
        'graded_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    // Validation
    protected $validationRules = [
        'assignment_id'   => 'required|integer',
        'student_id'      => 'required|integer',
        'submission_file' => 'permit_empty|max_length[255]',
        'submission_notes'=> 'permit_empty',
        'grade'           => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'feedback'        => 'permit_empty',
        'status'          => 'permit_empty|in_list[not_submitted,submitted,graded]',
        'submitted_at'    => 'permit_empty|valid_date',
        'graded_at'       => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'assignment_id' => [
            'required' => 'Assignment ID is required.',
            'integer'  => 'Assignment ID must be an integer.'
        ],
        'student_id' => [
            'required' => 'Student ID is required.',
            'integer'  => 'Student ID must be an integer.'
        ],
        'grade' => [
            'numeric'                   => 'Grade must be a number.',
            'greater_than_equal_to'     => 'Grade must be between 0 and 100.',
            'less_than_equal_to'        => 'Grade must be between 0 and 100.'
        ],
        'status' => [
            'in_list' => 'Status must be one of: not_submitted, submitted, graded.'
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
     * Get submission for a specific assignment and student
     */
    public function getSubmission($assignmentId, $studentId)
    {
        return $this->where('assignment_id', $assignmentId)
                    ->where('student_id', $studentId)
                    ->first();
    }

    /**
     * Get all submissions for an assignment
     */
    public function getSubmissionsByAssignment($assignmentId)
    {
        return $this->select('submissions.*, users.name as student_name, users.email as student_email')
                    ->join('users', 'users.id = submissions.student_id')
                    ->where('submissions.assignment_id', $assignmentId)
                    ->orderBy('submissions.submitted_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get student submissions for a course
     */
    public function getStudentSubmissionsByCourse($studentId, $courseId)
    {
        return $this->select('submissions.*, assignments.title, assignments.due_date, assignments.attachment as assignment_attachment')
                    ->join('assignments', 'assignments.id = submissions.assignment_id')
                    ->where('submissions.student_id', $studentId)
                    ->where('assignments.course_id', $courseId)
                    ->orderBy('assignments.due_date', 'ASC')
                    ->findAll();
    }

    /**
     * Check if student has submitted assignment
     */
    public function hasSubmitted($assignmentId, $studentId)
    {
        $submission = $this->where('assignment_id', $assignmentId)
                          ->where('student_id', $studentId)
                          ->where('status', 'submitted')
                          ->first();
        return $submission !== null;
    }

    /**
     * Create or update submission
     */
    public function submitAssignment($assignmentId, $studentId, $data)
    {
        $existing = $this->getSubmission($assignmentId, $studentId);

        if ($existing) {
            // Update existing submission
            return $this->update($existing['id'], array_merge($data, [
                'status' => 'submitted',
                'submitted_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]));
        } else {
            // Create new submission
            return $this->insert(array_merge($data, [
                'assignment_id' => $assignmentId,
                'student_id' => $studentId,
                'status' => 'submitted',
                'submitted_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]));
        }
    }

    /**
     * Grade a submission and update course grade
     */
    public function gradeSubmission($submissionId, $grade, $feedback = null)
    {
        // First, update the submission
        $updateResult = $this->update($submissionId, [
            'grade' => $grade,
            'feedback' => $feedback,
            'status' => 'graded',
            'graded_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($updateResult) {
            // Get submission details to find course and student
            $submission = $this->find($submissionId);
            if ($submission) {
                // Get assignment details to find course
                $db = \Config\Database::connect();
                $assignment = $db->table('assignments')
                                ->where('id', $submission['assignment_id'])
                                ->get()
                                ->getRowArray();

                if ($assignment) {
                    // Update course grade with the assignment grade
                    $gradesModel = new \App\Models\GradesModel();
                    $gradesModel->updateGrade($submission['student_id'], $assignment['course_id'], $grade);
                }
            }
        }

        return $updateResult;
    }
}
