<?php
include '../config.php'; // Make sure this path is correct

if (isset($_GET['id'])) {
    $reservationID = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM reservations WHERE ReservationID = ?");
    $stmt->bind_param("i", $reservationID);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'No ID parameter provided']);
}
?>
