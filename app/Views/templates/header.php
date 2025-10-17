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
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Contact</a>
        </li>
        <?php if (!session('isLoggedIn')): ?>
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'login' ? 'active' : '' ?>" href="<?= site_url('login') ?>">Login</a>
        </li>

        <?php else: ?>
        <?php
        $currentUri = uri_string();
        $userRole = strtolower(session('role'));
        $isBasicPage = in_array($currentUri, ['', 'about', 'contact']);
        ?>

        <!-- Show announcements link only for students -->
        <?php if ($userRole === 'student'): ?>
        <li class="nav-item">
          <a class="nav-link <?= $currentUri == 'announcements' ? 'active' : '' ?>" href="<?= site_url('announcements') ?>">Announcements</a>
        </li>
        <?php endif; ?>

        <!-- Dashboard link - always visible for logged-in users -->
        <li class="nav-item">
          <?php
          $dashboardUrl = '/dashboard'; // default for students
          $isDashboardActive = false;

          if ($userRole === 'admin') {
            $dashboardUrl = '/admin/dashboard';
            $isDashboardActive = in_array($currentUri, ['admin/dashboard', 'admin']);
          } elseif ($userRole === 'teacher') {
            $dashboardUrl = '/teacher/dashboard';
            $isDashboardActive = in_array($currentUri, ['teacher/dashboard', 'teacher']);
          } else {
            // For students, dashboard is /dashboard
            $isDashboardActive = $currentUri == 'dashboard';
          }
          ?>
          <a class="nav-link <?= $isDashboardActive ? 'active' : '' ?>" href="<?= site_url($dashboardUrl) ?>">Dashboard</a>
        </li>


        <li class="nav-item">
          <a class="nav-link" href="<?= site_url('logout') ?>">Logout</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
