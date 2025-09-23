<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><?= esc($title ?? 'Admin Dashboard') ?></h2>
        <small class="text-muted">System overview and recent activity</small>
    </div>
    <div class="d-flex gap-2">
        <a href="#" class="btn btn-outline-primary btn-sm disabled">Manage Users</a>
        <a href="#" class="btn btn-outline-secondary btn-sm disabled">Manage Courses</a>
        <a class="btn btn-danger btn-sm" href="<?= site_url('logout') ?>">Logout</a>
    </div>
    
</div>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Total Users</h6>
                <h3 class="card-title mb-0"><?= esc($totalUsers ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Admins</h6>
                <h3 class="card-title mb-0"><?= esc($totalAdmins ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Teachers</h6>
                <h3 class="card-title mb-0"><?= esc($totalTeachers ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Students</h6>
                <h3 class="card-title mb-0"><?= esc($totalStudents ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Total Courses</h6>
                <h3 class="card-title mb-0"><?= esc($totalCourses ?? 0) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><strong>Recent Users</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentUsers)): ?>
                                <?php foreach ($recentUsers as $u): ?>
                                    <tr>
                                        <td><?= esc($u['name'] ?? '-') ?></td>
                                        <td><?= esc($u['email'] ?? '-') ?></td>
                                        <td><span class="badge bg-secondary text-uppercase"><?= esc($u['role'] ?? '-') ?></span></td>
                                        <td><?= esc($u['created_at'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-3">No recent activity.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><strong>Quick Actions</strong></div>
            <div class="card-body d-grid gap-2">
                <a href="#" class="btn btn-outline-primary btn-sm disabled">Add New User</a>
                <a href="#" class="btn btn-outline-secondary btn-sm disabled">Create Course</a>
                <a href="#" class="btn btn-outline-dark btn-sm disabled">View Reports</a>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>


