<?php
session_start();
include 'includes/header.php';
include 'includes/auth.php';
include 'includes/db.php';
include 'config.php';
redirectIfNotLoggedIn();


$inventoryReport = $conn->query("
    SELECT p.name, p.stock_quantity, c.category_name
    FROM Products p
    JOIN Categories c ON p.category_id = c.category_id
")->fetchAll();

$salesReport = $conn->query("
    SELECT s.name AS supplier_name, SUM(oi.quantity) AS total_quantity, SUM(oi.price * oi.quantity) AS total_sales
    FROM Suppliers s
    JOIN Products p ON s.supplier_id = p.supplier_id
    JOIN Order_Items oi ON p.product_id = oi.product_id
    GROUP BY s.supplier_id
")->fetchAll();
?>

<h2>Reports</h2>

<h3>Inventory Report</h3>
<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Stock Quantity</th>
            <th>Category</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($inventoryReport as $item) { ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['stock_quantity']) ?></td>
                <td><?= htmlspecialchars($item['category_name']) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php if ($_SESSION['role'] !== 'sales_staff') { ?>
    <h3>Sales Report</h3>
    <table>
        <thead>
            <tr>
                <th>Supplier Name</th>
                <th>Total Quantity Sold</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salesReport as $item) { ?>
                <tr>
                    <td><?= htmlspecialchars($item['supplier_name']) ?></td>
                    <td><?= htmlspecialchars($item['total_quantity']) ?></td>
                    <td><?= htmlspecialchars($item['total_sales']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

<?php
include 'includes/footer.php';
?>