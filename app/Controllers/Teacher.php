<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Teacher extends Controller
{
    public function dashboard()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = strtolower((string) $session->get('role'));
        if ($role !== 'teacher') {
            $session->setFlashdata('error', 'Unauthorized access to teacher area.');
            return redirect()->to('/');
        }

        $teacherId = (int) $session->get('user_id');

        $db = \Config\Database::connect();
        $courses = [];
        try {
            $courses = $db->table('courses')
                ->where('teacher_id', $teacherId)
                ->orderBy('created_at', 'DESC')
                ->get(10)
                ->getResultArray();
        } catch (\Throwable $e) {
            $courses = [];
        }

        $notifications = [];
        try {
            $notifications = $db->table('submissions')
                ->select('student_name, course_id, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            $notifications = [];
        }

        $data = [
            'title' => 'Teacher Dashboard',
            'courses' => $courses,
            'notifications' => $notifications,
        ];

        return view('teacher/dashboard', $data);
    }
}


