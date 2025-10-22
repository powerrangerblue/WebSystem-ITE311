<?= $this->extend('template') ?>

<?= $this->section('title') ?>Course Materials<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h2>Materials for Course</h2>
    
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
    
    <?php if (!empty($materials)): ?>
        <div class="row">
            <?php foreach ($materials as $material): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($material['file_name']) ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt"></i> Uploaded: <?= date('M d, Y H:i', strtotime($material['created_at'])) ?>
                                </small>
                            </p>
                            <div class="d-grid gap-2">
                                <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <?php 
                                $role = strtolower((string) session()->get('role'));
                                if ($role === 'admin' || $role === 'teacher'): 
                                ?>
                                    <a href="<?= site_url('materials/delete/' . $material['id']) ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this material?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No materials available for this course yet.
        </div>
    <?php endif; ?>
    
    <div class="mt-4">
        <?php 
        $role = strtolower((string) session()->get('role'));
        $backUrl = 'dashboard'; // Default to dashboard
        
        // Set appropriate back URL based on user role
        if ($role === 'admin') {
            $backUrl = 'admin/materials';
        } elseif ($role === 'teacher') {
            $backUrl = 'teacher/dashboard';
        } elseif ($role === 'student') {
            $backUrl = 'dashboard';
        }
        ?>
        <a href="<?= site_url($backUrl) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to <?= $role === 'admin' ? 'Materials Management' : 'Dashboard' ?>
        </a>
        <button onclick="location.reload()" class="btn btn-outline-primary ms-2">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
</div>
<?= $this->endSection() ?>
