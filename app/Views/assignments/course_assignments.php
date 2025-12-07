<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<!-- Course Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">
            <strong><?php echo esc($course['course_name'] ?? 'Course'); ?></strong>
            <small class="text-muted d-block"><?php echo esc($course['course_code'] ?? ''); ?></small>
        </span>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#courseNavbar" aria-controls="courseNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="courseNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= base_url('assignments/course/' . $courseId) ?>">
                        <i class="bi bi-journal-text"></i> Assignments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('admin/course/' . $courseId . '/upload') ?>">
                        <i class="bi bi-upload"></i> Upload Materials
                    </a>
                </li>
            </ul>

            <div class="d-flex">
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary me-2">Back to Dashboard</a>
                <a href="<?= base_url('assignments/create/' . $courseId) ?>" class="btn btn-primary">Create Assignment</a>
            </div>
        </div>
    </div>
</nav>

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
        <h5 class="mb-0">Assignments</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($assignments)): ?>
            <div class="list-group">
                <?php foreach ($assignments as $assignment): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?= esc($assignment['title']) ?></h6>
                                <p class="mb-2 text-muted small"><?= esc(substr($assignment['description'], 0, 100)) ?>...</p>
                                <div class="d-flex gap-3 small text-muted">
                                    <span><i class="bi bi-calendar"></i> Due: <?= date('M j, Y g:i A', strtotime($assignment['due_date'])) ?></span>
                                    <span><i class="bi bi-file-earmark"></i> <?= $assignment['attachment'] ? 'Has attachment' : 'No attachment' ?></span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="<?= base_url('assignments/submissions/' . $assignment['id']) ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Submissions
                                </a>
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
                <h5 class="text-muted">No assignments yet</h5>
                <p class="text-muted">Create your first assignment to get started.</p>
                <a href="<?= base_url('assignments/create/' . $courseId) ?>" class="btn btn-primary">Create Assignment</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Alert container for AJAX messages -->
<div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

<?= $this->endSection() ?>
