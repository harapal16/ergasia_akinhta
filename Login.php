<?php
session_start();
include 'db.php';

$login_error = '';
$register_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Login'])) {
        $Username = $_POST['Username'];
        $Password = $_POST['Password'];

        $query = "SELECT * FROM users WHERE Username = ? AND Password = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("ss", $Username, $Password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Χρήστης βρέθηκε, δημιουργούμε cookies
                $userData = $result->fetch_assoc();
                setcookie("FirstName", $userData['FirstName'], time() + (86400 * 30), "/"); // Cookie διάρκειας 30 ημερών
                setcookie("LastName", $userData['LastName'], time() + (86400 * 30), "/"); // Cookie διάρκειας 30 ημερών
                setcookie("Email", $userData['Email'], time() + (86400 * 30), "/"); // Cookie διάρκειας 30 ημερών
                setcookie("Username", $userData['Username'], time() + (86400 * 30), "/"); // Cookie διάρκειας 30 ημερών
                $_SESSION['loggedin'] = true;
                header("Location: Feed.php");
                exit;
            } else {
                $login_error = "Invalid username or password.";
            }
        }
        $stmt->close();
    }

    if (isset($_POST['Register'])) {
        $FirstName = $_POST['FirstName'];
        $LastName = $_POST['LastName'];
        $Username = $_POST['Username'];
        $Password = $_POST['Password'];
        $Email = $_POST['Email'];

        if (empty($FirstName) || empty($LastName) || empty($Username) || empty($Password) || empty($Email)) {
            $register_error = "All fields are required.";
        } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $register_error = "Invalid email format.";
        } else {
            $sql = "INSERT INTO users (FirstName, LastName, Username, Password, Email) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $FirstName, $LastName, $Username, $Password, $Email);

            if ($stmt->execute()) {
                $register_success = "Registration successful. You can now log in.";
            } else {
                $register_error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ergasia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>DS Estate - Login/Register</title>
    <style>
        #register-form { display: none; }
    </style>
</head>
<body>
<button class="hamburger-menu" id="hamburger-menu">
    <i class="fas fa-bars"></i>
</button>
    <nav id="navbar">
        <ul>
            <li><a href="Feed.php">Feed</a></li>
            <li><a href="Create.php">Create Listing</a></li>
            <?php
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                echo '<li><a href="Logout_Check.php">Logout</a></li>';
            } else {
                echo '<li><a href="Login.php">Login</a></li>';
            }
            ?>
        </ul>
    </nav>

<script>
    document.getElementById('hamburger-menu').addEventListener('click', function() {
    var navbar = document.getElementById('navbar');
    navbar.classList.toggle('active');
    });
</script>

    <main>
        <h1>Login/Register</h1>
        <div id="auth-forms">
            <div id="login-form">
                <h2>Login</h2>
                <form action="Login.php" method="post">
                    <label for="Username1">Username:</label>
                    <input type="text" id="Username1" name="Username" required>
                    <label for="Password1">Password:</label>
                    <input type="password" id="Password1" name="Password" required>
                    <button type="submit" name="Login">Login</button>
                    <p>Αν δεν έχετε λογαριασμό πατήστε <a href="#" id="show-register">εδώ</a></p>
                </form>
                <?php
                if (!empty($login_error)) {
                    echo '<p style="color:red;">' . htmlspecialchars($login_error) . '</p>';
                }
                ?>
            </div>
            <div id="register-form">
                <h2>Register</h2>
                <form action="Login.php" method="post">
                    <label for="FirstName">First Name:</label>
                    <input type="text" id="FirstName" name="FirstName" required>
                    <label for="LastName">Last Name:</label>
                    <input type="text" id="LastName" name="LastName" required>
                    <label for="Username2">Username:</label>
                    <input type="text" id="Username2" name="Username" required>
                    <label for="Password2">Password:</label>
                    <input type="password" id="Password2" name="Password" required>
                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="Email" required>
                    <button type="submit" name="Register">Register</button>
                    <p>Αν έχετε λογαριασμό πατήστε <a href="#" id="show-login">εδώ</a></p>
                </form>
                <?php
                if (!empty($register_error)) {
                    echo '<p style="color:red;">' . htmlspecialchars($register_error) . '</p>';
                } elseif (!empty($register_success)) {
                    echo '<p style="color:green;">' . htmlspecialchars($register_success) . '</p>';
                }
                ?>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('show-register').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        });

        document.getElementById('show-login').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        });
    </script>

    <footer>
        <div>
            <p>Contact us: <a href="mailto:info@dsestate.com">info@dsestate.com</a></p>
            <p>Phone: <a href="tel:+1234567890">+1234567890</a></p>
        </div>
        <div>
            <iframe src="https://www.google.com/maps/embed?..." width="300" height="200"></iframe>
        </div>
    </footer>
</body>
</html>
