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
});
</script>
<?= $this->endSection() ?>
