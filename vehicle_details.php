<?php
require_once 'config/db.php';
if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }

$v_id = (int)$_GET['id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Submit Bid logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid_amount']) && $user_id) {
    if ($_SESSION['role'] === 'admin') {
        $error = "Admins cannot bid.";
    } else {
        $bid_amt = (float)$_POST['bid_amount'];
        // Check highest bid currently
        $hb_query = mysqli_query($conn, "SELECT MAX(bid_amount) as max_bid FROM bids WHERE vehicle_id = $v_id");
        $vehicle_base = mysqli_fetch_assoc(mysqli_query($conn, "SELECT base_price FROM vehicles WHERE vehicle_id = $v_id"))['base_price'];
        
        $current_highest = mysqli_fetch_assoc($hb_query)['max_bid'];
        $min_required = $current_highest ? $current_highest + 50 : $vehicle_base; // Minimum $50 increment
        
        if ($bid_amt < $min_required) {
            $error = "Bid must be at least ₹" . number_format($min_required, 2);
        } else {
            mysqli_query($conn, "INSERT INTO bids (user_id, vehicle_id, bid_amount) VALUES ($user_id, $v_id, $bid_amt)");
            $success = "Bid placed successfully!";
        }
    }
}

$v_query = mysqli_query($conn, "SELECT * FROM vehicles WHERE vehicle_id = $v_id");
if(mysqli_num_rows($v_query) == 0) { echo "Vehicle not found."; exit; }
$vehicle = mysqli_fetch_assoc($v_query);

$bids = mysqli_query($conn, "
    SELECT b.*, u.name 
    FROM bids b 
    JOIN users u ON b.user_id = u.user_id 
    WHERE b.vehicle_id = $v_id 
    ORDER BY b.bid_amount DESC LIMIT 5
");

$highest_bid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(bid_amount) as max_bid FROM bids WHERE vehicle_id = $v_id"))['max_bid'];
$highest_bid = $highest_bid ?: 0.00;
?>
<?php include 'includes/header.php'; ?>

<div class="bid-section">
    <div class="bid-image glass-panel" style="padding:20px;">
        <img src="<?= htmlspecialchars($vehicle['image_path']) ?>" alt="<?= htmlspecialchars($vehicle['vehicle_name']) ?>">
        
        <h3 style="margin-top:20px;">Recent Bids</h3>
        <table>
            <thead><tr><th>Bidder</th><th>Amount</th><th>Time</th></tr></thead>
            <tbody>
                <?php while($b = mysqli_fetch_assoc($bids)): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['name']) ?></td>
                        <td style="color:var(--accent-gold); font-weight:bold;">₹<?= number_format($b['bid_amount'], 2) ?></td>
                        <td style="color:var(--text-secondary);"><?= date('H:i m/d', strtotime($b['bid_time'])) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($bids) == 0): ?>
                    <tr><td colspan="3" style="text-align:center;">No bids yet. Be the first!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="bid-info glass-panel" style="padding:40px;">
        <h2 style="font-size:2rem; color:var(--accent-gold);"><?= htmlspecialchars($vehicle['vehicle_name']) ?></h2>
        <p style="color:var(--text-secondary); margin-bottom:20px; line-height: 1.8;">
            <strong>Description:</strong> <?= nl2br(htmlspecialchars($vehicle['description'])) ?><br>
            <strong>Registration No:</strong> <?= htmlspecialchars($vehicle['registration_no']) ?> | 
            <strong>Insurance:</strong> <?= ucfirst(htmlspecialchars($vehicle['insurance_status'])) ?> | 
            <strong>RC Book:</strong> <?= ucfirst(htmlspecialchars($vehicle['rc_book_status'])) ?><br>
            <strong>Location:</strong> <?= htmlspecialchars($vehicle['district']) ?>, <?= htmlspecialchars($vehicle['state']) ?>, <?= htmlspecialchars($vehicle['country']) ?>
        </p>
        
        <div style="display:flex; justify-content:space-between; margin-bottom:30px; padding:20px; background:#f8f9fa; border:1px solid var(--panel-border); border-radius:4px;">
            <div>
                <p style="color:var(--text-secondary);">Base Price</p>
                <h3 style="font-size:1.5rem;">₹<?= number_format($vehicle['base_price'], 2) ?></h3>
            </div>
            <div style="text-align:right;">
                <p style="color:var(--text-secondary);">Current Highest Bid</p>
                <h3 style="font-size:1.8rem; color:var(--accent-gold);">₹<?= number_format($highest_bid, 2) ?></h3>
            </div>
        </div>

        <h4>Time Remaining</h4>
        <div class="auction-timer" data-endtime="<?= date('Y-m-d\TH:i:s', strtotime($vehicle['auction_end'])) ?>">
            <!-- Javascript will update this -->
            <div class="time-box">...<span>Loading</span></div>
        </div>

        <hr style="border:1px solid var(--glass-border); margin:30px 0;">

        <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>

        <?php if($vehicle['status'] == 'active' && $user_id): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Your Bid Amount (₹)</label>
                    <input type="number" step="0.01" name="bid_amount" class="form-control" style="font-size:1.5rem; padding:15px;" required min="<?= $highest_bid ? $highest_bid + 50 : $vehicle['base_price'] ?>" placeholder="Minimum: ₹<?= number_format($highest_bid ? $highest_bid + 50 : $vehicle['base_price'], 2) ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; font-size:1.2rem; padding:15px;">PLACE BID</button>
            </form>
        <?php elseif($vehicle['status'] == 'upcoming'): ?>
            <button class="btn" style="width:100%; cursor:not-allowed;" disabled>Auction Starts on <?= $vehicle['auction_start'] ?></button>
        <?php elseif($vehicle['status'] == 'closed' || $vehicle['status'] == 'approved'): ?>
            <button class="btn btn-danger" style="width:100%; cursor:not-allowed;" disabled>Auction Closed</button>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary" style="width:100%;">Login to Place a Bid</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
