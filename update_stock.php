<?php
session_start();
include 'includes/header.php';
include 'includes/auth.php';
include 'includes/db.php';
redirectIfNotLoggedIn();
redirectIfNotInventoryManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = validateInput($_POST['product_id']);
    $new_quantity = validateInput($_POST['new_quantity']);

    $stmt = $conn->prepare("UPDATE Products SET stock_quantity = :new_quantity WHERE product_id = :product_id");
    $stmt->execute([
        'new_quantity' => $new_quantity,
        'product_id' => $product_id
    ]);

    $_SESSION['success'] = 'Stock updated successfully!';
    header('Location: update_stock.php');
    exit();
}

$products = $conn->query("SELECT * FROM Products")->fetchAll();
?>

<h2>Update Stock</h2>
<form method="POST" action="update_stock.php">
    <label for="product_id">Product:</label>
    <select id="product_id" name="product_id" required>
        <?php foreach ($products as $product) { ?>
            <option value="<?= htmlspecialchars($product['product_id']) ?>"><?= htmlspecialchars($product['name']) ?></option>
        <?php } ?>
    </select>
    <label for="new_quantity">New Stock Quantity:</label>
    <input type="number" id="new_quantity" name="new_quantity" required>
    <button type="submit">Update Stock</button>
</form>

<?php if (isset($_SESSION['success'])) { ?>
    <div class="alert-overlay">
        <div class="alert-box">
            <p><?= htmlspecialchars($_SESSION['success']) ?></p>
            <button onclick="closeAlert()">OK</button>
        </div>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php } ?>

<script>
function closeAlert() {
    document.querySelector('.alert-overlay').style.display = 'none';
}
</script>

<?php
include 'includes/footer.php';
?>