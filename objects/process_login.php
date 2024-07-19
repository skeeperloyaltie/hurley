<?php
session_start();
include 'config.php'; // Make sure to include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM Staff WHERE Email = ?");
    $stmt->bind_param("s", $email); $stmt->close();
    $conn->close();
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['StaffID'];
        $_SESSION['role'] = $user['Role'];

        // Redirect based on role
        if ($user['Role'] === 'Admin') {
            header("Location: staff/admin/admin.php");
        } elseif ($user['Role'] === 'Cook' || $user['Role'] === 'Waiter' || $user['Role'] === 'Manager') {
            header("Location: dashboard/staff_dashboard.php"); // Adjust if you have separate dashboards for staff
        } else {
            header("Location: index.php"); // Default redirection
        }
        exit();
    } else {
        echo "<p class='text-danger text-center'>Invalid email or password.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
