<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logout</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="alert-overlay">
        <div class="alert-box">
            <p>Are you sure you want to log out?</p>
            <button onclick="confirmLogout()">Yes</button>
            <button onclick="cancelLogout()">No</button>
        </div>
    </div>

    <script>
    function confirmLogout() {
        window.location.href = 'logout_confirm.php';
    }

    function cancelLogout() {
        window.location.href = 'dashboard.php';
    }
    </script>
</body>
</html>