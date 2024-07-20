<?php
session_start();
include 'config.php'; // Update this path to your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM Staff WHERE (Email = ? OR Username = ?)");
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['StaffID'];
        $_SESSION['role'] = $user['Role'];

        // Redirect based on role
        if ($user['Role'] === 'Admin') {
            header("Location: admin/admin.php");
        } elseif ($user['Role'] === 'Manager') {
            header("Location: staff/staff.php"); // Adjust if Managers have a separate page
        } elseif ($user['Role'] === 'Cook') {
            header("Location: staff/staff.php"); // Adjust if Cooks have a separate page
        } elseif ($user['Role'] === 'Waiter') {
            header("Location: staff/staff.php"); // Adjust if Waiters have a separate page
        } else {
            echo "<p class='text-danger text-center'>Role not recognized.</p>";
        }
        exit();
    } else {
        echo "<p class='text-danger text-center'>Invalid username/email or password.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>