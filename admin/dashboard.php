<?php
require_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch stats
$vehicles_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM vehicles"))['count'];
$bids_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bids"))['count'];
$active_auctions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM vehicles WHERE status = 'active'"))['count'];

?>
<?php include '../includes/header.php'; ?>

<div class="glass-panel" style="padding:40px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 30px;">
        <h2>Admin Dashboard</h2>
        <div>
            <a href="add_vehicle.php" class="btn btn-primary">+ Add New Vehicle</a>
            <a href="manage_vehicles.php" class="btn">Manage Vehicles & Auctions</a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <div class="glass-panel" style="padding:20px; text-align:center;">
            <h3>Total Vehicles</h3>
            <p style="font-size:2.5rem; font-weight:bold; color:var(--text-primary);"><?= $vehicles_count ?></p>
        </div>
        <div class="glass-panel" style="padding:20px; text-align:center;">
            <h3>Active Auctions</h3>
            <p style="font-size:2.5rem; font-weight:bold; color:var(--success);"><?= $active_auctions ?></p>
        </div>
        <div class="glass-panel" style="padding:20px; text-align:center;">
            <h3>Total Bids Placed</h3>
            <p style="font-size:2.5rem; font-weight:bold; color:#64ffda;"><?= $bids_count ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
