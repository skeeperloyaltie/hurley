<?php
// Include database connection file
require_once 'config.php'; // This file sets up the MySQLi connection

// Enable error reporting for debugging purposes
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $comments = $_POST['message'] ?? '';
    $feedbackDate = date('Y-m-d H:i:s'); // Current timestamp

    // Check if the customer exists by email
    $sqlCheckCustomer = "SELECT CustomerID FROM customers WHERE Email = ?";
    $stmtCheckCustomer = $conn->prepare($sqlCheckCustomer);

    if ($stmtCheckCustomer === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmtCheckCustomer->bind_param("s", $email);
    $stmtCheckCustomer->execute();
    $stmtCheckCustomer->bind_result($customerID);
    $stmtCheckCustomer->fetch();
    $stmtCheckCustomer->close();

    if (!$customerID) {
        // If customer does not exist, insert new customer
        $username = strtolower(explode(' ', $name)[0]); // Use the first part of the name as a username
        $defaultPassword = password_hash('defaultpassword', PASSWORD_BCRYPT); // Use a default password or generate a hash as needed

        // Split name into first and last names
        $nameParts = explode(' ', $name);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        $sqlInsertCustomer = "INSERT INTO customers (FirstName, LastName, Email, Username, Password, PhoneNumber, role) VALUES (?, ?, ?, ?, ?, '', 'customer')";
        $stmtInsertCustomer = $conn->prepare($sqlInsertCustomer);

        if ($stmtInsertCustomer === false) {
            die('Prepare failed: ' . $conn->error);
        }

        $stmtInsertCustomer->bind_param("sssss", $firstName, $lastName, $email, $username, $defaultPassword);

        if ($stmtInsertCustomer->execute()) {
            // Get the newly inserted customer ID
            $customerID = $conn->insert_id;
        } else {
            echo '<div class="error-message">Customer Insertion Error: ' . $stmtInsertCustomer->error . '</div>';
            $stmtInsertCustomer->close();
            $conn->close();
            exit;
        }
        $stmtInsertCustomer->close();
    }

    // Insert feedback into the feedback table
    $sqlInsertFeedback = "INSERT INTO feedback (CustomerID, FeedbackDate, Comments, Rating) VALUES (?, ?, ?, ?)";
    $stmtInsertFeedback = $conn->prepare($sqlInsertFeedback);

    if ($stmtInsertFeedback === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmtInsertFeedback->bind_param("issi", $customerID, $feedbackDate, $comments, $rating);

    if ($stmtInsertFeedback->execute()) {
        echo '<div class="sent-message">Your feedback has been sent. Thank you!</div>';
    } else {
        echo '<div class="error-message">Feedback Error: ' . $stmtInsertFeedback->error . '</div>';
    }

    // Close the feedback statement
    $stmtInsertFeedback->close();
    
    // Close the database connection
    $conn->close();
}
?>
