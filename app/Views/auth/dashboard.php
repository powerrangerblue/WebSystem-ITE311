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
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Enrolled Courses</div>
            <div class="card-body">
                <?php if (!empty($enrolledCourses)): ?>
                    <ul class="mb-0">
                        <?php foreach ($enrolledCourses as $c): ?>
                            <li><?= esc($c['title'] ?? 'Untitled') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-muted">No enrollments yet.</div>
                <?php endif; ?>
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
<?= $this->endSection() ?>


