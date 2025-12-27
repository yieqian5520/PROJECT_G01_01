<?php
// /admin/partials/head.php
?>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?= $pageTitle ?? "Admin Dashboard" ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="color-scheme" content="light dark" />
  <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
  <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />

  <meta name="author" content="ColorlibHQ" />
  <link rel="preload" href="../css/adminlte.css" as="style" />

  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
    crossorigin="anonymous"
    media="print"
    onload="this.media='all'"
  />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
    crossorigin="anonymous"
  />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    crossorigin="anonymous"
  />

  <link rel="stylesheet" href="../css/adminlte.css" />

  <!-- Optional: ApexCharts (for dashboard charts) -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
    crossorigin="anonymous"
  />
</head>
