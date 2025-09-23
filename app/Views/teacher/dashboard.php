<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><?= esc($title ?? 'Teacher Dashboard') ?></h2>
        <small class="text-muted">Welcome back, <?= esc(session('user_name')) ?></small>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary btn-sm" href="#">Create Course</a>
        <a class="btn btn-danger btn-sm" href="<?= site_url('logout') ?>">Logout</a>
    </div>
    
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <strong>Your Courses</strong>
            </div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= esc($course['name'] ?? 'Untitled Course') ?></td>
                                        <td><?= esc($course['created_at'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No courses found. Use "Create Course" to get started.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <strong>Recent Submissions</strong>
            </div>
            <div class="card-body">
                <?php if (!empty($notifications)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($notifications as $n): ?>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold"><?= esc($n['student_name'] ?? 'Student') ?></div>
                                    <small class="text-muted">Course #<?= esc($n['course_id'] ?? '-') ?></small>
                                </div>
                                <small class="text-muted"><?= esc($n['created_at'] ?? '-') ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted mb-0">No new submissions.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


