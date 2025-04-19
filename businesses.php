<?php
$pageTitle = "Businesses";
$currentPage = "businesses";
$extraCSS = "css/home.css";
$extraJS = "js/home.js";
require_once 'header.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize search term
$searchTerm = '';

// Get all businesses (or filtered if searching)
$businesses = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchTerm = sanitizeInput($_GET['search']);
    
    if (!empty($searchTerm)) {
        $searchQuery = "SELECT * FROM businesses 
                        WHERE business_name LIKE ? OR business_description LIKE ? 
                        ORDER BY business_name ASC";
        $searchParam = "%{$searchTerm}%";
        $stmt = $link->prepare($searchQuery);
        $stmt->bind_param("ss", $searchParam, $searchParam);
    }
} else {
    $stmt = $link->prepare("SELECT * FROM businesses ORDER BY business_name ASC");
}

// Execute and fetch results
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $businesses[] = $row;
}
$stmt->close();
?>

<!-- Hero Section -->
<section class="container py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="fw-bold">Business Directory</h1>
                <p class="lead">Discover and connect with businesses using our platform.</p>
            </div>
            <div class="col-lg-6">
                <form action="businesses.php" method="get" class="d-flex">
                    <input type="text" name="search" class="form-control form-control-lg me-2"
                        placeholder="Search businesses..." 
                        style="background-color: #f8f9fa; border: 1px solid #ced4da; color: #495057;"
                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button type="submit" class="btn btn-primary btn-lg" style="min-width: 120px;">Search</button>
                </form>
            </div>
        </div>
    </div>
</section>


<!-- Businesses List -->
<section class="hero">
    <div class="container">
        <?php if (!empty($searchTerm)): ?>
            <div class="mb-4">
                <h2>Search Results for "<?php echo htmlspecialchars($searchTerm); ?>"</h2>
                <p><?php echo count($businesses); ?> businesses found</p>
                <a href="businesses.php" class="btn btn-outline-primary">Clear Search</a>
            </div>
        <?php endif; ?>

        <?php if (count($businesses) > 0): ?>
            <div class="row g-4">
                <?php foreach ($businesses as $business): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card business-card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img src="<?php echo $upload_dir . htmlspecialchars($business["business_logo"] ?? 'default_business.png'); ?>" 
                                                     alt="<?php echo htmlspecialchars($business["business_name"]); ?>" 
                                                     class="me-3 rounded-circle" 
                                                     width="50" 
                                                     height="50">
                                    <h5 class="card-title"><?php echo htmlspecialchars($business['business_name']); ?></h5>
                                </div>
                                <p class="card-text"><?php echo htmlspecialchars($business['business_description']); ?></p>
                                <hr>
                                <div class="small text-muted">
                                    <?php if (!empty($business['business_address'])): ?>
                                        <p><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($business['business_address']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($business['business_phone'])): ?>
                                        <p><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($business['business_phone']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($business['business_email'])): ?>
                                        <p><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($business['business_email']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="business-details.php?id=<?php echo $business['business_id']; ?>" class="btn btn-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h3>No businesses found</h3>
                <?php if (!empty($searchTerm)): ?>
                    <p>No results match your search criteria. Please try a different search term.</p>
                <?php else: ?>
                    <p>There are no businesses in our directory yet.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Business Registration CTA -->
<section class="pt-5 pb-0" >
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Are You a Business Owner?</h2>
        <p class="lead mb-4">Join our platform and connect with potential customers.</p>
        <a href="signup.php" class="btn btn-primary btn-lg px-5">Register Your Business</a>
    </div>
</section>

<?php require_once 'footer.php'; ?>
