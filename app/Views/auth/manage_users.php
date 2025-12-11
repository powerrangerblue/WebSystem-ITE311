<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Manage Users</h1>
        <div class="text-muted small">View and manage all users</div>
    </div>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus"></i> Add User
        </button>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary ms-2">Back to Dashboard</a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold">All Users</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): foreach ($users as $user): ?>
                        <?php $isProtectedAdmin = $user['email'] === 'admin@example.com'; ?>
                        <tr>
                            <td><?= esc($user['id']) ?></td>
                            <td><?= esc($user['name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td>
                                <?php if ($isProtectedAdmin): ?>
                                    <span class="badge text-bg-danger">Admin (Protected)</span>
                                <?php else: ?>
                                    <select class="form-select form-select-sm role-select" data-user-id="<?= $user['id'] ?>" style="width: auto; display: inline-block;">
                                        <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                        <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($isProtectedAdmin): ?>
                                    <span class="badge text-bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge text-bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($user['created_at']) ?></td>
                            <td>
                                <?php if (!$isProtectedAdmin): ?>
                                    <button type="button" class="btn btn-warning btn-sm me-1"
                                            onclick="editUser(<?= $user['id'] ?>, '<?= esc($user['name']) ?>', '<?= esc($user['email']) ?>', '<?= esc($user['role']) ?>')">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-<?= $user['status'] === 'active' ? 'danger' : 'success' ?> btn-sm"
                                            onclick="toggleStatus(<?= $user['id'] ?>, '<?= $user['status'] === 'active' ? 'inactive' : 'active' ?>')">
                                        <?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="userName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="userName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email/Username</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <small><strong>Default Password:</strong> All new users will be assigned the password "Rmmc1960!". They can change it after first login.</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="userRole" class="form-label">Role</label>
                        <select class="form-select" id="userRole" name="role" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <!-- Enrollment Form - Only shown when role is Student -->
                    <div id="enrollmentForm" style="display: none;">
                        <hr>
                        <h6 class="mb-3">Enrollment Information</h6>
                        <div class="mb-3">
                            <label for="enrollmentYearLevel" class="form-label">Year Level</label>
                            <select class="form-select" id="enrollmentYearLevel" name="year_level">
                                <option value="">Select Year Level</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editUserName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label">Email/Username</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUserPassword" class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="editUserPassword" name="password">
                        <div class="form-text">Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editUserRole" class="form-label">Role</label>
                        <select class="form-select" id="editUserRole" name="role" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <!-- Edit Enrollment Form - Only shown when role is Student -->
                    <div id="editEnrollmentForm" style="display: none;">
                        <hr>
                        <h6 class="mb-3">Enrollment Information</h6>
                        <div class="mb-3">
                            <label for="editEnrollmentYearLevel" class="form-label">Year Level</label>
                            <select class="form-select" id="editEnrollmentYearLevel" name="year_level">
                                <option value="">Select Year Level</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Reset form when add user modal is shown
    $('#addUserModal').on('show.bs.modal', function() {
        $('#addUserForm')[0].reset();
        $('#enrollmentForm').hide();
    });

    // Handle role change
    $('.role-select').on('change', function() {
        const userId = $(this).data('user-id');
        const newRole = $(this).val();
        const selectElement = $(this);

        $.ajax({
            url: '<?= base_url('admin/change-role') ?>',
            type: 'POST',
            data: {
                user_id: userId,
                role: newRole
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('Role updated successfully!', 'success');
                } else {
                    // Revert the select
                    selectElement.val(selectElement.data('original-value'));
                    showAlert(response.message || 'Failed to update role.', 'error');
                }
            },
            error: function() {
                // Revert the select
                selectElement.val(selectElement.data('original-value'));
                showAlert('An error occurred while updating the role.', 'error');
            }
        });
    });

    // Store original values on load
    $('.role-select').each(function() {
        $(this).data('original-value', $(this).val());
    });

    // Edit user function
    window.editUser = function(userId, name, email, role) {
        $('#editUserId').val(userId);
        $('#editUserName').val(name);
        $('#editUserEmail').val(email);
        $('#editUserPassword').val(''); // Clear password field
        $('#editUserRole').val(role);

        // Handle enrollment form visibility and data
        if (role === 'student') {
            $('#editEnrollmentForm').show();
            // Fetch current enrollment data
            $.ajax({
                url: '<?= base_url('admin/get-user-enrollment') ?>',
                type: 'POST',
                data: { user_id: userId },
                success: function(response) {
                    if (response.success && response.enrollment) {
                        $('#editEnrollmentYearLevel').val(response.enrollment.year_level || '');
                    } else {
                        $('#editEnrollmentYearLevel').val('');
                    }
                },
                error: function() {
                    $('#editEnrollmentYearLevel').val('');
                }
            });
        } else {
            $('#editEnrollmentForm').hide();
            $('#editEnrollmentYearLevel').val('');
        }

        $('#editUserModal').modal('show');
    };

    // Toggle status function
    window.toggleStatus = function(userId, newStatus) {
        const action = newStatus === 'active' ? 'activate' : 'deactivate';
        if (!confirm(`Are you sure you want to ${action} this user?`)) {
            return;
        }

        $.ajax({
            url: '<?= base_url('admin/toggle-status') ?>',
            type: 'POST',
            data: {
                user_id: userId,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    showAlert(`User ${action}d successfully!`, 'success');
                    // Reload the page to show updated status
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(response.message || `Failed to ${action} user.`, 'error');
                }
            },
            error: function() {
                showAlert(`An error occurred while trying to ${action} the user.`, 'error');
            }
        });
    };

    // Handle edit user form submission
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/edit-user') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editUserModal').modal('hide');
                    showAlert('User updated successfully!', 'success');
                    // Reload the page to show updated user
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(response.message || 'Failed to update user.', 'error');
                }
            },
            error: function(xhr) {
                let message = 'An error occurred while updating the user.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showAlert(message, 'error');
            }
        });
    });

    // Handle role change to show/hide enrollment form (add modal)
    $('#userRole').on('change', function() {
        const selectedRole = $(this).val();
        if (selectedRole === 'student') {
            $('#enrollmentForm').show();
        } else {
            $('#enrollmentForm').hide();
            // Clear enrollment form fields when hiding
            $('#enrollmentForm input, #enrollmentForm select').val('');
        }
    });

    // Handle role change to show/hide enrollment form (edit modal)
    $('#editUserRole').on('change', function() {
        const selectedRole = $(this).val();
        if (selectedRole === 'student') {
            $('#editEnrollmentForm').show();
        } else {
            $('#editEnrollmentForm').hide();
            // Clear enrollment form fields when hiding
            $('#editEnrollmentForm input, #editEnrollmentForm select').val('');
        }
    });



    // Handle add user form submission
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/add-user') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addUserModal').modal('hide');
                    $('#addUserForm')[0].reset();
                    $('#enrollmentForm').hide(); // Hide enrollment form after successful submission
                    showAlert('User added successfully!', 'success');
                    // Reload the page to show the new user
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(response.message || 'Failed to add user.', 'error');
                }
            },
            error: function(xhr) {
                let message = 'An error occurred while adding the user.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showAlert(message, 'error');
            }
        });
    });

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        $('.container.mt-4').prepend(alertHtml);
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>
<?= $this->endSection() ?>
