<?php
session_start();
require_once ('config.php'); // Include your database connection file

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phoneNumber = $_POST['phoneNumber'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipCode = $_POST['zipCode'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt the password
    $role = "customer"; // Default role

    // Insert the data into the Customers table
    $sql = "INSERT INTO Customers (FirstName, LastName, Email, Username, Password, PhoneNumber, Address, City, State, ZipCode, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Use the $mysqli variable from the config file
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("sssssssssss", $firstName, $lastName, $email, $username, $password, $phoneNumber, $address, $city, $state, $zipCode, $role);

        if ($stmt->execute()) {
            // Registration successful
            header("Location: ../login.php");
            exit(); // Ensure no further code is executed
        } else {
            // Error occurred during execution
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Error occurred during preparation
        echo "Error: Could not prepare the query: " . $mysqli->error;
    }

    // Close the database connection
    $mysqli->close();
}
?>
