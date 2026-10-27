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

// Fetch Logged-in Employee Data
$user_id = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : 0;
$employeeData = null;
$bootcampName = "";
$bootcampData = null;
$volunteers = [];

$sql = "SELECT * FROM employees WHERE employeeID='$user_id'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) == 1) {
    $employeeData = mysqli_fetch_assoc($result);
    $bootcampName = $employeeData['bootcampname'];

    if (!empty($bootcampName)) {
        // Fetch Bootcamp Details
        $sql_bc = "SELECT * FROM bootcamp WHERE bootcamp_name='$bootcampName'";
        $res_bc = mysqli_query($conn, $sql_bc);
        if ($res_bc && mysqli_num_rows($res_bc) == 1) {
            $bootcampData = mysqli_fetch_assoc($res_bc);
        }

        // Fetch Volunteers assigned to this employee's bootcamp
        // We only want volunteers who have been accepted (secretoryID is not null)
        $sql_vol = "SELECT * FROM volunteer WHERE bootcampname='$bootcampName' AND secretoryID IS NOT NULL AND secretoryID != ''";
        $res_vol = mysqli_query($conn, $sql_vol);
        if ($res_vol && mysqli_num_rows($res_vol) > 0) {
            while ($vol = mysqli_fetch_assoc($res_vol)) {
                $volunteers[] = $vol;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    
    <link rel="stylesheet" href="Employees.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="main-wrapper">
    
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="header-content">
                <h1><i class="fa-solid fa-user-tie"></i> Employee Dashboard</h1>
                <p>Welcome back, <?php echo $employeeData ? ucfirst($employeeData['first_name']) : 'Employee'; ?>. Manage your volunteers and bootcamp efficiently.</p>
            </div>
        </header>

        <div class="grid-layout">
            
            <div class="card">
                <div class="card-header">
                    <h2><i class="fa-solid fa-hands-helping"></i> My Volunteers</h2>
                    <span class="badge">Total: <?php echo count($volunteers); ?></span>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($volunteers) > 0): ?>
                                <?php foreach ($volunteers as $v): ?>
                                    <tr>
                                        <td><b>#<?php echo $v['volunteerID']; ?></b></td>
                                        <td><?php echo $v['first_name']; ?></td>
                                        <td><?php echo $v['last_name']; ?></td>
                                        <td><?php echo $v['phone_number']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #6b7280; padding: 20px;">
                                        No volunteers have enrolled in your bootcamp yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><i class="fa-solid fa-chalkboard-user"></i> My Bootcamp Details</h2>
                    <?php if ($bootcampData): ?>
                        <span class="status-tag success">Active</span>
                    <?php else: ?>
                        <span class="status-tag pending">Not Assigned</span>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bootcampData): ?>
                                <tr>
                                    <td><b><?php echo $bootcampName; ?></b></td>
                                    <td><?php echo $bootcampData['location']; ?></td>
                                    <td><?php echo $bootcampData['start_date']; ?></td>
                                    <td><?php echo $bootcampData['end_date']; ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #6b7280; padding: 20px;">
                                        You are not currently assigned to manage a bootcamp.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>