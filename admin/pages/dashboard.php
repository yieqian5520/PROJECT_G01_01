<?php
require_once __DIR__ . '/../partials/auth_guard.php';
?>
<!doctype html>
<html lang="en">

<?php include __DIR__ . '/../partials/header.php'; ?>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
  <div class="app-wrapper">

    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <?php include __DIR__ . '/../partials/sidebar_admin.php'; ?>

    <main class="app-main">
      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div class="app-content">
        <div class="container-fluid">

          <div class="row">
            <div class="col-lg-3 col-6">
              <div class="small-box text-bg-primary">
                <div class="inner">
                  <h3><?= 0 ?></h3>
                  <p>Total Orders</p>
                </div>
                <i class="small-box-icon bi bi-bag-check"></i>
                <a href="order_list.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                  Manage Orders <i class="bi bi-link-45deg"></i>
                </a>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="small-box text-bg-success">
                <div class="inner">
                  <h3><?= 0 ?></h3>
                  <p>Total Customers</p>
                </div>
                <i class="small-box-icon bi bi-people"></i>
                <a href="customer_list.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                  View Customers <i class="bi bi-link-45deg"></i>
                </a>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="small-box text-bg-warning">
                <div class="inner">
                  <h3><?= 0 ?></h3>
                  <p>Total Staff</p>
                </div>
                <i class="small-box-icon bi bi-person-badge"></i>
                <a href="staff_list.php" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                  Manage Staff <i class="bi bi-link-45deg"></i>
                </a>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="small-box text-bg-danger">
                <div class="inner">
                  <h3><?= 0 ?></h3>
                  <p>New Feedback</p>
                </div>
                <i class="small-box-icon bi bi-chat-left-text"></i>
                <a href="feedback_list.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                  View Feedback <i class="bi bi-link-45deg"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-7">
              <div class="card mb-4">
                <div class="card-header"><h3 class="card-title">Sales Trend</h3></div>
                <div class="card-body"><div id="sales-chart"></div></div>
              </div>
            </div>

            <div class="col-lg-5">
              <div class="card mb-4">
                <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
                <div class="card-body d-grid gap-2">
                  <a class="btn btn-primary" href="order_list.php"><i class="bi bi-bag-check me-1"></i> View Orders</a>
                  <a class="btn btn-outline-primary" href="sales_report_view.php"><i class="bi bi-graph-up me-1"></i> Sales Report</a>
                  <a class="btn btn-outline-secondary" href="staff_add.php"><i class="bi bi-person-plus me-1"></i> Add Staff</a>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
  </div>

  <?php include __DIR__ . '/../partials/scripts.php'; ?>

</body>
</html>
