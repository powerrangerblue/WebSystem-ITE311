<?= $this->extend('template') ?>

<?= $this->section('title') ?>Manage Materials - Admin<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h2>Manage Materials</h2>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <?php foreach ($courses as $course): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><?= esc($course['course_name']) ?></h5>
                        <div>
                            <span class="badge bg-secondary me-2"><?= esc($course['course_code']) ?></span>
                            <span class="badge bg-info"><?= (int)($course['materials_count'] ?? 0) ?> materials</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= site_url('admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Materials
                            </a>
                            <a href="<?= site_url('course/materials/' . $course['id']) ?>" class="btn btn-secondary">
                                <i class="fas fa-eye"></i> View Materials
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
