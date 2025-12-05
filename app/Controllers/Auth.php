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
                'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
                'password_confirm' => 'matches[password]',
                'role' => 'permit_empty|in_list[student,teacher,admin]'
            ];
            
            if ($this->validate($rules)) {
                log_message('info', 'Validation passed');
                
                try {
                    // Get the data from form
                    $name = trim($this->request->getPost('name'));
                    $email = strtolower($this->request->getPost('email')); // Normalize email to lowercase
                    $roleInput = strtolower((string) $this->request->getPost('role'));
                    $role = in_array($roleInput, ['student','teacher','admin'], true) ? $roleInput : 'student';
                    
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
                $email = strtolower($this->request->getPost('email')); // Normalize email to lowercase
                $password = $this->request->getPost('password');
                
                try {
                    $model = new UserModel();
                    
                    // Find user by email only
                    $user = $model->where('email', $email)->first();

                    if ($user && password_verify($password, $user['password']) && $user['status'] === 'active') {
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
                        ->orderBy('created_at', 'DESC')
                        ->get(10)
                        ->getResultArray();
                } catch (\Throwable $e) {
                    $courses = [];
                }
                $roleData['courses'] = $courses;
            } elseif ($role === 'student') {
                $enrolledCourses = [];
                $availableCourses = [];
                $materialsByCourse = [];

                try {
                    // Load models
                    $enrollmentModel = new \App\Models\EnrollmentModel();
                    $materialModel = new \App\Models\MaterialModel();

                    // Get enrolled courses using EnrollmentModel::getUserEnrollments()
                    $enrolledCourses = $enrollmentModel->getUserEnrollments($userId);

                    // Get enrolled course IDs to exclude from available courses
                    $enrolledCourseIds = array_column($enrolledCourses, 'course_id');

                    // Get available courses (exclude already enrolled courses)
                    $coursesQuery = $db->table('courses');

                    // If user has enrolled courses, exclude them from available courses
                    if (!empty($enrolledCourseIds)) {
                        $coursesQuery->whereNotIn('id', $enrolledCourseIds);
                    }

                    $availableCourses = $coursesQuery
                        ->orderBy('course_name', 'ASC')
                        ->get()
                        ->getResultArray();
                    // Fetch materials per enrolled course
                    foreach ($enrolledCourses as $enCourse) {
                        $cid = (int) ($enCourse['course_id'] ?? 0);
                        if ($cid > 0) {
                            $materialsByCourse[$cid] = $materialModel->getMaterialsByCourse($cid);
                        }
                    }
                } catch (\Throwable $e) {
                    $enrolledCourses = [];
                    $availableCourses = [];
                    $materialsByCourse = [];
                }

                $roleData['enrolledCourses'] = $enrolledCourses;
                $roleData['availableCourses'] = $availableCourses;
                $roleData['materialsByCourse'] = $materialsByCourse;
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

    public function manageUsers()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            $session->setFlashdata('error', 'Access denied.');
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $users = $userModel->findAll(); // This will exclude soft deleted by default

        return view('auth/manage_users', ['users' => $users]);
    }

    public function changeRole()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $userId = $this->request->getPost('user_id');
        $newRole = $this->request->getPost('role');

        if (!$userId || !$newRole) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        // Prevent changing the protected admin's role
        if ($user['email'] === 'admin@example.com') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot change the role of the protected admin.']);
        }

        // Validate role
        $validRoles = ['student', 'teacher', 'admin'];
        if (!in_array($newRole, $validRoles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid role.']);
        }

        // Update the role
        if ($userModel->update($userId, ['role' => $newRole])) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update role.']);
        }
    }

    public function toggleStatus()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $userId = $this->request->getPost('user_id');
        $newStatus = $this->request->getPost('status');

        if (!$userId || !$newStatus) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        // Prevent changing the protected admin's status
        if ($user['email'] === 'admin@example.com') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot change the status of the protected admin.']);
        }

        // Validate status
        $validStatuses = ['active', 'inactive'];
        if (!in_array($newStatus, $validStatuses)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status.']);
        }

        // Update the status
        if ($userModel->update($userId, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status.']);
        }
    }

    public function addUser()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        helper(['form']);

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'role' => 'required|in_list[student,teacher,admin]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON(['success' => false, 'message' => implode(', ', $errors)]);
        }

        $userModel = new UserModel();

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => strtolower($this->request->getPost('email')), // Normalize email to lowercase
            'password' => 'Rmmc1960!', // Default password for all new users (meets strong password requirements)
            'role' => $this->request->getPost('role')
        ];

        if ($userModel->insert($data)) {
            return $this->response->setJSON(['success' => true]);
        } else {
            $errors = $userModel->errors();
            return $this->response->setJSON(['success' => false, 'message' => implode(', ', $errors)]);
        }
    }

    public function editUser()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        helper(['form']);

        $userId = $this->request->getPost('user_id');
        $email = strtolower($this->request->getPost('email')); // Normalize email to lowercase
        $password = $this->request->getPost('password');

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID is required.']);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        // Prevent editing the protected admin
        if ($user['email'] === 'admin@example.com') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot edit the protected admin.']);
        }

        // Build validation rules
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'role' => 'required|in_list[student,teacher,admin]'
        ];

        // Add password validation only if password is provided
        if (!empty($password)) {
            $rules['password'] = 'min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]';
        }

        // Check email uniqueness excluding current user only if email has changed (case-insensitive)
        if (strtolower($user['email']) !== $email) {
            $existingUser = $userModel->where('LOWER(email)', $email)->where('id !=', $userId)->first();
            if ($existingUser) {
                return $this->response->setJSON(['success' => false, 'message' => 'This email is already registered.']);
            }
        }

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON(['success' => false, 'message' => implode(', ', $errors)]);
        }

        // Prepare update data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $email,
            'role' => $this->request->getPost('role')
        ];

        // Only update password if provided
        if (!empty($password)) {
            $data['password'] = $password; // Model will hash it
        }

        // Skip model validation since uniqueness is checked manually above
        $userModel->skipValidation(true);

        if ($userModel->update($userId, $data)) {
            return $this->response->setJSON(['success' => true]);
        } else {
            $errors = $userModel->errors();
            return $this->response->setJSON(['success' => false, 'message' => implode(', ', $errors)]);
        }
    }

    public function deleteUser($id)
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            $session->setFlashdata('error', 'Access denied.');
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('/admin/manage-users');
        }

        // Prevent deleting the protected admin
        if ($user['email'] === 'admin@example.com') {
            $session->setFlashdata('error', 'Cannot delete the protected admin.');
            return redirect()->to('/admin/manage-users');
        }

        // Soft delete the user
        if ($userModel->delete($id)) {
            $session->setFlashdata('success', 'User has been marked as deleted.');
        } else {
            $session->setFlashdata('error', 'Failed to delete user.');
        }

        return redirect()->to('/admin/manage-users');
    }
}
