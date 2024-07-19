<?php
session_start();
include '../configconfig.php'; // Update this path to your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM Staff WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['StaffID'];
        $_SESSION['role'] = $user['Role'];
        
        // Redirect based on role
        if ($user['Role'] === 'Admin') {
            header("Location: ../dashboard/admin_dashboard.php");
        } else {
            header("Location: ../dashboard/staff.php"); // Adjust this based on staff roles
        }
        exit();
    } else {
        echo "<p>Invalid email or password.</p>";
    }

    $stmt->close();
}
?>
