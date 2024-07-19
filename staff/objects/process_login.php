<?php
session_start();
include 'config.php'; // Make sure to include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Prepare and execute query
    if ($stmt = $mysqli->prepare("SELECT * FROM Customers WHERE Email = ? OR Username = ?")) {
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['CustomerID'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'Admin') {
                header("Location: ../staff/admin/admin.php");
            } elseif ($user['role'] === 'Cook' || $user['role'] === 'Waiter' || $user['role'] === 'Manager') {
                header("Location: dashboard/staff_dashboard.php"); // Adjust if you have separate dashboards for staff
            } elseif ($user['role'] === 'customer') {
                header("Location: ../customer/index.php"); // Default redirection
            }  else {
                header("Location: index.php"); 
            }
            exit();
        } else {
            echo "<p class='text-danger text-center'>Invalid username/email or password.</p>";
        }

        $stmt->close();
    } else {
        echo "Error: Could not prepare the query: " . $mysqli->error;
    }

    $mysqli->close();
}
?>
