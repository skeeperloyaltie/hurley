<?php
// Include database connection file
require_once 'config.php'; // This file sets up the MySQLi connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $people = $_POST['people'];
    $message = $_POST['message'];

    // Combine date and time
    $reservationDate = "$date $time";

    // Check if the customer already exists
    $sqlCheckCustomer = "SELECT CustomerID FROM customers WHERE Email = ?";
    $stmtCheckCustomer = $conn->prepare($sqlCheckCustomer);
    $stmtCheckCustomer->bind_param("s", $email);
    $stmtCheckCustomer->execute();
    $stmtCheckCustomer->store_result();

    if ($stmtCheckCustomer->num_rows == 0) {
        // Customer does not exist, insert a new customer record
        $customerID = generateUniqueCustomerID($conn);

        $sqlInsertCustomer = "INSERT INTO customers (CustomerID, FirstName, LastName, Email, Username, Password, PhoneNumber, Address, City, State, ZipCode, RegistrationDate, role) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, '', '', '', '', NOW(), 'customer')";
        $stmtInsertCustomer = $conn->prepare($sqlInsertCustomer);
        $defaultPassword = password_hash('defaultpassword', PASSWORD_BCRYPT); // Use a default password or generate a hash as needed

        // Bind parameters for new customer
        $stmtInsertCustomer->bind_param("issssss", $customerID, $name, $name, $email, $email, $defaultPassword, $phone);
        
        if ($stmtInsertCustomer->execute()) {
            $stmtInsertCustomer->close();
        } else {
            echo '<p style="color: red; font-weight: bold;">Customer Insertion Error: ' . $stmtInsertCustomer->error . '</p>';
            $stmtInsertCustomer->close();
            $conn->close();
            exit();
        }
    } else {
        // Customer exists, get the existing customer ID
        $stmtCheckCustomer->bind_result($customerID);
        $stmtCheckCustomer->fetch();
    }
    $stmtCheckCustomer->close();

    // Prepare the reservation SQL statement
    $sqlInsertReservation = "INSERT INTO reservations (CustomerID, ReservationDate, NumberOfGuests, SpecialRequests, Status) VALUES (?, ?, ?, ?, ?)";
    $stmtInsertReservation = $conn->prepare($sqlInsertReservation);
    
    $status = 'Pending';
    $stmtInsertReservation->bind_param("issss", $customerID, $reservationDate, $people, $message, $status);

    // Execute and check reservation insertion
    if ($stmtInsertReservation->execute()) {
        echo '<p style="color: green; font-weight: bold;">Reservation successfully added!</p>';
    } else {
        echo '<p style="color: red; font-weight: bold;">Reservation Error: ' . $stmtInsertReservation->error . '</p>';
    }

    // Close the reservation statement
    $stmtInsertReservation->close();
    // Close the database connection
    $conn->close();
}

/**
 * Generate a unique CustomerID that does not exist in the database
 */
function generateUniqueCustomerID($conn) {
    do {
        $uniqueID = rand(10000, 99999); // Generate a random ID
        $sqlCheck = "SELECT COUNT(*) FROM customers WHERE CustomerID = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $uniqueID);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close();
    } while ($count > 0); // Loop until a unique ID is found

    return $uniqueID;
}
?>
