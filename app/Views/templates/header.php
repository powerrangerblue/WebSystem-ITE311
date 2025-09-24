<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= site_url('/') ?>">WebSystem</a>
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
        <li class="nav-item">
          <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">Dashboard</a>
        </li>
        
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= site_url('logout') ?>">Logout</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>


