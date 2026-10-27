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
session_start();

function redirectToRolePage($role, $userId) {
    $_SESSION['login_user'] = $userId;
    switch ($role) {
        case 'manager':
            header("location:../Manager/manager.php");
            break;
        case 'secretary':
            header("location:../Secretary/Secretary.php");
            break;
        case 'employee':
            header("location:../Employees/Employees.php");
            break;
        case 'volunteer':
            header("location:../Volunteers/Volunteers.php");
            break;
        default:
            header("location: login.php");
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = mysqli_real_escape_string($conn, $_POST['id']);
    $mypassword = mysqli_real_escape_string($conn, $_POST['password']);
    $error = "Your ID or Password is invalid"; // Default error

    // Check Manager
    if (mysqli_num_rows(mysqli_query($conn, "SELECT managerID FROM manager WHERE managerID='$userid' AND Password='$mypassword'")) == 1) {
        redirectToRolePage('manager', $userid);
    }
    // Check Secretary
    if (mysqli_num_rows(mysqli_query($conn, "SELECT secretoryID FROM secretory WHERE secretoryID='$userid' AND Password='$mypassword'")) == 1) {
        redirectToRolePage('secretary', $userid);
    }
    // Check Employee
    if (mysqli_num_rows(mysqli_query($conn, "SELECT employeeID FROM employees WHERE employeeID='$userid' AND Password='$mypassword'")) == 1) {
        redirectToRolePage('employee', $userid);
    }
    // Check Volunteer
    $vol_query = "SELECT volunteerID, secretoryID FROM volunteer WHERE volunteerID='$userid' AND Password='$mypassword'";
    $vol_result = mysqli_query($conn, $vol_query);
    
    if (mysqli_num_rows($vol_result) == 1) {
        $vol_data = mysqli_fetch_assoc($vol_result);
        
        // Check if secretoryID is NULL or empty
        if (empty($vol_data['secretoryID'])) {
            $error = "Your application is still pending. Please wait for a Secretary to approve your account.";
        } else {
            // Secretary has approved, let them in!
            redirectToRolePage('volunteer', $userid);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNDP Login Page</title>
    <link rel="stylesheet" href="UN.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="scroll-down">UNDP works in about 170 countries and territories, helping to eradicate poverty, reduce inequalities and exclusion, and build resilience so countries can sustain progress. As the UN's development agency, UNDP plays a critical role in helping countries achieve the Sustainable Development Goals.</div>

    <div class="container"></div>
    
    <div class="modal">
      <div class="modal-container">
        <div class="modal-left">
          <h1 class="modal-title">Welcome!</h1>
          <p class="modal-desc">Be an UNDP member, and expand our community!</p>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-block">
                <label for="id" class="input-label">ID</label>
                <input type="text" name="id" id="id" placeholder="Your ID">
            </div>
            <div class="input-block">
                <label for="password" class="input-label">Password</label>
                <input type="password" name="password" id="password" placeholder="Password">
            </div>
            <?php if(isset($error)): ?>
                <p class="error" style="color:red; text-align:center;"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="modal-buttons">
                <a href="" class="">Forgot your password?</a>
                <button type="submit" class="input-button">Login</button>
            </div>
          </form>
          <p class="sign-up">Don't have an account? <a href="./signup.php" id="sign-up-nav-button">Sign Up</a></p>
        </div>
        <div class="modal-right">
          <img src="../media/kaa.jpg" alt="">
        </div>
        <button class="icon-button close-button">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
            <path d="M 9.15625 6.3125 L 6.3125 9.15625 L 22.15625 25 L 6.21875 40.96875 L 9.03125 43.78125 L 25 27.84375 L 40.9375 43.78125 L 43.78125 40.9375 L 27.84375 25 L 43.6875 9.15625 L 40.84375 6.3125 L 25 22.15625 Z"></path>
          </svg>
        </button>
      </div>
      <button class="modal-button">Click here to login</button>
    </div>

    <div class="chat-widget-btn" onclick="toggleChat()">
        <i class="fa-solid fa-robot"></i>
    </div>

    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <span><i class="fa-solid fa-headset"></i> AI Assistant</span>
            <span style="cursor:pointer;" onclick="toggleChat()">✕</span>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message bot">Hello! I am the United Nations Assistant. How can I help you today?</div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="userMsg" placeholder="Type a question..." onkeypress="handleEnter(event)">
            <button onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>

    <script src="UN.js"></script>
    <script>
        function toggleChat() {
            const chat = document.getElementById('chatWindow');
            chat.style.display = chat.style.display === 'flex' ? 'none' : 'flex';
        }

        function handleEnter(e) {
            if (e.key === 'Enter') sendMessage();
        }

        async function sendMessage() {
            const input = document.getElementById('userMsg');
            const text = input.value.trim();
            const chatBox = document.getElementById('chatMessages');

            if (!text) return;

            // 1. User Message
            chatBox.innerHTML += `<div class="message user">${text}</div>`;
            input.value = '';
            chatBox.scrollTop = chatBox.scrollHeight;

            // 2. Loading State
            const loadingId = 'loading-' + Date.now();
            chatBox.innerHTML += `<div class="message bot" id="${loadingId}">Typing...</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                // 3. Connect to Python API
                const response = await fetch('http://127.0.0.1:5000/chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                });
                const data = await response.json();
                
                document.getElementById(loadingId).remove();
                
                if (data.status === 'success') {
                    chatBox.innerHTML += `<div class="message bot">${data.response}</div>`;
                } else {
                    chatBox.innerHTML += `<div class="message bot" style="color:red;">Error: ${data.error}</div>`;
                }
            } catch (error) {
                if(document.getElementById(loadingId)) document.getElementById(loadingId).remove();
                chatBox.innerHTML += `<div class="message bot" style="color:red;">AI is offline. Run 'python api.py'</div>`;
            }
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>

</body>
</html>