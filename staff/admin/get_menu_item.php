<?php
include '../config.php';

if (isset($_GET['id'])) {
    $itemID = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM menuitems WHERE MenuItemID = ?");
    $stmt->bind_param("i", $itemID);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data);
    $stmt->close();
}
?>
