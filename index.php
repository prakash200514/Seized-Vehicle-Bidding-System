<?php
require_once 'config/db.php';

// Transition logic
mysqli_query($conn, "UPDATE vehicles SET status = 'active' WHERE auction_start <= NOW() AND auction_end > NOW() AND status = 'upcoming'");
mysqli_query($conn, "UPDATE vehicles SET status = 'closed' WHERE auction_end <= NOW() AND status = 'active'");

// Query active and upcoming
$vehicles = mysqli_query($conn, "SELECT * FROM vehicles WHERE status IN ('active', 'upcoming') ORDER BY auction_start ASC");
?>
<?php include 'includes/header.php'; ?>

<div class="glass-panel" style="padding:40px; text-align:center; margin-bottom: 40px;">
    <h1 style="font-size:2.5rem; color:var(--accent-gold);">Police Seized Vehicle Bidding System</h1>
    <p style="color:var(--text-secondary); font-size:1.1rem;">Transparent and secure auctions for seized department assets.</p>
</div>

<h2>Live & Upcoming Auctions</h2>
<div class="vehicle-grid">
    <?php while($v = mysqli_fetch_assoc($vehicles)): ?>
        <div class="glass-panel vehicle-card">
            <img src="<?= htmlspecialchars($v['image_path']) ?>" alt="Vehicle Image">
            <div class="vehicle-card-content">
                <h3><?= htmlspecialchars($v['vehicle_name']) ?></h3>
                <div class="vehicle-details">
                    <p><strong>Model/Year:</strong> <?= htmlspecialchars($v['model']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($v['district']) ?>, <?= htmlspecialchars($v['state']) ?></p>
                    <p><strong>Base Price:</strong> ₹<?= number_format($v['base_price'], 2) ?></p>
                    <?php if($v['status'] == 'active'): ?>
                        <p style="margin-top:15px;"><span class="badge bg-success">ACTIVE</span></p>
                    <?php else: ?>
                        <p style="margin-top:15px;"><span class="badge" style="background:#f1f3f4; color:var(--text-secondary); border: 1px solid #dadce0;">UPCOMING</span></p>
                    <?php endif; ?>
                </div>
                <a href="vehicle_details.php?id=<?= $v['vehicle_id'] ?>" class="btn btn-primary">View Details & Bid</a>
            </div>
        </div>
    <?php endwhile; ?>
    <?php if(mysqli_num_rows($vehicles) == 0): ?>
        <p style="color:var(--text-secondary);">No live or upcoming auctions right now. Check back later.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
