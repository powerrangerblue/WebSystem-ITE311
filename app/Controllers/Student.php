<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Student extends Controller
{
	public function dashboard()
	{
		$session = session();

		if (!$session->get('isLoggedIn')) {
			return redirect()->to('login');
		}

		$role = strtolower((string) $session->get('role'));
		if ($role !== 'student') {
			$session->setFlashdata('error', 'Unauthorized access to student area.');
			return redirect()->to('/');
		}

		$studentId = (int) $session->get('user_id');

		$db = \Config\Database::connect();
		$enrolledCourses = [];
		$upcomingDeadlines = [];
		$recentGrades = [];
		try {
			$enrolledCourses = $db->table('enrollments e')
				->select('c.id, c.title, c.description, c.created_at')
				->join('courses c', 'c.id = e.course_id', 'left')
				->where('e.student_id', $studentId)
				->orderBy('c.title', 'ASC')
				->get()
				->getResultArray();
		} catch (\Throwable $e) {
			$enrolledCourses = [];
		}

		try {
			$upcomingDeadlines = $db->table('assignments a')
				->select('a.id, a.title, a.due_date, c.title as course_title')
				->join('courses c', 'c.id = a.course_id', 'left')
				->where('a.due_date >=', date('Y-m-d'))
				->orderBy('a.due_date', 'ASC')
				->limit(5)
				->get()
				->getResultArray();
		} catch (\Throwable $e) {
			$upcomingDeadlines = [];
		}

		try {
			$recentGrades = $db->table('grades g')
				->select('g.score, g.created_at, a.title as assignment_title, c.title as course_title')
				->join('assignments a', 'a.id = g.assignment_id', 'left')
				->join('courses c', 'c.id = a.course_id', 'left')
				->where('g.student_id', $studentId)
				->orderBy('g.created_at', 'DESC')
				->limit(5)
				->get()
				->getResultArray();
		} catch (\Throwable $e) {
			$recentGrades = [];
		}

		$data = [
			'title' => 'Student Dashboard',
			'enrolledCourses' => $enrolledCourses,
			'upcomingDeadlines' => $upcomingDeadlines,
			'recentGrades' => $recentGrades,
		];

		return view('student/dashboard', $data);
	}
}


