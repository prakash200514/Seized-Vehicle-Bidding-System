<?php
require_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_name = mysqli_real_escape_string($conn, $_POST['vehicle_name']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $seized_location = mysqli_real_escape_string($conn, $_POST['seized_location']);
    $base_price = $_POST['base_price'];
    $auction_start = $_POST['auction_start'];
    $auction_end = $_POST['auction_end'];
    $status = 'upcoming';

    // File upload logic (defaulting to the generated dummy image if no file chosen)
    $image_path = "/bidding/assets/img/car1.png"; // Default
    if (isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] == 0) {
        $target_dir = "../assets/img/uploads/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . '_' . basename($_FILES["vehicle_image"]["name"]);
        $target_file = $target_dir . $file_name;
        if(move_uploaded_file($_FILES["vehicle_image"]["tmp_name"], $target_file)){
            $image_path = "/bidding/assets/img/uploads/" . $file_name;
        }
    }

    $sql = "INSERT INTO vehicles (vehicle_name, model, seized_location, base_price, auction_start, auction_end, image_path, status) 
            VALUES ('$vehicle_name', '$model', '$seized_location', '$base_price', '$auction_start', '$auction_end', '$image_path', '$status')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: manage_vehicles.php?added=true");
        exit();
    } else {
        $error = "Error adding vehicle: " . mysqli_error($conn);
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="glass-panel" style="max-width: 600px; margin: 0 auto; padding: 30px;">
    <h2>➕ Add Seized Vehicle</h2>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label>Vehicle Name</label>
            <input type="text" name="vehicle_name" class="form-control" required placeholder="e.g. BMW M4 Competition">
        </div>
        <div class="form-group">
            <label>Model / Year</label>
            <input type="text" name="model" class="form-control" required placeholder="e.g. 2021">
        </div>
        <div class="form-group">
            <label>Seized Location</label>
            <input type="text" name="seized_location" class="form-control" required placeholder="Downtown District">
        </div>
        <div class="form-group">
            <label>Base Price ($)</label>
            <input type="number" step="0.01" name="base_price" class="form-control" required>
        </div>
        <div style="display:flex; gap:20px;">
            <div class="form-group" style="flex:1">
                <label>Auction Start Time</label>
                <input type="datetime-local" name="auction_start" class="form-control" required>
            </div>
            <div class="form-group" style="flex:1">
                <label>Auction End Time</label>
                <input type="datetime-local" name="auction_end" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label>Vehicle Image</label>
            <input type="file" name="vehicle_image" class="form-control" accept="image/*">
            <small style="color:var(--text-secondary)">If omitted, default dummy image will be used.</small>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; margin-top: 15px;">List Vehicle for Auction</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
