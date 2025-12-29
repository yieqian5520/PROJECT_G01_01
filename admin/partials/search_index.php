<?php
// admin/partials/search_index.php
// Central index for admin search (menu/pages). Extend anytime.
return [
  [
    'title' => 'Dashboard',
    'url' => './dashboard.php',
    'keywords' => ['dashboard', 'home', 'summary', 'overview'],
  ],
  [
    'title' => 'Orders',
    'url' => './order_list.php',
    'keywords' => ['orders', 'order', 'purchase', 'status'],
  ],
  [
    'title' => 'Customers',
    'url' => './customer_list.php',
    'keywords' => ['customers', 'customer', 'users', 'buyers'],
  ],
  [
    'title' => 'Staff',
    'url' => './staff_list.php',
    'keywords' => ['staff', 'employee', 'admin'],
  ],
  [
    'title' => 'Feedback',
    'url' => './feedback_list.php',
    'keywords' => ['feedback', 'reviews', 'complaints'],
  ],
  [
    'title' => 'Sales Report',
    'url' => './sales_report_view.php',
    'keywords' => ['sales', 'report', 'revenue'],
  ],
  [
    'title' => 'My Profile',
    'url' => './profile_view.php',
    'keywords' => ['profile', 'account', 'settings'],
  ],
];
