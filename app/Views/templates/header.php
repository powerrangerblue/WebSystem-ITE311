<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= site_url('/') ?>">Learning Management System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (!session('isLoggedIn')): ?>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'login' ? 'active' : '' ?>" href="<?= site_url('login') ?>">Login</a>
        </li>

        <?php else: ?>
        <?php
        $currentUri = uri_string();
        $userRole = strtolower(session('role'));
        $isBasicPage = in_array($currentUri, ['', 'about', 'contact']);
        ?>

        <!-- Show Courses link only for logged-in users (student, admin, teacher) -->
        <?php if (in_array($userRole, ['student', 'admin', 'teacher'])): ?>
        <li class="nav-item">
          <a class="nav-link <?= $currentUri == 'courses' ? 'active' : '' ?>" href="<?= site_url('courses') ?>">Courses</a>
        </li>
        <?php endif; ?>

        <!-- Show Assignments link for students and teachers -->
        <?php if (in_array($userRole, ['student', 'teacher'])): ?>
        <li class="nav-item">
          <?php if ($userRole === 'student'): ?>
            <a class="nav-link <?= strpos($currentUri, 'assignments/student') === 0 ? 'active' : '' ?>" href="<?= site_url('assignments/student') ?>">Assignments</a>
          <?php else: ?>
            <a class="nav-link <?= strpos($currentUri, 'assignments/') === 0 ? 'active' : '' ?>" href="<?= site_url('assignments/teacher') ?>">Assignments</a>
          <?php endif; ?>
        </li>
        <?php endif; ?>

        <!-- Show announcements link only for students -->
        <?php if ($userRole === 'student'): ?>
        <li class="nav-item">
          <a class="nav-link <?= $currentUri == 'announcements' ? 'active' : '' ?>" href="<?= site_url('announcements') ?>">Announcements</a>
        </li>
        <?php endif; ?>

        <!-- Dashboard link - unified route for all roles -->
        <li class="nav-item">
          <?php
          $dashboardUrl = 'dashboard';
          $isDashboardActive = ($currentUri == 'dashboard');
          ?>
          <a class="nav-link <?= $isDashboardActive ? 'active' : '' ?>" href="<?= site_url($dashboardUrl) ?>">Dashboard</a>
        </li>

        <!-- Profile link for all logged-in users -->
        <li class="nav-item">
          <a class="nav-link <?= $currentUri == 'profile' ? 'active' : '' ?>" href="<?= site_url('profile') ?>">Profile</a>
        </li>

        <!-- Manage Users link for admins -->
        <?php if ($userRole === 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'admin/manage-users' ? 'active' : '' ?>" href="<?= site_url('/admin/manage-users') ?>">Manage Users</a>
        </li>
        <?php endif; ?>

        <!-- Notifications dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Notifications <span class="badge bg-danger" id="notification-badge" style="display: none;">0</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown">
            <li id="notifications-list">
              <div class="notification-item text-center text-muted">
                Loading notifications...
              </div>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= site_url('logout') ?>">Logout</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
