<?= $this->extend('template') ?>

<?= $this->section('title') ?>Manage Courses - Admin<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h2>Manage Courses</h2>
    <div class="row">
        <?php foreach ($courses as $course): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($course['course_name']) ?></h5>
                        <p class="card-text">Code: <?= esc($course['course_code']) ?></p>
                        <a href="<?= site_url('admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-primary">Upload Materials</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
