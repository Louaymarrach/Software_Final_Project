<?php
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

// Check if the POST parameter exists
if (isset($_POST['bootcampname'])) {
    
    // Sanitize the input to prevent SQL errors (Security best practice)
    $bootcampname = mysqli_real_escape_string($conn, $_POST['bootcampname']);

    // Query the database
    $sql = "SELECT * FROM bootcamp WHERE bootcamp_name = '$bootcampname'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Prepare the response array matches the IDs in List.php
        $response = [
            'location' => $row['location'],
            'startDate' => $row['start_date'],
            'endDate' => $row['end_date'],
            'activity' => $row['Activity']
        ];
        
        // Send back JSON data
        echo json_encode($response);
    } else {
        // Send back an error if nothing found
        echo json_encode(['location' => '', 'startDate' => '', 'endDate' => '', 'activity' => '']);
    }
}
?>