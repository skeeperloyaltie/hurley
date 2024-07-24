<?php
// Include database connection file
require_once 'config.php'; // This file sets up the MySQLi connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $customerID = $_POST['customer_id'];
    $rating = $_POST['rating'];
    $comments = $_POST['message'];
    $feedbackDate = date('Y-m-d H:i:s'); // Current timestamp

    // Insert feedback into the feedback table
    $sqlInsertFeedback = "INSERT INTO feedback (CustomerID, FeedbackDate, Comments, Rating) VALUES (?, ?, ?, ?)";
    $stmtInsertFeedback = $conn->prepare($sqlInsertFeedback);
    
    // Check if the statement was prepared successfully
    if ($stmtInsertFeedback === false) {
        die('Error preparing the statement: ' . $conn->error);
    }
    
    // Bind parameters for feedback insertion
    $stmtInsertFeedback->bind_param("isss", $customerID, $feedbackDate, $comments, $rating);
    
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
