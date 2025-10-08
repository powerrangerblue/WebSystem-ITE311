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
$routes->post('course/enroll', 'Course::enroll');
$routes->get('course/available', 'Course::getAvailableCourses');
$routes->get('course/enrollments', 'Course::getUserEnrollments');
$routes->get('course/(:num)', 'Course::view/$1');
$routes->get('courses', 'Course::index');


