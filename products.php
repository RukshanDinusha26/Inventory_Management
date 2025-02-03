<?php
session_start();
include 'includes/header.php';
include 'includes/auth.php';
include 'includes/db.php';
include 'config.php';
redirectIfNotLoggedIn();
if (!isAdmin()) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $category_id = $_POST['category_id'];
        $supplier_id = $_POST['supplier_id'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];

        $stmt = $conn->prepare("INSERT INTO Products (name, description, category_id, supplier_id, price, stock_quantity) VALUES (:name, :description, :category_id, :supplier_id, :price, :stock_quantity)");
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'category_id' => $category_id,
            'supplier_id' => $supplier_id,
            'price' => $price,
            'stock_quantity' => $stock_quantity
        ]);

        $_SESSION['success'] = 'Product added successfully!';
        header('Location: products.php');
        exit();
    } elseif (isset($_POST['update_product'])) {
        $product_id = $_POST['product_id'];
        $new_quantity = $_POST['new_quantity'];
        $new_price = $_POST['new_price'];

        $stmt = $conn->prepare("UPDATE Products SET stock_quantity = :new_quantity, price = :new_price WHERE product_id = :product_id");
        $stmt->execute([
            'new_quantity' => $new_quantity,
            'new_price' => $new_price,
            'product_id' => $product_id
        ]);

        $_SESSION['success'] = 'Product updated successfully!';
        header('Location: products.php');
        exit();
    }
}

$categories = $conn->query("SELECT * FROM Categories")->fetchAll();
$suppliers = $conn->query("SELECT * FROM Suppliers")->fetchAll();
$products = $conn->query("SELECT * FROM Products")->fetchAll();
?>

<h2>Add Product</h2>
<form method="POST" action="products.php">
    <input type="hidden" name="add_product" value="1">
    <label for="name">Product Name:</label>
    <input type="text" id="name" name="name" required>
    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea>
    <label for="category_id">Category:</label>
    <select id="category_id" name="category_id" required>
        <?php foreach ($categories as $category) { ?>
            <option value="<?= htmlspecialchars($category['category_id']) ?>"><?= htmlspecialchars($category['category_name']) ?></option>
        <?php } ?>
    </select>
    <label for="supplier_id">Supplier:</label>
    <select id="supplier_id" name="supplier_id" required>
        <?php foreach ($suppliers as $supplier) { ?>
            <option value="<?= htmlspecialchars($supplier['supplier_id']) ?>"><?= htmlspecialchars($supplier['name']) ?></option>
        <?php } ?>
    </select>
    <label for="price">Price:</label>
    <input type="number" id="price" name="price" step="0.01" required>
    <label for="stock_quantity">Stock Quantity:</label>
    <input type="number" id="stock_quantity" name="stock_quantity" required>
    <button type="submit">Add Product</button>
</form>

<h2>Update Product</h2>
<form method="POST" action="products.php">
    <input type="hidden" name="update_product" value="1">
    <label for="product_id">Product:</label>
    <select id="product_id" name="product_id" required>
        <?php foreach ($products as $product) { ?>
            <option value="<?= htmlspecialchars($product['product_id']) ?>"><?= htmlspecialchars($product['name']) ?></option>
        <?php } ?>
    </select>
    <label for="new_quantity">New Stock Quantity:</label>
    <input type="number" id="new_quantity" name="new_quantity" required>
    <label for="new_price">New Price:</label>
    <input type="number" id="new_price" name="new_price" step="0.01" required>
    <button type="submit">Update Product</button>
</form>

<h2>Product List</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>Supplier</th>
            <th>Price</th>
            <th>Stock Quantity</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product) { ?>
            <tr>
                <td><?= htmlspecialchars($product['product_id']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td><?= htmlspecialchars($product['category_id']) ?></td>
                <td><?= htmlspecialchars($product['supplier_id']) ?></td>
                <td><?= htmlspecialchars($product['price']) ?></td>
                <td><?= htmlspecialchars($product['stock_quantity']) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

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