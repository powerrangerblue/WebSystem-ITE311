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
                'password_confirm' => 'matches[password]'
            ];
            
            if ($this->validate($rules)) {
                log_message('info', 'Validation passed');
                
                try {
                    // Get the data from form
                    $name = trim($this->request->getPost('name'));
                    $email = strtolower($this->request->getPost('email')); // Normalize email to lowercase
                    $role = 'student'; // All new registrations are students
                    
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
                $roleData['recentUsers'] = $userModel->orderBy('created_at', 'DESC')->limit(5)->find();
            } elseif ($role === 'teacher') {
                // Get teacher dashboard metrics
                $assignmentModel = new \App\Models\AssignmentModel();

                // Get courses assigned to this teacher by admin
                $teacherCourses = $assignmentModel->getTeacherCourses($userId);
                $roleData['totalCourses'] = count($teacherCourses);

                // Count enrolled students in teacher's courses
                $db = \Config\Database::connect();
                $roleData['totalStudents'] = $db->table('enrollments')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('courses.teacher_id', $userId)
                    ->where('enrollments.status !=', 'Dropped')
                    ->countAllResults();

                $roleData['assignmentsPosted'] = $assignmentModel->getAssignmentsPostedCount();
                $roleData['pendingGrades'] = $assignmentModel->getPendingGradesCount($userId);

                // Get courses assigned to this teacher
                $roleData['courses'] = $teacherCourses;

                // Get upcoming assignments only for teacher's courses
                $roleData['upcomingAssignments'] = $db->table('assignments')
                    ->select('assignments.*, courses.course_name, courses.course_code')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->where('courses.teacher_id', $userId)
                    ->where('assignments.due_date >', date('Y-m-d H:i:s'))
                    ->where('assignments.due_date <=', date('Y-m-d H:i:s', strtotime("+7 days")))
                    ->orderBy('assignments.due_date', 'ASC')
                    ->get()
                    ->getResultArray();

                // Get assignments needing grading
                $roleData['assignmentsNeedingGrading'] = $assignmentModel->getAssignmentsNeedingGrading($userId);

                // Get recent materials uploaded by teacher (limit to 10)
                $roleData['recentMaterials'] = $db->table('materials m')
                    ->select('m.file_name, m.file_path, m.created_at, c.course_name, c.course_code')
                    ->join('courses c', 'c.id = m.course_id')
                    ->where('c.teacher_id', $userId)
                    ->orderBy('m.created_at', 'DESC')
                    ->limit(10)
                    ->get()
                    ->getResultArray();
            } elseif ($role === 'student') {
                $enrolledCourses = [];
                $materialsByCourse = [];

                try {
                    // Load models
                    $enrollmentModel = new \App\Models\EnrollmentModel();
                    $materialModel = new \App\Models\MaterialModel();

                    // Get enrolled courses using EnrollmentModel::getUserEnrollments()
                    $enrolledCourses = $enrollmentModel->getUserEnrollments($userId);

                    // Fetch materials per enrolled course
                    foreach ($enrolledCourses as $enCourse) {
                        $cid = (int) ($enCourse['course_id'] ?? 0);
                        if ($cid > 0) {
                            $materialsByCourse[$cid] = $materialModel->getMaterialsByCourse($cid);
                        }
                    }

                    // Get total courses count in the system
                    $totalCourses = $db->table('courses')->countAllResults();
                } catch (\Throwable $e) {
                    $enrolledCourses = [];
                    $materialsByCourse = [];
                    $totalCourses = 0;
                }

                $roleData['enrolledCourses'] = $enrolledCourses;
                $roleData['materialsByCourse'] = $materialsByCourse;
                $roleData['totalCourses'] = $totalCourses;
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

        // Prevent changing the protected admin's role (Admin ID=1)
        if ($user['id'] == 1) {
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

        // Prevent changing the protected admin's status (Admin ID=1)
        if ($user['id'] == 1) {
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

        try {
            helper(['form']);

            $email = strtolower($this->request->getPost('email')); // Normalize email to lowercase

            $rules = [
                'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-Z\s\-\.\']+$/]',
                'email' => 'required|valid_email'
            ];

            $userModel = new UserModel();

            // Check email uniqueness (emails are stored in lowercase)
            $existingUser = $userModel->where('email', $email)->first();
            if ($existingUser) {
                return $this->response->setJSON(['success' => false, 'message' => 'This email is already registered.']);
            }

            if (!$this->validate($rules)) {
                $errors = $this->validator->getErrors();
                return $this->response->setJSON(['success' => false, 'message' => implode(', ', $errors)]);
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $email, // Already lowercased
                'password' => 'Rmmc1960!', // Default password for all new users (meets strong password requirements)
                'role' => 'student', // All admin-added users are students
                'status' => 'active'
            ];

            log_message('info', 'Attempting to add user with data: ' . print_r($data, true));

            // Skip model validation since we handled it manually above
            $userModel->skipValidation(true);

            $insertResult = $userModel->insert($data);
            log_message('info', 'Insert result: ' . ($insertResult ? 'success' : 'failed'));
            if ($insertResult) {
                return $this->response->setJSON(['success' => true]);
            } else {
                $errors = $userModel->errors();
                log_message('error', 'Insert errors: ' . print_r($errors, true));
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Unknown error during user insertion.';
                return $this->response->setJSON(['success' => false, 'message' => $errorMsg]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Add user exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to add user. Please try again.']);
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

        // Prevent editing the protected admin (Admin ID=1)
        if ($user['id'] == 1) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot edit the protected admin.']);
        }

        // Build validation rules
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-Z\s\-\.\']+$/]',
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

        // Prevent deleting the protected admin (Admin ID=1)
        if ($user['id'] == 1) {
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

    public function profile()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access your profile.');
            return redirect()->to('login');
        }

        $userId = (int) $session->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('/dashboard');
        }

        return view('auth/profile', ['user' => $user]);
    }

    public function updateProfile()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please login to update your profile.']);
        }

        $userId = (int) $session->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        // Validate current password (required for any changes)
        $currentPassword = $this->request->getPost('current_password');
        if (empty($currentPassword) || !password_verify($currentPassword, $user['password'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Current password is incorrect.']);
        }

        // Get form data
        $name = trim($this->request->getPost('name'));
        $email = strtolower(trim($this->request->getPost('email')));
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        // Prepare validation rules
        $rules = [];

        // Validate name if provided
        if (!empty($name)) {
            $rules['name'] = 'required|min_length[3]|max_length[100]';
        }

        // Validate email if provided and different from current
        if (!empty($email) && $email !== $user['email']) {
            $rules['email'] = 'required|valid_email|is_unique[users.email,id,' . $userId . ']';

            // Manual check for uniqueness (excluding current user)
            $existingUser = $userModel->where('LOWER(email)', $email)->where('id !=', $userId)->first();
            if ($existingUser) {
                return $this->response->setJSON(['success' => false, 'message' => 'This email is already registered.']);
            }
        }

        // Validate password if provided
        if (!empty($password)) {
            $rules['password'] = 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]';
            $rules['password_confirm'] = 'required|matches[password]';

            if ($password !== $passwordConfirm) {
                return $this->response->setJSON(['success' => false, 'message' => 'Password confirmation does not match.']);
            }
        }

        // Run validation if there are rules to check
        if (!empty($rules) && !$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON(['success' => false, 'message' => implode(', ', $errors)]);
        }

        // Prepare update data
        $updateData = [];

        if (!empty($name)) {
            $updateData['name'] = $name;
        }

        if (!empty($email) && $email !== $user['email']) {
            $updateData['email'] = $email;
        }

        if (!empty($password)) {
            $updateData['password'] = $password; // Model will hash it
        }

        // Check if any changes were made
        if (empty($updateData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No changes were made.']);
        }

        // Skip model validation since we handled it manually
        $userModel->skipValidation(true);

        try {
            // Update user
            if ($userModel->update($userId, $updateData)) {
                // Update session data if name or email changed
                if (!empty($updateData['name'])) {
                    $session->set('user_name', $updateData['name']);
                }
                if (!empty($updateData['email'])) {
                    $session->set('user_email', $updateData['email']);
                }

                // Get updated user data for response
                $updatedUser = $userModel->find($userId);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'user' => [
                        'name' => $updatedUser['name'],
                        'email' => $updatedUser['email']
                    ]
                ]);
            } else {
                $errors = $userModel->errors();
                return $this->response->setJSON(['success' => false, 'message' => implode(', ', $errors)]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Profile update exception: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update profile. Please try again.']);
        }
    }
}
