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
        <!-- Dashboard Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h4 class="mb-1">Welcome back, <?= esc(session('user_name')) ?>!</h4>
                <p class="text-muted mb-0">Here's an overview of your teaching dashboard</p>
            </div>
        </div>

        <!-- Quick Overview Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0 text-primary"><?= (int)($totalCourses ?? 0) ?></div>
                        <div class="text-muted small">Courses</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0 text-success"><?= (int)($totalStudents ?? 0) ?></div>
                        <div class="text-muted small">Students</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0 text-info"><?= (int)($assignmentsPosted ?? 0) ?></div>
                        <div class="text-muted small">Assignments</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="h2 mb-0 text-warning"><?= (int)($pendingGrades ?? 0) ?></div>
                        <div class="text-muted small">Pending Grades</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Your Courses -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Your Courses</div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <div class="list-group">
                        <?php foreach ($courses as $course): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= esc($course['course_name']) ?></h6>
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-tag"></i> <?= esc($course['course_code']) ?>
                                        </span>
                                        <p class="mb-2 text-muted small mt-2">
                                            <?= esc(substr($course['description'] ?? '', 0, 100)) ?>...
                                        </p>
                                    </div>

                                    <div class="text-end">
                                        <div class="text-muted small mb-2">
                                            <i class="bi bi-journal-text"></i>
                                            <?= (int)$course['assignment_count'] ?> assignment<?= (int)$course['assignment_count'] !== 1 ? 's' : '' ?>
                                        </div>
                                        <!-- Teacher can upload materials to their assigned courses -->
                                        <a href="<?= base_url('admin/course/' . $course['id'] . '/upload') ?>"
                                           class="btn btn-sm btn-success">
                                            <i class="bi bi-upload"></i> Upload Material
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="bi bi-journal-x" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-muted">No courses available</h5>
                        <p class="text-muted">There are no courses available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Materials Uploaded -->
        <?php if (!empty($recentMaterials ?? [])): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Recent Materials Uploaded</h5>
                <small class="text-muted">Materials you've uploaded to your courses</small>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach ($recentMaterials as $material): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= esc($material['file_name']) ?></h6>
                                <p class="mb-1 text-muted small">
                                    Course: <?= esc($material['course_name']) ?> (<?= esc($material['course_code']) ?>) |
                                    Uploaded: <?= date('M j, Y \a\t g:i A', strtotime($material['created_at'])) ?>
                                </p>
                            </div>
                            <a href="<?= base_url($material['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i> View
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 text-center">
                    <a href="#" class="btn btn-outline-secondary btn-sm" onclick="loadMoreMaterials()">Load More Materials</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Upcoming Assignments -->
        <?php if (!empty($upcomingAssignments)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Upcoming Assignments</h5>
                <small class="text-muted">Assignments due within the next 7 days</small>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php foreach ($upcomingAssignments as $assignment): ?>
                        <div class="list-group-item">
                            <div>
                                <h6 class="mb-1"><?= esc($assignment['title']) ?></h6>
                                <p class="mb-2 text-muted small">
                                    <strong>Course:</strong> <?= esc($assignment['course_name']) ?> |
                                    <strong>Due:</strong> <?= date('M j, Y \a\t g:i A', strtotime($assignment['due_date'])) ?>
                                </p>
                                <p class="mb-0 text-muted small">
                                    <?= esc(substr($assignment['description'], 0, 120)) ?>...
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pending Submissions / Needs Grading -->
        <?php if (!empty($assignmentsNeedingGrading)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Pending Submissions / Needs Grading</h5>
                <small class="text-muted">Assignments with ungraded student submissions</small>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php foreach ($assignmentsNeedingGrading as $assignment): ?>
                        <div class="list-group-item">
                            <div>
                                <h6 class="mb-1"><?= esc($assignment['title']) ?></h6>
                                <p class="mb-2 text-muted small">
                                    <strong>Course:</strong> <?= esc($assignment['course_name']) ?> |
                                    <strong>Due:</strong> <?= date('M j, Y \a\t g:i A', strtotime($assignment['due_date'])) ?>
                                </p>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-warning text-dark me-2">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <?= (int)$assignment['pending_count'] ?> pending grade<?= (int)$assignment['pending_count'] !== 1 ? 's' : '' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <?php elseif ($roleLower === 'student'): ?>
        <?php
            $enrolledCount = !empty($enrolledCourses) ? count($enrolledCourses) : 0;
            $totalCoursesCount = $totalCourses ?? 0; // Total courses in the system
            $materialsCount = 0; // Keep as 0 for now as requested
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
                        <div class="h2 mb-0"><?= (int)$totalCoursesCount ?></div>
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

        <!-- Enrolled Courses Section Only -->
        <div class="card border-0 shadow-sm">
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
                                        <button class="btn btn-sm btn-outline-info view-details-btn" data-course-id="<?= $cid ?>">
                                            <i class="bi bi-eye"></i> View Details
                                        </button>
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

    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="mb-0">Your role is not recognized.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Alert container for dynamic messages -->
    <div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

    <!-- Enrollment Details Modal -->
    <div class="modal fade" id="enrollmentDetailsModal" tabindex="-1" aria-labelledby="enrollmentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enrollmentDetailsModalLabel">Course Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="enrollment-details-content">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading course details...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
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

    // Polling for real-time material updates
    let materialCounts = {}; // Store current material counts for each course

    // Initialize material counts for enrolled courses
    function initializeMaterialCounts() {
        $('#enrolled-courses .list-group-item').each(function() {
            const courseId = $(this).find('[data-bs-toggle="collapse"]').attr('data-bs-target').replace('#materials-', '');
            const countText = $(this).find('[data-bs-toggle="collapse"]').text().match(/Materials \((\d+)\)/);
            const count = countText ? parseInt(countText[1], 10) : 0;
            materialCounts[courseId] = count;
        });
    }

    // Check for material updates
    function checkForMaterialUpdates() {
        for (const courseId in materialCounts) {
            $.get('<?= base_url('materials/list') ?>/' + courseId)
                .done(function(res) {
                    if (!res || !res.success) return;
                    const list = res.materials || [];
                    const currentCount = materialCounts[courseId];
                    const newCount = list.length;

                    if (newCount !== currentCount) {
                        // Update the stored count
                        materialCounts[courseId] = newCount;

                        // Update the button text
                        const toggleBtn = $('#enrolled-courses [data-bs-target="#materials-' + courseId + '"]');
                        if (toggleBtn.length) {
                            toggleBtn.text('Materials (' + newCount + ')');
                        }

                        // If materials section is expanded, update the content
                        const container = $('#materials-' + courseId);
                        if (container.hasClass('show')) {
                            if (newCount === 0) {
                                container.html('<div class="text-muted small">No materials yet.</div>');
                            } else {
                                let html = '<div class="list-group list-group-flush">';
                                list.forEach(function(m) {
                                    html += '<div class="list-group-item px-0 d-flex justify-content-between align-items-center">'
                                         + '<span class="text-truncate" style="max-width: 75%;">' + m.file_name + '</span>'
                                         + '<a class="btn btn-sm btn-outline-primary" href="<?= site_url('/materials/download/') ?>' + m.id + '">Download</a>'
                                         + '</div>';
                                });
                                html += '</div>';
                                container.html(html);
                            }
                        }
                    }
                });
        }
    }

    // Initialize material counts and start polling after page load
    setTimeout(function() {
        initializeMaterialCounts();
        // Start polling every 15 seconds for more responsive updates
        setInterval(checkForMaterialUpdates, 15000);
    }, 1000);

    // Handle view details button clicks for enrolled courses
    $(document).on('click', '.view-details-btn', function(e) {
        e.preventDefault();

        const courseId = $(this).data('course-id');

        $('#enrollmentDetailsModal').modal('show');

        // Load enrollment details via AJAX
        $.ajax({
            url: '<?= base_url('/course/enrollment-details/') ?>' + courseId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.enrollment) {
                    const enrollment = response.enrollment;

                    const detailsHtml = `
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="bi bi-person me-2"></i>Course Information</h6>
                                <table class="table table-sm">
                                    <tr><td class="fw-semibold">Course Code:</td><td>${enrollment.course_code || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Course Name:</td><td>${enrollment.course_name || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">School Year:</td><td>${enrollment.school_year || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Semester:</td><td>${enrollment.semester || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Schedule:</td><td>${enrollment.schedule || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Room:</td><td>${enrollment.room || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Day of Class:</td><td>${enrollment.day_of_class || 'N/A'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="bi bi-mortarboard me-2"></i>Instructor & Enrollment</h6>
                                <table class="table table-sm">
                                    <tr><td class="fw-semibold">Teacher:</td><td>${enrollment.teacher_name || 'Not Assigned'}</td></tr>
                                    <tr><td class="fw-semibold">Status:</td><td>
                                        <span class="badge bg-${enrollment.status === 'Active' ? 'success' : (enrollment.status === 'Inactive' ? 'warning' : 'danger')}">
                                            ${enrollment.status || 'N/A'}
                                        </span>
                                    </td></tr>
                                    <tr><td class="fw-semibold">Enrollment Date:</td><td>${enrollment.enrollment_date || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Remarks:</td><td>${enrollment.enrollment_status || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                        ${enrollment.description ? `
                        <div class="mt-3">
                            <h6 class="text-primary"><i class="bi bi-info-circle me-2"></i>Course Description</h6>
                            <p class="text-muted small">${enrollment.description}</p>
                        </div>
                        ` : ''}
                    `;

                    $('#enrollment-details-content').html(detailsHtml);
                } else {
                    $('#enrollment-details-content').html(`
                        <div class="alert alert-warning">
                            <h5>Details Not Found</h5>
                            <p>Unable to load course details. Please try again.</p>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#enrollment-details-content').html(`
                    <div class="alert alert-danger">
                        <h5>Error</h5>
                        <p>Failed to load course details. Please try again.</p>
                    </div>
                `);
            }
        });
    });

    // Load more materials for teachers
    let materialsOffset = 10; // Assuming initial load shows 10

    function loadMoreMaterials() {
        const btn = event.target;
        const originalText = $(btn).text();
        $(btn).text('Loading...').prop('disabled', true);

        $.get('<?= base_url('materials/teacher-materials') ?>?offset=' + materialsOffset + '&limit=10')
            .done(function(res) {
                if (res && res.success && res.materials && res.materials.length > 0) {
                    let html = '';
                    res.materials.forEach(function(material) {
                        html += `
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">${material.file_name}</h6>
                                    <p class="mb-1 text-muted small">
                                        Course: ${material.course_name} (${material.course_code}) |
                                        Uploaded: ${material.upload_date}
                                    </p>
                                </div>
                                <a href="${material.file_path}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> View
                                </a>
                            </div>
                        `;
                    });
                    $('.list-group-flush').append(html);
                    materialsOffset += 10;

                    if (res.materials.length < 10) {
                        $(btn).hide(); // No more materials
                    }
                } else {
                    $(btn).hide(); // No more materials
                }
            })
            .fail(function() {
                showAlert('danger', 'Failed to load more materials');
            })
            .always(function() {
                $(btn).text(originalText).prop('disabled', false);
            });
    }
});
</script>
<?= $this->endSection() ?>
