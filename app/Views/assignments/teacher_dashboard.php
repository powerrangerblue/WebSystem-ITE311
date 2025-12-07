<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Manage Assignments</h1>
        <div class="text-muted small">Create and manage assignments for your courses</div>
    </div>
    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Your Courses</h5>
    </div>
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

                            <div class="d-flex flex-column gap-2 align-items-end">
                                <div class="text-muted small">
                                    <i class="bi bi-journal-text"></i>
                                    <?= (int)$course['assignment_count'] ?> assignment<?= (int)$course['assignment_count'] !== 1 ? 's' : '' ?>
                                </div>

                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('assignments/course/' . $course['id']) ?>"
                                       class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye"></i> View Assignments
                                    </a>
                                    <a href="<?= base_url('assignments/create/' . $course['id']) ?>"
                                       class="btn btn-success btn-sm">
                                        <i class="bi bi-plus-circle"></i> Create Assignment
                                    </a>
                                    <a href="<?= base_url('admin/course/' . $course['id'] . '/upload') ?>"
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-upload"></i> Materials
                                    </a>
                                </div>
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
                <p class="text-muted">There are no courses available for creating assignments.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Alert container for AJAX messages -->
<div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Function to show Bootstrap alert
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    $('#alert-container').html(alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('#alert-container').fadeOut();
    }, 5000);
}
</script>
<?= $this->endSection() ?>
