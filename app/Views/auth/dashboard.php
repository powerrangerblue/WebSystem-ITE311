<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Dashboard</h1>
            <div class="text-muted small">A quick overview of your account</div>
        </div>
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-semibold">Welcome back, <?= esc(session('user_name')) ?>.</div>
                <small class="text-muted">Email: <?= esc(session('user_email')) ?> • Role: <?= esc($role) ?></small>
            </div>
            <span class="badge text-bg-primary">Active</span>
        </div>
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
                    <div class="list-group">
                        <?php foreach ($courses as $c): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold mb-0"><?= esc($c['course_name'] ?? 'Untitled') ?></div>
                                    <span class="badge text-bg-light border small"><?= esc($c['course_code'] ?? '') ?></span>
                                </div>
                                <a class="btn btn-primary btn-sm" href="<?= site_url('/admin/course/' . (int)$c['id'] . '/upload') ?>">Upload Material</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-muted">No courses found.</div>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif ($roleLower === 'student'): ?>
        <?php
            $enrolledCount = !empty($enrolledCourses) ? count($enrolledCourses) : 0;
            $availableCount = !empty($availableCourses) ? count($availableCourses) : 0;
            $materialsCount = 0;
            if (!empty($materialsByCourse)) {
                foreach ($materialsByCourse as $matList) {
                    $materialsCount += is_array($matList) ? count($matList) : 0;
                }
            }
        ?>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?= (int)$enrolledCount ?></div>
                        <div class="text-muted">Enrolled</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?= (int)$availableCount ?></div>
                        <div class="text-muted">Available</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?= (int)$materialsCount ?></div>
                        <div class="text-muted">Materials</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled and Available sections shown together -->
        <div class="mb-3">
            <!-- Enrolled Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Enrolled Courses</span>
                        <input id="course-search" type="text" class="form-control form-control-sm w-auto" placeholder="Search courses">
                    </div>
                    <div class="card-body" id="enrolled-courses">
                        <?php if (!empty($enrolledCourses)): ?>
                            <div class="list-group">
                                <?php foreach ($enrolledCourses as $c): ?>
                                    <?php $cid = (int)($c['course_id'] ?? 0); ?>
                                    <?php $courseMaterials = $materialsByCourse[$cid] ?? []; ?>
                                    <?php $matCount = is_array($courseMaterials) ? count($courseMaterials) : 0; ?>
                                    <div class="list-group-item" data-search-text="<?= esc(strtolower(($c['course_name'] ?? '') . ' ' . ($c['course_code'] ?? ''))) ?>">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?= esc($c['course_name'] ?? 'Untitled') ?></h6>
                                                <span class="badge text-bg-light border small"><?= esc($c['course_code'] ?? '') ?></span>
                                            </div>
                                            <div class="d-flex gap-2 align-items-center">
                                                <span class="badge rounded-pill text-bg-success">Enrolled</span>
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#materials-<?= $cid ?>" aria-expanded="false" aria-controls="materials-<?= $cid ?>">Materials (<?= (int)$matCount ?>)</button>
                                            </div>
                                        </div>

                                        <div id="materials-<?= $cid ?>" class="collapse mt-2">
                                            <?php if (!empty($courseMaterials)): ?>
                                                <div class="list-group list-group-flush">
                                                    <?php foreach ($courseMaterials as $m): ?>
                                                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                            <span class="text-truncate" style="max-width: 75%;"><?= esc($m['file_name']) ?></span>
                                                            <a class="btn btn-sm btn-outline-primary" href="<?= site_url('/materials/download/' . (int)$m['id']) ?>">Download</a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-muted small">No materials yet.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">No enrollments yet.</div>
                        <?php endif; ?>
                    </div>
                </div>

            <!-- Available Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Available Courses</div>
                    <div class="card-body" id="available-courses">
                        <?php if (!empty($availableCourses)): ?>
                            <div class="list-group">
                                <?php foreach ($availableCourses as $c): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center" data-search-text="<?= esc(strtolower(($c['course_name'] ?? '') . ' ' . ($c['course_code'] ?? ''))) ?>">
                                        <div>
                                            <h6 class="mb-1"><?= esc($c['course_name'] ?? 'Untitled') ?></h6>
                                            <span class="badge text-bg-light border small"><?= esc($c['course_code'] ?? '') ?></span>
                                        </div>
                                        <button class="btn btn-success btn-sm" data-course-id="<?= esc($c['id']) ?>">Enroll</button>
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
    // CSRF helpers (read fresh token from cookie after every request)
    const CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
    const CSRF_COOKIE_NAME = '<?= config('Security')->cookieName ?>';
    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }
    function currentCsrf() {
        return getCookie(CSRF_COOKIE_NAME);
    }
    // Listen for a click on the Enroll button (event delegation for reliability)
    $(document).on('click', 'button[data-course-id]', function(e) {
        // Prevent the default form submission behavior
        e.preventDefault();
        
        const button = $(this);
        const courseId = button.data('course-id');
        
        // Disable button to prevent multiple clicks
        button.prop('disabled', true).text('Enrolling...');
        
        // Use $.post() to send the course_id to the /course/enroll URL with a fresh CSRF token
        const payload = { course_id: courseId };
        payload[CSRF_TOKEN_NAME] = currentCsrf();
        $.ajax({
            url: '<?= base_url('course/enroll') ?>',
            method: 'POST',
            data: payload,
            dataType: 'json',
            headers: {
                '<?= config('Security')->headerName ?>': currentCsrf(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function(response) {
            if (response.success) {
                // Handle successful enrollment with UI updates
                handleEnrollmentSuccess(response);

                // Smooth scroll to the Enrolled Courses section and briefly highlight the new item
                try {
                    const target = $('#enrolled-courses');
                    if (target.length) {
                        $('html, body').animate({ scrollTop: target.offset().top - 80 }, 400);
                        const highlight = target.find('.list-group-item').first();
                        highlight.addClass('bg-warning-subtle');
                        setTimeout(function(){ highlight.removeClass('bg-warning-subtle'); }, 1200);
                    }
                } catch(_e) {}

                // Fetch and render materials for the newly enrolled course
                fetchAndAttachMaterials(response.course.id);
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
    
    // Lightweight client-side filtering for enrolled courses
    $('#course-search').on('input', function() {
        const q = $(this).val().toString().toLowerCase().trim();
        const items = $('#enrolled-courses .list-group-item');
        if (!q) {
            items.show();
            return;
        }
        items.each(function() {
            const text = ($(this).data('search-text') || '').toString();
            $(this).toggle(text.indexOf(q) !== -1);
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

        // Create the course HTML matching enrolled layout
        const cid = course.id;
        const courseHtml = `
            <div class="list-group-item" data-search-text="${((course.course_name || '') + ' ' + (course.course_code || '')).toLowerCase()}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">${course.course_name}</h6>
                        <span class="badge text-bg-light border small">${course.course_code || ''}</span>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge rounded-pill text-bg-success">Enrolled</span>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#materials-${cid}" aria-expanded="false" aria-controls="materials-${cid}">Materials (0)</button>
                    </div>
                </div>
                <div id="materials-${cid}" class="collapse mt-2">
                    <div class="text-muted small">Loading materials...</div>
                </div>
            </div>
        `;

        // Append and fade in the new course
        const newCourse = $(courseHtml);
        listGroup.prepend(newCourse);
        newCourse.hide().fadeIn(300);
    }

    // Function to handle enrollment success
    function handleEnrollmentSuccess(data) {
        // Add the course to enrolled courses list
        addToEnrolledCourses(data.course);

        // Remove the course from available courses list
        $('#available-courses .list-group-item:has(button[data-course-id="' + data.course.id + '"])').fadeOut(300, function() {
            $(this).remove();
        });

        // Show success message
        showAlert('success', data.message);

        // Update counters
        updateCounters();
    }

    function fetchAndAttachMaterials(courseId) {
        $.get('<?= base_url('materials/list') ?>/' + courseId)
            .done(function(res) {
                if (!res || !res.success) return;
                const list = res.materials || [];
                const container = $('#materials-' + courseId);
                const toggleBtn = container.closest('.list-group-item').find('[data-bs-target="#materials-' + courseId + '"]');
                if (toggleBtn.length) {
                    toggleBtn.text('Materials (' + list.length + ')');
                }
                if (list.length === 0) {
                    container.html('<div class="text-muted small">No materials yet.</div>');
                    return;
                }
                let html = '<div class="list-group list-group-flush">';
                list.forEach(function(m) {
                    html += '<div class="list-group-item px-0 d-flex justify-content-between align-items-center">'
                         + '<span class="text-truncate" style="max-width: 75%;">' + m.file_name + '</span>'
                         + '<a class="btn btn-sm btn-outline-primary" href="<?= site_url('/materials/download/') ?>' + m.id + '">Download</a>'
                         + '</div>';
                });
                html += '</div>';
                container.html(html);
            });
    }

    // Function to update counters
    function updateCounters() {
        const enrolledCard = $(".card .card-body:contains('Enrolled')").closest('.card').find('.h2');
        const availableCard = $(".card .card-body:contains('Available')").closest('.card').find('.h2');
        if (enrolledCard.length) {
            const n = parseInt(enrolledCard.text(), 10) || 0;
            enrolledCard.text(n + 1);
        }
        if (availableCard.length) {
            const n = parseInt(availableCard.text(), 10) || 0;
            availableCard.text(Math.max(0, n - 1));
        }
    }
});
</script>
<?= $this->endSection() ?>
