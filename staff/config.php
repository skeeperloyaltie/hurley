<?php
$servername = "localhost";
$username = "root";
$password = ""; // Your MySQL password
$database = "hurley";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Admin details
$firstName = 'Admin';
$lastName = 'User';
$role = 'Admin';
$email = 'admin@example.com';
$username = 'adminuser';
$password = 'admin'; // Replace with the actual password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$phoneNumber = '1234567890';

// Prepare SQL statement
$sql = "INSERT INTO Staff (FirstName, LastName, Role, Email, Username, Password, PhoneNumber)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssssss", $firstName, $lastName, $role, $email, $username, $hashedPassword, $phoneNumber);

    if ($stmt->execute()) {
        echo "Admin user created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: " . $conn->error;
}
?>
