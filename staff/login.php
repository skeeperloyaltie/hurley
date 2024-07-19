<?php
session_start();
include 'config.php'; // Update this path to your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM Staff WHERE (Email = ? OR Username = ?) AND Role = 'Admin'");
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
        } else {
            header("Location: ../dashboard/staff.php"); // Adjust this based on staff roles
        }
        exit();
    } else {
        echo "<p class='text-danger text-center'>Invalid username/email or password.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
