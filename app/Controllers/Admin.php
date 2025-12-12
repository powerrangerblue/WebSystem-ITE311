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

        $courseId = $input['course_id'] ?? null;

        if (!$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID not provided']);
        }

        // Get existing course data to check what changed
        $existingCourse = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
        if (!$existingCourse) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        // Check if dates have changed
        $startDate = $input['start_date'] ?? null;
        $endDate = $input['end_date'] ?? null;
        $schoolYear = $input['school_year'] ?? null;

        $datesChanged = ($startDate !== $existingCourse['start_date']) ||
                        ($endDate !== $existingCourse['end_date']) ||
                        ($schoolYear !== $existingCourse['school_year']);

        // Validate dates against academic year ONLY if dates have changed
        if ($datesChanged && !empty($startDate) && !empty($endDate) && !empty($schoolYear)) {
            if (strtotime($endDate) <= strtotime($startDate)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'End date must be after the start date'
                ]);
            }

            // Validate against academic year
            if (!$this->validateDatesAgainstAcademicYear($startDate, $endDate, $schoolYear)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'The Start Date and End Date must match the selected Academic Year (' . $schoolYear . ').'
                ]);
            }
        }

        // Check for schedule conflict if teacher is assigned
        $teacherId = $input['teacher_id'] ?? null;
        $schedule = $input['schedule'] ?? null;
        $dayOfClass = $input['day_of_class'] ?? null;

        if ($teacherId && $schedule && $dayOfClass) {
            // Check for schedule conflicts
            $conflict = $this->checkScheduleConflict($teacherId, $schedule, $dayOfClass, $input['semester'], $input['school_year'], $courseId);
            if ($conflict) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Schedule conflict detected: This teacher is already assigned to another course during the same days and times.'
                ]);
            }
        }

        $data = [
            'course_name' => $input['course_name'] ?? null,
            'description' => $input['description'] ?? null,
            'year_level' => $input['year_level'] ?? null,
            'school_year' => $input['school_year'] ?? null,
            'semester' => $input['semester'] ?? null,
            'schedule' => $input['schedule'] ?? null,
            'room' => $input['room'] ?? null,
            'day_of_class' => $input['day_of_class'] ?? null,
            'teacher_id' => $input['teacher_id'] ?? null,
            'start_date' => $input['start_date'] ?? null,
            'end_date' => $input['end_date'] ?? null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('courses')->where('id', $courseId)->update($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Course updated successfully']);
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

        // Validate dates: end_date should be after start_date and match academic year
        $startDate = $input['start_date'] ?? null;
        $endDate = $input['end_date'] ?? null;
        $schoolYear = $input['school_year'] ?? null;

        if (!empty($startDate) && !empty($endDate)) {
            if (strtotime($endDate) <= strtotime($startDate)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'End date must be after the start date'
                ]);
            }

            // Validate against academic year if school year is provided
            if (!empty($schoolYear)) {
                if (!$this->validateDatesAgainstAcademicYear($startDate, $endDate, $schoolYear)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'The Start Date and End Date must match the selected Academic Year (' . $schoolYear . ').'
                    ]);
                }
            }
        }

        // Check for schedule conflict if teacher is assigned
        $teacherId = $input['teacher_id'] ?? null;
        $schedule = $input['schedule'] ?? null;
        $dayOfClass = $input['day_of_class'] ?? null;

        if ($teacherId && $schedule && $dayOfClass) {
            // Check for schedule conflicts
            $conflict = $this->checkScheduleConflict($teacherId, $schedule, $dayOfClass, $input['semester'], $input['school_year']);
            if ($conflict) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Schedule conflict detected: This teacher is already assigned to another course during the same days and times.'
                ]);
            }
        }

        $data = [
            'course_code' => $input['course_code'],
            'course_name' => $input['course_name'],
            'description' => $input['description'] ?? null,
            'year_level' => $input['year_level'] ?? null,
            'school_year' => $input['school_year'] ?? null,
            'semester' => $input['semester'] ?? null,
            'schedule' => $input['schedule'] ?? null,
            'room' => $input['room'] ?? null,
            'day_of_class' => $input['day_of_class'] ?? null,
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

    /**
     * Admin: Manage Enrollment Requests
     */
    public function manageEnrollmentRequests()
    {
        // Check if user is logged in and is an admin
        if (!session()->get('isLoggedIn') || strtolower((string) session()->get('role')) !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Get all pending enrollment requests
        $pendingRequests = $enrollmentModel->getAllPendingEnrollments();

        return view('admin/enrollment_requests', [
            'pendingRequests' => $pendingRequests
        ]);
    }

    /**
     * Admin: Process Enrollment Request (Approve/Decline)
     */
    public function processEnrollmentRequest()
    {
        // Check if user is logged in and is an admin
        if (!session()->get('isLoggedIn') || strtolower((string) session()->get('role')) !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $adminId = session()->get('user_id');
        $enrollmentId = $this->request->getPost('enrollment_id');
        $action = $this->request->getPost('action'); // 'approve' or 'decline'
        $notes = $this->request->getPost('notes');

        if (!$enrollmentId || !in_array($action, ['approve', 'decline'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request parameters.']);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Verify the enrollment exists and is pending
        $enrollment = $enrollmentModel->where('id', $enrollmentId)->where('approval_status', 'pending')->first();

        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment request not found or already processed.']);
        }

        // Get course and student details for notification
        $db = \Config\Database::connect();
        $enrollmentDetails = $db->table('enrollments')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.id', $enrollmentId)
            ->select('enrollments.*, courses.course_name, users.name as student_name, users.email as student_email')
            ->get()
            ->getRowArray();

        if (!$enrollmentDetails) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment details not found.']);
        }

        // Process the enrollment request
        if ($enrollmentModel->processEnrollmentRequest($enrollmentId, $action, $adminId, $notes)) {
            // Create notification for the student
            $notificationModel = new \App\Models\NotificationModel();
            $actionText = ($action === 'approve') ? 'approved' : 'declined';
            $message = 'Your enrollment request for ' . $enrollmentDetails['course_name'] . ' has been ' . $actionText . ' by an administrator.';

            if (!empty($notes)) {
                $message .= ' Notes: ' . $notes;
            }

            $notificationModel->insert([
                'user_id' => $enrollmentDetails['user_id'],
                'message' => $message,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // If enrollment is approved, notify the teacher
            if ($action === 'approve') {
                // Get the teacher assigned to this course
                $course = $db->table('courses')
                    ->select('teacher_id')
                    ->where('id', $enrollmentDetails['course_id'])
                    ->get()
                    ->getRowArray();

                if ($course && $course['teacher_id']) {
                    // Create notification for the teacher
                    $notificationModel->insert([
                        'user_id' => $course['teacher_id'],
                        'message' => 'Student ' . $enrollmentDetails['student_name'] . ' has been approved for enrollment in your course: ' . $enrollmentDetails['course_name'],
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'type' => 'enrollment',
                        'reference_id' => $enrollmentDetails['id'], // Reference to the enrollment ID
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment request ' . $actionText . ' successfully.'
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to process enrollment request.']);
    }

    /**
     * Check for schedule conflicts when assigning a teacher to a course
     */
    private function checkScheduleConflict($teacherId, $newSchedule, $newDays, $newSemester, $newSchoolYear, $excludeCourseId = null)
    {
        $db = \Config\Database::connect();

        // Parse new course schedule and days
        $newTimeRange = $this->parseSchedule($newSchedule);
        $newDaysArray = $this->parseDays($newDays);

        if (!$newTimeRange || empty($newDaysArray)) {
            return false; // No conflict if schedule is invalid
        }

        // Query other courses by this teacher in the same semester and school year
        $query = $db->table('courses')
            ->where('teacher_id', $teacherId)
            ->where('status', 'Active')
            ->where('semester', $newSemester)
            ->where('school_year', $newSchoolYear);

        if ($excludeCourseId) {
            $query->where('id !=', $excludeCourseId);
        }

        $existingCourses = $query->get()->getResultArray();

        foreach ($existingCourses as $course) {
            if (empty($course['schedule']) || empty($course['day_of_class'])) {
                continue; // Skip courses without schedule
            }

            $existingTimeRange = $this->parseSchedule($course['schedule']);
            $existingDaysArray = $this->parseDays($course['day_of_class']);

            if (!$existingTimeRange || empty($existingDaysArray)) {
                continue;
            }

            // Check if days overlap
            $daysOverlap = !empty(array_intersect($newDaysArray, $existingDaysArray));
            if (!$daysOverlap) {
                continue;
            }

            // Check if times overlap
            if ($this->timesOverlap($newTimeRange, $existingTimeRange)) {
                return true; // Conflict found
            }
        }

        return false; // No conflict
    }

    /**
     * Parse schedule string like "08:00–09:00" into start and end times
     */
    private function parseSchedule($schedule)
    {
        if (empty($schedule)) {
            return null;
        }

        // Handle different formats: "08:00–09:00" or "08:00 - 09:00"
        // Try different approaches for the en dash
        if (strpos($schedule, '–') !== false) {
            $parts = explode('–', $schedule);
        } elseif (strpos($schedule, '-') !== false) {
            $parts = explode('-', $schedule);
        } else {
            $parts = [$schedule]; // No separator found
        }

        if (count($parts) !== 2) {
            return null;
        }

        $startTime = trim($parts[0]);
        $endTime = trim($parts[1]);

        // Convert to minutes since midnight for easier comparison
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes = $this->timeToMinutes($endTime);

        if ($startMinutes === false || $endMinutes === false || $startMinutes >= $endMinutes) {
            return null;
        }

        return ['start' => $startMinutes, 'end' => $endMinutes];
    }

    /**
     * Parse days string like "Mon, Wed, Fri" into array
     */
    private function parseDays($daysString)
    {
        if (empty($daysString)) {
            return [];
        }

        $days = array_map('trim', explode(',', $daysString));

        // Normalize day names to full names for consistent comparison
        $normalizedDays = [];
        foreach ($days as $day) {
            $normalized = $this->normalizeDayName($day);
            if ($normalized) {
                $normalizedDays[] = $normalized;
            }
        }

        return array_unique($normalizedDays);
    }

    /**
     * Normalize day abbreviations to full names
     */
    private function normalizeDayName($day)
    {
        $day = trim(strtolower($day));

        $dayMappings = [
            'mon' => 'monday',
            'tue' => 'tuesday',
            'wed' => 'wednesday',
            'thu' => 'thursday',
            'fri' => 'friday',
            'sat' => 'saturday',
            'sun' => 'sunday',
            'monday' => 'monday',
            'tuesday' => 'tuesday',
            'wednesday' => 'wednesday',
            'thursday' => 'thursday',
            'friday' => 'friday',
            'saturday' => 'saturday',
            'sunday' => 'sunday'
        ];

        return $dayMappings[$day] ?? null;
    }

    /**
     * Convert time string like "08:00" to minutes since midnight
     */
    private function timeToMinutes($time)
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $time, $matches)) {
            return false;
        }

        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];

        if ($hours < 0 || $hours > 23 || $minutes < 0 || $minutes > 59) {
            return false;
        }

        return $hours * 60 + $minutes;
    }

    /**
     * Check if two time ranges overlap
     */
    private function timesOverlap($range1, $range2)
    {
        // Two ranges overlap if one starts before the other ends and ends after the other starts
        return $range1['start'] < $range2['end'] && $range1['end'] > $range2['start'];
    }

    /**
     * Validate that start and end dates match the academic year years
     * Academic year format: "2025–2026" corresponds to Start Date year = 2025, End Date year = 2026
     */
    private function validateDatesAgainstAcademicYear($startDate, $endDate, $schoolYear)
    {
        if (empty($startDate) || empty($endDate) || empty($schoolYear)) {
            return false;
        }

        // Parse school year (e.g., "2025–2026")
        if (!preg_match('/^(\d{4})[–\-](\d{4})$/', $schoolYear, $matches)) {
            return false; // Invalid format
        }

        $startYear = (int) $matches[1];
        $endYear = (int) $matches[2];

        // Extract years from dates
        $startDateYear = (int) date('Y', strtotime($startDate));
        $endDateYear = (int) date('Y', strtotime($endDate));

        // Start Date year must match the first year, End Date year must match the second year
        return ($startDateYear === $startYear) && ($endDateYear === $endYear);
    }

}
