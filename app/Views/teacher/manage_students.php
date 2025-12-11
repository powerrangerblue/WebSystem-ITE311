<?php $this->extend('template') ?>

<?php $this->section('content') ?>
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="mb-4">
        <h1 class="h3 mb-1">Manage Students</h1>
        <?php if ($currentCourse): ?>
            <p class="text-muted mb-0">Course: <strong><?= esc($currentCourse['course_code']) ?> – <?= esc($currentCourse['course_name']) ?></strong></p>
        <?php else: ?>
            <p class="text-muted mb-0">No courses assigned</p>
        <?php endif; ?>
    </div>

    <?php if (empty($courses)): ?>
        <div class="alert alert-info">
            <h5>No Courses Available</h5>
            <p>You are not assigned to any active courses.</p>
        </div>
    <?php else: ?>

    <!-- Course Selection -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="course-select" class="form-label">Select Course</label>
                    <select class="form-select" id="course-select" onchange="changeCourse(this.value)">
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= esc($course['id']) ?>" <?= $currentCourse['id'] == $course['id'] ? 'selected' : '' ?>>
                                <?= esc($course['course_code']) ?> – <?= esc($course['course_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Overview -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-primary mb-0"><?= count($students) ?></h4>
                        <small class="text-muted">Total Students</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-success mb-0">
                            <?php
                            $activeCount = 0;
                            foreach ($students as $student) {
                                if (($student['status'] ?? 'Active') === 'Active') $activeCount++;
                            }
                            echo $activeCount;
                            ?>
                        </h4>
                        <small class="text-muted">Active Students</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-warning mb-0">
                            <?php
                            $inactiveCount = 0;
                            foreach ($students as $student) {
                                if (($student['status'] ?? 'Active') === 'Inactive') $inactiveCount++;
                            }
                            echo $inactiveCount;
                            ?>
                        </h4>
                        <small class="text-muted">Inactive Students</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student List Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Enrolled Students (<?= count($students) ?>)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="students-table">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">Name</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 pe-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                                <tr data-student-id="<?= esc($student['user_id']) ?>" data-enrollment-id="<?= esc($student['id']) ?>">
                                    <td class="ps-4 student-name">
                                        <div class="fw-semibold"><?= esc($student['name']) ?></div>
                                        <?php if (!empty($student['section'])): ?>
                                            <small class="text-muted">Section: <?= esc($student['section']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="student-email">
                                        <?= esc($student['email']) ?>
                                    </td>
                                    <td class="student-status">
                                        <span class="badge bg-<?= ($student['status'] ?? 'Active') === 'Active' ? 'success' : (($student['status'] ?? 'Active') === 'Inactive' ? 'warning' : 'danger') ?>">
                                            <?= esc($student['status'] ?? 'Active') ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-info view-details-btn"
                                                    data-student-id="<?= esc($student['user_id']) ?>"
                                                    title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary update-status-btn"
                                                    data-enrollment-id="<?= esc($student['id']) ?>"
                                                    data-current-status="<?= esc($student['status'] ?? 'Active') ?>"
                                                    title="Update Status">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger remove-student-btn"
                                                    data-enrollment-id="<?= esc($student['id']) ?>"
                                                    data-student-name="<?= esc($student['name']) ?>"
                                                    title="Remove from Course">
                                                <i class="bi bi-person-dash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="bi bi-people" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5>No Students Found</h5>
                                    <p>No students match your current filters.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="student-details-content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading student details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Student Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm">
                <div class="modal-body">
                    <input type="hidden" id="update-enrollment-id" name="enrollment_id">

                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <input type="text" class="form-control" id="current-status" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="new-status" class="form-label">New Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="new-status" name="new_status" required>
                            <option value="">Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Dropped">Dropped</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status-remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="status-remarks" name="remarks" rows="3"
                                  placeholder="Optional remarks about this status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
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
    // Course change handler
    window.changeCourse = function(courseId) {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('course', courseId);
        window.location.href = currentUrl.toString();
    };

    // View student details button click
    $('.view-details-btn').on('click', function() {
        const studentId = $(this).data('studentId');

        $('#studentDetailsModal').modal('show');

        // Load student details via AJAX
        $.ajax({
            url: '<?= base_url('/teacher/manage-students/student/details/') ?>' + studentId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.student.length > 0) {
                    const student = response.student[0]; // Take first enrollment (or modify to show all)

                    const detailsHtml = `
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="bi bi-person me-2"></i>Basic Information</h6>
                                <table class="table table-sm">
                                    <tr><td class="fw-semibold">Full Name:</td><td>${student.name || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Email:</td><td>${student.email || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Section:</td><td>${student.section || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Room:</td><td>${student.room || 'N/A'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="bi bi-journal me-2"></i>Enrollment Information</h6>
                                <table class="table table-sm">
                                    <tr><td class="fw-semibold">Course:</td><td>${student.course_code} - ${student.course_name}</td></tr>
                                    <tr><td class="fw-semibold">School Year:</td><td>${student.school_year || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Semester:</td><td>${student.semester || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Status:</td><td>
                                        <span class="badge bg-${student.status === 'Active' ? 'success' : (student.status === 'Inactive' ? 'warning' : 'danger')}">
                                            ${student.status || 'N/A'}
                                        </span>
                                    </td></tr>
                                    <tr><td class="fw-semibold">Enrollment Date:</td><td>${student.enrollment_date || 'N/A'}</td></tr>
                                    <tr><td class="fw-semibold">Remarks:</td><td>${student.enrollment_status || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    `;

                    $('#student-details-content').html(detailsHtml);
                } else {
                    $('#student-details-content').html(`
                        <div class="alert alert-warning">
                            <h5>Student Not Found</h5>
                            <p>Unable to load student details. Please try again.</p>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#student-details-content').html(`
                    <div class="alert alert-danger">
                        <h5>Error</h5>
                        <p>Failed to load student details. Please try again.</p>
                    </div>
                `);
            }
        });
    });

    // Update status button click
    $('.update-status-btn').on('click', function() {
        const enrollmentId = $(this).data('enrollmentId');
        const currentStatus = $(this).data('currentStatus');

        $('#update-enrollment-id').val(enrollmentId);
        $('#current-status').val(currentStatus);
        $('#new-status').val(currentStatus);
        $('#status-remarks').val('');

        $('#updateStatusModal').modal('show');
    });

    // Update status form submission
    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('/teacher/manage-students/update-status') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Student status updated successfully!');

                    const enrollmentId = formData.get('enrollment_id');
                    const newStatus = formData.get('new_status');

                    // Update the status badge and button data attribute
                    const row = $(`tr[data-enrollment-id="${enrollmentId}"]`);
                    row.find('.student-status .badge').attr('class',
                        `badge bg-${newStatus === 'Active' ? 'success' : (newStatus === 'Inactive' ? 'warning' : 'danger')}`
                    ).text(newStatus);

                    row.find('.update-status-btn').data('currentStatus', newStatus);

                    $('#updateStatusModal').modal('hide');
                } else {
                    showAlert('danger', response.message || 'Failed to update student status.');
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred while updating the status.');
            }
        });
    });

    // Remove student button click (for future implementation)
    $('.remove-student-btn').on('click', function() {
        const enrollmentId = $(this).data('enrollmentId');
        const studentName = $(this).data('studentName');

        if (confirm(`Are you sure you want to remove ${studentName} from this course? This will change their status to "Dropped".`)) {
            // For now, we'll use the status update to drop the student
            $('#update-enrollment-id').val(enrollmentId);
            $('#current-status').val('Active'); // Default current status
            $('#new-status').val('Dropped');
            $('#status-remarks').val('Removed from course by teacher');

            // Submit the form
            $('#updateStatusForm').submit();
        }
    });
});

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
