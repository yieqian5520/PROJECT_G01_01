<?php

session_start();
if(!isset($_SESSION['email'])) {
    header('Location: index1.php');
    exit();
}

$db = require __DIR__ . "/../config/config.php";

$userId = $_SESSION['id'] ?? null;

if ($userId) {
    $stmt = $db->prepare(
        "SELECT id, name, email, phone, role, profile_image
         FROM user 
         WHERE id = ?"
    );
    $stmt->bind_param("i", $userId);
} else {
    $email = $_SESSION['email'];
    $stmt = $db->prepare(
        "SELECT id, name, email, phone, role, profile_image
         FROM user 
         WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
}

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$_SESSION['id'] = $user['id']; 


if (!$user) {
    session_destroy();
    header("Location: index1.php");
    exit();
}

$allowedTabs = ['dashboard','customers','orders','staff','feedback','reports','profile'];

$activeTab = $_GET['tab'] ?? 'dashboard';
if (!in_array($activeTab, $allowedTabs, true)) {
    $activeTab = 'dashboard';
}

// If user is searching, force customers tab
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $activeTab = 'customers';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Sharp"
      rel="stylesheet">
    <link rel="stylesheet" href="../css/style_db.css">
</head>
<body>
    <div class="container">
        
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../assets/img/puckslogo.jpg" alt="">
                    <h2>PUCKS COFFEE <span class="danger">Admin</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-symbols-sharp">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="dashboard.php?tab=dashboard">
                    <span class="material-symbols-sharp">grid_view</span>
                    <h3>Dashboard</h3>
                </a>

                <a href="dashboard.php?tab=customers">
                    <span class="material-symbols-sharp">person</span>
                    <h3>Customers</h3>
                </a>

                <a href="dashboard.php?tab=orders">
                    <span class="material-symbols-sharp">receipt_long</span>
                    <h3>Orders</h3>
                </a>

                <a href="dashboard.php?tab=staff">
                    <span class="material-symbols-sharp">person_3</span>
                    <h3>Staff</h3>
                </a>

                <a href="dashboard.php?tab=reports">
                    <span class="material-symbols-sharp">report_gmailerrorred</span>
                    <h3>Reports</h3>
                </a>

                <a href="dashboard.php?tab=profile">
                    <span class="material-symbols-sharp">account_circle</span>
                    <h3>Profile</h3>
                </a>

                <a href="logout1.php" id="logout-link">
                    <span class="material-symbols-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>

        </aside>
        <!-- END OF ASIDE -->

        <main>
            <div id="dashboard" class="tab-content <?= $activeTab === 'dashboard' ? 'active' : '' ?>">
                <h1>Dashboard</h1>
                <div class="date">
                    <input type="date">
                </div>

                <div class="insights">
                    <div class="sales">
                        <span class="material-symbols-sharp">analytics</span>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Sales</h3>
                                <h1>RM25,024</h1>
                            </div>
                            <div class="progress">
                                <svg>
                                    <circle cx='38' cy='38' r='36'></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Last 24 Hours</small>
                    </div>
                    <!-- END OF SALES -->
                    <div class="expenses">
                        <span class="material-symbols-sharp">bar_chart</span>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Expenses</h3>
                                <h1>RM14,160</h1>
                            </div>
                            <div class="progress">
                                <svg>
                                    <circle cx='38' cy='38' r='36'></circle>
                                </svg>
                                <div class="number">
                                    <p>62%</p>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Last 24 Hours</small>
                    </div>
                    <!-- END OF EXPENSES -->
                    <div class="income">
                        <span class="material-symbols-sharp">trending_up</span>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Income</h3>
                                <h1>RM10,864</h1>
                            </div>
                            <div class="progress">
                                <svg>
                                    <circle cx='38' cy='38' r='36'></circle>
                                </svg>
                                <div class="number">
                                    <p>44%</p>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Last 24 Hours</small>
                    </div>
                    <!-- END OF INCOME -->
                </div>
                <!-- END OF INSIGHTS -->

                <div class="recent-orders">
                    <h2>Recent Orders</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John Doe</td>
                                <td>Cappuccino</td>
                                <td>RM12.00</td>
                                <td>2026-01-15</td>
                                <td><span class="status delivered">Served</span></td>
                                <td class="primary">Details</td>
                            </tr>
                            <tr>
                                <td>Jane Smith</td>
                                <td>Latte</td>
                                <td>RM10.00</td>
                                <td>2026-01-15</td>
                                <td><span class="status pending">Preparing</span></td>
                                <td class="primary">Details</td>
                            </tr>
                            <tr>
                                <td>Mike Johnson</td>
                                <td>Espresso</td>
                                <td>RM8.00</td>
                                <td>2026-01-15</td>
                                <td><span class="status cancelled">Cancelled</span></td>
                                <td class="primary">Details</td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="#">Show All</a>
                </div>
            </div>
            <div id="customers" class="tab-content <?= $activeTab === 'customers' ? 'active' : '' ?>">
                <h1>Customers</h1>

                <?php
                // Simple search
                $search = trim($_GET['search'] ?? '');
                $like = "%{$search}%";

                if ($search !== '') {
                    $stmt = $db->prepare("
                        SELECT id, name, phone, email, address, verify_status, created_at
                        FROM users
                        WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?
                        ORDER BY created_at DESC
                    ");
                    $stmt->bind_param("sss", $like, $like, $like);
                } else {
                    $stmt = $db->prepare("
                        SELECT id, name, phone, email, address, verify_status, created_at
                        FROM users
                        ORDER BY created_at DESC
                    ");
                }

                $stmt->execute();
                $customers = $stmt->get_result();

                // CSRF token for delete
                if (empty($_SESSION['csrf'])) {
                    $_SESSION['csrf'] = bin2hex(random_bytes(16));
                }
                ?>

                <form method="GET" style="margin: 12px 0; display:flex; gap:10px; align-items:center;">
                    <input type="hidden" name="tab" value="customers">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search name / email / phone..."
                        value="<?= htmlspecialchars($search) ?>"
                        style="padding:10px; border-radius:10px; border:1px solid #333; width: 320px;"
                    />
                    <button type="submit" class="btn-primary" style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
                        Search
                    </button>

                    <?php if ($search !== ''): ?>
                        <a href="dashboard.php?tab=customers" style="margin-left:6px;">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="recent-orders">
                    <h2>Customer List</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Verified</th>
                                <th>Joined</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($customers->num_rows === 0): ?>
                            <tr>
                                <td colspan="8" style="text-align:center; padding:16px;">No customers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = $customers->fetch_assoc()): ?>
                                <tr>
                                    <td><?= (int)$row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['address']) ?></td>
                                    <td>
                                        <span class="status <?= ((int)$row['verify_status'] === 1) ? 'delivered' : 'pending' ?>">
                                            <?= ((int)$row['verify_status'] === 1) ? 'Yes' : 'No' ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>

                                    <td style="text-align:right;">
                                        <a class="primary" href="edit-customer.php?id=<?= (int)$row['id'] ?>">Edit</a>

                                        <form method="POST" action="delete-customer.php" style="display:inline;" onsubmit="return confirm('Delete this customer?');">
                                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                            <button type="submit" class="primary" style="background:none;border:none;color:#ff5c5c;cursor:pointer;">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="orders"   class="tab-content <?= $activeTab === 'orders' ? 'active' : '' ?>">
                <h1>Orders</h1>
                <p>Order management content goes here.</p>
            </div>
            <div id="staff"    class="tab-content <?= $activeTab === 'staff' ? 'active' : '' ?>">
                <h1>Staff</h1>
                <p>Staff management content goes here.</p>
            </div>
            <div id="feedback" class="tab-content <?= $activeTab === 'feedback' ? 'active' : '' ?>">
                <h1>Feedback</h1>
                <p>Feedback content goes here.</p>
            </div>
            <div id="reports"  class="tab-content <?= $activeTab === 'reports' ? 'active' : '' ?>">
                <h1>Reports</h1>
                <p>Reports content goes here.</p>
            </div>           
            <div id="profile"  class="tab-content <?= $activeTab === 'profile' ? 'active' : '' ?>">
                <h1>Profile</h1>
                <div class="profile-container">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <img src="<?= htmlspecialchars($user['profile_image'] ?: '../assets/img/profile-1.jpg') ?>?t=<?= time() ?>" alt="Profile Picture" id="profile-img">
                        </div>
                        <div class="profile-info">
                            <h2><?= htmlspecialchars($user['name']) ?></h2>
                            <p>Admin</p>
                            <button type="button" class="change-photo-btn" onclick="document.getElementById('profile_photo').click()">
                                <span class="material-symbols-sharp">camera_alt</span> Change Profile Picture
                            </button>
                        </div>
                    </div>
                    <div class="profile-card">
                        <h3><span class="material-symbols-sharp">edit</span> Edit Your Details</h3>
                        <form method="POST" action="update-profile.php" enctype="multipart/form-data" class="profile-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name"><span class="material-symbols-sharp">person</span> Full Name</label>
                                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email"><span class="material-symbols-sharp">email</span> Email</label>
                                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone"><span class="material-symbols-sharp">phone</span> Phone Number</label>
                                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="role"><span class="material-symbols-sharp">badge</span> Role</label>
                                    <input type="text" id="role" name="role" value="<?= htmlspecialchars($user['role']) ?>" readonly>
                                </div>
                            </div>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display: none;">
                            <button type="submit" class="btn-primary"><span class="material-symbols-sharp">save</span> Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <!-----------------------END OF MAIN --------------------->

        <div class="right">
            <div class="top">
                <div class="theme-toggler">
                    <span class="material-symbols-sharp active">light_mode</span>
                    <span class="material-symbols-sharp">dark_mode</span>
                </div>
                <div class="profile">
                    <div class="info">
                        <p>Hey, <?= htmlspecialchars($user['name']) ?></p>
                        <small class="text-muted"><?= htmlspecialchars($user['role']) ?></small>
                    </div>
                    <div class="profile-photo">
                        <img
                        src="<?= htmlspecialchars($user['profile_image'] ?: '../assets/img/profile-1.jpg') ?>?t=<?= time() ?>"
                        alt=""
                        >
                    </div>
                </div>
            </div>
            <!--------END OF TOP------------>
                <div class="feedback">
                    <h2>Feedback</h2>
                    <div class="fback">
                        <div class="fb">
                            <div class="profile-photo">
                                <img src="../assets/img/profile-2.jpg">
                            </div>
                            <div class="message">
                                <p><b>John Doe</b> rated 5 star</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>

                        <div class="fb">
                            <div class="profile-photo">
                                <img src="../assets/img/profile-3.jpg">
                            </div>
                            <div class="message">
                                <p><b>John Doe</b> rated 5 star</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>

                        <div class="fb">
                            <div class="profile-photo">
                                <img src="../assets/img/profile-4.jpg">
                            </div>
                            <div class="message">
                                <p><b>John Doe</b> rated 5 star</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>

                        <div class="fb">
                            <div class="profile-photo">
                                <img src="../assets/img/profile-3.jpg">
                            </div>
                            <div class="message">
                                <p><b>John Doe</b> rated 5 star</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>

                        <div class="fb">
                            <div class="profile-photo">
                                <img src="../assets/img/profile-3.jpg">
                            </div>
                            <div class="message">
                                <p><b>John Doe</b> rated 5 star</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>

                        <div class="fb">
                            <div class="profile-photo">
                                <img src="../assets/img/profile-3.jpg">
                            </div>
                            <div class="message">
                                <p><b>John Doe</b> rated 5 star</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

    <script src="../js/index.js"></script>
</body>
</html>
