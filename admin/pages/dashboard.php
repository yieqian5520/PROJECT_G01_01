<?php

session_start();
if(!isset($_SESSION['email'])) {
    header('Location: index1.php');
    exit();
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
                    <img src="../assets/img/p.png" alt="">
                    <h2>PUCKS COFFEE <span class="danger">Admin</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-symbols-sharp">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="#">
                    <span class="material-symbols-sharp">grid_view</span>
                    <h3>Dashboard</h3> 
                </a>

                <a href="#" class="active">
                    <span class="material-symbols-sharp">person</span>
                    <h3>Customers</h3> 
                </a>

                <a href="#">
                    <span class="material-symbols-sharp">receipt_long</span>
                    <h3>Orders</h3> 
                </a>

                <a href="#">
                    <span class="material-symbols-sharp">person_3</span>
                    <h3>Staff</h3> 
                </a>

                <a href="#">
                    <span class="material-symbols-sharp">report_gmailerrorred</span>
                    <h3>Reports</h3> 
                </a>

                <a href="#">
                    <span class="material-symbols-sharp">account_circle</span>
                    <h3>Profile</h3> 
                </a>

                <a href="#">
                    <span class="material-symbols-sharp">logout</span>
                    <h3>Logout</h3> 
                </a>
            </div>
        </aside>
</body>
</html>
