<?php
session_start();
include '../db.php'; // Update this path to your database connection file

if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php"); // Redirect if not admin
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_staff'])) {
        // Add staff
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $role = $_POST['role'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO Staff (FirstName, LastName, Role, Email, PhoneNumber, Password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $firstName, $lastName, $role, $email, $phone, $password);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Admin Dashboard</h2>
        
        <!-- Add Staff Form -->
        <div class="mb-5">
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
        <div class="mb-5">
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
        <h3>Staff List</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Hire Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($staff = $staffResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $staff['StaffID']; ?></td>
                        <td><?php echo $staff['FirstName']; ?></td>
                        <td><?php echo $staff['LastName']; ?></td>
                        <td><?php echo $staff['Role']; ?></td>
                        <td><?php echo $staff['Email']; ?></td>
                        <td><?php echo $staff['PhoneNumber']; ?></td>
                        <td><?php echo $staff['HireDate']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
