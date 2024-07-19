<?php
session_start();
include('config.php'); // Make sure to include your database connection file


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipCode = $_POST['zipCode'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt the password
    $role = "Customer"; // Default role

    // Insert the data into the Customers table
    $sql = "INSERT INTO Customers (FirstName, LastName, Email, PhoneNumber, Address, City, State, ZipCode, Password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $firstName, $lastName, $email, $phoneNumber, $address, $city, $state, $zipCode, $password);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
