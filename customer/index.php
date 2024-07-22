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
                    $_SESSION['message'] = 'Reservation made successfully.';
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
                                    $stmt->execute();
                                    $stmt->close();
                                } else {
                                    $_SESSION['message'] = "Error preparing statement: " . $mysqli->error;
                                    $_SESSION['message_type'] = 'error';
                                }
                            } else {
                                $_SESSION['message'] = "Error preparing statement: " . $mysqli->error;
                                $_SESSION['message_type'] = 'error';
                            }
                        }
                    }
                    $_SESSION['message'] = 'Order placed successfully.';
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
                        $_SESSION['message'] = 'Payment made successfully.';
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

    // Redirect to the same page to show the message
    header("Location: customer_dashboard.php");
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
    echo "Error preparing statement: " . $mysqli->error;
}

// Fetch orders
$ordersQuery = "SELECT * FROM orders WHERE CustomerID = ?";
$ordersStmt = $mysqli->prepare($ordersQuery);
if ($ordersStmt) {
    $ordersStmt->bind_param("i", $customerID);
    $ordersStmt->execute();
    $ordersResult = $ordersStmt->get_result();
} else {
    echo "Error preparing statement: " . $mysqli->error;
}

// Fetch payments
$paymentsQuery = "SELECT * FROM payments WHERE OrderID IN (SELECT OrderID FROM orders WHERE CustomerID = ?)";
$paymentsStmt = $mysqli->prepare($paymentsQuery);
if ($paymentsStmt) {
    $paymentsStmt->bind_param("i", $customerID);
    $paymentsStmt->execute();
    $paymentsResult = $paymentsStmt->get_result();
} else {
    echo "Error preparing statement: " . $mysqli->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Customer Dashboard</h1>

        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type'] === 'success' ? 'success' : 'danger'; ?>">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        endif;
        ?>

        <!-- Navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="make-reservation-tab" data-toggle="tab" href="#make-reservation" role="tab" aria-controls="make-reservation" aria-selected="true">Make Reservation</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="place-order-tab" data-toggle="tab" href="#place-order" role="tab" aria-controls="place-order" aria-selected="false">Place Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="make-payment-tab" data-toggle="tab" href="#make-payment" role="tab" aria-controls="make-payment" aria-selected="false">Make Payment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="view-reservations-tab" data-toggle="tab" href="#view-reservations" role="tab" aria-controls="view-reservations" aria-selected="false">View Reservations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="view-orders-tab" data-toggle="tab" href="#view-orders" role="tab" aria-controls="view-orders" aria-selected="false">View Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="view-payments-tab" data-toggle="tab" href="#view-payments" role="tab" aria-controls="view-payments" aria-selected="false">View Payments</a>
            </li>
            <div style="float:right;" class="mb-4">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">
            <!-- Make Reservation -->
            <div class="tab-pane fade show active" id="make-reservation" role="tabpanel" aria-labelledby="make-reservation-tab">
                <form action="customer_dashboard.php" method="POST" class="mt-4">
                    <div class="form-group">
                        <label for="number_of_guests">Number of Guests</label>
                        <input type="number" class="form-control" id="number_of_guests" name="number_of_guests" required>
                    </div>
                    <div class="form-group">
                        <label for="special_requests">Special Requests</label>
                        <textarea class="form-control" id="special_requests" name="special_requests"></textarea>
                    </div>
                    <button type="submit" name="make_reservation" class="btn btn-primary">Submit Reservation</button>
                </form>
            </div>

            <!-- Place Order -->
            <div class="tab-pane fade" id="place-order" role="tabpanel" aria-labelledby="place-order-tab">
                <form action="customer_dashboard.php" method="POST" class="mt-4">
                    <div class="form-group">
                        <label for="menu_item_id">Menu Item</label>
                        <select multiple class="form-control" id="menu_item_id" name="menu_item_id[]">
                            <?php while ($menuItem = $menuItemsResult->fetch_assoc()): ?>
                            <option value="<?php echo $menuItem['MenuItemID']; ?>"><?php echo $menuItem['Name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity[]" required>
                    </div>
                    <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
                </form>
            </div>

            <!-- Make Payment -->
            <div class="tab-pane fade" id="make-payment" role="tabpanel" aria-labelledby="make-payment-tab">
                <form action="customer_dashboard.php" method="POST" class="mt-4">
                    <div class="form-group">
                        <label for="order_id">Order ID</label>
                        <input type="number" class="form-control" id="order_id" name="order_id" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <input type="text" class="form-control" id="payment_method" name="payment_method" required>
                    </div>
                    <div class="form-group">
                        <label for="transaction_id">Transaction ID</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id" required>
                    </div>
                    <button type="submit" name="make_payment" class="btn btn-primary">Make Payment</button>
                </form>
            </div>

            <!-- View Reservations -->
            <div class="tab-pane fade" id="view-reservations" role="tabpanel" aria-labelledby="view-reservations-tab">
                <h3 class="mt-4">Your Reservations</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Number of Guests</th>
                            <th>Special Requests</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($reservation = $reservationsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $reservation['ReservationID']; ?></td>
                            <td><?php echo $reservation['NumberOfGuests']; ?></td>
                            <td><?php echo $reservation['SpecialRequests']; ?></td>
                            <td><?php echo $reservation['ReservationDate']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- View Orders -->
            <div class="tab-pane fade" id="view-orders" role="tabpanel" aria-labelledby="view-orders-tab">
                <h3 class="mt-4">Your Orders</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $ordersResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $order['OrderID']; ?></td>
                            <td><?php echo $order['TotalAmount']; ?></td>
                            <td><?php echo $order['Status']; ?></td>
                            <td><?php echo $order['OrderDate']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- View Payments -->
            <div class="tab-pane fade" id="view-payments" role="tabpanel" aria-labelledby="view-payments-tab">
                <h3 class="mt-4">Your Payments</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Transaction ID</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $paymentsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $payment['PaymentID']; ?></td>
                            <td><?php echo $payment['OrderID']; ?></td>
                            <td><?php echo $payment['Amount']; ?></td>
                            <td><?php echo $payment['PaymentMethod']; ?></td>
                            <td><?php echo $payment['TransactionID']; ?></td>
                            <td><?php echo $payment['PaymentDate']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap and jQuery scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
