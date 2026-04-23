<?php
require_once 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') { header("Location: login.php"); exit(); }

$user_id = (int)$_SESSION['user_id'];

// Get unique vehicles the user has bid on
$bids_query = mysqli_query($conn, "
    SELECT v.*, MAX(b.bid_amount) as my_highest_bid, MAX(b.bid_time) as last_bid_time
    FROM bids b
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE b.user_id = $user_id
    GROUP BY v.vehicle_id
    ORDER BY last_bid_time DESC
");

// Check the overall highest bid for these vehicles to determine if user is winning
$vehicles_data = [];
while ($row = mysqli_fetch_assoc($bids_query)) {
    $v_id = $row['vehicle_id'];
    $global_highest = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(bid_amount) as max_bid FROM bids WHERE vehicle_id = $v_id"))['max_bid'];
    
    // Check if approved winner
    $is_winner = false;
    $res = mysqli_query($conn, "SELECT winner_user_id FROM auction_result WHERE vehicle_id = $v_id");
    if(mysqli_num_rows($res) > 0) {
        $winner_id = mysqli_fetch_assoc($res)['winner_user_id'];
        if($winner_id == $user_id) $is_winner = true;
    }

    $row['global_highest'] = $global_highest;
    $row['is_winning'] = ($row['my_highest_bid'] == $global_highest);
    $row['is_final_winner'] = $is_winner;
    $vehicles_data[] = $row;
}
?>
<?php include 'includes/header.php'; ?>

<div class="glass-panel" style="padding:40px;">
    <h2>My Bids</h2>
    <p style="color:var(--text-secondary); margin-bottom: 30px;">Track your bidding activity and winning history here.</p>

    <table>
        <thead>
            <tr>
                <th>Vehicle Name</th>
                <th>My Highest Bid</th>
                <th>Current Overall Highest</th>
                <th>Status</th>
                <th>Final Outcome</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($vehicles_data as $v): ?>
                <tr style="<?= $v['is_final_winner'] ? 'background:rgba(0, 200, 150, 0.2);' : '' ?>">
                    <td><?= htmlspecialchars($v['vehicle_name']) ?></td>
                    <td style="font-weight:bold;">$<?= number_format($v['my_highest_bid'], 2) ?></td>
                    <td>$<?= number_format($v['global_highest'], 2) ?></td>
                    <td>
                        <span class="badge <?= $v['status']=='active'?'bg-success':'bg-danger'?>"><?= strtoupper($v['status']) ?></span>
                    </td>
                    <td>
                        <?php if($v['is_final_winner']): ?>
                            <span class="badge bg-success">🏆 YOU WON!</span>
                        <?php elseif($v['status'] == 'approved' && !$v['is_final_winner']): ?>
                            <span class="badge" style="background:#444;">Lost</span>
                        <?php elseif($v['status'] == 'active' && $v['is_winning']): ?>
                            <span style="color:var(--accent-gold);">Currently Winning</span>
                        <?php elseif($v['status'] == 'active' && !$v['is_winning']): ?>
                            <span style="color:var(--danger);">Outbid</span>
                        <?php else: ?>
                            <span style="color:var(--text-secondary);">Waiting Approval</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="vehicle_details.php?id=<?= $v['vehicle_id'] ?>" class="btn">View Auction</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(count($vehicles_data) == 0): ?>
                <tr><td colspan="6" style="text-align:center;">You haven't placed any bids yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
