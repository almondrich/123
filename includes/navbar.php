<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Get user info from session via auth function
require_once 'auth.php';
$current_user = get_auth_user();
$user_full = $current_user['full_name'] ?? 'Guest';
$user_role = strtolower($current_user['role'] ?? 'guest');
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
        <i class="bi bi-file-medical" style="font-size: 32px; margin-right: 8px; color: #0d6efd;"></i>
        Pre-Hospital Care
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <!-- Left Nav -->
      <ul class="navbar-nav ms-auto">
        <!-- Dashboard -->
        <li class="nav-item">
          <a class="nav-link <?php if($current_page=='dashboard.php' || $current_page=='index.php') echo 'active'; ?>" href="dashboard.php">
            <span class="material-icons">home</span> Dashboard
          </a>
        </li>

        <!-- New Form -->
        <li class="nav-item">
          <a class="nav-link <?php if($current_page=='TONYANG.php') echo 'active'; ?>" href="TONYANG.php">
            <span class="material-icons">add</span> New Form
          </a>
        </li>

        <!-- Records -->
        <li class="nav-item">
          <a class="nav-link <?php if($current_page=='records.php') echo 'active'; ?>" href="records.php">
            <span class="material-icons">table_view</span> Records
          </a>
        </li>

        <!-- Admin Dashboard (if admin) -->
        <?php if ($user_role === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link <?php if($current_page=='dashboard.php' && strpos($_SERVER['REQUEST_URI'], 'admin') !== false) echo 'active'; ?>" href="admin/dashboard.php">
              <span class="material-icons">admin_panel_settings</span> Admin
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Right User Dropdown -->
      <ul class="navbar-nav ms-3">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
             role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                 style="width:35px; height:35px;">
              <?= strtoupper(substr($user_full,0,1)) ?>
            </div>
            <span class="ms-2"><?= htmlspecialchars($user_full) ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="change_password.php">Change Password</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
.navbar .nav-link {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: 500;
  color: #555;
}
.navbar .nav-link:hover {
  color: #0d6efd;
}
.navbar .nav-link.active {
  color: #0d6efd;
  font-weight: 600;
}
.dropdown-menu .material-icons {
  font-size: 18px;
  vertical-align: middle;
  margin-right: 4px;
}
</style>

<!-- Material Icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
