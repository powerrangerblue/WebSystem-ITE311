<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row g-4">
	<div class="col-12">
		<div class="d-flex justify-content-between align-items-center mb-2">
			<h2 class="mb-0">Student Dashboard</h2>
			<a href="<?= site_url('logout') ?>" class="btn btn-outline-secondary btn-sm">Logout</a>
		</div>
		<hr class="mt-2" />
	</div>

	<div class="col-lg-4 col-md-6">
		<div class="card h-100">
			<div class="card-header bg-white">
				<strong>Enrolled Courses</strong>
			</div>
			<div class="card-body">
				<?php if (!empty($enrolledCourses)): ?>
					<ul class="list-group list-group-flush">
						<?php foreach ($enrolledCourses as $course): ?>
							<li class="list-group-item">
								<div class="fw-semibold"><?= esc($course['title'] ?? 'Untitled Course') ?></div>
								<div class="text-muted small"><?= esc($course['description'] ?? 'No description') ?></div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p class="text-muted mb-0">You are not enrolled in any courses yet.</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="col-lg-4 col-md-6">
		<div class="card h-100">
			<div class="card-header bg-white">
				<strong>Upcoming Deadlines</strong>
			</div>
			<div class="card-body">
				<?php if (!empty($upcomingDeadlines)): ?>
					<div class="table-responsive">
						<table class="table table-sm align-middle mb-0">
							<thead>
								<tr>
									<th>Assignment</th>
									<th>Course</th>
									<th class="text-end">Due</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($upcomingDeadlines as $item): ?>
									<tr>
										<td><?= esc($item['title'] ?? 'Untitled') ?></td>
										<td><?= esc($item['course_title'] ?? '—') ?></td>
										<td class="text-end">
											<?= esc(isset($item['due_date']) ? date('M d, Y', strtotime($item['due_date'])) : '—') ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<p class="text-muted mb-0">No upcoming deadlines.</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="col-lg-4 col-12">
		<div class="card h-100">
			<div class="card-header bg-white">
				<strong>Recent Grades</strong>
			</div>
			<div class="card-body">
				<?php if (!empty($recentGrades)): ?>
					<ul class="list-group list-group-flush">
						<?php foreach ($recentGrades as $grade): ?>
							<li class="list-group-item d-flex justify-content-between align-items-start">
								<div class="me-3">
									<div class="fw-semibold"><?= esc($grade['assignment_title'] ?? 'Assignment') ?></div>
									<div class="text-muted small"><?= esc($grade['course_title'] ?? 'Course') ?></div>
								</div>
								<span class="badge bg-primary rounded-pill"><?= esc($grade['score'] ?? '-') ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p class="text-muted mb-0">No grades available yet.</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>


