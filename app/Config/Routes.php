<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Authentication Routes
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('dashboard', 'Auth::dashboard');
$routes->get('profile', 'Auth::profile');
$routes->post('profile/update', 'Auth::updateProfile');

// Course Routes
$routes->get('/courses', 'Course::index');
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');
$routes->post('/course/enroll', 'Course::enroll');
$routes->get('/course/enrollment-details/(:num)', 'Course::getEnrollmentDetails/$1');

// Materials Routes
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->get('/materials/teacher-materials', 'Materials::teacherMaterials');

// Notifications Routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

// Admin Routes
$routes->get('/admin/manage-users', 'Auth::manageUsers');
$routes->post('/admin/change-role', 'Auth::changeRole');
$routes->post('/admin/add-user', 'Auth::addUser');
$routes->post('/admin/edit-user', 'Auth::editUser');
$routes->post('/admin/get-user-enrollment', 'Auth::getUserEnrollment');
$routes->post('/admin/toggle-status', 'Auth::toggleStatus');
$routes->get('/admin/delete-user/(:num)', 'Auth::deleteUser/$1');
$routes->get('/admin/courses', 'Admin::courses');
$routes->post('/admin/courses/update', 'Admin::updateCourse');
$routes->post('/admin/courses/create', 'Admin::createCourse');

// Materials API
$routes->get('/materials/list/(:num)', 'Materials::listByCourse/$1');

// Assignment Routes
$routes->get('/assignments/teacher', 'Assignment::teacherDashboard');
$routes->get('/assignments/student', 'Assignment::studentDashboard');
$routes->get('/assignments/course/(:num)', 'Assignment::courseAssignments/$1');
$routes->get('/assignments/create/(:num)', 'Assignment::create/$1');
$routes->post('/assignments/store', 'Assignment::store');
$routes->get('/assignments/submissions/(:num)', 'Assignment::submissions/$1');
$routes->post('/assignments/grade/(:num)', 'Assignment::grade/$1');
$routes->get('/assignments/student/course/(:num)', 'Assignment::courseAssignmentsStudent/$1');
$routes->post('/assignments/submit/(:num)', 'Assignment::submit/$1');

// Teacher Routes
$routes->get('/teacher/manage-students', 'Assignment::manageStudents');
$routes->post('/teacher/manage-students/update-status', 'Assignment::updateStudentStatus');
$routes->get('/teacher/manage-students/student/details/(:num)', 'Assignment::getStudentDetails/$1');

// Admin Routes for Enrollment Requests
$routes->get('/admin/enrollment-requests', 'Admin::manageEnrollmentRequests');
$routes->post('/admin/enrollment-requests/process', 'Admin::processEnrollmentRequest');

// File Downloads
$routes->get('/assignments/download/(:num)', 'Assignment::downloadAssignment/$1');
$routes->get('/assignments/download-submission/(:num)', 'Assignment::downloadSubmission/$1');
