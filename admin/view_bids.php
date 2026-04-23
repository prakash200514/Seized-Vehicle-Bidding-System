<?php
require_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

if (!isset($_GET['id'])) { header("Location: manage_vehicles.php"); exit(); }
$v_id = (int)$_GET['id'];

// Handle Approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_winner'])) {
    $winner_id = (int)$_POST['winner_id'];
    $final_price = (float)$_POST['final_price'];

    // Ensure it's not already approved
    $check = mysqli_query($conn, "SELECT * FROM auction_result WHERE vehicle_id = $v_id");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO auction_result (vehicle_id, winner_user_id, final_price, approved_by_admin) VALUES ($v_id, $winner_id, $final_price, TRUE)");
        mysqli_query($conn, "UPDATE vehicles SET status = 'approved' WHERE vehicle_id = $v_id");
        $success = "Winner successfully approved! Auction finalized.";
    }
}

$v_query = mysqli_query($conn, "SELECT * FROM vehicles WHERE vehicle_id = $v_id");
$vehicle = mysqli_fetch_assoc($v_query);

$bids = mysqli_query($conn, "
    SELECT b.*, u.name, u.email 
    FROM bids b 
    JOIN users u ON b.user_id = u.user_id 
    WHERE b.vehicle_id = $v_id 
    ORDER BY b.bid_amount DESC
");

$is_closed = (strtotime($vehicle['auction_end']) <= time()) || $vehicle['status'] == 'closed' || $vehicle['status'] == 'approved';
$is_approved = $vehicle['status'] == 'approved';
?>
<?php include '../includes/header.php'; ?>

<div class="glass-panel" style="padding: 30px;">
    <h2>Bids for: <?= htmlspecialchars($vehicle['vehicle_name']) ?></h2>
    <p>Status: <span class="badge <?= $is_closed ? 'bg-danger' : 'bg-success' ?>"><?= strtoupper($vehicle['status']) ?></span></p>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <table style="margin-top:30px;">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Bid Amount</th>
                <th>Bidder Name</th>
                <th>Bidder Email</th>
                <th>Time Placed</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $rank = 1;
            while($b = mysqli_fetch_assoc($bids)): 
            ?>
            <tr style="<?= ($rank == 1 && $is_closed && !$is_approved) ? 'background:rgba(100,255,218,0.1);' : ($is_approved && $rank == 1 ? 'background:rgba(0, 200, 150, 0.2);' : '') ?>">
                <td>#<?= $rank ?></td>
                <td style="font-weight:bold; color:var(--accent-gold);">$<?= number_format($b['bid_amount'], 2) ?></td>
                <td><?= htmlspecialchars($b['name']) ?></td>
                <td><?= htmlspecialchars($b['email']) ?></td>
                <td><?= $b['bid_time'] ?></td>
                <td>
                    <?php if($rank == 1 && $is_closed && !$is_approved): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="winner_id" value="<?= $b['user_id'] ?>">
                            <input type="hidden" name="final_price" value="<?= $b['bid_amount'] ?>">
                            <button type="submit" name="approve_winner" class="btn btn-primary" style="padding:5px 10px;">Approve Winner</button>
                        </form>
                    <?php elseif($is_approved && $rank == 1): ?>
                        <span class="badge bg-success">APPROVED WINNER</span>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php $rank++; endwhile; ?>
            <?php if(mysqli_num_rows($bids) == 0): ?>
            <tr><td colspan="6" style="text-align:center;">No bids placed yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="manage_vehicles.php" class="btn" style="margin-top:20px;">Back to Vehicles</a>
</div>

<?php include '../includes/footer.php'; ?>
