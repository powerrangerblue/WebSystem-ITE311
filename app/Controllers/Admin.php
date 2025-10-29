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
        // Keep any additional admin routes as-is; not required by task
        $db = \Config\Database::connect();
        $courses = $db->table('courses')->get()->getResultArray();
        return view('admin_courses', ['courses' => $courses]);
    }
}
