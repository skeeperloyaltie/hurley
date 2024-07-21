<?php
// Include database connection
include '../config.php'; // Update with your actual connection file

// Check user role
session_start();
$userRole = $_SESSION['role']; // Ensure role is set when user logs in

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create menu item (for cooks)
    if (isset($_POST['create_menu_item']) && $userRole == 'Cook') {
        $name = $_POST['item_name'];
        $description = $_POST['item_description'];
        $price = $_POST['item_price'];
        $category = $_POST['item_category'];
        $available = $_POST['item_available'];

        $stmt = $conn->prepare("INSERT INTO menuitems (Name, Description, Price, Category, Available) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $description, $price, $category, $available);
        $stmt->execute();
        $stmt->close();
    }

    // Make reservation (for waiters)
    elseif (isset($_POST['make_reservation']) && $userRole == 'Waiter') {
        $customerID = $_POST['customer_id'];
        $numberOfGuests = $_POST['number_of_guests'];
        $specialRequests = $_POST['special_requests'];

        $stmt = $conn->prepare("INSERT INTO reservations (CustomerID, NumberOfGuests, SpecialRequests) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $customerID, $numberOfGuests, $specialRequests);
        $stmt->execute();
        $stmt->close();
    }

    // Approve/Reject reservation (for managers)
    elseif (isset($_POST['approve_reservation']) && $userRole == 'Manager') {
        $reservationID = $_POST['reservation_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE reservations SET Status = ? WHERE ReservationID = ?");
        $stmt->bind_param("si", $status, $reservationID);
        $stmt->execute();
        $stmt->close();
    }

    // Add new staff member (for managers)
    elseif (isset($_POST['add_staff']) && $userRole == 'Manager') {
        $username = $_POST['staff_username'];
        $email = $_POST['staff_email'];
        $password = password_hash($_POST['staff_password'], PASSWORD_BCRYPT);
        $role = $_POST['staff_role'];

        $stmt = $conn->prepare("INSERT INTO Staff (Username, Email, Password, Role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        $stmt->execute();
        $stmt->close();
    }

    // Update menu item (for managers)
    elseif (isset($_POST['update_menu_item']) && $userRole == 'Manager') {
        $itemID = $_POST['item_id'];
        $name = $_POST['item_name'];
        $description = $_POST['item_description'];
        $price = $_POST['item_price'];
        $category = $_POST['item_category'];
        $available = $_POST['item_available'];

        $stmt = $conn->prepare("UPDATE menuitems SET Name = ?, Description = ?, Price = ?, Category = ?, Available = ? WHERE MenuItemID = ?");
        $stmt->bind_param("sssssi", $name, $description, $price, $category, $available, $itemID);
        $stmt->execute();
        $stmt->close();
    }

    // Delete menu item (for managers)
    elseif (isset($_POST['delete_menu_item']) && $userRole == 'Manager') {
        $itemID = $_POST['item_id'];

        $stmt = $conn->prepare("DELETE FROM menuitems WHERE MenuItemID = ?");
        $stmt->bind_param("i", $itemID);
        $stmt->execute();
        $stmt->close();
    }

    // View sales report (for managers)
    if (isset($_POST['view_sales_report']) && $userRole == 'Manager') {
        $fromDate = $_POST['from_date'];
        $toDate = $_POST['to_date'];

        $stmt = $conn->prepare("SELECT OrderDate AS SaleDate, SUM(TotalAmount) AS Amount FROM orders WHERE OrderDate BETWEEN ? AND ? GROUP BY OrderDate");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $fromDate, $toDate);
        $stmt->execute();
        $salesResult = $stmt->get_result();
        $stmt->close();
    }

    // Manage Orders (for managers)
    if (isset($_POST['update_order_status']) && $userRole == 'Manager') {
        $orderID = $_POST['order_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE orders SET Status = ? WHERE OrderID = ?");
        $stmt->bind_param("si", $status, $orderID);
        $stmt->execute();
        $stmt->close();
    }

    // Confirm/Cancel Payment (for managers)
    if (isset($_POST['process_payment']) && $userRole == 'Manager') {
        $orderID = $_POST['order_id'];
        $amount = $_POST['amount'];
        $paymentMethod = $_POST['payment_method'];
        $transactionID = $_POST['transaction_id'];

        $stmt = $conn->prepare("INSERT INTO payments (OrderID, Amount, PaymentMethod, TransactionID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $orderID, $amount, $paymentMethod, $transactionID);
        $stmt->execute();
        $stmt->close();

        // Update order status to 'Completed'
        $stmt = $conn->prepare("UPDATE orders SET Status = 'Completed' WHERE OrderID = ?");
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch reservations
$reservationsQuery = "SELECT * FROM reservations";
$reservationsResult = $conn->query($reservationsQuery);

// Fetch menu items for managing
$menuItemsQuery = "SELECT * FROM menuitems";
$menuItemsResult = $conn->query($menuItemsQuery);

// Fetch staff for adding new staff
$staffQuery = "SELECT * FROM Staff";
$staffResult = $conn->query($staffQuery);

// Fetch orders for management
$ordersQuery = "SELECT * FROM orders";
$ordersResult = $conn->query($ordersQuery);

// Fetch payments for management
$paymentsQuery = "SELECT * FROM payments";
$paymentsResult = $conn->query($paymentsQuery);

// Handle sales report display
$salesResult = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container mt-4">
        <h1>Staff Dashboard</h1>
        <!-- Navigation -->
        <ul class="nav nav-pills mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#reservation_section">Reservations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#menu_section">Menu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#orders_section">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#staffs">Staff</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#reports">Reports</a>
            </li>
            <div style="float:right;" class="mb-4">
                <a href="../admin/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </ul>

        <div class="tab-content">
            <!-- Reservations Section -->
            <div id="reservation_section" class="tab-pane fade show active">
                <div class="mb-4">
                    <h3>Reservations</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Reservation ID</th>
                                <th>Customer ID</th>
                                <th>Reservation Date</th>
                                <th>Number of Guests</th>
                                <th>Special Requests</th>
                                <th>Status</th>
                                <?php if ($userRole == 'Manager') {
                                    echo '<th>Action</th>';
                                } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($reservation = $reservationsResult->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $reservation['ReservationID']; ?></td>
                                    <td><?php echo $reservation['CustomerID']; ?></td>
                                    <td><?php echo $reservation['ReservationDate']; ?></td>
                                    <td><?php echo $reservation['NumberOfGuests']; ?></td>
                                    <td><?php echo $reservation['SpecialRequests']; ?></td>
                                    <td><?php echo $reservation['Status']; ?></td>
                                    <?php if ($userRole == 'Manager') : ?>
                                        <td>
                                            <form method="post" action="">
                                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['ReservationID']; ?>">
                                                <select name="status" class="form-control">
                                                    <option value="Approved">Approve</option>
                                                    <option value="Rejected">Reject</option>
                                                </select>
                                                <button type="submit" name="approve_reservation" class="btn btn-primary mt-2">Update Status</button>
                                            </form>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Menu Section -->
            <div id="menu_section" class="tab-pane fade">
                <h3>Menu Management</h3>
                <!-- Add Menu Item Form -->
                <form method="post" action="">
                    <h4>Add New Menu Item</h4>
                    <div class="form-group">
                        <label for="item_name">Name:</label>
                        <input type="text" class="form-control" name="item_name" required>
                    </div>
                    <div class="form-group">
                        <label for="item_description">Description:</label>
                        <textarea class="form-control" name="item_description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="item_price">Price:</label>
                        <input type="number" step="0.01" class="form-control" name="item_price" required>
                    </div>
                    <div class="form-group">
                        <label for="item_category">Category:</label>
                        <input type="text" class="form-control" name="item_category">
                    </div>
                    <div class="form-group">
                        <label for="item_available">Available:</label>
                        <select class="form-control" name="item_available">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <button type="submit" name="create_menu_item" class="btn btn-primary">Add Menu Item</button>
                </form>

                <!-- Update/Delete Menu Item Form -->
                <form method="post" action="" class="mt-4">
                    <h4>Update/Delete Menu Item</h4>
                    <div class="form-group">
                        <label for="item_id">Select Item:</label>
                        <select class="form-control" name="item_id" required>
                            <?php while ($item = $menuItemsResult->fetch_assoc()) : ?>
                                <option value="<?php echo $item['MenuItemID']; ?>"><?php echo $item['Name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item_name">Name:</label>
                        <input type="text" class="form-control" name="item_name">
                    </div>
                    <div class="form-group">
                        <label for="item_description">Description:</label>
                        <textarea class="form-control" name="item_description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="item_price">Price:</label>
                        <input type="number" step="0.01" class="form-control" name="item_price">
                    </div>
                    <div class="form-group">
                        <label for="item_category">Category:</label>
                        <input type="text" class="form-control" name="item_category">
                    </div>
                    <div class="form-group">
                        <label for="item_available">Available:</label>
                        <select class="form-control" name="item_available">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <button type="submit" name="update_menu_item" class="btn btn-warning">Update Menu Item</button>
                    <button type="submit" name="delete_menu_item" class="btn btn-danger">Delete Menu Item</button>
                </form>
            </div>

            <!-- Orders Section -->
            <div id="orders_section" class="tab-pane fade">
                <h3>Manage Orders</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Table Number</th>
                            <th>Order Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $ordersResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $order['OrderID']; ?></td>
                                <td><?php echo $order['TableNumber']; ?></td>
                                <td><?php echo $order['OrderDate']; ?></td>
                                <td><?php echo $order['TotalAmount']; ?></td>
                                <td><?php echo $order['Status']; ?></td>
                                <td>
                                    <form method="post" action="">
                                        <input type="hidden" name="order_id" value="<?php echo $order['OrderID']; ?>">
                                        <select name="status" class="form-control">
                                            <option value="Pending">Pending</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_order_status" class="btn btn-primary mt-2">Update Status</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Process Payment Form -->
                <form method="post" action="" class="mt-4">
                    <h4>Process Payment</h4>
                    <div class="form-group">
                        <label for="order_id">Order ID:</label>
                        <input type="number" class="form-control" name="order_id" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount:</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method:</label>
                        <input type="text" class="form-control" name="payment_method" required>
                    </div>
                    <div class="form-group">
                        <label for="transaction_id">Transaction ID:</label>
                        <input type="text" class="form-control" name="transaction_id" required>
                    </div>
                    <button type="submit" name="process_payment" class="btn btn-success">Process Payment</button>
                </form>
            </div>

            <!-- Staff Section -->
            <div id="staffs" class="tab-pane fade">
                <h3>Manage Staff</h3>
                <form method="post" action="">
                    <h4>Add New Staff Member</h4>
                    <div class="form-group">
                        <label for="staff_username">Username:</label>
                        <input type="text" class="form-control" name="staff_username" required>
                    </div>
                    <div class="form-group">
                        <label for="staff_email">Email:</label>
                        <input type="email" class="form-control" name="staff_email" required>
                    </div>
                    <div class="form-group">
                        <label for="staff_password">Password:</label>
                        <input type="password" class="form-control" name="staff_password" required>
                    </div>
                    <div class="form-group">
                        <label for="staff_role">Role:</label>
                        <select class="form-control" name="staff_role" required>
                            <option value="Manager">Manager</option>
                            <option value="Cook">Cook</option>
                            <option value="Waiter">Waiter</option>
                        </select>
                    </div>
                    <button type="submit" name="add_staff" class="btn btn-primary">Add Staff Member</button>
                </form>
            </div>

            <!-- Reports Section -->
            <div id="reports" class="tab-pane fade">
                <h3>Sales Reports</h3>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="from_date">From Date:</label>
                        <input type="date" class="form-control" name="from_date" required>
                    </div>
                    <div class="form-group">
                        <label for="to_date">To Date:</label>
                        <input type="date" class="form-control" name="to_date" required>
                    </div>
                    <button type="submit" name="view_sales_report" class="btn btn-primary">View Sales Report</button>
                </form>

                <?php if ($salesResult) : ?>
                    <h4 class="mt-4">Sales Report</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Order Date</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($sale = $salesResult->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $sale['OrderID']; ?></td>
                                    <td><?php echo $sale['OrderDate']; ?></td>
                                    <td><?php echo $sale['TotalAmount']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>
    </div>
</body>

</html>