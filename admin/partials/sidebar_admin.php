<?php
// /admin/partials/sidebar_admin.php

function activeClass($current, $target) {
  return $current === $target ? 'active' : '';
}

$currentPage = $currentPage ?? ''; // set in each page
?>
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="./dashboard.php" class="brand-link">
      <img src="../assets/img/p.png" alt="Logo" class="brand-image opacity-75 shadow" />
      <span class="brand-text fw-light">Pucks Coffee</span>
    </a>
  </div>

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" data-accordion="false">

        <li class="nav-item">
          <a href="./dashboard.php" class="nav-link <?= activeClass($currentPage, 'dashboard') ?>">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-header">MANAGEMENT</li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-bag-check"></i>
            <p>
              Orders
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="./orders_list.php" class="nav-link <?= activeClass($currentPage, 'orders_list') ?>">
                <i class="nav-icon bi bi-circle"></i>
                <p>View Orders</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-people"></i>
            <p>
              Customers
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="./customers_list.php" class="nav-link <?= activeClass($currentPage, 'customers_list') ?>">
                <i class="nav-icon bi bi-circle"></i>
                <p>View Customers</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-person-badge"></i>
            <p>
              Staff
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="./staff_list.php" class="nav-link <?= activeClass($currentPage, 'staff_list') ?>">
                <i class="nav-icon bi bi-circle"></i>
                <p>View Staff</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./staff_add.php" class="nav-link <?= activeClass($currentPage, 'staff_add') ?>">
                <i class="nav-icon bi bi-circle"></i>
                <p>Add Staff</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="./feedback_list.php" class="nav-link <?= activeClass($currentPage, 'feedback') ?>">
            <i class="nav-icon bi bi-chat-left-text"></i>
            <p>Feedback</p>
          </a>
        </li>

        <li class="nav-header">REPORTS</li>
        <li class="nav-item">
          <a href="./sales_report_view.php" class="nav-link <?= activeClass($currentPage, 'sales_report') ?>">
            <i class="nav-icon bi bi-graph-up"></i>
            <p>Sales Report</p>
          </a>
        </li>

        <li class="nav-header">ACCOUNT</li>
        <li class="nav-item">
          <a href="./profile_view.php" class="nav-link <?= activeClass($currentPage, 'profile_view') ?>">
            <i class="nav-icon bi bi-person-circle"></i>
            <p>My Profile</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../pages/logout1.php" class="nav-link">
            <i class="nav-icon bi bi-box-arrow-right"></i>
            <p>Logout</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
