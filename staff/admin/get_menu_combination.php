<?php
include '../config.php';

if (isset($_GET['id'])) {
    $combinationID = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM menu_combinations WHERE CombinationID = ?");
    $stmt->bind_param("i", $combinationID);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data);
    $stmt->close();
}
?>
