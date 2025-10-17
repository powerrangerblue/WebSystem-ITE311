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

        // Prepare data for view
        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role
        ];

        return view('admin_dashboard', $data);
    }
}
