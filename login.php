<!-- 

session_start();
include 'config/config.php'; // Update this path to your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM Customers WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['Password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['CustomerID'];
        $_SESSION['email'] = $user['Email'];

        // Redirect based on the page requesting login
        header("Location: /index.php.php");
        exit();
    } else {
        echo "<p>Invalid email or password.</p>";
    }

    $stmt->close();
}
?> -->

<?php
include("objects/header.php");?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Customer Login</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
        </div>
    </div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
    <?php
include("objects/footer.php");?>