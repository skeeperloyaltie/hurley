<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include 'config.php'; // Update with your actual connection file

// Check user session
session_start();
$customerID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // Ensure customer ID is set when user logs in

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Make reservation
    if (isset($_POST['make_reservation'])) {
        $numberOfGuests = $_POST['number_of_guests'];
        $specialRequests = $_POST['special_requests'];

        if (empty($numberOfGuests)) {
            echo "Number of guests is required.";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO reservations (CustomerID, NumberOfGuests, SpecialRequests) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iis", $customerID, $numberOfGuests, $specialRequests);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $mysqli->error;
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
                        echo "Error preparing statement: " . $mysqli->error;
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
                                echo "Error preparing statement: " . $mysqli->error;
                            }
                        } else {
                            echo "Error preparing statement: " . $mysqli->error;
                        }
                    }
                }
            } else {
                echo "Error preparing statement: " . $mysqli->error;
            }
        } else {
            echo "Error: Menu items and quantities are required.";
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
            } else {
                echo "Error preparing statement: " . $mysqli->error;
            }
        } else {
            echo "Error preparing statement: " . $mysqli->error;
        }
    }
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
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
                <form method="post">
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
                <h3 class="mt-4">Your Reservations</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Reservation ID</th>
                            <th>Number of Guests</th>
                            <th>Special Requests</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($reservation = $reservationsResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['ReservationID']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['NumberOfGuests']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['SpecialRequests']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Place Order Section -->
            <div id="order_section" class="tab-pane fade">
                <h3>Place an Order</h3>
                <form method="post">
                    <div class="form-group">
                        <label for="menu_item_id">Menu Item</label>
                        <select class="form-control" id="menu_item_id" name="menu_item_id[]" multiple required>
                            <?php while ($menuItem = $menuItemsResult->fetch_assoc()) : ?>
                                <option value="<?php echo htmlspecialchars($menuItem['MenuItemID']); ?>">
                                    <?php echo htmlspecialchars($menuItem['Name']); ?> - $<?php echo htmlspecialchars($menuItem['Price']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity[]" required>
                    </div>
                    <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
                </form>
                <h3 class="mt-4">Your Orders</h3>
                <div class="form-group">
                    <label for="order_status_filter">Filter Orders By Status</label>
                    <select class="form-control" id="order_status_filter" onchange="filterOrders(this.value)">
                        <option value="">All</option>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="orders_table_body">
                        <?php while ($order = $ordersResult->fetch_assoc()) : ?>
                            <tr class="order-row" data-status="<?php echo htmlspecialchars($order['Status']); ?>">
                                <td><?php echo htmlspecialchars($order['OrderID']); ?></td>
                                <td>$<?php echo htmlspecialchars($order['TotalAmount']); ?></td>
                                <td><?php echo htmlspecialchars($order['Status']); ?></td>
                                <td>
                                    <?php if (htmlspecialchars($order['Status']) === 'Pending') : ?>
                                        <button class="btn btn-primary btn-sm" onclick="showPaymentForm(<?php echo htmlspecialchars($order['OrderID']); ?>)">Pay Now</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Make Payment Section -->
            <div id="payment_section" class="tab-pane fade">
                <h3>Make a Payment</h3>
                <div id="payment_form_container" style="display:none;">
                    <form method="post" id="payment_form">
                        <input type="hidden" id="order_id" name="order_id">
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="PayPal">PayPal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="transaction_id">Transaction ID</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" required>
                        </div>
                        <button type="submit" name="make_payment" class="btn btn-primary">Make Payment</button>
                    </form>
                </div>
            </div>

            <!-- Profile Section -->
            <div id="profile_section" class="tab-pane fade">
                <h3>Your Profile</h3>
                <?php
                // Fetch customer profile
                $profileQuery = "SELECT * FROM Customers WHERE CustomerID = ?";
                $profileStmt = $mysqli->prepare($profileQuery);
                if ($profileStmt) {
                    $profileStmt->bind_param("i", $customerID);
                    $profileStmt->execute();
                    $profileResult = $profileStmt->get_result();
                    $customerProfile = $profileResult->fetch_assoc();
                } else {
                    echo "Error preparing statement: " . $mysqli->error;
                }
                ?>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($customerProfile['FirstName']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($customerProfile['LastName']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customerProfile['Email']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($customerProfile['Username']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($customerProfile['PhoneNumber']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($customerProfile['Address']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($customerProfile['City']); ?></p>
                <p><strong>State:</strong> <?php echo htmlspecialchars($customerProfile['State']); ?></p>
                <p><strong>Zip Code:</strong> <?php echo htmlspecialchars($customerProfile['ZipCode']); ?></p>
            </div>
        </div>
    </div>

    <script>
        function filterOrders(status) {
            const rows = document.querySelectorAll('.order-row');
            rows.forEach(row => {
                if (status === "" || row.dataset.status === status) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        function showPaymentForm(orderID) {
            document.getElementById('order_id').value = orderID;
            document.getElementById('payment_form_container').style.display = 'block';
        }
    </script>
</body>
</html>

<?php
$mysqli->close();
?>
