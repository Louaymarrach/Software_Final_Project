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

// --- PHP FORM HANDLING ---

// 1. Accept Volunteer Logic
if (isset($_POST['acceptVolunteer'])) {
    $volunteerId = $_POST['volunteerId'];
    $secretoryId = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : 0; 

    $updateSql = "UPDATE volunteer SET secretoryid='$secretoryId' WHERE volunteerID='$volunteerId'";
    
    if ($conn->query($updateSql) === TRUE) {
        $_SESSION['msg'] = "Volunteer accepted successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Error: " . $conn->error;
        $_SESSION['msg_type'] = "error";
    }
    header("location: Secretary.php");
    exit();
}

// 2. Hire Employee Logic
if (isset($_POST['hireEmp'])) {
    $first_name = strtolower($_POST['firstName']);
    $last_name = strtolower($_POST['lastName']);
    $bootcamp = strtolower($_POST['bootcampname']);
    $branchName = strtolower($_POST['branchName']);
    $phone = strtolower($_POST['phone']);
    $startDate = strtolower($_POST['startDate']);
    $endDate = strtolower($_POST['endDate']);
    $user_id = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : 0;

    $check = mysqli_query($conn, "SELECT * FROM employees WHERE
                    LOWER(first_name)='$first_name' AND
                    LOWER(last_name)='$last_name' AND
                    LOWER(phone_number)='$phone' AND
                    LOWER(branch_name)='$branchName' AND
                    LOWER(bootcampname)='$bootcamp'");

    if (mysqli_num_rows($check) > 0) {
        $_SESSION['msg'] = "Employee already exists!";
        $_SESSION['msg_type'] = "error";
    } else {
        $sql2 = "INSERT INTO employees (first_name, last_name, phone_number, branch_name, bootcampname, secretoryID, start_date, end_date)
                 VALUES ('$first_name', '$last_name', '$phone', '$branchName', '$bootcamp', '$user_id', '$startDate', '$endDate')";

        if (mysqli_query($conn, $sql2)) {
            $_SESSION['msg'] = "Employee successfully hired!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "Error: " . mysqli_error($conn);
            $_SESSION['msg_type'] = "error";
        }
    }
    header("location: Secretary.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretary Dashboard</title>
    
    <link rel="stylesheet" href="Secretary.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="main-wrapper">
    <div class="dashboard-container">
        
        <header class="dashboard-header">
            <h1><i class="fa-solid fa-briefcase"></i> Secretary Dashboard</h1>
            <p>Process applications and manage human resources.</p>
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
            <button id="toggleVolunteers" class="tab-btn active">
                <i class="fa-solid fa-users-viewfinder"></i> Volunteer Apps
            </button>
            <button id="toggleHireEmployees" class="tab-btn">
                <i class="fa-solid fa-user-plus"></i> Hire Employees
            </button>
        </div>

        <div class="content-area">
            
            <div id="volunteersSection" class="form-card active-section">
                
                <div class="card-header">
                    <h2>Pending Applications</h2>
                    <p>Review and accept new volunteers.</p>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT * FROM volunteer WHERE secretoryid IS NULL OR secretoryid = ''";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><b>#" . $row["volunteerID"] . "</b></td>";
                                echo "<td><div class='user-info'><i class='fa-regular fa-user-circle'></i> " . $row["first_name"] . " " . $row["last_name"] . "</div></td>";
                                echo "<td>" . $row["phone_number"] . "</td>";
                                echo "<td style='text-align: right;'>
                                        <form method='post' style='display:inline;'>
                                            <input type='hidden' name='volunteerId' value='" . $row["volunteerID"] . "'>
                                            <button type='submit' name='acceptVolunteer' class='btn-accept'>
                                                <i class='fa-solid fa-check'></i> Accept
                                            </button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; padding: 20px; color: #6b7280;'>No pending applications found.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="divider" style="height: 1px; background: #e5e7eb; margin: 40px 0;"></div>

                <div class="card-header">
                    <h2>Accepted Volunteers</h2>
                    <p>Volunteers currently enrolled in the program.</p>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Bootcamp</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql_acc = "SELECT * FROM volunteer WHERE secretoryid IS NOT NULL AND secretoryid != ''";
                        $result_acc = $conn->query($sql_acc);

                        if ($result_acc->num_rows > 0) {
                            while($row = $result_acc->fetch_assoc()) {
                                $bootcampDisplay = !empty($row["bootcampname"]) ? $row["bootcampname"] : "<span style='color: #9ca3af;'>Not assigned</span>";
                                echo "<tr>";
                                echo "<td><b>#" . $row["volunteerID"] . "</b></td>";
                                echo "<td><div class='user-info'><i class='fa-solid fa-check-circle' style='color:#10b981;'></i> " . $row["first_name"] . " " . $row["last_name"] . "</div></td>";
                                echo "<td>" . $row["phone_number"] . "</td>";
                                echo "<td>" . $bootcampDisplay . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; padding: 20px; color: #6b7280;'>No accepted volunteers yet.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="hireEmployeesSection" class="form-card" style="display: none;">
                
                <div class="card-header">
                    <h2>Onboard New Employee</h2>
                    <p>Enter details to add a new staff member.</p>
                </div>
                <form action="" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastName" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Bootcamp</label>
                            <select name="bootcampname" required>
                                <option value="" disabled selected>Select Bootcamp</option>
                                <?php
                                $sql0 = "SELECT bootcamp_name FROM bootcamp";
                                $res0 = mysqli_query($conn, $sql0);
                                if (mysqli_num_rows($res0) > 0) {
                                    foreach ($res0 as $items) {
                                        echo "<option value='{$items['bootcamp_name']}'>{$items['bootcamp_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Branch Name</label>
                            <select name="branchName" required>
                                <option value="" disabled selected>Select Branch</option>
                                <?php
                                // Fetching branches dynamically is safer than typing!
                                $sql_branch = "SELECT branch_name FROM branch";
                                $res_branch = mysqli_query($conn, $sql_branch);
                                if (mysqli_num_rows($res_branch) > 0) {
                                    foreach ($res_branch as $b) {
                                        echo "<option value='{$b['branch_name']}'>{$b['branch_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" required>
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

                    <button type="submit" name="hireEmp" class="btn-submit">Hire Employee</button>
                </form>

                <div class="divider" style="height: 1px; background: #e5e7eb; margin: 40px 0;"></div>

                <div class="card-header">
                    <h2>Current Employees</h2>
                    <p>All active staff members.</p>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Branch</th>
                                <th>Bootcamp</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql_emp = "SELECT * FROM employees";
                        $result_emp = $conn->query($sql_emp);

                        if ($result_emp->num_rows > 0) {
                            while($row = $result_emp->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><b>#" . $row["employeeID"] . "</b></td>";
                                echo "<td><div class='user-info'><i class='fa-solid fa-user-tie' style='color:#006eb6;'></i> " . $row["first_name"] . " " . $row["last_name"] . "</div></td>";
                                echo "<td>" . $row["branch_name"] . "</td>";
                                echo "<td>" . $row["bootcampname"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; padding: 20px; color: #6b7280;'>No employees hired yet.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

            <div id="hireEmployeesSection" class="form-card" style="display: none;">
                <div class="card-header">
                    <h2>Onboard New Employee</h2>
                    <p>Enter details to add a new staff member.</p>
                </div>
                
                <form action="" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastName" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Bootcamp</label>
                            <select name="bootcampname" required>
                                <option value="" disabled selected>Select Bootcamp</option>
                                <?php
                                $sql0 = "SELECT bootcamp_name FROM bootcamp";
                                $res0 = mysqli_query($conn, $sql0);
                                if (mysqli_num_rows($res0) > 0) {
                                    foreach ($res0 as $items) {
                                        echo "<option value='{$items['bootcamp_name']}'>{$items['bootcamp_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Branch Name</label>
                            <input type="text" name="branchName" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" required>
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

                    <button type="submit" name="hireEmp" class="btn-submit">Hire Employee</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="Secretary.js"></script>
</body>
</html>