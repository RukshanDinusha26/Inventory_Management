<?php
session_start();
include 'includes/header.php';
include 'includes/auth.php';
include 'includes/db.php';
include 'includes/functions.php';
redirectIfNotLoggedIn();
if (!isAdmin() && !isSalesStaff()) {
    header('Location: dashboard.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = validateInput($_POST['customer_id']);
    $order_date = date('Y-m-d H:i:s');
    $total_amount = 0;

    $errors = validateOrderForm($_POST);

    // Server-side validation for stock quantity
    foreach ($_POST['products'] as $product) {
        $product_id = validateInput($product['product_id']);
        $quantity = validateInput($product['quantity']);

        $stmt = $conn->prepare("SELECT stock_quantity FROM Products WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        $available_quantity = $stmt->fetchColumn();

        if ($quantity > $available_quantity) {
            $errors[] = "The quantity for product ID $product_id exceeds the available stock.";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO Orders (customer_id, order_date, total_amount) VALUES (:customer_id, :order_date, :total_amount)");
        $stmt->execute([
            'customer_id' => $customer_id,
            'order_date' => $order_date,
            'total_amount' => $total_amount
        ]);

        $order_id = $conn->lastInsertId();

        foreach ($_POST['products'] as $product) {
            $product_id = validateInput($product['product_id']);
            $quantity = validateInput($product['quantity']);
            $price = validateInput($product['price']);

            $stmt = $conn->prepare("INSERT INTO Order_Items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
            $stmt->execute([
                'order_id' => $order_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'price' => $price
            ]);

            // Update the stock quantity of the product
            $stmt = $conn->prepare("UPDATE Products SET stock_quantity = stock_quantity - :quantity WHERE product_id = :product_id");
            $stmt->execute([
                'quantity' => $quantity,
                'product_id' => $product_id
            ]);

            $total_amount += $quantity * $price;
        }

        $stmt = $conn->prepare("UPDATE Orders SET total_amount = :total_amount WHERE order_id = :order_id");
        $stmt->execute([
            'total_amount' => $total_amount,
            'order_id' => $order_id
        ]);

        $_SESSION['success'] = 'Order created successfully!';
        header('Location: orders.php');
        exit();
    }
}

$customers = $conn->query("SELECT * FROM Customers")->fetchAll();
$products = $conn->query("SELECT * FROM Products")->fetchAll();
$orders = $conn->query("SELECT * FROM Orders")->fetchAll();
?>

<h2>Orders</h2>
<?php if (!empty($errors)) { ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $error) { ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
<form method="POST" class="order-form">
    <label for="customer_id">Customer:</label>
    <select id="customer_id" name="customer_id" required>
        <?php foreach ($customers as $customer) { ?>
            <option value="<?= htmlspecialchars($customer['customer_id']) ?>"><?= htmlspecialchars($customer['name']) ?></option>
        <?php } ?>
    </select>

    <h3>Products</h3>
    <div id="product-list">
        <div class="product-item" style="display: flex; gap: 10px; margin-bottom: 10px;">
            <select name="products[0][product_id]" onchange="updatePrice(this)" required>
                <option value="" data-price="0" data-stock="0">Select a product</option>
                <?php foreach ($products as $product) { ?>
                    <option value="<?= htmlspecialchars($product['product_id']) ?>" data-price="<?= htmlspecialchars($product['price']) ?>" data-stock="<?= htmlspecialchars($product['stock_quantity']) ?>"><?= htmlspecialchars($product['name']) ?></option>
                <?php } ?>
            </select>
            <input type="number" name="products[0][quantity]" placeholder="Quantity" oninput="calculatePrice(this)" required>
            <input type="number" name="products[0][price]" placeholder="Price" readonly required>
            <button type="button" class="remove-btn" onclick="removeProduct(this)">Remove</button>
        </div>
    </div>
    <div class="form-buttons">
        <button type="button" onclick="addProduct()">Add Product</button>
    </div>
    <button type="submit" class="create-order-btn">Create Order</button>
</form>

<h3>Order List</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order) { ?>
            <tr>
                <td><?= htmlspecialchars($order['order_id']) ?></td>
                <td><?= htmlspecialchars($order['customer_id']) ?></td>
                <td><?= htmlspecialchars($order['order_date']) ?></td>
                <td><?= htmlspecialchars($order['total_amount']) ?></td>
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
let productCount = 1;

function addProduct() {
    const productList = document.getElementById('product-list');
    const newProduct = document.createElement('div');
    newProduct.className = 'product-item';
    newProduct.style.display = 'flex';
    newProduct.style.gap = '10px';
    newProduct.style.marginBottom = '10px';
    newProduct.innerHTML = `
        <select name="products[${productCount}][product_id]" onchange="updatePrice(this)" required>
            <option value="" data-price="0" data-stock="0">Select a product</option>
            <?php foreach ($products as $product) { ?>
                <option value="<?= htmlspecialchars($product['product_id']) ?>" data-price="<?= htmlspecialchars($product['price']) ?>" data-stock="<?= htmlspecialchars($product['stock_quantity']) ?>"><?= htmlspecialchars($product['name']) ?></option>
            <?php } ?>
        </select>
        <input type="number" name="products[${productCount}][quantity]" placeholder="Quantity" oninput="calculatePrice(this)" required>
        <input type="number" name="products[${productCount}][price]" placeholder="Price" readonly required>
        <button type="button" class="remove-btn" onclick="removeProduct(this)">Remove</button>
    `;
    productList.appendChild(newProduct);
    productCount++;
}

function removeProduct(button) {
    const productItem = button.parentElement;
    productItem.remove();
}

function updatePrice(selectElement) {
    const price = selectElement.options[selectElement.selectedIndex].getAttribute('data-price');
    const quantityInput = selectElement.nextElementSibling;
    const priceInput = quantityInput.nextElementSibling;
    const quantity = quantityInput.value;
    priceInput.value = quantity * price;
}

function calculatePrice(quantityInput) {
    const selectElement = quantityInput.previousElementSibling;
    const price = selectElement.options[selectElement.selectedIndex].getAttribute('data-price');
    const priceInput = quantityInput.nextElementSibling;
    const quantity = quantityInput.value;
    priceInput.value = quantity * price;
}

function closeAlert() {
    document.querySelector('.alert-overlay').style.display = 'none';
}
</script>

<style>
.remove-btn {
    background-color: red;
    color: white;
    border: none;
    padding: 6px 6px;
    border-radius: 3px;
    cursor: pointer;
}

.create-order-btn {
    background-color: green;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.form-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}

.form-buttons button {
    background-color: #333;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
}

.alert-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(5px);
}

.alert-box {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.alert-box p {
    margin-bottom: 20px;
}

.alert-box button {
    padding: 10px 20px;
    background-color: #333;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.alert-box button:hover {
    background-color: #575757;
}
</style>

<?php
include 'includes/footer.php';
?>