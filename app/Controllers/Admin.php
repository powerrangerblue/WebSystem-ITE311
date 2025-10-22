<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends BaseController
{
    public function dashboard()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access the admin dashboard.');
            return redirect()->to('login');
        }

        // Check if user has admin role
        $role = strtolower((string) $session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('error', 'Access denied. Admin role required.');
            return redirect()->to('login');
        }

        // Get dashboard statistics
        $db = \Config\Database::connect();

        $totalUsers = $db->table('users')->countAllResults();
        $totalAdmins = $db->table('users')->where('role', 'admin')->countAllResults();
        $totalTeachers = $db->table('users')->where('role', 'teacher')->countAllResults();
        $totalStudents = $db->table('users')->where('role', 'student')->countAllResults();

        $recentUsers = $db->table('users')
            ->select('name, email, role, created_at')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Prepare data for view
        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role,
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalTeachers' => $totalTeachers,
            'totalStudents' => $totalStudents,
            'recentUsers' => $recentUsers
        ];

        return view('admin_dashboard', $data);
    }

    public function courses()
    {
        // Check if user is logged in and admin
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('login');
        }

        $db = \Config\Database::connect();
        $courses = $db->table('courses')->get()->getResultArray();

        return view('admin_courses', ['courses' => $courses]);
    }

    public function materials()
    {
        // Check if user is logged in and admin
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('login');
        }

        $db = \Config\Database::connect();
        $courses = $db->table('courses')->get()->getResultArray();

        // Get materials count for each course
        $materialModel = new \App\Models\MaterialModel();
        foreach ($courses as &$course) {
            $course['materials_count'] = $materialModel->where('course_id', $course['id'])->countAllResults();
        }

        return view('admin_materials', ['courses' => $courses]);
    }
}
