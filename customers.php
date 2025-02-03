<?php
session_start();
include 'includes/header.php';
include 'includes/auth.php';
include 'includes/db.php';
include 'config.php'; 
include 'includes/functions.php';

redirectIfNotLoggedIn();
if (!isAdmin()) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = validateInput($_POST['customer_name']);
    $customer_email = validateInput($_POST['customer_email']);
    $customer_phone = validateInput($_POST['customer_phone']);

    $stmt = $conn->prepare("INSERT INTO Customers (name, email, phone) VALUES (:name, :email, :phone)");
    $stmt->execute([
        'name' => $customer_name,
        'email' => $customer_email,
        'phone' => $customer_phone
    ]);

    $_SESSION['success'] = 'Customer added successfully!';
    header('Location: customers.php');
    exit();
}

$customers = $conn->query("SELECT * FROM Customers")->fetchAll();
?>

<h2>Add Customer</h2>
<form method="POST" action="customers.php">
    <label for="customer_name">Customer Name:</label>
    <input type="text" id="customer_name" name="customer_name" required>
    <label for="customer_email">Customer Email:</label>
    <input type="email" id="customer_email" name="customer_email" required>
    <label for="customer_phone">Customer Phone:</label>
    <input type="text" id="customer_phone" name="customer_phone" required>
    <button type="submit">Add Customer</button>
</form>

<h2>Customer List</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer) { ?>
            <tr>
                <td><?= htmlspecialchars($customer['customer_id']) ?></td>
                <td><?= htmlspecialchars($customer['name']) ?></td>
                <td><?= htmlspecialchars($customer['email']) ?></td>
                <td><?= htmlspecialchars($customer['phone']) ?></td>
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