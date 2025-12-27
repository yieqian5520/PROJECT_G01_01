<?php
// admin/index.php
require_once __DIR__ . '/../partials/auth_guard.php';

$page = $_GET['page'] ?? 'dashboard';

$routes = [
  'dashboard' => __DIR__ . '/pages/dashboard.php',

  'profile_view' => __DIR__ . '/pages/profile_view.php',
  'profile_edit' => __DIR__ . '/pages/profile_edit.php',

  'sales_report_view' => __DIR__ . '/pages/sales_report_view.php',
  'sales_report_download' => __DIR__ . '/pages/sales_report_download.php',

  'orders_list' => __DIR__ . '/pages/orders_list.php',
  'orders_update_status' => __DIR__ . '/pages/orders_update_status.php',
  'orders_delete' => __DIR__ . '/pages/orders_delete.php',

  'staff_list' => __DIR__ . '/pages/staff_list.php',
  'staff_add' => __DIR__ . '/pages/staff_add.php',
  'staff_edit' => __DIR__ . '/pages/staff_edit.php',
  'staff_delete' => __DIR__ . '/pages/staff_delete.php',

  'customers_list' => __DIR__ . '/pages/customers_list.php',
  'customers_edit' => __DIR__ . '/pages/customers_edit.php',
  'customers_delete' => __DIR__ . '/pages/customers_delete.php',

  'feedback_list' => __DIR__ . '/pages/feedback_list.php',
  'feedback_delete' => __DIR__ . '/pages/feedback_delete.php',
];

$contentFile = $routes[$page] ?? $routes['dashboard'];

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar_admin.php';
include $contentFile;
include __DIR__ . '/../partials/footer.php';
