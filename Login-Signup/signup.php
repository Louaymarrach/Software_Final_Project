<?php
// Define your database credentials as constants
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'undp_db');

// Initialize error and success messages
$errorMsg = '';
$successMsg = '';

// Establish a connection to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check the connection
if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
}

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if passwords match
    if ($_POST['password'] !== $_POST['confirm-password']) {
        $errorMsg = "Passwords do not match.";
    } else {
        // Escape user inputs for security
        $firstName = mysqli_real_escape_string($conn, $_POST['first-name']);
        $lastName = mysqli_real_escape_string($conn, $_POST['last-name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $password = mysqli_real_escape_string($conn, $_POST['password']); 
        
        $sql = "INSERT INTO volunteer (first_name, last_name, phone_number, Password) VALUES ('$firstName', '$lastName', '$phone', '$password')";

        // Execute the query
        if ($conn->query($sql) === TRUE) {
            $successMsg = "Signup successful!";
        } else {
            $errorMsg = "ERROR: Could not execute query: " . $conn->error;
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | UNDP</title>
    
    <link rel="stylesheet" href="UN.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="main-wrapper">
    <div class="glass-container">
        
        <div class="auth-header">
            <h2>Create Account</h2>
            <p>Join our volunteer program today.</p>
        </div>

        <?php if (!empty($errorMsg)): ?>
            <div class="alert error">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $errorMsg; ?>
            </div>
        <?php elseif (!empty($successMsg)): ?>
            <div class="alert success">
                <i class="fa-solid fa-check-circle"></i>
                <p><?php echo $successMsg; ?></p>
                <a href="login.php" class="btn-small">Go to Login</a>
            </div>
        <?php else: ?>

        <form class="auth-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            
            <div class="form-row">
                <div class="input-group">
                    <label>First Name</label>
                    <input type="text" name="first-name" placeholder="Jane" required>
                </div>
                <div class="input-group">
                    <label>Last Name</label>
                    <input type="text" name="last-name" placeholder="Doe" required>
                </div>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="+1 234 567 890" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm-password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-primary">Sign Up</button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </div>

        <?php endif; ?>
    </div>
</div>

</body>
</html>