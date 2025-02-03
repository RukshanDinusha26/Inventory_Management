<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/auth.php';
require_once 'includes/db.php';
redirectIfNotLoggedIn();


$totalProducts = $conn->query("SELECT COUNT(*) FROM Products")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM Orders")->fetchColumn();
$totalSuppliers = $conn->query("SELECT COUNT(*) FROM Suppliers")->fetchColumn();
$outOfStockProducts = $conn->query("SELECT COUNT(*) FROM Products WHERE stock_quantity = 0")->fetchColumn();
?>

<h2>Dashboard</h2>
<p>Welcome to the Inventory Management System.</p>

<div style="margin-bottom: 20px; color: #333;">
    <strong>Logged in as:</strong> <?= htmlspecialchars($_SESSION['role']) ?>
</div>

<?php if (isset($_SESSION['success'])) { ?>
    <div class="alert-overlay">
        <div class="alert-box">
            <p><?= htmlspecialchars($_SESSION['success']) ?></p>
            <button onclick="closeAlert()">OK</button>
        </div>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php } ?>

<div class="dashboard-summary">
    <?php if ($_SESSION['role'] === 'admin') { ?>
        <div class="summary-card">
            <h3>Total Products</h3>
            <p><?= htmlspecialchars($totalProducts) ?></p>
        </div>
        <div class="summary-card">
            <h3>Total Orders</h3>
            <p><?= htmlspecialchars($totalOrders) ?></p>
        </div>
        <div class="summary-card">
            <h3>Total Suppliers</h3>
            <p><?= htmlspecialchars($totalSuppliers) ?></p>
        </div>
        <div class="summary-card">
            <h3>Out of Stock Products</h3>
            <p><?= htmlspecialchars($outOfStockProducts) ?></p>
        </div>
    <?php } elseif ($_SESSION['role'] === 'sales_staff') { ?>
        <div class="summary-card">
            <h3>Total Orders</h3>
            <p><?= htmlspecialchars($totalOrders) ?></p>
        </div>
        <div class="summary-card">
            <h3>Total Products</h3>
            <p><?= htmlspecialchars($totalProducts) ?></p>
        </div>
    <?php } elseif ($_SESSION['role'] === 'inventory_manager') { ?>
        <div class="summary-card">
            <h3>Total Products</h3>
            <p><?= htmlspecialchars($totalProducts) ?></p>
        </div>
        <div class="summary-card">
            <h3>Out of Stock Products</h3>
            <p><?= htmlspecialchars($outOfStockProducts) ?></p>
        </div>
    <?php } ?>
</div>

<script>
function closeAlert() {
    document.querySelector('.alert-overlay').style.display = 'none';
}
</script>

<?php
require_once 'includes/footer.php';
?>