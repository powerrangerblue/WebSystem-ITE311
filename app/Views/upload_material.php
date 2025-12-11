<?php
/** @var int $course_id */
$session = session();
?>
<?= $this->extend('template') ?>

<?= $this->section('content') ?>
	<div class="d-flex justify-content-between align-items-center mb-3">
		<div>
			<h2 class="mb-1">Upload Course Material</h2>
			<div class="text-muted small">Attach learning resources for your students</div>
		</div>
		<a href="<?= site_url('/dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
	</div>

	<?php if ($session->getFlashdata('success')): ?>
		<div class="alert alert-success"><?php echo esc($session->getFlashdata('success')); ?></div>
	<?php endif; ?>
	<?php if ($session->getFlashdata('error')): ?>
		<div class="alert alert-danger"><?php echo esc($session->getFlashdata('error')); ?></div>
	<?php endif; ?>

	<div class="card border-0 shadow-sm">
		<div class="card-body">
			<form action="<?php echo site_url('/admin/course/' . $course_id . '/upload'); ?>" method="post" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<div class="mb-3">
					<label for="material_file" class="form-label">Choose file</label>
					<input type="file" class="form-control" id="material_file" name="material_file" accept=".pdf,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt" required>
					<div class="form-text">Allowed: pdf, ppt, pptx, xls, xlsx, zip, rar, txt • Max 10MB</div>
				</div>
				<div class="d-flex gap-2">
					<button type="submit" class="btn btn-primary">Upload</button>
					<a href="<?php echo site_url('/dashboard'); ?>" class="btn btn-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>
<?= $this->endSection() ?>
