<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\AssignmentSubmissionModel;
use App\Models\EnrollmentModel;

class Assignment extends BaseController
{
    protected AssignmentModel $assignmentModel;
    protected AssignmentSubmissionModel $submissionModel;
    protected EnrollmentModel $enrollmentModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->submissionModel = new AssignmentSubmissionModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Teacher: List assignments for a course
     */
    public function courseAssignments($courseId)
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('user_id');

        // Check if teacher teaches this course (you might need to implement course ownership logic)
        // For now, allow access to any course

        $assignments = $this->assignmentModel->getAssignmentsByCourse($courseId);

        return view('assignments/course_assignments', [
            'assignments' => $assignments,
            'courseId' => $courseId
        ]);
    }

    /**
     * Teacher: Create new assignment form
     */
    public function create($courseId)
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        return view('assignments/create', ['courseId' => $courseId]);
    }

    /**
     * Teacher: Store new assignment
     */
    public function store()
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        // Build validation rules - attachment is optional
        $rules = [
            'course_id'   => 'required|integer',
            'title'       => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'due_date'    => 'required'
        ];

        // Only validate attachment if a file was actually uploaded
        $attachmentFile = $this->request->getFile('attachment');
        if ($attachmentFile && $attachmentFile->isValid()) {
            $rules['attachment'] = 'uploaded[attachment]|max_size[attachment,5120]|ext_in[attachment,pdf,doc,docx,txt,jpg,jpeg,png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $courseId = $this->request->getPost('course_id');
        $teacherId = (int) $session->get('user_id');

        // Handle file upload
        $attachmentPath = null;
        if ($this->request->getFile('attachment')->isValid()) {
            $file = $this->request->getFile('attachment');
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/assignments', $newName);
            $attachmentPath = 'uploads/assignments/' . $newName;
        }

        $data = [
            'course_id'   => $courseId,
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'due_date'    => $this->request->getPost('due_date'),
            'attachment'  => $attachmentPath,
            'created_by'  => $teacherId
        ];

        $assignmentId = $this->assignmentModel->insert($data);

        if ($assignmentId) {
            // Create notifications for all enrolled students
            $this->createAssignmentNotifications($assignmentId, $courseId, $data['title']);

            return redirect()->to('/assignments/course/' . $courseId)->with('success', 'Assignment created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create assignment.');
        }
    }

    /**
     * Teacher: View assignment submissions
     */
    public function submissions($assignmentId)
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $assignment = $this->assignmentModel->getAssignmentWithCourse($assignmentId);
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found.');
        }

        $submissions = $this->submissionModel->getSubmissionsByAssignment($assignmentId);

        return view('assignments/submissions', [
            'assignment' => $assignment,
            'submissions' => $submissions
        ]);
    }

    /**
     * Teacher: Grade a submission
     */
    public function grade($submissionId)
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $rules = [
            'grade' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'feedback' => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Validation failed: ' . implode(', ', $this->validator->getErrors())]);
        }

        $grade = $this->request->getPost('grade');
        $feedback = $this->request->getPost('feedback');

        if ($this->submissionModel->gradeSubmission($submissionId, $grade, $feedback)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Submission graded successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to grade submission.']);
        }
    }

    /**
     * Teacher: View all courses they can manage assignments for
     */
    public function teacherDashboard()
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $teacherId = (int) $session->get('user_id');

            // Get dashboard metrics specific to this teacher
            $totalCourses = $this->assignmentModel->getTeacherCourses($teacherId); // Count teacher's courses
            $totalCoursesCount = count($totalCourses); // Get count from actual result
            $totalStudents = $this->countTeacherStudents($teacherId); // Count enrolled students in teacher's courses
            $assignmentsPosted = $this->assignmentModel->getAssignmentsPostedCount();
            $pendingGrades = $this->assignmentModel->getPendingGradesCount($teacherId);

            // Get courses assigned to this teacher by admin
            $courses = $this->assignmentModel->getTeacherCourses($teacherId);

            // Get upcoming assignments only for teacher's courses
            $upcomingAssignments = $this->getTeacherUpcomingAssignments($teacherId, 7);

            // Get assignments needing grading
            $assignmentsNeedingGrading = $this->assignmentModel->getAssignmentsNeedingGrading($teacherId);

            return view('assignments/teacher_dashboard', [
                'courses' => $courses ?: [],
                'totalCourses' => $totalCoursesCount,
                'totalStudents' => $totalStudents,
                'assignmentsPosted' => $assignmentsPosted,
                'pendingGrades' => $pendingGrades,
                'upcomingAssignments' => $upcomingAssignments ?: [],
                'assignmentsNeedingGrading' => $assignmentsNeedingGrading ?: []
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in teacher dashboard: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Unable to load assignments at this time.');
        }
    }

    /**
     * Student: View all assignments across enrolled courses
     */
    public function studentDashboard()
    {
        $session = session();

        // Check if user is logged in and is a student
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $studentId = (int) $session->get('user_id');

            // Get all enrolled courses for this student
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $enrolledCourses = $enrollmentModel->getUserEnrollments($studentId);

            if (empty($enrolledCourses)) {
                return view('assignments/student_dashboard', [
                    'assignments' => [],
                    'enrolledCourses' => []
                ]);
            }

            $enrolledCourseIds = array_column($enrolledCourses, 'course_id');

            // Get assignments for all enrolled courses
            $assignments = $this->assignmentModel
                ->whereIn('course_id', $enrolledCourseIds)
                ->orderBy('due_date', 'ASC')
                ->findAll();

            // Get submission status for each assignment
            foreach ($assignments as &$assignment) {
                $submission = $this->submissionModel->getSubmission($assignment['id'], $studentId);
                $assignment['submission'] = $submission;
                $assignment['status'] = $submission ? $submission['status'] : 'not_submitted';

                // Add course name
                foreach ($enrolledCourses as $course) {
                    if ($course['course_id'] == $assignment['course_id']) {
                        $assignment['course_name'] = $course['course_name'];
                        $assignment['course_code'] = $course['course_code'];
                        break;
                    }
                }
            }

            return view('assignments/student_dashboard', [
                'assignments' => $assignments ?: [],
                'enrolledCourses' => $enrolledCourses ?: []
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in student dashboard: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Unable to load assignments at this time.');
        }
    }

    /**
     * Student: View assignments for a course
     */
    public function courseAssignmentsStudent($courseId)
    {
        $session = session();

        // Check if user is logged in and is a student
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $studentId = (int) $session->get('user_id');

        // Check if student is enrolled in this course
        if (!$this->enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
            return redirect()->to('/dashboard')->with('error', 'You are not enrolled in this course.');
        }

        $assignments = $this->assignmentModel->getAssignmentsByCourse($courseId);

        // Get submission status for each assignment
        foreach ($assignments as &$assignment) {
            $submission = $this->submissionModel->getSubmission($assignment['id'], $studentId);
            $assignment['submission'] = $submission;
            $assignment['status'] = $submission ? $submission['status'] : 'not_submitted';
        }

        return view('assignments/student_assignments', [
            'assignments' => $assignments,
            'courseId' => $courseId
        ]);
    }

    /**
     * Student: Submit assignment
     */
    public function submit($assignmentId)
    {
        $session = session();

        // Check if user is logged in and is a student
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'student') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $studentId = (int) $session->get('user_id');

        // Check if assignment exists and student is enrolled
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found.']);
        }

        if (!$this->enrollmentModel->isAlreadyEnrolled($studentId, $assignment['course_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'You are not enrolled in this course.']);
        }

        // Check if assignment is past due date
        $now = new \DateTime();
        $dueDate = new \DateTime($assignment['due_date']);

        if ($now > $dueDate) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment is past the due date.']);
        }

        // Build validation rules - either file OR text submission is required
        $rules = [
            'submission_notes' => 'permit_empty|max_length[1000]'
        ];

        // Check if either file or text submission is provided
        $submissionNotes = trim($this->request->getPost('submission_notes') ?? '');
        $hasText = !empty($submissionNotes);

        // Check file upload separately to avoid issues
        $submissionFile = $this->request->getFile('submission_file');
        $hasFile = ($submissionFile !== null && $submissionFile->isValid() && !$submissionFile->hasMoved());

        // Require at least one form of submission
        if (!$hasFile && !$hasText) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please provide either a file upload or text submission.']);
        }

        // Validate file only if provided
        if ($hasFile) {
            $rules['submission_file'] = 'uploaded[submission_file]|max_size[submission_file,10240]|ext_in[submission_file,pdf,doc,docx,txt,jpg,jpeg,png,zip]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Validation failed: ' . implode(', ', $this->validator->getErrors())]);
        }

        // Prepare submission data
        $submissionNotes = trim($this->request->getPost('submission_notes') ?? '');
        $data = [
            'submission_notes' => !empty($submissionNotes) ? $submissionNotes : null
        ];

        // Handle file upload only if file was provided
        if ($hasFile) {
            $file = $this->request->getFile('submission_file');
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/submissions', $newName);
            $data['submission_file'] = 'uploads/submissions/' . $newName;
        } else {
            $data['submission_file'] = null;
        }

        try {
            $result = $this->submissionModel->submitAssignment($assignmentId, $studentId, $data);

            if ($result) {
                // Create notification for the teacher who created the assignment
                $this->createSubmissionNotification($assignmentId, $studentId);

                return $this->response->setJSON(['success' => true, 'message' => 'Assignment submitted successfully!']);
            } else {
                // Check for database errors
                $db = \Config\Database::connect();
                $error = $db->error();
                $errorMessage = 'Failed to submit assignment.';
                if (!empty($error['message'])) {
                    $errorMessage .= ' Database error: ' . $error['message'];
                }
                return $this->response->setJSON(['success' => false, 'message' => $errorMessage]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Assignment submission error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Server error occurred while submitting assignment.']);
        }
    }

    /**
     * Download assignment attachment
     */
    public function downloadAssignment($assignmentId)
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment || !$assignment['attachment']) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $userId = (int) $session->get('user_id');
        $role = strtolower((string) $session->get('role'));

        // Check access permissions
        if ($role === 'student') {
            // Student must be enrolled in the course
            if (!$this->enrollmentModel->isAlreadyEnrolled($userId, $assignment['course_id'])) {
                return redirect()->back()->with('error', 'Access denied.');
            }
        } elseif ($role === 'teacher') {
            // Teacher must be the creator or have access to the course
            // For now, allow all teachers
        } else {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $filePath = WRITEPATH . $assignment['attachment'];
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null);
    }

    /**
     * Download submission file
     */
    public function downloadSubmission($submissionId)
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $submission = $this->submissionModel->find($submissionId);
        if (!$submission || !$submission['submission_file']) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $userId = (int) $session->get('user_id');
        $role = strtolower((string) $session->get('role'));

        // Check access permissions
        if ($role === 'student') {
            // Student can only download their own submissions
            if ($submission['student_id'] != $userId) {
                return redirect()->back()->with('error', 'Access denied.');
            }
        } elseif ($role === 'teacher') {
            // Teacher can download any submission for assignments they created
            $assignment = $this->assignmentModel->find($submission['assignment_id']);
            if (!$assignment || $assignment['created_by'] != $userId) {
                return redirect()->back()->with('error', 'Access denied.');
            }
        } else {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $filePath = WRITEPATH . $submission['submission_file'];
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null);
    }

    /**
     * Create notifications for all approved enrolled students when a new assignment is created
     */
    private function createAssignmentNotifications($assignmentId, $courseId, $assignmentTitle)
    {
        // Get all approved enrolled students for this course
        $enrolledStudents = $this->enrollmentModel
            ->where('course_id', $courseId)
            ->where('approval_status', 'approved')
            ->findAll();

        if (!empty($enrolledStudents)) {
            $notificationModel = new \App\Models\NotificationModel();

            // Create notification for each approved enrolled student
            foreach ($enrolledStudents as $enrollment) {
                $notificationModel->insert([
                    'user_id' => $enrollment['user_id'],
                    'message' => 'New assignment "' . $assignmentTitle . '" has been posted.',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'type' => 'assignment', // Optional: for filtering notifications
                    'reference_id' => $assignmentId // Optional: link to the assignment
                ]);
            }
        }
    }

    /**
     * Teacher: Manage Students in their courses
     */
    public function manageStudents()
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('user_id');

        // Get courses taught by this teacher
        $db = \Config\Database::connect();
        $courses = $db->table('courses')
            ->where('teacher_id', $teacherId)
            ->where('status', 'Active')
            ->select('id, course_code, course_name, school_year, semester')
            ->get()
            ->getResultArray();

        if (empty($courses)) {
            return view('teacher/manage_students', [
                'courses' => [],
                'students' => [],
                'filters' => [],
                'currentCourse' => null
            ]);
        }

        // Get selected course or default to first course
        $selectedCourseId = $this->request->getGet('course') ?: $courses[0]['id'];

        // Verify teacher teaches this course
        $selectedCourse = null;
        foreach ($courses as $course) {
            if ($course['id'] == $selectedCourseId) {
                $selectedCourse = $course;
                break;
            }
        }

        if (!$selectedCourse) {
            return redirect()->to('/teacher/manage-students')->with('error', 'Course not found.');
        }

        // Get all approved enrolled students for the selected course
        $students = $db->table('enrollments')
            ->select('enrollments.*, users.name, users.email')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $selectedCourseId)
            ->where('enrollments.approval_status', 'approved')
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();

        return view('teacher/manage_students', [
            'courses' => $courses,
            'students' => $students,
            'currentCourse' => $selectedCourse
        ]);
    }

    /**
     * Teacher: Update Student Status
     */
    public function updateStudentStatus()
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $teacherId = (int) $session->get('user_id');
        $enrollmentId = $this->request->getPost('enrollment_id');
        $newStatus = $this->request->getPost('new_status');
        $remarks = $this->request->getPost('remarks');

        if (!$enrollmentId || !$newStatus) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields.']);
        }

        // Verify enrollment exists and teacher has access
        $db = \Config\Database::connect();
        $enrollment = $db->table('enrollments')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.id', $enrollmentId)
            ->where('courses.teacher_id', $teacherId)
            ->select('enrollments.id, courses.course_name')
            ->get()
            ->getRow();

        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found or access denied.']);
        }

        // Update student status
        $updateData = ['status' => $newStatus];
        if (!empty($remarks)) {
            $updateData['enrollment_status'] = $remarks;
        }

        if ($this->enrollmentModel->update($enrollmentId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student status updated successfully.'
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update student status.']);
    }

    /**
     * Teacher: Get Student Details for Modal
     */
    public function getStudentDetails($studentId)
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $teacherId = (int) $session->get('user_id');

        // Get student details with enrollment info and course verification
        $db = \Config\Database::connect();
        $studentDetails = $db->table('enrollments')
            ->select('enrollments.*, users.name, users.email, courses.course_name, courses.course_code, courses.school_year, courses.semester')
            ->join('users', 'users.id = enrollments.user_id')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.user_id', $studentId)
            ->where('courses.teacher_id', $teacherId)
            ->where('enrollments.status !=', 'Dropped') // Don't show dropped students in details
            ->get()
            ->getResultArray();

        if (empty($studentDetails)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Student not found or access denied.']);
        }

        // Return details for all courses this student is enrolled in with this teacher
        return $this->response->setJSON(['success' => true, 'student' => $studentDetails]);
    }

    /**
     * Count total students enrolled in teacher's courses (approved enrollments only)
     */
    private function countTeacherStudents($teacherId)
    {
        try {
            return $this->db->table('enrollments')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->where('courses.teacher_id', $teacherId)
                ->where('enrollments.approval_status', 'approved')
                ->where('enrollments.status !=', 'Dropped')
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error counting teacher students: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get upcoming assignments for teacher's courses
     */
    private function getTeacherUpcomingAssignments($teacherId, $daysAhead = 7)
    {
        try {
            $futureDate = date('Y-m-d H:i:s', strtotime("+{$daysAhead} days"));
            $currentDate = date('Y-m-d H:i:s');

            return $this->db->table('assignments')
                ->select('assignments.*, courses.course_name, courses.course_code')
                ->join('courses', 'courses.id = assignments.course_id')
                ->where('courses.teacher_id', $teacherId)
                ->where('assignments.due_date >', $currentDate)
                ->where('assignments.due_date <=', $futureDate)
                ->orderBy('assignments.due_date', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting teacher upcoming assignments: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create notification for teacher when a student submits an assignment
     */
    private function createSubmissionNotification($assignmentId, $studentId)
    {
        // Get assignment details
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment) {
            return;
        }

        // Get student details
        $userModel = new \App\Models\UserModel();
        $student = $userModel->find($studentId);
        if (!$student) {
            return;
        }

        // Create notification for the teacher who created the assignment
        $notificationModel = new \App\Models\NotificationModel();
        $notificationModel->insert([
            'user_id' => $assignment['created_by'],
            'message' => 'Student ' . $student['name'] . ' has submitted assignment "' . $assignment['title'] . '".',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'type' => 'submission', // Optional: for filtering notifications
            'reference_id' => $assignmentId // Optional: link to the assignment
        ]);
    }


}
