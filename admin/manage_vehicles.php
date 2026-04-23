<?php
require_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

// Simple logic to transition vehicle status based on time
mysqli_query($conn, "UPDATE vehicles SET status = 'active' WHERE auction_start <= NOW() AND auction_end > NOW() AND status = 'upcoming'");
mysqli_query($conn, "UPDATE vehicles SET status = 'closed' WHERE auction_end <= NOW() AND status = 'active'");

$vehicles = mysqli_query($conn, "SELECT * FROM vehicles ORDER BY auction_start DESC");
?>
<?php include '../includes/header.php'; ?>

<div class="glass-panel" style="padding: 30px;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Manage Vehicles</h2>
        <a href="add_vehicle.php" class="btn btn-primary">Add New</a>
    </div>

    <?php if(isset($_GET['added'])): ?>
        <div class="alert alert-success">Vehicle successfully added!</div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Vehicle Name</th>
                <th>Base Price</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($v = mysqli_fetch_assoc($vehicles)): ?>
            <tr>
                <td>#<?= $v['vehicle_id'] ?></td>
                <td><?= htmlspecialchars($v['vehicle_name']) ?> (<?= htmlspecialchars($v['model']) ?>)</td>
                <td>₹<?= number_format($v['base_price'], 2) ?></td>
                <td><?= $v['auction_start'] ?></td>
                <td><?= $v['auction_end'] ?></td>
                <td>
                    <span class="badge <?= $v['status']=='active'?'bg-success':'bg-danger'?>"><?= strtoupper($v['status']) ?></span>
                </td>
                <td>
                    <a href="view_bids.php?id=<?= $v['vehicle_id'] ?>" class="btn">View Bids</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if(mysqli_num_rows($vehicles) == 0): ?>
            <tr><td colspan="7" style="text-align:center;">No vehicles added yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
