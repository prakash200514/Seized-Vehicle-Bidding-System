<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Seized Vehicle Bidding</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/bidding/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container header-inner">
            <a href="/bidding/index.php" class="logo">
                🚔 Police Auctions
            </a>
            <nav>
                <a href="/bidding/index.php">Auctions</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <a href="/bidding/admin/dashboard.php">Admin Panel</a>
                    <?php else: ?>
                        <a href="/bidding/my_bids.php">My Bids</a>
                    <?php endif; ?>
                    <a href="/bidding/logout.php" class="btn btn-danger" style="margin-left:20px;">Logout (<?= htmlspecialchars($_SESSION['name']) ?>)</a>
                <?php else: ?>
                    <a href="/bidding/login.php">Login</a>
                    <a href="/bidding/register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <div class="container main-content">
