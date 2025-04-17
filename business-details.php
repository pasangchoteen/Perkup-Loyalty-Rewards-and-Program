<?php
require_once 'header.php';


// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get business ID from URL
$businessId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($businessId <= 0) {
    echo "<p>Invalid business ID.</p>";
    require_once 'footer.php';
    exit;
}

// Fetch business details
$stmt = $link->prepare("SELECT * FROM businesses WHERE business_id = ?");
$stmt->bind_param("i", $businessId);
$stmt->execute();
$result = $stmt->get_result();
$business = $result->fetch_assoc();
$stmt->close();
?>

<div class="container py-5 bg-gradient">
    <?php if ($business): ?>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <img src="<?php echo !empty($business['business_logo']) ? $business['business_logo'] : 'img/default-business.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($business['business_name']); ?>" 
                     class="img-fluid rounded mb-3" style="max-height: 200px;">
                <h3><?php echo htmlspecialchars($business['business_name']); ?></h3>
                <p class="text-muted"><?php echo htmlspecialchars($business['business_category']); ?></p>
            </div>
            <div class="col-md-8">
                <h4>Description</h4>
                <p><?php echo nl2br(htmlspecialchars($business['business_description'])); ?></p>

                <h5>Contact Information</h5>
                <ul class="list-unstyled">
                    <?php if (!empty($business['business_address'])): ?>
                        <li><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($business['business_address']); ?></li>
                    <?php endif; ?>
                    <?php if (!empty($business['business_phone'])): ?>
                        <li><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($business['business_phone']); ?></li>
                    <?php endif; ?>
                    <?php if (!empty($business['business_email'])): ?>
                        <li><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($business['business_email']); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Business not found.</div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
