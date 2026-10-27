<?php
session_start();

// Define your database credentials as constants
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'undp_db');

// Establish a connection to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PHP Logic for Apply
$msg = "";
if (isset($_POST['apply'])) {
    $bootcampnameSelected = $_POST['bootcampname'];
    $user_id = $_SESSION['login_user'];
    
    $sql2 = "UPDATE volunteer SET bootcampname='$bootcampnameSelected' WHERE volunteerID=$user_id";
    
    if (mysqli_query($conn, $sql2)) {
        $msg = "success";
    } else {
        $msg = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Bootcamp</title>
    
    <link rel="stylesheet" href="Volunteers.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<div class="main-wrapper">
    <div class="dashboard-container">
        
        <div class="header-simple">
            <a href="Volunteers.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
            <h1>Available Bootcamps</h1>
        </div>

        <?php if ($msg == "success"): ?>
            <div class="glass-card text-center">
                <i class="fa-solid fa-circle-check" style="font-size: 3rem; color: #10b981; margin-bottom: 20px;"></i>
                <h2>Application Successful!</h2>
                <p>You have successfully enrolled in the bootcamp.</p>
                <script>setTimeout(function() { window.location.href='Volunteers.php'; }, 2000);</script>
            </div>
        <?php else: ?>

            <div class="glass-card">
                <div class="card-header">
                    <h2>Select a Program</h2>
                    <p>Choose a bootcamp to view details and apply.</p>
                </div>

                <form action="" method="POST">
                    <div class="form-group">
                        <label>Bootcamp Name</label>
                        <select name="bootcampname" id="bootcampname" required>
                            <option value="">-- Select a Bootcamp --</option>
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

                    <div class="details-box">
                        <h3><i class="fa-solid fa-info-circle"></i> Program Details</h3>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Location</label>
                                <input name="location" id="location" readonly placeholder="Auto-filled">
                            </div>
                            <div class="form-group">
                                <label>Activity</label>
                                <input name="activity" id="activity" readonly placeholder="Auto-filled">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input name="startDate" id="startDate" readonly placeholder="YYYY-MM-DD">
                            </div>
                            <div class="form-group">
                                <label>End Date</label>
                                <input name="endDate" id="endDate" readonly placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="apply" class="btn-primary full-width">
                        Apply for Bootcamp <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>

        <?php endif; ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#bootcampname').change(function () {
            var bootcampname = $(this).val();
            if (bootcampname !== '') {
                $.ajax({
                    url: 'get_bootcamp_info.php',
                    method: 'POST',
                    data: { bootcampname: bootcampname },
                    dataType: 'json',
                    success: function (response) {
                        $('#location').val(response.location);
                        $('#startDate').val(response.startDate);
                        $('#endDate').val(response.endDate);
                        $('#activity').val(response.activity);
                    }
                });
            } else {
                $('#location, #startDate, #endDate, #activity').val('');
            }
        });
    });
</script>

</body>
</html>