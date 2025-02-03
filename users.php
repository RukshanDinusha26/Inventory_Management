<?php
session_start();
include 'includes/header.php';
include 'includes/auth.php';
include 'includes/db.php';
include 'includes/functions.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = validateInput($_POST['username']);
    $password = password_hash(validateInput($_POST['password']), PASSWORD_DEFAULT);
    $role = validateInput($_POST['role']);

    $stmt = $conn->prepare("INSERT INTO Users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->execute([
        'username' => $username,
        'password' => $password,
        'role' => $role
    ]);

    $_SESSION['success'] = 'User added successfully!';
    header('Location: users.php');
    exit();
}

$users = $conn->query("SELECT * FROM Users")->fetchAll();
?>

<h2>Add User</h2>
<form method="POST" action="users.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="admin">Admin</option>
        <option value="sales_staff">Sales Staff</option>
        <option value="inventory_manager">Inventory Manager</option>
    </select>
    <button type="submit">Add User</button>
</form>

<h3>Current Users</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user) { ?>
            <tr>
                <td><?= htmlspecialchars($user['user_id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
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