<?= $this->extend('template') ?>

<?= $this->section('title') ?>Courses<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="mb-2">Courses</h1>
        <p class="text-muted mb-0">Browse the available courses or search instantly.</p>
    </div>
    <div class="col-md-4">
        <form id="searchForm" class="d-flex" role="search">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search courses..."
                    name="search_term" value="<?= esc($searchTerm ?? '') ?>">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </div>
        </form>
    </div>
</div>

<div id="coursesContainer" class="row">
    <?php if (!empty($courses)): ?>
        <?php foreach ($courses as $course): ?>
            <div class="col-md-4 mb-4 course-card" data-filter-text="<?= esc(strtolower(($course['course_name'] ?? '') . ' ' . ($course['course_code'] ?? '') . ' ' . ($course['description'] ?? ''))) ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($course['course_name'] ?? 'Untitled Course') ?></h5>
                        <p class="card-text text-muted mb-2"><?= esc($course['description'] ?? 'No description available.') ?></p>
                        <span class="badge text-bg-light border"><?= esc($course['course_code'] ?? 'N/A') ?></span>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="<?= site_url('courses/view/' . (int)($course['id'] ?? 0)) ?>" class="btn btn-primary btn-sm w-100">View Course</a>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Client-side filtering
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.course-card').each(function() {
            const text = $(this).data('filter-text') || '';
            $(this).toggle(text.indexOf(value) !== -1);
        });
    });

    // Server-side search with AJAX
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const searchTerm = $('#searchInput').val();

        $.get('<?= site_url('courses/search') ?>', { search_term: searchTerm }, function(response) {
            if (!response || response.success !== true) {
                renderNoResults('Unable to fetch search results. Please try again.');
                return;
            }
            renderCourses(response.courses || []);
        }, 'json')
        .fail(function() {
            renderNoResults('Search request failed. Please check your connection and try again.');
        });
    });

    function renderCourses(courses) {
        const container = $('#coursesContainer');
        if (!courses.length) {
            renderNoResults('No courses found matching your search.');
            return;
        }

        const fragments = courses.map(function(course) {
            const filterText = ((course.course_name || '') + ' ' + (course.course_code || '') + ' ' + (course.description || '')).toLowerCase();
            return `
                <div class="col-md-4 mb-4 course-card" data-filter-text="${filterText}">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">${course.course_name || 'Untitled Course'}</h5>
                            <p class="card-text text-muted mb-2">${course.description || 'No description available.'}</p>
                            <span class="badge text-bg-light border">${course.course_code || 'N/A'}</span>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="<?= site_url('courses/view/') ?>${course.id || 0}" class="btn btn-primary btn-sm w-100">View Course</a>
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
});
</script>
<?= $this->endSection() ?>

