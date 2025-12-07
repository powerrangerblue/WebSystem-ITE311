<?= $this->extend('template') ?>

<?= $this->section('title') ?>Courses<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Courses</h1>
        <div class="text-muted small">Browse all available courses and enroll in the ones you're interested in.</div>
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

<div class="row mb-4">
    <div class="col-md-8">
        <div class="input-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Search courses..."
                name="search_term" value="<?= esc($searchTerm ?? '') ?>">
            <button class="btn btn-outline-primary" type="button" id="searchBtn">Search</button>
        </div>
    </div>
</div>

<div id="coursesContainer" class="row">
    <?php if (!empty($courses)): ?>
        <?php foreach ($courses as $course): ?>
            <?php
            $courseId = (int)($course['id'] ?? 0);
            $isEnrolled = in_array($courseId, $enrolledCourseIds ?? []);
            ?>
            <div class="col-md-4 mb-4 course-card" data-filter-text="<?= esc(strtolower(($course['course_name'] ?? '') . ' ' . ($course['course_code'] ?? '') . ' ' . ($course['description'] ?? ''))) ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($course['course_name'] ?? 'Untitled Course') ?></h5>
                        <p class="card-text text-muted mb-2"><?= esc($course['description'] ?? 'No description available.') ?></p>
                        <span class="badge text-bg-light border"><?= esc($course['course_code'] ?? 'N/A') ?></span>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <?php if ($isEnrolled): ?>
                            <button class="btn btn-success btn-sm w-100" disabled>
                                <i class="bi bi-check-circle"></i> Enrolled
                            </button>
                        <?php else: ?>
                            <button class="btn btn-primary btn-sm w-100 enroll-btn" data-course-id="<?= $courseId ?>">
                                <span class="btn-text">Enroll</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info mb-0">No courses found. Try updating your search.</div>
        </div>
    <?php endif; ?>
</div>

<!-- Alert container for AJAX messages -->
<div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    // Client-side filtering
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.course-card').each(function() {
            const text = $(this).data('filter-text') || '';
            $(this).toggle(text.indexOf(value) !== -1);
        });
    });

    // Search button click
    $('#searchBtn').on('click', function() {
        const searchTerm = $('#searchInput').val();
        performSearch(searchTerm);
    });

    // Enter key in search input
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            const searchTerm = $(this).val();
            performSearch(searchTerm);
        }
    });

    function performSearch(searchTerm) {
        // Show loading state
        $('#searchBtn').prop('disabled', true).text('Searching...');

        $.get('<?= site_url('courses/search') ?>', { search_term: searchTerm }, function(response) {
            if (!response || response.success !== true) {
                renderNoResults('Unable to fetch search results. Please try again.');
                return;
            }
            renderCourses(response.courses || [], response.enrolledCourseIds || []);
        }, 'json')
        .fail(function() {
            renderNoResults('Search request failed. Please check your connection and try again.');
        })
        .always(function() {
            $('#searchBtn').prop('disabled', false).text('Search');
        });
    }

    // Handle enrollment button clicks
    $(document).on('click', '.enroll-btn', function(e) {
        e.preventDefault();

        const button = $(this);
        const courseId = button.data('course-id');
        const btnText = button.find('.btn-text');
        const spinner = button.find('.spinner-border');

        // Show loading state
        button.prop('disabled', true);
        btnText.addClass('d-none');
        spinner.removeClass('d-none');

        // Prepare form data
        const payload = { course_id: courseId };
        payload[CSRF_TOKEN_NAME] = currentCsrf();

        // Send enrollment request
        $.ajax({
            url: '<?= base_url('course/enroll') ?>',
            method: 'POST',
            data: payload,
            dataType: 'json',
            headers: {
                '<?= config('Security')->headerName ?>': currentCsrf(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);

                // Update button to show enrolled state
                button.removeClass('btn-primary').addClass('btn-success').prop('disabled', true);
                btnText.html('<i class="bi bi-check-circle"></i> Enrolled').removeClass('d-none');
                spinner.addClass('d-none');

                // Remove spinner completely
                spinner.remove();

                // Redirect to dashboard after short delay
                setTimeout(function() {
                    window.location.href = '<?= base_url('dashboard') ?>';
                }, 1500);
            } else {
                showAlert('danger', response.message);

                // Reset button state
                button.prop('disabled', false);
                btnText.removeClass('d-none');
                spinner.addClass('d-none');
            }
        })
        .fail(function(xhr) {
            let message = 'An error occurred while enrolling. Please try again.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }

            showAlert('danger', message);

            // Reset button state
            button.prop('disabled', false);
            btnText.removeClass('d-none');
            spinner.addClass('d-none');
        });
    });

    function renderCourses(courses, enrolledCourseIds) {
        const container = $('#coursesContainer');
        if (!courses.length) {
            renderNoResults('No courses found matching your search.');
            return;
        }

        const enrolledIds = enrolledCourseIds || [];

        const fragments = courses.map(function(course) {
            const courseId = course.id || 0;
            const isEnrolled = enrolledIds.includes(courseId);
            const filterText = ((course.course_name || '') + ' ' + (course.course_code || '') + ' ' + (course.description || '')).toLowerCase();

            let buttonHtml;
            if (isEnrolled) {
                buttonHtml = `<button class="btn btn-success btn-sm w-100" disabled>
                    <i class="bi bi-check-circle"></i> Enrolled
                </button>`;
            } else {
                buttonHtml = `<button class="btn btn-primary btn-sm w-100 enroll-btn" data-course-id="${courseId}">
                    <span class="btn-text">Enroll</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>`;
            }

            return `
                <div class="col-md-4 mb-4 course-card" data-filter-text="${filterText}">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">${course.course_name || 'Untitled Course'}</h5>
                            <p class="card-text text-muted mb-2">${course.description || 'No description available.'}</p>
                            <span class="badge text-bg-light border">${course.course_code || 'N/A'}</span>
                        </div>
                        <div class="card-footer bg-white border-0">
                            ${buttonHtml}
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(fragments.join(''));
    }

    function renderNoResults(message) {
        $('#coursesContainer').html(`
            <div class="col-12">
                <div class="alert alert-info mb-0">${message}</div>
            </div>
        `);
    }

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
