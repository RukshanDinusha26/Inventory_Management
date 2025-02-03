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
    $name = validateInput($_POST['name']);
    $contact_info = validateInput($_POST['contact_info']);

    $stmt = $conn->prepare("INSERT INTO Suppliers (name, contact_info) VALUES (:name, :contact_info)");
    $stmt->execute([
        'name' => $name,
        'contact_info' => $contact_info
    ]);

    $_SESSION['success'] = 'Supplier added successfully!';
    header('Location: suppliers.php');
    exit();
}

$suppliers = $conn->query("SELECT * FROM Suppliers")->fetchAll();
?>

<h2>Add Supplier</h2>
<form method="POST" action="suppliers.php">
    <label for="name">Supplier Name:</label>
    <input type="text" id="name" name="name" required>
    <label for="contact_info">Supplier Contact Info:</label>
    <input type="text" id="contact_info" name="contact_info" required>
    <button type="submit">Add Supplier</button>
</form>

<h3>Current Suppliers</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact Info</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($suppliers as $supplier) { ?>
            <tr>
                <td><?= htmlspecialchars($supplier['supplier_id']) ?></td>
                <td><?= htmlspecialchars($supplier['name']) ?></td>
                <td><?= htmlspecialchars($supplier['contact_info']) ?></td>
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