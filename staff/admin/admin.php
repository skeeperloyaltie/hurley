<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../config.php'; // Make sure this path is correct

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php"); // Redirect if not admin
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_staff'])) {
        // Add staff
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $role = $_POST['role'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $phone = $_POST['phone'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO Staff (FirstName, LastName, Role, Email, Username, PhoneNumber, Password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $firstName, $lastName, $role, $email,  $username, $phone, $password);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_role'])) {
        // Update staff role
        $staffID = $_POST['staff_id'];
        $newRole = $_POST['new_role'];

        $stmt = $conn->prepare("UPDATE Staff SET Role = ? WHERE StaffID = ?");
        $stmt->bind_param("si", $newRole, $staffID);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all staff
$staffResult = $conn->query("SELECT * FROM Staff");

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_staff'])) {
        // Add staff code here...
    } elseif (isset($_POST['update_staff'])) {
        // Update staff
        $staffID = $_POST['staff_id'];
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $role = $_POST['role'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $stmt = $conn->prepare("UPDATE Staff SET FirstName = ?, LastName = ?, Role = ?, Email = ?, Username = ?, PhoneNumber = ? WHERE StaffID = ?");
        $stmt->bind_param("ssssssi", $firstName, $lastName, $role, $email, $phone, $staffID);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_staff'])) {
        // Delete staff
        $staffID = $_POST['staff_id'];

        $stmt = $conn->prepare("DELETE FROM Staff WHERE StaffID = ?");
        $stmt->bind_param("i", $staffID);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['blacklist_staff'])) {
        // Blacklist staff
        $staffID = $_POST['staff_id'];

        $stmt = $conn->prepare("UPDATE Staff SET IsBlacklisted = 1 WHERE StaffID = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $staffID);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_inventory'])) {
        // Add inventory
        $menuItemID = $_POST['menu_item'];
        $quantity = $_POST['quantity'];

        $stmt = $conn->prepare("INSERT INTO inventory (MenuItemID, Quantity) VALUES (?, ?)");
        $stmt->bind_param("ii", $menuItemID, $quantity);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_inventory'])) {
        // Update inventory
        // Code to handle inventory update
    } elseif (isset($_POST['delete_inventory'])) {
        // Delete inventory
        $inventoryID = $_POST['inventory_id'];

        $stmt = $conn->prepare("DELETE FROM inventory WHERE InventoryID = ?");
        $stmt->bind_param("i", $inventoryID);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_reservation'])) {
        // Add reservation
        $customerID = $_POST['customer_id'];
        $numberOfGuests = $_POST['number_of_guests'];
        $specialRequests = $_POST['special_requests'];

        $stmt = $conn->prepare("INSERT INTO reservations (CustomerID, NumberOfGuests, SpecialRequests) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $customerID, $numberOfGuests, $specialRequests);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_reservation'])) {
        // Update reservation
        // Code to handle reservation update
    } elseif (isset($_POST['delete_reservation'])) {
        // Delete reservation
        $reservationID = $_POST['reservation_id'];

        $stmt = $conn->prepare("DELETE FROM reservations WHERE ReservationID = ?");
        $stmt->bind_param("i", $reservationID);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all staff
$staffResult = $conn->query("SELECT * FROM Staff");


$inventoryID = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM inventory WHERE InventoryID = ?");
$stmt->bind_param("i", $inventoryID);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
echo json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .nav-link {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Admin Dashboard</h2>

        <!-- Navigation -->
        <ul class="nav nav-pills mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#add_staff_section">Add Staff</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#update_role_section">Update Staff Role</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#staff_list_section">Staff List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#inventories_section">Inventories</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#reservations_section">Reservations</a>
            </li>
        </ul>
     


        <div class="tab-content">
            <!-- Add Staff Form -->
            <div id="add_staff_section" class="tab-pane fade show active">
                <h3>Add New Staff</h3>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="Cook">Cook</option>
                            <option value="Waiter">Waiter</option>
                            <option value="Manager">Manager</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="username" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="add_staff" class="btn btn-primary">Add Staff</button>
                </form>
            </div>


            <!-- Update Staff Role Form -->
            <div id="update_role_section" class="tab-pane fade">
                <h3>Update Staff Role</h3>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="staff_id">Staff ID</label>
                        <input type="number" class="form-control" id="staff_id" name="staff_id" required>
                    </div>
                    <div class="form-group">
                        <label for="new_role">New Role</label>
                        <select class="form-control" id="new_role" name="new_role" required>
                            <option value="Cook">Cook</option>
                            <option value="Waiter">Waiter</option>
                            <option value="Manager">Manager</option>
                        </select>
                    </div>
                    <button type="submit" name="update_role" class="btn btn-primary">Update Role</button>
                </form>
            </div>

            <!-- Staff List -->
            <div id="staff_list_section" class="tab-pane fade">
                <h3>Staff List</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Role</th>
                            <th>Username</th>
                            <th>IsBlacklisted</th>



                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Hire Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($staff = $staffResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($staff['StaffID']); ?></td>
                                <td><?php echo htmlspecialchars($staff['FirstName']); ?></td>
                                <td><?php echo htmlspecialchars($staff['LastName']); ?></td>
                                <td><?php echo htmlspecialchars($staff['Role']); ?></td>
                                <td><?php echo htmlspecialchars($staff['Username']); ?></td>
                                <td><?php echo htmlspecialchars($staff['IsBlacklisted']); ?></td>

                                <td><?php echo htmlspecialchars($staff['Email']); ?></td>
                                <td><?php echo htmlspecialchars($staff['PhoneNumber']); ?></td>
                                <td><?php echo htmlspecialchars($staff['HireDate']); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="editStaff(<?php echo $staff['StaffID']; ?>)">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteStaff(<?php echo $staff['StaffID']; ?>)">Delete</button>
                                    <button class="btn btn-warning btn-sm" onclick="blacklistStaff(<?php echo $staff['StaffID']; ?>)">Blacklist</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <!-- Inventories Section -->
        <div id="inventories_section" class="tab-pane fade">
            <h3>Manage Inventories</h3>

            <!-- Add Inventory Form -->
            <div class="mb-5">
                <h4>Add New Inventory</h4>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="menu_item">Menu Item</label>
                        <select class="form-control" id="menu_item" name="menu_item" required>
                            <?php
                            // Fetch menu items
                            $menuItems = $conn->query("SELECT MenuItemID, Name FROM menuitems");
                            while ($item = $menuItems->fetch_assoc()) {
                                echo "<option value='{$item['MenuItemID']}'>{$item['Name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    <button type="submit" name="add_inventory" class="btn btn-primary">Add Inventory</button>
                </form>
            </div>

            <!-- Inventory List -->
            <h4>Inventory List</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Inventory ID</th>
                        <th>Menu Item</th>
                        <th>Quantity</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch inventory items
                    $inventoryResult = $conn->query("SELECT i.InventoryID, m.Name, i.Quantity, i.LastUpdated FROM inventory i JOIN menuitems m ON i.MenuItemID = m.MenuItemID");
                    while ($inventory = $inventoryResult->fetch_assoc()) {
                        echo "<tr>
                    <td>{$inventory['InventoryID']}</td>
                    <td>{$inventory['Name']}</td>
                    <td>{$inventory['Quantity']}</td>
                    <td>{$inventory['LastUpdated']}</td>
                    <td>
                        <a href='#' class='btn btn-warning btn-sm' onclick='editInventory({$inventory['InventoryID']})'>Edit</a>
                        <a href='#' class='btn btn-danger btn-sm' onclick='deleteInventory({$inventory['InventoryID']})'>Delete</a>
                    </td>
                </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Reservations Section -->
        <div id="reservations_section" class="tab-pane fade">
            <h3>Manage Reservations</h3>

            <!-- Add Reservation Form -->
            <div class="mb-5">
                <h4>Add New Reservation</h4>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="customer_id">Customer ID</label>
                        <input type="number" class="form-control" id="customer_id" name="customer_id" required>
                    </div>
                    <div class="form-group">
                        <label for="number_of_guests">Number of Guests</label>
                        <input type="number" class="form-control" id="number_of_guests" name="number_of_guests" required>
                    </div>
                    <div class="form-group">
                        <label for="special_requests">Special Requests</label>
                        <textarea class="form-control" id="special_requests" name="special_requests"></textarea>
                    </div>
                    <button type="submit" name="add_reservation" class="btn btn-primary">Add Reservation</button>
                </form>
            </div>

            <!-- Reservation List -->
            <h4>Reservation List</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reservation ID</th>
                        <th>Customer ID</th>
                        <th>Reservation Date</th>
                        <th>Number of Guests</th>
                        <th>Special Requests</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch reservations
                    $reservationResult = $conn->query("SELECT * FROM reservations");
                    while ($reservation = $reservationResult->fetch_assoc()) {
                        echo "<tr>
                    <td>{$reservation['ReservationID']}</td>
                    <td>{$reservation['CustomerID']}</td>
                    <td>{$reservation['ReservationDate']}</td>
                    <td>{$reservation['NumberOfGuests']}</td>
                    <td>{$reservation['SpecialRequests']}</td>
                    <td>{$reservation['Status']}</td>
                    <td>
                        <a href='#' class='btn btn-warning btn-sm' onclick='editReservation({$reservation['ReservationID']})'>Edit</a>
                        <a href='#' class='btn btn-danger btn-sm' onclick='deleteReservation({$reservation['ReservationID']})'>Delete</a>
                    </td>
                </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>






        </div>
        <!-- Edit Staff Modal -->
        <div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="editStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editStaffForm" method="post" action="">
                            <input type="hidden" id="edit_staff_id" name="staff_id">
                            <div class="form-group">
                                <label for="edit_first_name">First Name</label>
                                <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_last_name">Last Name</label>
                                <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_role">Role</label>
                                <select class="form-control" id="edit_role" name="role" required>
                                    <option value="Cook">Cook</option>
                                    <option value="Waiter">Waiter</option>
                                    <option value="Manager">Manager</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_email">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_phone">Phone Number</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone">
                            </div>
                            <button type="submit" name="update_staff" class="btn btn-primary">Update Staff</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Staff Confirmation Modal -->
        <div class="modal fade" id="deleteStaffModal" tabindex="-1" role="dialog" aria-labelledby="deleteStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteStaffModalLabel">Delete Staff</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this staff member?</p>
                    </div>
                    <div class="modal-footer">
                        <form id="deleteStaffForm" method="post" action="">
                            <input type="hidden" id="delete_staff_id" name="staff_id">
                            <button type="submit" name="delete_staff" class="btn btn-danger">Delete</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blacklist Staff Confirmation Modal -->
        <div class="modal fade" id="blacklistStaffModal" tabindex="-1" role="dialog" aria-labelledby="blacklistStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="blacklistStaffModalLabel">Blacklist Staff</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to blacklist this staff member? This will prevent them from logging in.</p>
                    </div>
                    <div class="modal-footer">
                        <form id="blacklistStaffForm" method="post" action="">
                            <input type="hidden" id="blacklist_staff_id" name="staff_id">
                            <button type="submit" name="blacklist_staff" class="btn btn-warning">Blacklist</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Inventory Edit Modal -->
        <div class="modal fade" id="editInventoryModal" tabindex="-1" role="dialog" aria-labelledby="editInventoryModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editInventoryModalLabel">Edit Inventory</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="admin.php">
                            <input type="hidden" id="edit_inventory_id" name="inventory_id">
                            <div class="form-group">
                                <label for="edit_menu_item">Menu Item</label>
                                <select class="form-control" id="edit_menu_item" name="menu_item" required>
                                    <?php
                                    // Fetch menu items for the select input
                                    $menuItems = $conn->query("SELECT MenuItemID, Name FROM menuitems");
                                    while ($item = $menuItems->fetch_assoc()) {
                                        echo "<option value='{$item['MenuItemID']}'>{$item['Name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_quantity">Quantity</label>
                                <input type="number" class="form-control" id="edit_quantity" name="quantity" required>
                            </div>
                            <button type="submit" name="update_inventory" class="btn btn-primary">Update Inventory</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reservation Edit Modal -->
        <div class="modal fade" id="editReservationModal" tabindex="-1" role="dialog" aria-labelledby="editReservationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editReservationModalLabel">Edit Reservation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="admin.php">
                            <input type="hidden" id="edit_reservation_id" name="reservation_id">
                            <div class="form-group">
                                <label for="edit_customer_id">Customer ID</label>
                                <input type="number" class="form-control" id="edit_customer_id" name="customer_id" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_number_of_guests">Number of Guests</label>
                                <input type="number" class="form-control" id="edit_number_of_guests" name="number_of_guests" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_special_requests">Special Requests</label>
                                <textarea class="form-control" id="edit_special_requests" name="special_requests"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="Pending">Pending</option>
                                    <option value="Confirmed">Confirmed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                            <button type="submit" name="update_reservation" class="btn btn-primary">Update Reservation</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        function editStaff(staffID) {
            // Fetch staff details using AJAX and populate the modal
            // Example of setting values directly (you should ideally fetch from server)
            document.getElementById('edit_staff_id').value = staffID;
            // Open the edit modal
            $('#editStaffModal').modal('show');
        }

        function deleteStaff(staffID) {
            document.getElementById('delete_staff_id').value = staffID;
            // Open the delete modal
            $('#deleteStaffModal').modal('show');
        }

        function blacklistStaff(staffID) {
            document.getElementById('blacklist_staff_id').value = staffID;
            // Open the blacklist modal
            $('#blacklistStaffModal').modal('show');
        }

        function editInventory(inventoryID) {
            // Fetch the current data for the inventory item
            fetch('get_inventory.php?id=' + inventoryID)
                .then(response => response.json())
                .then(data => {
                    // Populate the form with current data
                    document.getElementById('edit_inventory_id').value = data.InventoryID;
                    document.getElementById('edit_menu_item').value = data.MenuItemID;
                    document.getElementById('edit_quantity').value = data.Quantity;
                    // Show the modal or form for editing
                    $('#editInventoryModal').modal('show');
                })
                .catch(error => console.error('Error fetching inventory data:', error));
        }

        function deleteInventory(inventoryID) {
            if (confirm('Are you sure you want to delete this inventory item?')) {
                // Send the delete request
                fetch('admin.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            'delete_inventory': true,
                            'inventory_id': inventoryID
                        })
                    })
                    .then(response => response.text())
                    .then(result => {
                        alert('Inventory item deleted successfully.');
                        location.reload(); // Reload the page to reflect changes
                    })
                    .catch(error => console.error('Error deleting inventory:', error));
            }
        }

        function editReservation(reservationID) {
            // Fetch the current data for the reservation item
            fetch('get_reservation.php?id=' + reservationID)
                .then(response => response.json())
                .then(data => {
                    // Populate the form with current data
                    document.getElementById('edit_reservation_id').value = data.ReservationID;
                    document.getElementById('edit_customer_id').value = data.CustomerID;
                    document.getElementById('edit_number_of_guests').value = data.NumberOfGuests;
                    document.getElementById('edit_special_requests').value = data.SpecialRequests;
                    document.getElementById('edit_status').value = data.Status;
                    // Show the modal or form for editing
                    $('#editReservationModal').modal('show');
                })
                .catch(error => console.error('Error fetching reservation data:', error));
        }

        function deleteReservation(reservationID) {
            if (confirm('Are you sure you want to delete this reservation?')) {
                // Send the delete request
                fetch('admin.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            'delete_reservation': true,
                            'reservation_id': reservationID
                        })
                    })
                    .then(response => response.text())
                    .then(result => {
                        alert('Reservation deleted successfully.');
                        location.reload(); // Reload the page to reflect changes
                    })
                    .catch(error => console.error('Error deleting reservation:', error));
            }
        }
    </script>



</body>

</html>