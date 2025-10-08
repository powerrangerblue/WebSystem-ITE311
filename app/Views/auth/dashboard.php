<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Dashboard</h1>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger">Logout</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="alert alert-info" role="alert">
        Welcome, <?= esc(session('user_name')) ?>!<br>
        <small class="text-muted">Email: <?= esc(session('user_email')) ?> | Role: <?= esc($role) ?></small>
    </div>

    <?php $roleLower = strtolower((string) $role); ?>

    <?php if ($roleLower === 'admin'): ?>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?= (int)($totalUsers ?? 0) ?></div>
                        <div class="text-muted">Users</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?= (int)($totalAdmins ?? 0) ?></div>
                        <div class="text-muted">Admins</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?= (int)($totalTeachers ?? 0) ?></div>
                        <div class="text-muted">Teachers</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?= (int)($totalStudents ?? 0) ?></div>
                        <div class="text-muted">Students</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Recent Users</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentUsers)): foreach ($recentUsers as $u): ?>
                                <tr>
                                    <td><?= esc($u['name'] ?? '') ?></td>
                                    <td><?= esc($u['email'] ?? '') ?></td>
                                    <td><?= esc($u['role'] ?? '') ?></td>
                                    <td><?= esc($u['created_at'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" class="text-center text-muted">No recent users.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php elseif ($roleLower === 'teacher'): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Your Courses</div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <ul class="mb-0">
                        <?php foreach ($courses as $c): ?>
                            <li><?= esc($c['title'] ?? 'Untitled') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-muted">No courses found.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Recent Submissions</div>
            <div class="card-body">
                <?php if (!empty($notifications)): ?>
                    <ul class="mb-0">
                        <?php foreach ($notifications as $n): ?>
                            <li><?= esc($n['student_name'] ?? 'Student') ?> - <?= esc($n['created_at'] ?? '') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-muted">No recent submissions.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($roleLower === 'student'): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Enrolled Courses</div>
                    <div class="card-body" id="enrolled-courses">
                        <?php if (!empty($enrolledCourses)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($enrolledCourses as $c): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= esc($c['course_name'] ?? 'Untitled') ?></h6>
                                            <small class="text-muted"><?= esc($c['course_code'] ?? '') ?></small>
                                        </div>
                                        <span class="badge bg-success">Enrolled</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">No enrollments yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Available Courses</div>
                    <div class="card-body" id="available-courses">
                        <?php if (!empty($availableCourses)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($availableCourses as $c): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= esc($c['course_name'] ?? 'Untitled') ?></h6>
                                            <small class="text-muted"><?= esc($c['course_code'] ?? '') ?></small>
                                            <?php if (!empty($c['description'])): ?>
                                                <p class="mb-0 small text-muted"><?= esc($c['description']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-primary btn-sm enroll-btn" 
                                                data-course-id="<?= esc($c['id']) ?>"
                                                data-course-name="<?= esc($c['course_name']) ?>">
                                            Enroll
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">No available courses.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">Upcoming Deadlines</div>
                    <div class="card-body">
                        <?php if (!empty($upcomingDeadlines)): ?>
                            <ul class="mb-0">
                                <?php foreach ($upcomingDeadlines as $a): ?>
                                    <li><?= esc($a['title'] ?? '') ?> — <?= esc($a['due_date'] ?? '') ?> (<?= esc($a['course_title'] ?? '') ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-muted">No upcoming deadlines.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">Recent Grades</div>
                    <div class="card-body">
                        <?php if (!empty($recentGrades)): ?>
                            <ul class="mb-0">
                                <?php foreach ($recentGrades as $g): ?>
                                    <li><?= esc($g['assignment_title'] ?? '') ?> — <?= esc($g['score'] ?? '') ?> (<?= esc($g['course_title'] ?? '') ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-muted">No grades yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="mb-0">Your role is not recognized.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Alert container for dynamic messages -->
    <div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Handle enrollment button clicks
    $(document).on('click', '.enroll-btn', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const courseId = button.data('course-id');
        const courseName = button.data('course-name');
        
        // Disable button and show loading state
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enrolling...');
        
        // Send AJAX request
        $.post('<?= base_url('course/enroll') ?>', {
            course_id: courseId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        })
        .done(function(response) {
            if (response.success) {
                // Show success message
                showAlert('success', response.message);
                
                // Remove the course from available courses
                button.closest('.list-group-item').fadeOut(300, function() {
                    $(this).remove();
                    
                    // Check if no more available courses
                    if ($('#available-courses .list-group-item').length === 0) {
                        $('#available-courses').html('<div class="text-muted">No available courses.</div>');
                    }
                });
                
                // Add to enrolled courses
                addToEnrolledCourses(response.course);
                
            } else {
                // Show error message
                showAlert('danger', response.message);
                
                // Re-enable button
                button.prop('disabled', false).html('Enroll');
            }
        })
        .fail(function(xhr) {
            let message = 'An error occurred while enrolling. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            
            showAlert('danger', message);
            
            // Re-enable button
            button.prop('disabled', false).html('Enroll');
        });
    });
    
    // Function to show alert messages
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('#alert-container').html(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('#alert-container .alert').fadeOut();
        }, 5000);
    }
    
    // Function to add course to enrolled courses list
    function addToEnrolledCourses(course) {
        const enrolledContainer = $('#enrolled-courses');
        
        // Check if the container has "No enrollments yet" message
        if (enrolledContainer.find('.text-muted').length > 0) {
            enrolledContainer.html('<div class="list-group list-group-flush"></div>');
        }
        
        const courseHtml = `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">${course.course_name}</h6>
                    <small class="text-muted">${course.course_code}</small>
                </div>
                <span class="badge bg-success">Enrolled</span>
            </div>
        `;
        
        enrolledContainer.find('.list-group').append(courseHtml);
    }
});
</script>
<?= $this->endSection() ?>


