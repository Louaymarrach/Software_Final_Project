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

// Fetch Volunteer Data
$user_id = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : 0;
$bootcampData = null;
$employees = [];

$sql = "SELECT * FROM volunteer WHERE volunteerID='$user_id'";
$query_run = mysqli_query($conn, $sql);

if (mysqli_num_rows($query_run) == 1) {
    $volunteer = mysqli_fetch_assoc($query_run);
    $bootCampName = $volunteer['bootcampname'];

    if ($bootCampName) {
        // Fetch Bootcamp Details
        $sql2 = "SELECT * FROM bootcamp WHERE bootcamp_name='$bootCampName'";
        $res2 = mysqli_query($conn, $sql2);
        
        if (mysqli_num_rows($res2) == 1) {
            $bootcampData = mysqli_fetch_assoc($res2);
            
            // Fetch Employees for this bootcamp
            $sql3 = "SELECT * FROM employees WHERE bootcampname='$bootCampName'";
            $res3 = mysqli_query($conn, $sql3);
            if ($res3 && mysqli_num_rows($res3) > 0) {
                while ($emp = mysqli_fetch_assoc($res3)) {
                    $employees[] = $emp;
                }
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
    <title>Volunteer Dashboard</title>
    
    <link rel="stylesheet" href="Volunteers.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="main-wrapper">
    <div class="dashboard-container">
        
        <div class="logo-header">
            <img src="../media/UNDP-Logo-Blue-Large (1).png" alt="UNDP Logo">
        </div>

        <?php if ($bootcampData): ?>
            <div class="glass-card">
                <div class="card-header">
                    <h2><i class="fa-solid fa-tent"></i> Your Bootcamp</h2>
                    <span class="badge-active">Active</span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>Bootcamp Name</label>
                        <p><?php echo $bootCampName; ?></p>
                    </div>
                    <div class="info-item">
                        <label>Location</label>
                        <p><i class="fa-solid fa-location-dot"></i> <?php echo $bootcampData['location']; ?></p>
                    </div>
                    <div class="info-item">
                        <label>Start Date</label>
                        <p><?php echo $bootcampData['start_date']; ?></p>
                    </div>
                    <div class="info-item">
                        <label>End Date</label>
                        <p><?php echo $bootcampData['end_date']; ?></p>
                    </div>
                    <div class="info-item full-width">
                        <label>Activity Description</label>
                        <p><?php echo $bootcampData['Activity']; ?></p>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="supervisors-section">
                    <h3><i class="fa-solid fa-id-badge"></i> Operating Supervisors</h3>
                    <?php if (count($employees) > 0): ?>
                        <div class="employee-list">
                            <?php foreach ($employees as $emp): ?>
                                <div class="employee-chip">
                                    <i class="fa-regular fa-user"></i>
                                    <span><?php echo $emp['first_name'] . " " . $emp['last_name']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No supervisors assigned yet.</p>
                    <?php endif; ?>
                </div>

                <div class="action-footer">
                    <a href="List.php" class="btn-secondary">Change Bootcamp</a>
                </div>
            </div>

        <?php else: ?>
            <div class="glass-card text-center">
                <div class="empty-state">
                    <i class="fa-solid fa-person-hiking"></i>
                    <h2>You are not enrolled in a bootcamp</h2>
                    <p>Join a program to start your volunteering journey.</p>
                    <a href="List.php" class="btn-primary">Browse Bootcamps</a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>