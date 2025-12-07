<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Assignment Submissions</h1>
        <div class="text-muted small">
            <?= esc($assignment['course_name']) ?> - <?= esc($assignment['course_code']) ?>
        </div>
    </div>
    <div>
        <a href="<?= base_url('assignments/course/' . $assignment['course_id']) ?>" class="btn btn-outline-secondary me-2">Back to Assignments</a>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Dashboard</a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-1"><?= esc($assignment['title']) ?></h5>
        <small class="text-muted">Due: <?= date('M j, Y \a\t g:i A', strtotime($assignment['due_date'])) ?></small>
    </div>
    <div class="card-body">
        <p class="mb-0"><?= nl2br(esc($assignment['description'])) ?></p>
        <?php if ($assignment['attachment']): ?>
            <div class="mt-3">
                <a href="<?= base_url('assignments/download/' . $assignment['id']) ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-download"></i> Download Assignment File
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Student Submissions</h5>
        <span class="badge bg-primary"><?= count($submissions) ?> submissions</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($submissions)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Grade</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong><?= esc($submission['student_name']) ?></strong>
                                        <br><small class="text-muted"><?= esc($submission['student_email']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $status = $submission['status'];
                                    $statusClass = match($status) {
                                        'graded' => 'success',
                                        'submitted' => 'primary',
                                        default => 'secondary'
                                    };
                                    $statusText = match($status) {
                                        'graded' => 'Graded',
                                        'submitted' => 'Submitted',
                                        default => 'Not Submitted'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td>
                                    <?php if ($submission['submitted_at']): ?>
                                        <?= date('M j, g:i A', strtotime($submission['submitted_at'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($submission['status'] === 'graded'): ?>
                                        <span class="fw-bold text-success"><?= esc($submission['grade']) ?>%</span>
                                        <?php if ($submission['graded_at']): ?>
                                            <br><small class="text-muted">on <?= date('M j', strtotime($submission['graded_at'])) ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($submission['status'] === 'submitted' || $submission['status'] === 'graded'): ?>
                                        <div class="d-flex flex-column gap-2">
                                            <!-- Show submission content -->
                                            <?php if (!empty($submission['submission_file']) || !empty($submission['submission_notes'])): ?>
                                                <div class="small text-muted">
                                                    <?php if (!empty($submission['submission_file'])): ?>
                                                        <span><i class="bi bi-file-earmark"></i> File submitted</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($submission['submission_file']) && !empty($submission['submission_notes'])): ?>
                                                        <span class="mx-2">•</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($submission['submission_notes'])): ?>
                                                        <span><i class="bi bi-text-paragraph"></i> Text submitted</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Action buttons -->
                                            <div class="btn-group" role="group">
                                                <?php if (!empty($submission['submission_file'])): ?>
                                                    <a href="<?= base_url('assignments/download-submission/' . $submission['id']) ?>"
                                                       class="btn btn-outline-primary btn-sm" title="Download File">
                                                        <i class="bi bi-download"></i> File
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($submission['submission_notes'])): ?>
                                                    <button class="btn btn-outline-info btn-sm view-text-btn"
                                                            data-student-name="<?= esc($submission['student_name']) ?>"
                                                            data-submission-text="<?= esc($submission['submission_notes']) ?>"
                                                            title="View Text Submission">
                                                        <i class="bi bi-eye"></i> Text
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn btn-outline-success btn-sm grade-btn"
                                                        data-submission-id="<?= $submission['id'] ?>"
                                                        data-student-name="<?= esc($submission['student_name']) ?>"
                                                        data-current-grade="<?= $submission['grade'] ?? '' ?>"
                                                        data-feedback="<?= esc($submission['feedback'] ?? '') ?>"
                                                        title="Grade Submission">
                                                    <i class="bi bi-pencil-square"></i> Grade
                                                </button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Not submitted</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-muted">No submissions yet</h5>
                <p class="text-muted">Students haven't submitted this assignment yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Grade Modal -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeModalLabel">Grade Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="gradeForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="studentName" class="form-label">Student</label>
                        <input type="text" class="form-control" id="studentName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="grade" class="form-label fw-semibold">Grade (0-100) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="grade" name="grade" min="0" max="100" required>
                        <div class="form-text">Enter a grade between 0 and 100.</div>
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label fw-semibold">Feedback (Optional)</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4"
                                  placeholder="Provide feedback for the student..."></textarea>
                        <div class="form-text">Share constructive feedback with the student.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Save Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Text Submission Modal -->
<div class="modal fade" id="textModal" tabindex="-1" aria-labelledby="textModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="textModalLabel">Text Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Student</label>
                    <input type="text" class="form-control" id="textStudentName" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Submission Text</label>
                    <div class="border rounded p-3 bg-light" style="min-height: 200px; white-space: pre-wrap;" id="submissionText">
                        <!-- Text content will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Alert container for AJAX messages -->
<div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let currentSubmissionId = null;

    // Handle view text button clicks
    $('.view-text-btn').on('click', function() {
        const studentName = $(this).data('student-name');
        const submissionText = $(this).data('submission-text');

        // Populate modal
        $('#textStudentName').val(studentName);
        $('#submissionText').text(submissionText);

        // Show modal
        $('#textModal').modal('show');
    });

    // Handle grade button clicks
    $('.grade-btn').on('click', function() {
        const submissionId = $(this).data('submission-id');
        const studentName = $(this).data('student-name');
        const currentGrade = $(this).data('current-grade');
        const feedback = $(this).data('feedback');

        currentSubmissionId = submissionId;

        // Populate modal
        $('#studentName').val(studentName);
        $('#grade').val(currentGrade);
        $('#feedback').val(feedback);

        // Show modal
        $('#gradeModal').modal('show');
    });

    // Handle grade form submission
    $('#gradeForm').on('submit', function(e) {
        e.preventDefault();

        if (!currentSubmissionId) return;

        const grade = $('#grade').val();
        const feedback = $('#feedback').val();
        const submitBtn = $(this).find('button[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');

        // Show loading state
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        // Send AJAX request
        $.ajax({
            url: '<?= base_url('assignments/grade') ?>/' + currentSubmissionId,
            method: 'POST',
            data: {
                grade: grade,
                feedback: feedback,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
                $('#gradeModal').modal('hide');

                // Reload page to show updated grades
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                showAlert('danger', response.message);
            }
        })
        .fail(function(xhr) {
            let message = 'An error occurred while grading the submission.';
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
