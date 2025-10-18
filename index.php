<?php 
session_start();
include 'conn.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="./style/toast.css">
    <script src="./js/toast.js" defer></script>
    
</head>

<body class="bg-gray-50">

    <?php include 'nav.php'; ?>
    <?php include 'banner.php'; ?>
    <?php include 'products.php'; ?>
    <?php include 'about.php'; ?>
    <?php include 'contact.php'; ?>
    <?php include 'footer.php'; ?>

    <script>
        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    </script>
</body>

</html>