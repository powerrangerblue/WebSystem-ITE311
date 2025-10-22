<?= $this->extend('template') ?>

<?= $this->section('title') ?>Admin Dashboard - Student Portal<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Admin Dashboard</h1>
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">Quick Actions</div>
        <div class="card-body">
            <a href="<?= site_url('admin/courses') ?>" class="btn btn-primary me-2">Manage Courses</a>
            <a href="<?= site_url('admin/materials') ?>" class="btn btn-secondary">Manage Materials</a>
        </div>
    </div>
<?= $this->endSection() ?>
