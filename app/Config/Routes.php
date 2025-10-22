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

// Course Routes
$routes->get('/course/materials/(:num)', 'Course::materials/$1');
$routes->post('/course/enroll', 'Course::enroll');

// Announcement Routes
$routes->get('/announcements', 'Announcement::index');

// Teacher Routes
$routes->get('/teacher/dashboard', 'Teacher::dashboard');

// Admin Routes
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->get('/admin/courses', 'Admin::courses');
$routes->get('/admin/materials', 'Admin::materials');

// Materials Routes
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->get('/materials/test', 'Materials::test');
$routes->get('/admin/course/(:num)/debug', 'Materials::debug/$1');
$routes->post('/admin/course/(:num)/debug', 'Materials::debug/$1');
