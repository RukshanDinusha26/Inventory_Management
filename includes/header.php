
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul style="list-style-type: none; padding: 0; margin: 0; display: flex; justify-content: center; background-color: #333;">
                <li><a href="dashboard.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Dashboard</a></li>
                <?php if ($_SESSION['role'] === 'admin') { ?>
                    <li><a href="products.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Products</a></li>
                    <li><a href="orders.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Orders</a></li>
                    <li><a href="customers.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Customers</a></li>
                    <li><a href="suppliers.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Suppliers</a></li>
                    <li><a href="users.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Users</a></li>
                <?php } elseif ($_SESSION['role'] === 'sales_staff') { ?>
                    <li><a href="orders.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Orders</a></li>
                    <li><a href="reports.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Inventory</a></li>
                <?php } elseif ($_SESSION['role'] === 'inventory_manager') { ?>
                    <li><a href="update_stock.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Update Stock</a></li>
                    <li><a href="reports.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Reports</a></li>
                <?php } ?>
                <li><a href="logout.php" style="display: block; color: white; text-align: center; padding: 14px 20px; text-decoration: none;">Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">