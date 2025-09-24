<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
        helper(['form']);
        $session = session();
        $model = new UserModel();
        
        if ($this->request->getMethod() === 'POST') {
            // Add detailed logging
            log_message('info', 'Registration POST request received');
            log_message('info', 'POST data: ' . print_r($this->request->getPost(), true));
            
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'matches[password]',
                'role' => 'permit_empty|in_list[student,teacher]'
            ];
            
            if ($this->validate($rules)) {
                log_message('info', 'Validation passed');
                
                try {
                    // Get the data from form
                    $name = trim($this->request->getPost('name'));
                    $email = $this->request->getPost('email');
                    $roleInput = strtolower((string) $this->request->getPost('role'));
                    $role = in_array($roleInput, ['student','teacher'], true) ? $roleInput : 'student';
                    
                    $data = [
                        'name' => $name,
                        'email' => $email,
                        'password' => $this->request->getPost('password'), // Let model handle hashing
                        'role' => $role
                    ];
                    
                    log_message('info', 'Attempting to insert user data: ' . print_r($data, true));
                    
                    // Save user to database
                    $insertResult = $model->insert($data);
                    
                    if ($insertResult) {
                        log_message('info', 'User inserted successfully with ID: ' . $insertResult);
                        $session->setFlashdata('register_success', 'Registration successful. Please login.');
                        return redirect()->to(base_url('login'));
                    } else {
                        // Get the last error for debugging
                        $errors = $model->errors();
                        $errorMessage = 'Registration failed. ';
                        
                        log_message('error', 'Model insert failed. Errors: ' . print_r($errors, true));
                        log_message('error', 'Model validation errors: ' . print_r($model->getValidationMessages(), true));
                        
                        if (!empty($errors)) {
                            $errorMessage .= implode(', ', $errors);
                        } else {
                            $errorMessage .= 'Please try again.';
                        }
                        $session->setFlashdata('register_error', $errorMessage);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Registration exception: ' . $e->getMessage());
                    log_message('error', 'Stack trace: ' . $e->getTraceAsString());
                    $session->setFlashdata('register_error', 'Registration failed. Please try again. Error: ' . $e->getMessage());
                }
            } else {
                // Validation failed
                $validationErrors = $this->validator->getErrors();
                log_message('error', 'Validation failed: ' . print_r($validationErrors, true));
                
                $errorMessage = 'Validation failed: ' . implode(', ', $validationErrors);
                $session->setFlashdata('register_error', $errorMessage);
            }
        }
        
        return view('auth/register', [
            'validation' => $this->validator
        ]);
    }

    public function login()
    {
        helper(['form']);
        $session = session();
        
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required'
            ];
            
            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');
                
                try {
                    $model = new UserModel();
                    
                    // Find user by email only
                    $user = $model->where('email', $email)->first();
                    
                    if ($user && password_verify($password, $user['password'])) {
                        // Use the name field directly from database
                        $userName = $user['name'] ?? $user['email'];
                        
                        // Set session data
                        $sessionData = [
                            'user_id' => $user['id'],
                            'user_name' => $userName,
                            'user_email' => $user['email'],
                            'role' => $user['role'] ?? 'student',
                            'isLoggedIn' => true
                        ];
                        
                        // Prevent session fixation
                        $session->regenerate();
                        $session->set($sessionData);
                        $session->setFlashdata('success', 'Welcome, ' . $userName . '!');

                        // Unified dashboard redirect
                        return redirect()->to('/dashboard');
                    } else {
                        $session->setFlashdata('login_error', 'Invalid email or password.');
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Login exception: ' . $e->getMessage());
                    $session->setFlashdata('login_error', 'Login failed. Please try again.');
                }
            } else {
                $session->setFlashdata('login_error', 'Please check your input and try again.');
            }
        }
        
        return view('auth/login', [
            'validation' => $this->validator
        ]);
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('login');
    }

    public function dashboard()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access the dashboard.');
            return redirect()->to('login');
        }
        
        $role = strtolower((string) $session->get('role'));
        $userId = (int) $session->get('user_id');

        // Prepare role-specific data
        $db = \Config\Database::connect();
        $roleData = [];
        try {
            if ($role === 'admin') {
                $userModel = new UserModel();
                $roleData['totalUsers'] = $userModel->countAllResults();
                $roleData['totalAdmins'] = $userModel->where('role', 'admin')->countAllResults();
                $roleData['totalTeachers'] = $userModel->where('role', 'teacher')->countAllResults();
                $roleData['totalStudents'] = $userModel->where('role', 'student')->countAllResults();
                try {
                    $roleData['totalCourses'] = $db->table('courses')->countAllResults();
                } catch (\Throwable $e) {
                    $roleData['totalCourses'] = 0;
                }
                $roleData['recentUsers'] = $userModel->orderBy('created_at', 'DESC')->limit(5)->find();
            } elseif ($role === 'teacher') {
                $courses = [];
                try {
                    $courses = $db->table('courses')
                        ->where('teacher_id', $userId)
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
                $roleData['courses'] = $courses;
                $roleData['notifications'] = $notifications;
            } elseif ($role === 'student') {
                $enrolledCourses = [];
                $upcomingDeadlines = [];
                $recentGrades = [];
                try {
                    $enrolledCourses = $db->table('enrollments e')
                        ->select('c.id, c.title, c.description, c.created_at')
                        ->join('courses c', 'c.id = e.course_id', 'left')
                        ->where('e.student_id', $userId)
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
                        ->where('g.student_id', $userId)
                        ->orderBy('g.created_at', 'DESC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();
                } catch (\Throwable $e) {
                    $recentGrades = [];
                }
                $roleData['enrolledCourses'] = $enrolledCourses;
                $roleData['upcomingDeadlines'] = $upcomingDeadlines;
                $roleData['recentGrades'] = $recentGrades;
            }
        } catch (\Throwable $e) {
            $roleData = [];
        }

        $data = array_merge([
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role
        ], $roleData);

        return view('auth/dashboard', $data);
    }
}