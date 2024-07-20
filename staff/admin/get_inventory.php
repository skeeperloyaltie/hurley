<?php
include '../config.php'; // Ensure this path is correct

if (isset($_GET['id'])) {
    $inventoryID = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM inventory WHERE InventoryID = ?");
    $stmt->bind_param("i", $inventoryID);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'No ID parameter provided']);
}
?>
