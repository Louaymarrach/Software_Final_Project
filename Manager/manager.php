<?php
session_start();

// Define your database credentials as constants
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'undp_db');

// Establish a connection to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- PHP FORM HANDLING (Moved to top to prevent header errors) ---

// 1. Hire Secretary Logic
if (isset($_POST['hire'])) {
    $first_name = strtolower($_POST['firstName']);
    $last_name = strtolower($_POST['lastName']);
    $phone = strtolower($_POST['phone']);

    $check = mysqli_query($conn, "SELECT * FROM secretory WHERE 
                    LOWER(first_name)='$first_name' AND 
                    LOWER(last_name)='$last_name' AND 
                    LOWER(phone_number)='$phone'");

    if (mysqli_num_rows($check) > 0) {
        $_SESSION['msg'] = "Secretary already exists!";
        $_SESSION['msg_type'] = "error";
    } else {
        // Assuming login_user is set in session from login page
        $managerId = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : 0; 
        
        $sql2 = "INSERT INTO secretory (first_name, last_name, phone_number, managerID) 
                 VALUES ('$first_name', '$last_name', '$phone', '$managerId')";

        if (mysqli_query($conn, $sql2)) {
            $_SESSION['msg'] = "Secretary successfully hired!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "Error: " . mysqli_error($conn);
            $_SESSION['msg_type'] = "error";
        }
    }
    // Refresh to clear post data
    header("location: manager.php");
    exit();
}

// 2. Sponsorship Logic
if (isset($_POST['signSponsorship'])) {
    $companyName = strtolower($_POST['companyName']);
    $amount = strtolower($_POST['amount']);
    $startDate = strtolower($_POST['startDate']);
    $endDate = strtolower($_POST['endDate']);

    $check = mysqli_query($conn, "SELECT * FROM sponsorship WHERE
                  LOWER(Company_name)='$companyName' AND
                  LOWER(budget)='$amount' AND
                  LOWER(start_Date)='$startDate' AND 
                  LOWER(end_Date)='$endDate'");

    if (mysqli_num_rows($check) > 0) {
        $_SESSION['msg'] = "Sponsorship already signed!";
        $_SESSION['msg_type'] = "error";
    } else {
        $managerId = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : 0;
        
        $sql3 = "INSERT INTO sponsorship (Company_name, budget, start_Date, end_Date, managerID)
                 VALUES ('$companyName', '$amount', '$startDate', '$endDate', '$managerId')";

        if (mysqli_query($conn, $sql3)) {
            $_SESSION['msg'] = "Sponsorship signed successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "Error: " . mysqli_error($conn);
            $_SESSION['msg_type'] = "error";
        }
    }
    header("location: manager.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    
    <link rel="stylesheet" href="manager.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="main-wrapper">
    <div class="dashboard-container">
        
        <header class="dashboard-header">
            <h1><i class="fa-solid fa-user-tie"></i> Manager Dashboard</h1>
            <p>Oversee operations, hire staff, and manage sponsorships.</p>
        </header>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert <?php echo $_SESSION['msg_type']; ?>">
                <?php 
                    echo $_SESSION['msg']; 
                    unset($_SESSION['msg']); 
                    unset($_SESSION['msg_type']);
                ?>
            </div>
        <?php endif; ?>

        <div class="action-tabs">
            <button id="toggleSecretary" class="tab-btn active">
                <i class="fa-solid fa-user-plus"></i> Hire Secretary
            </button>
            <button id="toggleSponsorship" class="tab-btn">
                <i class="fa-solid fa-file-signature"></i> Sign Sponsorship
            </button>
        </div>

        <div class="content-area">
            
            <div id="secretarySection" class="form-card active-section">
                
                <div class="card-header">
                    <h2>Hire New Secretary</h2>
                    <p>Enter the candidate's details below.</p>
                </div>
                <form action="" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="firstName" placeholder="e.g. Sarah" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastName" placeholder="e.g. Connor" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="+1 234 567 890" required>
                    </div>
                    <button type="submit" name="hire" class="btn-submit">Confirm Hire</button>
                </form>

                <div class="divider" style="height: 1px; background: #e5e7eb; margin: 40px 0;"></div>

                <div class="card-header">
                    <h2>Current Secretaries</h2>
                    <p>All active secretaries.</p>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql_sec = "SELECT * FROM secretory";
                        $result_sec = $conn->query($sql_sec);

                        if ($result_sec->num_rows > 0) {
                            while($row = $result_sec->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><b>#" . $row["secretoryID"] . "</b></td>";
                                echo "<td><div class='user-info'><i class='fa-solid fa-user' style='color:#006eb6;'></i> " . ucfirst($row["first_name"]) . " " . ucfirst($row["last_name"]) . "</div></td>";
                                echo "<td>" . $row["phone_number"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; padding: 20px; color: #6b7280;'>No secretaries hired yet.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="sponsorshipSection" class="form-card" style="display: none;">
                
                <div class="card-header">
                    <h2>Sign Sponsorship Deal</h2>
                    <p>Register a new partnership agreement.</p>
                </div>
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="companyName" placeholder="e.g. TechCorp" required>
                    </div>
                    <div class="form-group">
                        <label>Sponsorship Amount ($)</label>
                        <input type="number" name="amount" placeholder="5000" required>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="startDate" required>
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="endDate" required>
                        </div>
                    </div>
                    <button type="submit" name="signSponsorship" class="btn-submit">Sign Agreement</button>
                </form>

                <div class="divider" style="height: 1px; background: #e5e7eb; margin: 40px 0;"></div>

                <div class="card-header">
                    <h2>Active Sponsorships</h2>
                    <p>All signed partnership agreements.</p>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Budget</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql_spon = "SELECT * FROM sponsorship";
                        $result_spon = $conn->query($sql_spon);

                        if ($result_spon->num_rows > 0) {
                            while($row = $result_spon->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><b>" . $row["Company_name"] . "</b></td>";
                                // Add a dollar sign and number format for a clean look
                                echo "<td>$" . number_format($row["budget"]) . "</td>";
                                echo "<td>" . $row["start_date"] . "</td>";
                                echo "<td>" . $row["end_date"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; padding: 20px; color: #6b7280;'>No sponsorships signed yet.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

            <div id="sponsorshipSection" class="form-card" style="display: none;">
                <div class="card-header">
                    <h2>Sign Sponsorship Deal</h2>
                    <p>Register a new partnership agreement.</p>
                </div>
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="companyName" placeholder="e.g. TechCorp" required>
                    </div>
                    <div class="form-group">
                        <label>Sponsorship Amount ($)</label>
                        <input type="number" name="amount" placeholder="5000" required>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="startDate" required>
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="endDate" required>
                        </div>
                    </div>
                    <button type="submit" name="signSponsorship" class="btn-submit">Sign Agreement</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="manager.js"></script>
</body>
</html>