<?php
require_once __DIR__ . '/../partials/auth_guard.php';

$currentPage = 'search';
$pageTitle = 'Search';

$q = trim($_GET['q'] ?? '');
$qLower = mb_strtolower($q);

$items = require __DIR__ . '/../partials/search_index.php';

$results = [];
if ($q !== '') {
  foreach ($items as $item) {
    $haystack = mb_strtolower($item['title'] . ' ' . implode(' ', $item['keywords']));
    if (str_contains($haystack, $qLower)) {
      $results[] = $item;
    }
  }
}
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
            <div class="col-sm-6"><h3 class="mb-0">Search</h3></div>
          </div>
        </div>
      </div>

      <div class="app-content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-body">
              <form class="row g-2" method="get" action="./search.php">
                <div class="col-md-10">
                  <input type="text" class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search pages (orders, customers, reports...)">
                </div>
                <div class="col-md-2 d-grid">
                  <button class="btn btn-primary" type="submit">Search</button>
                </div>
              </form>

              <hr>

              <?php if ($q === ''): ?>
                <div class="text-muted">Type something to search.</div>
              <?php else: ?>
                <div class="text-muted mb-2">Results for: "<?= htmlspecialchars($q) ?>"</div>

                <?php if (count($results) === 0): ?>
                  <div class="alert alert-warning mb-0">No matching pages found.</div>
                <?php else: ?>
                  <div class="list-group">
                    <?php foreach ($results as $r): ?>
                      <a class="list-group-item list-group-item-action" href="<?= htmlspecialchars($r['url']) ?>">
                        <?= htmlspecialchars($r['title']) ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              <?php endif; ?>

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
