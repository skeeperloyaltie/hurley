<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include 'config.php'; // Update with your actual connection file

// Check user session
session_start();
$customerID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // Ensure customer ID is set when user logs in

$response = ['status' => 'error', 'message' => 'An error occurred.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Make reservation
    if (isset($_POST['make_reservation'])) {
        $numberOfGuests = $_POST['number_of_guests'];
        $specialRequests = $_POST['special_requests'];

        if (empty($numberOfGuests)) {
            $response['message'] = 'Number of guests is required.';
        } else {
            $stmt = $mysqli->prepare("INSERT INTO reservations (CustomerID, NumberOfGuests, SpecialRequests) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iis", $customerID, $numberOfGuests, $specialRequests);
                $stmt->execute();
                $stmt->close();
                $response = ['status' => 'success', 'message' => 'Reservation made successfully.'];
            } else {
                $response['message'] = "Error preparing statement: " . $mysqli->error;
            }
        }
    }

    // Place order
    elseif (isset($_POST['place_order'])) {
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
                        $response['message'] = "Error preparing statement: " . $mysqli->error;
                    }
                }
            }

            // Insert order
            $stmt = $mysqli->prepare("INSERT INTO orders (CustomerID, TotalAmount, Status) VALUES (?, ?, 'Pending')");
            if ($stmt) {
                $stmt->bind_param("id", $customerID, $totalAmount);
                $stmt->execute();
                $orderID = $stmt->insert_id;
                $stmt->close();

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
                                $response['message'] = "Error preparing statement: " . $mysqli->error;
                            }
                        } else {
                            $response['message'] = "Error preparing statement: " . $mysqli->error;
                        }
                    }
                }
                $response = ['status' => 'success', 'message' => 'Order placed successfully.'];
            } else {
                $response['message'] = "Error preparing statement: " . $mysqli->error;
            }
        } else {
            $response['message'] = 'Menu items and quantities are required.';
        }
    }

    // Make payment
    elseif (isset($_POST['make_payment'])) {
        $orderID = $_POST['order_id'];
        $amount = $_POST['amount'];
        $paymentMethod = $_POST['payment_method'];
        $transactionID = $_POST['transaction_id'];

        $stmt = $mysqli->prepare("INSERT INTO payments (OrderID, Amount, PaymentMethod, TransactionID) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("idss", $orderID, $amount, $paymentMethod, $transactionID);
            $stmt->execute();
            $stmt->close();

            // Update order status
            $stmt = $mysqli->prepare("UPDATE orders SET Status = 'Paid' WHERE OrderID = ?");
            if ($stmt) {
                $stmt->bind_param("i", $orderID);
                $stmt->execute();
                $stmt->close();
                $response = ['status' => 'success', 'message' => 'Payment made successfully.'];
            } else {
                $response['message'] = "Error preparing statement: " . $mysqli->error;
            }
        } else {
            $response['message'] = "Error preparing statement: " . $mysqli->error;
        }
    }

    echo json_encode($response);
    exit;
}

// Fetch menu items
$menuItemsQuery = "SELECT MenuItemID, Name, Price FROM menuitems";
$menuItemsResult = $mysqli->query($menuItemsQuery);

if ($menuItemsResult === false) {
    die("Error executing query: " . $mysqli->error);
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Customer Dashboard</h1>
        <!-- Navigation -->
        <ul class="nav nav-pills mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#reservation_section">Reservations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#order_section">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#payment_section">Payments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#profile_section">Profile</a>
            </li>
            <div style="float:right;" class="mb-4">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </ul>

        <div class="tab-content">
            <!-- Make Reservation Section -->
            <div id="reservation_section" class="tab-pane fade show active">
                <h3>Make a Reservation</h3>
                <form id="reservation_form" method="post">
                    <div class="form-group">
                        <label for="number_of_guests">Number of Guests</label>
                        <input type="number" class="form-control" id="number_of_guests" name="number_of_guests" required>
                    </div>
                    <div class="form-group">
                        <label for="special_requests">Special Requests</label>
                        <textarea class="form-control" id="special_requests" name="special_requests"></textarea>
                    </div>
                    <button type="submit" name="make_reservation" class="btn btn-primary">Make Reservation</button>
                </form>
                <h3 class="mt-4">My Reservations</h3>
                <ul class="list-group">
                    <?php while ($reservation = $reservationsResult->fetch_assoc()): ?>
                        <li class="list-group-item">
                            Reservation ID: <?= $reservation['ReservationID']; ?> | Number of Guests: <?= $reservation['NumberOfGuests']; ?> | Special Requests: <?= $reservation['SpecialRequests']; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Place Order Section -->
            <div id="order_section" class="tab-pane fade">
                <h3>Place an Order</h3>
                <form id="order_form" method="post">
                    <div class="form-group">
                        <label for="menu_items">Menu Items</label>
                        <div id="menu_items">
                            <?php while ($menuItem = $menuItemsResult->fetch_assoc()): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="<?= $menuItem['MenuItemID']; ?>" name="menu_item_id[]">
                                    <label class="form-check-label">
                                        <?= htmlspecialchars($menuItem['Name']); ?> - $<?= number_format($menuItem['Price'], 2); ?>
                                    </label>
                                    <input type="number" class="form-control mt-2" name="quantity[]" placeholder="Quantity">
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
                </form>
                <h3 class="mt-4">My Orders</h3>
                <ul class="list-group">
                    <?php while ($order = $ordersResult->fetch_assoc()): ?>
                        <li class="list-group-item">
                            Order ID: <?= $order['OrderID']; ?> | Total Amount: $<?= $order['TotalAmount']; ?> | Status: <?= $order['Status']; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Make Payment Section -->
            <div id="payment_section" class="tab-pane fade">
                <h3>Make a Payment</h3>
                <form id="payment_form" method="post">
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

            <!-- Profile Section -->
            <div id="profile_section" class="tab-pane fade">
                <h3>My Profile</h3>
                <p>Customer ID: <?= $customerID; ?></p>
                <!-- Additional profile details can be added here -->
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Handle reservation form submission
            $('#reservation_form').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('An error occurred.');
                    }
                });
            });

            // Handle order form submission
            $('#order_form').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('An error occurred.');
                    }
                });
            });

            // Handle payment form submission
            $('#payment_form').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('An error occurred.');
                    }
                });
            });
        });
    </script>
</body>
</html>
