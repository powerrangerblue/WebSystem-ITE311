<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Dashboard</h1>
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
                            <li><?= esc($c['course_name'] ?? 'Untitled') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-muted">No courses found.</div>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif ($roleLower === 'student'): ?>
        <!-- Display Enrolled Courses -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Enrolled Courses</div>
            <div class="card-body" id="enrolled-courses">
                <?php if (!empty($enrolledCourses)): ?>
                    <div class="list-group">
                        <?php foreach ($enrolledCourses as $c): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= esc($c['course_name'] ?? 'Untitled') ?></h6>
                                    <small class="text-muted"><?= esc($c['course_code'] ?? '') ?></small>
                                </div>
                                <a href="<?= site_url('course/materials/' . $c['course_id']) ?>" class="btn btn-info btn-sm">View Materials</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-muted">No enrollments yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Available Courses -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Available Courses</div>
            <div class="card-body" id="available-courses">
                <?php if (!empty($availableCourses)): ?>
                    <div class="list-group">
                        <?php foreach ($availableCourses as $c): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= esc($c['course_name'] ?? 'Untitled') ?></h6>
                                    <small class="text-muted"><?= esc($c['course_code'] ?? '') ?></small>
                                </div>
                                <button class="btn btn-primary btn-sm" 
                                        data-course-id="<?= esc($c['id']) ?>">
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
    // Listen for a click on the Enroll button
    $('button[data-course-id]').click(function(e) {
        // Prevent the default form submission behavior
        e.preventDefault();
        
        const button = $(this);
        const courseId = button.data('course-id');
        
        // Disable button to prevent multiple clicks
        button.prop('disabled', true).text('Enrolling...');
        
        // Use $.post() to send the course_id to the /course/enroll URL
        $.post('<?= base_url('course/enroll') ?>', {
            course_id: courseId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        })
        .done(function(response) {
            if (response.success) {
                // On a successful response from the server:
                // Display a Bootstrap alert message
                showAlert('success', response.message);
                
                // Hide or disable the Enroll button for that course
                button.closest('.list-group-item').fadeOut(300, function() {
                    $(this).remove();
                });
                
                // Update the Enrolled Courses list dynamically without reloading the page
                addToEnrolledCourses(response.course);
            } else {
                showAlert('danger', response.message);
                button.prop('disabled', false).text('Enroll');
            }
        })
        .fail(function() {
            showAlert('danger', 'An error occurred while enrolling. Please try again.');
            button.prop('disabled', false).text('Enroll');
        });
    });
    
    // Function to show Bootstrap alert message
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
        
        // Check if list-group exists
        let listGroup = enrolledContainer.find('.list-group');
        
        // If no list-group exists (meaning "No enrollments yet" message is shown)
        if (listGroup.length === 0) {
            // Replace the entire content with a new list-group
            enrolledContainer.html('<div class="list-group"></div>');
            listGroup = enrolledContainer.find('.list-group');
        }
        
        // Create the course HTML with animation - include the View Materials button
        const courseHtml = `
            <div class="list-group-item d-flex justify-content-between align-items-center" style="display: none;">
                <div>
                    <h6 class="mb-1">${course.course_name}</h6>
                    <small class="text-muted">${course.course_code}</small>
                </div>
                <a href="<?= site_url('course/materials/') ?>${course.id}" class="btn btn-info btn-sm">View Materials</a>
            </div>
        `;
        
        // Append and fade in the new course
        const newCourse = $(courseHtml);
        listGroup.append(newCourse);
        newCourse.fadeIn(300);
    }
});
</script>
<?= $this->endSection() ?>
