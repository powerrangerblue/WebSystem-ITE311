<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\UserModel;

class TestEnrollment extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:enrollment';
    protected $description = 'Test the course enrollment system functionality';

    public function run(array $params)
    {
        CLI::write('=== Course Enrollment System Test ===', 'yellow');
        CLI::newLine();

        try {
            // Test 1: Check if models can be instantiated
            CLI::write('1. Testing model instantiation...', 'green');
            $courseModel = new CourseModel();
            $enrollmentModel = new EnrollmentModel();
            $userModel = new UserModel();
            CLI::write('   ✅ All models instantiated successfully', 'green');
            CLI::newLine();

            // Test 2: Check if courses exist
            CLI::write('2. Testing course data...', 'green');
            $courses = $courseModel->findAll();
            CLI::write("   Found " . count($courses) . " courses in database", 'white');
            if (count($courses) > 0) {
                CLI::write('   ✅ Courses are available for enrollment', 'green');
                foreach ($courses as $course) {
                    CLI::write("   - {$course['course_code']}: {$course['course_name']}", 'white');
                }
            } else {
                CLI::write('   ❌ No courses found. Run the CourseSeeder first.', 'red');
            }
            CLI::newLine();

            // Test 3: Check if users exist
            CLI::write('3. Testing user data...', 'green');
            $users = $userModel->findAll();
            CLI::write("   Found " . count($users) . " users in database", 'white');
            if (count($users) > 0) {
                CLI::write('   ✅ Users are available for testing', 'green');
                foreach ($users as $user) {
                    CLI::write("   - {$user['name']} ({$user['email']}) - Role: {$user['role']}", 'white');
                }
            } else {
                CLI::write('   ❌ No users found. Create some test users first.', 'red');
            }
            CLI::newLine();

            // Test 4: Test enrollment methods
            CLI::write('4. Testing enrollment methods...', 'green');
            if (count($users) > 0 && count($courses) > 0) {
                $testUser = $users[0];
                $testCourse = $courses[0];
                
                CLI::write("   Testing with user: {$testUser['name']} (ID: {$testUser['id']})", 'white');
                CLI::write("   Testing with course: {$testCourse['course_name']} (ID: {$testCourse['id']})", 'white');
                
                // Check if already enrolled
                $isEnrolled = $enrollmentModel->isAlreadyEnrolled($testUser['id'], $testCourse['id']);
                CLI::write("   Already enrolled: " . ($isEnrolled ? "Yes" : "No"), 'white');
                
                if (!$isEnrolled) {
                    // Test enrollment
                    $enrollmentData = [
                        'user_id' => $testUser['id'],
                        'course_id' => $testCourse['id']
                    ];
                    
                    $enrollmentId = $enrollmentModel->enrollUser($enrollmentData);
                    if ($enrollmentId) {
                        CLI::write("   ✅ Successfully enrolled user in course (Enrollment ID: {$enrollmentId})", 'green');
                    } else {
                        CLI::write('   ❌ Failed to enroll user in course', 'red');
                    }
                }
                
                // Test getting user enrollments
                $userEnrollments = $enrollmentModel->getUserEnrollments($testUser['id']);
                CLI::write("   User enrollments: " . count($userEnrollments) . " courses", 'white');
                
                // Test getting available courses
                $availableCourses = $courseModel->getAvailableCourses($testUser['id']);
                CLI::write("   Available courses for user: " . count($availableCourses) . " courses", 'white');
                
            } else {
                CLI::write('   ❌ Cannot test enrollment - missing users or courses', 'red');
            }
            CLI::newLine();

            // Test 5: Database connection
            CLI::write('5. Testing database connection...', 'green');
            $db = \Config\Database::connect();
            if ($db->connID) {
                CLI::write('   ✅ Database connection successful', 'green');
            } else {
                CLI::write('   ❌ Database connection failed', 'red');
            }
            CLI::newLine();

            CLI::write('=== Test Complete ===', 'yellow');
            CLI::write('If all tests passed, the enrollment system is ready for use!', 'green');
            CLI::write('Access the application at: http://localhost:8080', 'white');
            CLI::write('Login with a student account and navigate to the dashboard to test enrollment.', 'white');

        } catch (\Exception $e) {
            CLI::write('❌ Error during testing: ' . $e->getMessage(), 'red');
            CLI::write('Stack trace:', 'red');
            CLI::write($e->getTraceAsString(), 'red');
        }
    }
}
