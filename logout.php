<?php
session_start();

// Clear session variables
session_unset();
session_destroy();

// redirect to login page
header("Location: login.php");
exit();
?>
