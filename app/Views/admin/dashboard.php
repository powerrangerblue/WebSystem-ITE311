<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?= esc($title ?? 'Admin Dashboard') ?></h2>
    <div>
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
</div>

<div class="mt-4">
    <div class="card">
        <div class="card-header">Quick Actions</div>
        <div class="card-body">
            <a href="#" class="btn btn-primary me-2 disabled">Manage Users</a>
            <a href="#" class="btn btn-outline-primary disabled">Manage Courses</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


