
<?php 
require_once "accounts/config.php";
// Admin details
$firstName = 'Admin';
$lastName = 'User';
$username = "admin";
$role = 'Admin';
$email = 'admin@example.com';
$password = 'your_password'; // Replace with the actual password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$phoneNumber = '1234567890';

// Prepare SQL statement
$sql = "INSERT INTO staff (FirstName, LastName, Username, Role, Email, Password, PhoneNumber)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssssss", $firstName, $lastName, $username, $role, $email, $hashedPassword, $phoneNumber);

    if ($stmt->execute()) {
        echo "Admin user created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: " . $conn->error;
}

// Close the database connection
$conn->close();
?>