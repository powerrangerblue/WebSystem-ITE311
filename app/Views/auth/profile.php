<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">My Profile</h1>
        <div class="text-muted small">Manage your account information</div>
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

<!-- Profile Information Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Profile Information</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Full Name</label>
                <p class="mb-0 text-muted"><?= esc($user['name']) ?></p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Username/Email</label>
                <p class="mb-0 text-muted"><?= esc($user['email']) ?></p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Role</label>
                <p class="mb-0 text-muted">
                    <span class="badge text-bg-primary text-capitalize"><?= esc($user['role']) ?></span>
                </p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Date Created</label>
                <p class="mb-0 text-muted">
                    <?= date('F j, Y \a\t g:i A', strtotime($user['created_at'])) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Update Credentials Form -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Update Credentials</h5>
    </div>
    <div class="card-body">
        <form id="profileForm" method="post" action="<?= base_url('profile/update') ?>">
            <?= csrf_field() ?>

            <!-- Current Password (required for any changes) -->
            <div class="mb-3">
                <label for="current_password" class="form-label fw-semibold">
                    Current Password <span class="text-danger">*</span>
                </label>
                <input type="password" class="form-control" id="current_password" name="current_password"
                       placeholder="Enter your current password" required>
                <div class="form-text">Required to confirm your identity before making changes.</div>
            </div>

            <!-- Full Name -->
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Full Name</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= esc($user['name']) ?>" placeholder="Enter your full name">
            </div>

            <!-- Username/Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Username/Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= esc($user['email']) ?>" placeholder="Enter your email">
                <div class="form-text">Must be unique across all users.</div>
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">New Password</label>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Leave blank to keep current password">
                <div class="form-text">
                    Minimum 8 characters with uppercase, lowercase, number, and special character.
                </div>
            </div>

            <!-- Confirm New Password -->
            <div class="mb-3">
                <label for="password_confirm" class="form-label fw-semibold">Confirm New Password</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                       placeholder="Confirm your new password">
            </div>

            <!-- Role (disabled) -->
            <?php if ($user['id'] != 1): // Only show role field if not admin ID=1 ?>
            <div class="mb-3">
                <label for="role" class="form-label fw-semibold">Role</label>
                <input type="text" class="form-control" id="role" value="<?= esc($user['role']) ?>"
                       readonly disabled>
                <div class="form-text">Role cannot be changed from this page.</div>
            </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    Update Profile
                </button>
                <button type="reset" class="btn btn-outline-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<!-- Alert container for AJAX messages -->
<div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // CSRF helpers
    const CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
    const CSRF_COOKIE_NAME = '<?= config('Security')->cookieName ?>';

    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function currentCsrf() {
        return getCookie(CSRF_COOKIE_NAME);
    }

    // Password confirmation validation
    $('#password_confirm').on('input', function() {
        const password = $('#password').val();
        const confirm = $(this).val();

        if (password && confirm && password !== confirm) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Passwords do not match.</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Form submission
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = $('#submitBtn');
        const spinner = submitBtn.find('.spinner-border');

        // Show loading state
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        // Clear previous alerts
        $('#alert-container').empty();

        // Prepare form data
        const formData = new FormData(form[0]);
        formData.append(CSRF_TOKEN_NAME, currentCsrf());

        // Send AJAX request
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                '<?= config('Security')->headerName ?>': currentCsrf(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);

                // Update displayed information if successful
                if (response.user) {
                    $('#name').val(response.user.name);
                    $('#email').val(response.user.email);
                    // Update profile display
                    $('p:contains("' + response.user.name + '")').first().text(response.user.name);
                    $('p:contains("' + response.user.email + '")').first().text(response.user.email);
                }

                // Clear password fields
                $('#current_password, #password, #password_confirm').val('');

                // Reload page after short delay to show updated session data
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('danger', response.message);
            }
        })
        .fail(function(xhr) {
            let message = 'An error occurred while updating your profile.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        message = response.message;
                    }
                } catch (e) {}
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
