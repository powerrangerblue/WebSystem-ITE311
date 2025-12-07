<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Create Assignment</h1>
        <div class="text-muted small">Create a new assignment for this course</div>
    </div>
    <a href="<?= base_url('assignments/course/' . $courseId) ?>" class="btn btn-outline-secondary">Back to Assignments</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?= base_url('assignments/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <input type="hidden" name="course_id" value="<?= $courseId ?>">

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Assignment Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                               value="<?= old('title') ?>" placeholder="Enter assignment title" required>
                        <div class="form-text">Give your assignment a clear, descriptive title.</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="6"
                                  placeholder="Describe the assignment requirements, instructions, and expectations..." required><?= old('description') ?></textarea>
                        <div class="form-text">Provide detailed instructions for students.</div>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="due_date" name="due_date"
                               value="<?= old('due_date') ?>" required>
                        <div class="form-text">Set the deadline for assignment submission.</div>
                    </div>

                    <div class="mb-3">
                        <label for="attachment" class="form-label fw-semibold">Attachment (Optional)</label>
                        <input type="file" class="form-control" id="attachment" name="attachment"
                               accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                        <div class="form-text">Upload assignment materials, rubrics, or additional resources (max 5MB).</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Assignment Guidelines</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Title should be clear and descriptive</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Include detailed instructions</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Set a reasonable due date</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Attachments are optional</li>
                                <li class="mb-0"><i class="bi bi-check-circle text-success"></i> Students can download attachments</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    Create Assignment
                </button>
                <a href="<?= base_url('assignments/course/' . $courseId) ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Set minimum date to current date/time
document.addEventListener('DOMContentLoaded', function() {
    const dueDateInput = document.getElementById('due_date');
    const now = new Date();
    const formatted = now.toISOString().slice(0, 16); // Format for datetime-local
    dueDateInput.min = formatted;
});
</script>

<?= $this->endSection() ?>
