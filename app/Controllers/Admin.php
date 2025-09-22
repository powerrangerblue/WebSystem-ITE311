<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Admin extends Controller
{
    public function dashboard()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = strtolower((string) $session->get('role'));
        if ($role !== 'admin') {
            // Prevent access for non-admins
            $session->setFlashdata('error', 'Unauthorized access to admin area.');
            return redirect()->to('dashboard');
        }

        // Example admin data: totals
        $userModel = new UserModel();
        $totalUsers = $userModel->countAllResults();
        $totalAdmins = $userModel->where('role', 'admin')->countAllResults();
        $totalTeachers = $userModel->where('role', 'teacher')->countAllResults();
        $totalStudents = $userModel->where('role', 'student')->countAllResults();

        // Total courses (if courses table exists)
        $db = \Config\Database::connect();
        $totalCourses = 0;
        try {
            $totalCourses = $db->table('courses')->countAllResults();
        } catch (\Throwable $e) {
            $totalCourses = 0;
        }

        // Recent activity: latest users as a simple placeholder
        $recentUsers = $userModel->orderBy('created_at', 'DESC')->limit(5)->find();

        $data = [
            'title' => 'Admin Dashboard',
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalTeachers' => $totalTeachers,
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'recentUsers' => $recentUsers,
        ];

        return view('admin/dashboard', $data);
    }
}


