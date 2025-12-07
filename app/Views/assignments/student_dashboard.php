<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">My Assignments</h1>
        <div class="text-muted small">View and submit assignments from all your enrolled courses</div>
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
        <h5 class="mb-0">All Assignments</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($assignments)): ?>
            <div class="list-group">
                <?php foreach ($assignments as $assignment): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="mb-0"><?= esc($assignment['title']) ?></h6>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-book"></i> <?= esc($assignment['course_code']) ?>
                                    </span>
                                </div>
                                <p class="mb-2 text-muted small">
                                    <strong>Course:</strong> <?= esc($assignment['course_name']) ?> |
                                    <strong>Due:</strong> <?= date('M j, Y \a\t g:i A', strtotime($assignment['due_date'])) ?>
                                </p>
                                <p class="mb-2 text-muted small"><?= esc(substr($assignment['description'], 0, 150)) ?>...</p>

                                <!-- Submission Status -->
                                <div class="mb-2">
                                    <?php
                                    $status = $assignment['status'];
                                    $statusClass = match($status) {
                                        'graded' => 'success',
                                        'submitted' => 'primary',
                                        default => 'warning'
                                    };
                                    $statusText = match($status) {
                                        'graded' => 'Graded',
                                        'submitted' => 'Submitted',
                                        default => 'Not Submitted'
                                    };
                                    $statusIcon = match($status) {
                                        'graded' => 'check-circle-fill',
                                        'submitted' => 'clock',
                                        default => 'exclamation-triangle'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <i class="bi bi-<?= $statusIcon ?>"></i> <?= $statusText ?>
                                    </span>

                                    <?php if ($status === 'graded' && isset($assignment['submission'])): ?>
                                        <span class="ms-2 fw-bold text-success">
                                            Grade: <?= esc($assignment['submission']['grade']) ?>%
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Show feedback if graded -->
                                <?php if ($status === 'graded' && isset($assignment['submission']['feedback']) && !empty($assignment['submission']['feedback'])): ?>
                                    <div class="alert alert-info p-2 small">
                                        <strong>Feedback:</strong> <?= nl2br(esc($assignment['submission']['feedback'])) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Check if past due date -->
                                <?php
                                $now = new DateTime();
                                $dueDate = new DateTime($assignment['due_date']);
                                $isPastDue = $now > $dueDate;
                                $canSubmit = !$isPastDue || ($status !== 'submitted' && $status !== 'graded');
                                ?>
                                <?php if ($isPastDue && $status !== 'submitted' && $status !== 'graded'): ?>
                                    <div class="text-danger small mb-2">
                                        <i class="bi bi-exclamation-triangle"></i> This assignment is past the due date.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex flex-column gap-2 align-items-end">
                                <!-- Action buttons -->
                                <?php if ($status === 'not_submitted'): ?>
                                    <?php if ($canSubmit): ?>
                                        <button class="btn btn-primary btn-sm submit-btn"
                                                data-assignment-id="<?= $assignment['id'] ?>"
                                                data-title="<?= esc($assignment['title']) ?>"
                                                data-course="<?= esc($assignment['course_name']) ?>">
                                            <i class="bi bi-upload"></i> Submit
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="bi bi-clock"></i> Past Due
                                        </button>
                                    <?php endif; ?>
                                <?php elseif ($status === 'submitted'): ?>
                                    <button class="btn btn-outline-primary btn-sm" disabled>
                                        <i class="bi bi-check-circle"></i> Submitted
                                    </button>
                                    <small class="text-muted">
                                        <?= date('M j, g:i A', strtotime($assignment['submission']['submitted_at'])) ?>
                                    </small>
                                <?php elseif ($status === 'graded'): ?>
                                    <button class="btn btn-success btn-sm" disabled>
                                        <i class="bi bi-trophy"></i> Graded
                                    </button>
                                    <small class="text-muted">
                                        <?= date('M j', strtotime($assignment['submission']['graded_at'])) ?>
                                    </small>
                                <?php endif; ?>

                                <!-- Download assignment attachment -->
                                <?php if ($assignment['attachment']): ?>
                                    <a href="<?= base_url('assignments/download/' . $assignment['id']) ?>"
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-download"></i> Materials
                                    </a>
                                <?php endif; ?>
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
                <p class="text-muted">Your instructors haven't created any assignments for your enrolled courses yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Submit Assignment Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submitModalLabel">Submit Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="submitForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assignment</label>
                        <input type="text" class="form-control" id="assignmentTitle" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Course</label>
                        <input type="text" class="form-control" id="assignmentCourse" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="submission_file" class="form-label fw-semibold">Submission File (Optional)</label>
                        <input type="file" class="form-control" id="submission_file" name="submission_file"
                               accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip">
                        <div class="form-text">Upload your assignment file (max 10MB). You can submit either a file or text, or both.</div>
                    </div>

                    <div class="mb-3">
                        <label for="submission_notes" class="form-label fw-semibold">Text Submission (Optional)</label>
                        <textarea class="form-control" id="submission_notes" name="submission_notes" rows="4"
                                  placeholder="Write your assignment submission here..."></textarea>
                        <div class="form-text">You can submit your assignment as text instead of (or in addition to) a file.</div>
                    </div>

                    <div class="alert alert-info">
                        <small><i class="bi bi-info-circle"></i> Please provide either a file upload, text submission, or both.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Submit Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alert container for AJAX messages -->
<div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let currentAssignmentId = null;

    // Handle submit button clicks
    $('.submit-btn').on('click', function() {
        const assignmentId = $(this).data('assignment-id');
        const title = $(this).data('title');
        const course = $(this).data('course');

        currentAssignmentId = assignmentId;

        // Populate modal
        $('#assignmentTitle').val(title);
        $('#assignmentCourse').val(course);
        $('#submission_file').val('');
        $('#submission_notes').val('');

        // Show modal
        $('#submitModal').modal('show');
    });

    // Handle submit form submission
    $('#submitForm').on('submit', function(e) {
        e.preventDefault();

        if (!currentAssignmentId) return;

        const submitBtn = $(this).find('button[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');

        // Show loading state
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        // Create FormData for file upload
        const formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        // Send AJAX request
        $.ajax({
            url: '<?= base_url('assignments/submit') ?>/' + currentAssignmentId,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
                $('#submitModal').modal('hide');

                // Reload page to show updated status
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                showAlert('danger', response.message);
            }
        })
        .fail(function(xhr) {
            let message = 'An error occurred while submitting the assignment.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showAlert('danger', message);
        })
        .always(function() {
            // Hide loading state
            submitBtn.prop('disabled', false);
            spinner.addClass('d-none');
        });
    });

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
            $('#alert-container .alert').fadeOut();
        }, 5000);
    }
});
</script>
<?= $this->endSection() ?>
