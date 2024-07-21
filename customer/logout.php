<?php
session_start();
session_unset();
session_destroy();
header("Location: ../index.php"); // Update with your login page URL
exit();
?>
