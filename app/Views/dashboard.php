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
        <small class="text-muted">Email: <?= esc(session('user_email')) ?> | Role: <?= esc(session('role')) ?></small>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title">Dashboard</h5>
            <p class="mb-0">This is a protected page only visible after login.</p>
            <hr>
            <p class="text-muted mb-0">You are successfully logged in as <?= esc(session('role')) ?>.</p>
        </div>
    </div>
<?= $this->endSection() ?>