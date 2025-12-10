<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Course Management</h1>
        <p class="text-muted mb-0">Manage all courses in the system</p>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-primary"><?= esc($totalCourses) ?></div>
                    <div class="text-muted">Total Courses</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-success"><?= esc($activeCourses) ?></div>
                    <div class="text-muted">Active Courses</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">Courses</h5>
        </div>
        <button class="btn btn-primary btn-sm" id="add-course-btn">
            <i class="bi bi-plus-circle"></i> Add New Course
        </button>
    </div>

    <!-- Search Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" id="course-search" class="form-control" placeholder="Search by course code, title, or teacher...">
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="courses-table">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">Course Code</th>
                            <th class="border-0">Course Title</th>
                            <th class="border-0">Description</th>
                            <th class="border-0">School Year</th>
                            <th class="border-0">Semester</th>
                            <th class="border-0">Schedule</th>
                            <th class="border-0">Teacher</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 pe-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <tr data-course-id="<?= esc($course['id']) ?>">
                                    <td class="ps-4 fw-bold"><?= esc($course['course_code']) ?></td>
                                    <td class="course-title"><?= esc($course['course_name']) ?></td>
                                    <td class="course-description" style="max-width: 200px;">
                                        <span class="text-truncate d-inline-block" style="max-width: 100%;" title="<?= esc($course['description']) ?>">
                                            <?= esc(substr($course['description'] ?? '', 0, 50)) ?><?= strlen($course['description'] ?? '') > 50 ? '...' : '' ?>
                                        </span>
                                    </td>
                                    <td class="course-school-year"><?= esc($course['school_year'] ?? '-') ?></td>
                                    <td class="course-semester"><?= esc($course['semester'] ?? '-') ?></td>
                                    <td class="course-schedule"><?= esc($course['schedule'] ?? '-') ?></td>
                                    <td class="course-teacher"><?= esc($course['teacher_name'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($course['status'] ?? 'Active') === 'Active' ? 'success' : 'secondary' ?>">
                                            <?= esc($course['status'] ?? 'Active') ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <button class="btn btn-sm btn-outline-primary edit-course-btn"
                                                data-course-id="<?= esc($course['id']) ?>"
                                                data-course-code="<?= esc($course['course_code']) ?>"
                                                data-course-name="<?= esc($course['course_name']) ?>"
                                                data-description="<?= esc($course['description']) ?>"
                                                data-school-year="<?= esc($course['school_year']) ?>"
                                                data-semester="<?= esc($course['semester']) ?>"
                                                data-schedule="<?= esc($course['schedule']) ?>"
                                                data-teacher-id="<?= esc($course['teacher_id']) ?>"
                                                data-status="<?= esc($course['status']) ?>"
                                                data-start-date="<?= esc($course['start_date']) ?>"
                                                data-end-date="<?= esc($course['end_date']) ?>">
                                            <i class="bi bi-pencil"></i> Edit Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">No courses found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel">Edit Course Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCourseForm">
                <div class="modal-body">
                    <input type="hidden" id="edit-course-id" name="course_id">

                    <!-- Course Information Section -->
                    <div class="border-bottom pb-3 mb-3">
                        <h6 class="text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Course Information</h6>
                        <p class="text-muted small mb-3">These fields define the identity of the course.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-course-code" class="form-label">Course Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-course-code" name="course_code" placeholder="e.g., CS101, DB301, WD201" readonly>
                                <div class="form-text">Unique identifier for the course</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-course-name" class="form-label">Course Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-course-name" name="course_name" placeholder="e.g., Computer Science Basics" required>
                                <div class="form-text">Full name of the course</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit-description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit-description" name="description" rows="2" placeholder="Short overview of what the course covers"></textarea>
                        </div>
                    </div>

                    <!-- Academic Settings Section -->
                    <div class="border-bottom pb-3 mb-3">
                        <h6 class="text-primary mb-3"><i class="bi bi-calendar-event me-2"></i>Academic Settings</h6>
                        <p class="text-muted small mb-3">These fields determine when and how the course applies.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-school-year" class="form-label">School Year</label>
                                <input type="text" class="form-control" id="edit-school-year" name="school_year" placeholder="e.g., 2025–2026">
                                <div class="form-text">Academic year (e.g., 2025–2026)</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-semester" class="form-label">Semester</label>
                                <select class="form-select" id="edit-semester" name="semester">
                                    <option value="">Select Semester</option>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                    <option value="Summer Term">Summer Term (optional)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule and Duration Section -->
                    <div class="border-bottom pb-3 mb-3">
                        <h6 class="text-primary mb-3"><i class="bi bi-clock me-2"></i>Schedule and Duration</h6>
                        <p class="text-muted small mb-3">Set the course timeline and schedule.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-start-date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="edit-start-date" name="start_date">
                                <div class="form-text">Course start date</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-end-date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="edit-end-date" name="end_date">
                                <div class="form-text">Course end date</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit-schedule" class="form-label">Schedule</label>
                            <select class="form-select" id="edit-schedule" name="schedule">
                                <option value="">Select Schedule</option>
                                <option value="08:00–09:00">08:00–09:00</option>
                                <option value="09:00–10:00">09:00–10:00</option>
                                <option value="10:00–11:00">10:00–11:00</option>
                                <option value="11:00–12:00">11:00–12:00</option>
                                <option value="13:00–14:00">13:00–14:00</option>
                                <option value="14:00–15:00">14:00–15:00</option>
                                <option value="15:00–16:00">15:00–16:00</option>
                                <option value="16:00–17:00">16:00–17:00</option>
                            </select>
                            <div class="form-text">Select class time slot</div>
                        </div>
                    </div>

                    <!-- Instructor Assignment Section -->
                    <div class="border-bottom pb-3 mb-3">
                        <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Instructor Assignment</h6>
                        <p class="text-muted small mb-3">Assign a teacher to this course.</p>

                        <div class="mb-3">
                            <label for="edit-teacher" class="form-label">Teacher / Instructor</label>
                            <select class="form-select" id="edit-teacher" name="teacher_id">
                                <option value="">Select Teacher</option>
                                <?php if (!empty($teachers)): ?>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Choose from registered teachers</div>
                        </div>
                    </div>

                    <!-- Status Section -->
                    <div>
                        <h6 class="text-primary mb-3"><i class="bi bi-check-circle me-2"></i>Status</h6>
                        <p class="text-muted small mb-3">Set the course availability status.</p>

                        <div class="mb-3">
                            <label for="edit-status" class="form-label">Status</label>
                            <select class="form-select" id="edit-status" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            <div class="form-text">Course availability status</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alert container -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1056;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let isCreateMode = false;

    // Search functionality
    $('#course-search').on('input', function() {
        const query = $(this).val().toLowerCase().trim();
        const rows = $('#courses-table tbody tr');

        if (!query) {
            rows.show();
            return;
        }

        rows.each(function() {
            const row = $(this);
            const text = row.text().toLowerCase();
            row.toggle(text.indexOf(query) !== -1);
        });
    });

    // Add course button click
    $('#add-course-btn').on('click', function() {
        isCreateMode = true;

        // Set modal to create mode
        $('#editCourseModalLabel').text('Create New Course');
        $('#editCourseForm button[type="submit"]').text('Create Course');

        // Clear all form fields
        $('#edit-course-id').val('');
        $('#edit-course-code').val('').prop('readonly', false);
        $('#edit-course-name').val('');
        $('#edit-description').val('');
        $('#edit-school-year').val('');
        $('#edit-semester').val('');
        $('#edit-schedule').val('');
        $('#edit-teacher').val('');
        $('#edit-start-date').val('');
        $('#edit-end-date').val('');
        $('#edit-status').val('Active'); // Default to Active for new courses

        // Show modal
        $('#editCourseModal').modal('show');
    });

    // Edit course button click
    $('.edit-course-btn').on('click', function() {
        isCreateMode = false;

        // Set modal to edit mode
        $('#editCourseModalLabel').text('Edit Course Details');
        $('#editCourseForm button[type="submit"]').text('Update');

        const courseData = $(this).data();

        // Populate modal
        $('#edit-course-id').val(courseData.courseId);
        $('#edit-course-code').val(courseData.courseCode).prop('readonly', true);
        $('#edit-course-name').val(courseData.courseName);
        $('#edit-description').val(courseData.description);
        $('#edit-school-year').val(courseData.schoolYear);
        $('#edit-semester').val(courseData.semester);
        $('#edit-schedule').val(courseData.schedule);
        $('#edit-teacher').val(courseData.teacherId);
        $('#edit-start-date').val(courseData.startDate);
        $('#edit-end-date').val(courseData.endDate);
        $('#edit-status').val(courseData.status || 'Active');

        // Show modal
        $('#editCourseModal').modal('show');
    });

    // Form submission
    $('#editCourseForm').on('submit', function(e) {
        e.preventDefault();

        const startDate = $('#edit-start-date').val();
        const endDate = $('#edit-end-date').val();

        // Client-side validation: end date must be after start date
        if (startDate && endDate) {
            const startDateTime = new Date(startDate);
            const endDateTime = new Date(endDate);

            if (endDateTime <= startDateTime) {
                showAlert('danger', 'End date must be after the start date');
                return false;
            }
        }

        const formData = new FormData(this);
        const url = isCreateMode ? '<?= base_url('/admin/courses/create') ?>' : '<?= base_url('/admin/courses/update') ?>';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Course updated successfully!');

                    if (isCreateMode) {
                        // For creation, refresh the page to show the new course
                        location.reload();
                    } else {
                        // For updates, update table row
                        const courseId = formData.get('course_id');
                        const row = $(`tr[data-course-id="${courseId}"]`);

                        row.find('.course-title').text(formData.get('course_name'));
                        row.find('.course-description span').text(formData.get('description').substring(0, 50) + (formData.get('description').length > 50 ? '...' : ''));
                        row.find('.course-description span').attr('title', formData.get('description'));
                        row.find('.course-school-year').text(formData.get('school_year') || '-');
                        row.find('.course-semester').text(formData.get('semester') || '-');
                        row.find('.course-schedule').text(formData.get('schedule') || '-');

                        // Update teacher name
                        const teacherId = formData.get('teacher_id');
                        const teacherName = $('#edit-teacher option:selected').text();
                        row.find('.course-teacher').text(teacherName || '-');

                        // Update data attributes on the edit button for future edits
                        const editButton = row.find('.edit-course-btn');
                        editButton.data({
                            'courseName': formData.get('course_name'),
                            'description': formData.get('description'),
                            'schoolYear': formData.get('school_year'),
                            'semester': formData.get('semester'),
                            'schedule': formData.get('schedule'),
                            'teacherId': teacherId,
                            'startDate': formData.get('start_date'),
                            'endDate': formData.get('end_date')
                        });

                        $('#editCourseModal').modal('hide');
                    }
                } else {
                    showAlert('danger', response.message || 'Failed to update course.');
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred while updating the course.');
            }
        });
    });

    // Reset modal state when hidden
    $('#editCourseModal').on('hidden.bs.modal', function() {
        isCreateMode = false;
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
<?= $this->endSection() ?>
