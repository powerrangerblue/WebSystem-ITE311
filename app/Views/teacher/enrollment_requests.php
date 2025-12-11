<?php $this->extend('template') ?>

<?php $this->section('content') ?>
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="mb-4">
        <h1 class="h3 mb-1">Manage Enrollment Requests</h1>
        <p class="text-muted mb-0">Review and approve/decline student enrollment requests for your courses.</p>
    </div>

    <?php if (empty($pendingRequests)): ?>
        <div class="alert alert-info">
            <h5>No Pending Requests</h5>
            <p>There are currently no pending enrollment requests for your courses.</p>
        </div>
    <?php else: ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-0"><?= count($pendingRequests) ?></h4>
                    <small class="text-muted">Pending Requests</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <?php
                    $uniqueCourses = array_unique(array_column($pendingRequests, 'course_id'));
                    ?>
                    <h4 class="text-info mb-0"><?= count($uniqueCourses) ?></h4>
                    <small class="text-muted">Courses with Requests</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <?php
                    $uniqueStudents = array_unique(array_column($pendingRequests, 'user_id'));
                    ?>
                    <h4 class="text-success mb-0"><?= count($uniqueStudents) ?></h4>
                    <small class="text-muted">Students Requesting</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Pending Enrollment Requests (<?= count($pendingRequests) ?>)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="requests-table">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">Student</th>
                            <th class="border-0">Course</th>
                            <th class="border-0">Request Date</th>
                            <th class="border-0 pe-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingRequests as $request): ?>
                            <tr data-request-id="<?= esc($request['id']) ?>">
                                <td class="ps-4">
                                    <div class="fw-semibold"><?= esc($request['student_name']) ?></div>
                                    <small class="text-muted"><?= esc($request['student_email']) ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= esc($request['course_code']) ?> - <?= esc($request['course_name']) ?></div>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <?= date('M d, Y H:i', strtotime($request['enrollment_date'])) ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-success approve-btn"
                                                data-request-id="<?= esc($request['id']) ?>"
                                                data-student-name="<?= esc($request['student_name']) ?>"
                                                data-course-name="<?= esc($request['course_name']) ?>"
                                                title="Approve Request">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger decline-btn"
                                                data-request-id="<?= esc($request['id']) ?>"
                                                data-student-name="<?= esc($request['student_name']) ?>"
                                                data-course-name="<?= esc($request['course_name']) ?>"
                                                title="Decline Request">
                                            <i class="bi bi-x-circle"></i> Decline
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Enrollment Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    <input type="hidden" id="approve-enrollment-id" name="enrollment_id">
                    <input type="hidden" name="action" value="approve">

                    <div class="alert alert-success">
                        <h6>Approve Request</h6>
                        <p class="mb-1"><strong>Student:</strong> <span id="approve-student-name"></span></p>
                        <p class="mb-1"><strong>Course:</strong> <span id="approve-course-name"></span></p>
                    </div>

                    <div class="mb-3">
                        <label for="approve-notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approve-notes" name="notes" rows="3"
                                  placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="declineModalLabel">Decline Enrollment Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="declineForm">
                <div class="modal-body">
                    <input type="hidden" id="decline-enrollment-id" name="enrollment_id">
                    <input type="hidden" name="action" value="decline">

                    <div class="alert alert-danger">
                        <h6>Decline Request</h6>
                        <p class="mb-1"><strong>Student:</strong> <span id="decline-student-name"></span></p>
                        <p class="mb-1"><strong>Course:</strong> <span id="decline-course-name"></span></p>
                    </div>

                    <div class="mb-3">
                        <label for="decline-notes" class="form-label">Reason for Decline <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="decline-notes" name="notes" rows="3" required
                                  placeholder="Please provide a reason for declining this request..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alert container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1056;"></div>

<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Approve button click
    $('.approve-btn').on('click', function() {
        const requestId = $(this).data('requestId');
        const studentName = $(this).data('studentName');
        const courseName = $(this).data('courseName');

        $('#approve-enrollment-id').val(requestId);
        $('#approve-student-name').text(studentName);
        $('#approve-course-name').text(courseName);
        $('#approve-notes').val('');

        $('#approveModal').modal('show');
    });

    // Decline button click
    $('.decline-btn').on('click', function() {
        const requestId = $(this).data('requestId');
        const studentName = $(this).data('studentName');
        const courseName = $(this).data('courseName');

        $('#decline-enrollment-id').val(requestId);
        $('#decline-student-name').text(studentName);
        $('#decline-course-name').text(courseName);
        $('#decline-notes').val('');

        $('#declineModal').modal('show');
    });

    // Approve form submission
    $('#approveForm').on('submit', function(e) {
        e.preventDefault();
        processEnrollmentRequest($(this).serialize(), 'approve');
    });

    // Decline form submission
    $('#declineForm').on('submit', function(e) {
        e.preventDefault();
        processEnrollmentRequest($(this).serialize(), 'decline');
    });
});

function processEnrollmentRequest(formData, action) {
    $.ajax({
        url: '<?= base_url('/teacher/enrollment-requests/process') ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message || 'Request processed successfully!');

                // Remove the processed row from the table
                const enrollmentId = action === 'approve' ?
                    $('#approve-enrollment-id').val() :
                    $('#decline-enrollment-id').val();
                $(`tr[data-request-id="${enrollmentId}"]`).fadeOut(500, function() {
                    $(this).remove();

                    // Check if table is empty
                    if ($('#requests-table tbody tr').length === 0) {
                        location.reload(); // Reload to show "no requests" message
                    }
                });

                // Close modal
                if (action === 'approve') {
                    $('#approveModal').modal('hide');
                } else {
                    $('#declineModal').modal('hide');
                }
            } else {
                showAlert('danger', response.message || 'Failed to process request.');
            }
        },
        error: function() {
            showAlert('danger', 'An error occurred while processing the request.');
        }
    });
}

function showAlert(type, message) {
    const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;

    $('#alert-container').html(alertHtml);

    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}
</script>
<?php $this->endSection() ?>
