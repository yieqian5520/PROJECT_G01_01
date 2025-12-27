<?php
// /admin/partials/navbar.php
$userName = $_SESSION['user']['full_name'] ?? 'Admin';
?>
<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav ms-auto">
      <li class="nav-item">
        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
          <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
          <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display:none"></i>
        </a>
      </li>

      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
          <img src="../assets/img/profile-1335-svgrepo-com.svg" class="user-image rounded-circle shadow" alt="User Image" />
          <span class="d-none d-md-inline"><?= htmlspecialchars($userName) ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
          <li class="user-header text-bg-primary">
            <img src="../assets/img/profile-1335-svgrepo-com.svg" class="rounded-circle shadow" alt="User Image" />
            <p>
              <?= htmlspecialchars($userName) ?>
              
            </p>
          </li>

          <li class="user-footer">
            <a href="./profile_view.php" class="btn btn-default btn-flat">Profile</a>
            <a href="../auth/logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
