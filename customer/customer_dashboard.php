<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Include database connection
include 'config.php'; // Update with your actual connection file

// Check user session
$customerID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // Ensure customer ID is set when user logs in

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle different form submissions
    if (isset($_POST['make_reservation'])) {
        $numberOfGuests = $_POST['number_of_guests'];
        $specialRequests = $_POST['special_requests'];

        if (empty($numberOfGuests)) {
            $_SESSION['message'] = 'Number of guests is required.';
            $_SESSION['message_type'] = 'error';
        } else {
            $stmt = $mysqli->prepare("INSERT INTO reservations (CustomerID, NumberOfGuests, SpecialRequests) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iis", $customerID, $numberOfGuests, $specialRequests);
                if ($stmt->execute()) {
                    $_SESSION['message'] = 'Reservation made successfully. Redirecting...';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = "Error executing statement: " . $stmt->error;
                    $_SESSION['message_type'] = 'error';
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "Error preparing statement: " . $mysqli->error;
                $_SESSION['message_type'] = 'error';
            }
        }
    } elseif (isset($_POST['place_order'])) {
        if (!empty($_POST['menu_item_id']) && !empty($_POST['quantity'])) {
            $menuItemIDs = $_POST['menu_item_id'];
            $quantities = $_POST['quantity'];
            $totalAmount = 0.00;

            foreach ($menuItemIDs as $index => $menuItemID) {
                if (isset($quantities[$index])) {
                    $quantity = $quantities[$index];

                    $stmt = $mysqli->prepare("SELECT Price FROM menuitems WHERE MenuItemID = ?");
                    if ($stmt) {
                        $stmt->bind_param("i", $menuItemID);
                        $stmt->execute();
                        $stmt->bind_result($price);
                        $stmt->fetch();
                        $totalAmount += $price * $quantity;
                        $stmt->close();
                    } else {
                        $_SESSION['message'] = "Error preparing statement: " . $mysqli->error;
                        $_SESSION['message_type'] = 'error';
                    }
                }
            }

            // Insert order
            $stmt = $mysqli->prepare("INSERT INTO orders (CustomerID, TotalAmount, Status) VALUES (?, ?, 'Pending')");
            if ($stmt) {
                $stmt->bind_param("id", $customerID, $totalAmount);
                if ($stmt->execute()) {
                    $orderID = $stmt->insert_id;

                    // Insert order items
                    foreach ($menuItemIDs as $index => $menuItemID) {
                        if (isset($quantities[$index])) {
                            $quantity = $quantities[$index];

                            $stmt = $mysqli->prepare("SELECT Price FROM menuitems WHERE MenuItemID = ?");
                            if ($stmt) {
                                $stmt->bind_param("i", $menuItemID);
                                $stmt->execute();
                                $stmt->bind_result($price);
                                $stmt->fetch();
                                $stmt->close();

                                $stmt = $mysqli->prepare("INSERT INTO orderitems (OrderID, MenuItemID, Quantity, Price) VALUES (?, ?, ?, ?)");
                                if ($stmt) {
                                    $stmt->bind_param("iiid", $orderID, $menuItemID, $quantity, $price);
                                    if ($stmt->execute()) {
                                        // Success, move to the next item
                                    } else {
                                        $_SESSION['message'] = "Error executing statement for order item: " . $stmt->error;
                                        $_SESSION['message_type'] = 'error';
                                    }
                                    $stmt->close();
                                } else {
                                    $_SESSION['message'] = "Error preparing statement for order item: " . $mysqli->error;
                                    $_SESSION['message_type'] = 'error';
                                }
                            } else {
                                $_SESSION['message'] = "Error preparing statement to fetch price: " . $mysqli->error;
                                $_SESSION['message_type'] = 'error';
                            }
                        }
                    }

                    $_SESSION['message'] = 'Order placed successfully. Redirecting...';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = "Error executing statement for order: " . $stmt->error;
                    $_SESSION['message_type'] = 'error';
                }
            } else {
                $_SESSION['message'] = "Error preparing statement for order: " . $mysqli->error;
                $_SESSION['message_type'] = 'error';
            }
        } else {
            $_SESSION['message'] = 'Menu items and quantities are required.';
            $_SESSION['message_type'] = 'error';
        }
    } elseif (isset($_POST['make_payment'])) {
        $orderID = $_POST['order_id'];
        $amount = $_POST['amount'];
        $paymentMethod = $_POST['payment_method'];
        $transactionID = $_POST['transaction_id'];

        $stmt = $mysqli->prepare("INSERT INTO payments (OrderID, Amount, PaymentMethod, TransactionID) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("idss", $orderID, $amount, $paymentMethod, $transactionID);
            if ($stmt->execute()) {
                // Update order status
                $stmt = $mysqli->prepare("UPDATE orders SET Status = 'Paid' WHERE OrderID = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $orderID);
                    if ($stmt->execute()) {
                        $_SESSION['message'] = 'Payment made successfully. Redirecting...';
                        $_SESSION['message_type'] = 'success';
                    } else {
                        $_SESSION['message'] = "Error executing statement to update order status: " . $stmt->error;
                        $_SESSION['message_type'] = 'error';
                    }
                    $stmt->close();
                } else {
                    $_SESSION['message'] = "Error preparing statement to update order status: " . $mysqli->error;
                    $_SESSION['message_type'] = 'error';
                }
            } else {
                $_SESSION['message'] = "Error executing statement for payment: " . $stmt->error;
                $_SESSION['message_type'] = 'error';
            }
        } else {
            $_SESSION['message'] = "Error preparing statement for payment: " . $mysqli->error;
            $_SESSION['message_type'] = 'error';
        }
    }

    // Redirect to index.php to show the message and handle redirection
    header("Location: index.php");
    exit;
}

// Fetch menu items
$menuItemsQuery = "SELECT * FROM menuitems";
$menuItemsResult = $mysqli->query($menuItemsQuery);

// Fetch reservations
$reservationsQuery = "SELECT * FROM reservations WHERE CustomerID = ?";
$reservationsStmt = $mysqli->prepare($reservationsQuery);
if ($reservationsStmt) {
    $reservationsStmt->bind_param("i", $customerID);
    $reservationsStmt->execute();
    $reservationsResult = $reservationsStmt->get_result();
} else {
    echo "Error preparing statement for reservations: " . $mysqli->error;
}

// Fetch orders
$ordersQuery = "SELECT * FROM orders WHERE CustomerID = ?";
$ordersStmt = $mysqli->prepare($ordersQuery);
if ($ordersStmt) {
    $ordersStmt->bind_param("i", $customerID);
    $ordersStmt->execute();
    $ordersResult = $ordersStmt->get_result();
} else {
    echo "Error preparing statement for orders: " . $mysqli->error;
}

// Fetch payments
$paymentsQuery = "SELECT * FROM payments WHERE OrderID IN (SELECT OrderID FROM orders WHERE CustomerID = ?)";
$paymentsStmt = $mysqli->prepare($paymentsQuery);
if ($paymentsStmt) {
    $paymentsStmt->bind_param("i", $customerID);
    $paymentsStmt->execute();
    $paymentsResult = $paymentsStmt->get_result();
} else {
    echo "Error preparing statement for payments: " . $mysqli->error;
}
?>
