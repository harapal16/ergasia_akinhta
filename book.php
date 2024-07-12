<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ergasia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>DS Estate - Book</title>
    <style>
        #step2 { display: none; }
        <?php if (isset($_POST['action']) && $_POST['action'] == 'step2'): ?>
        #step1 { display: none; }
        #step2 { display: block; }
        <?php endif; ?>
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
            if (isset($_COOKIE['Username'])) {
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

    <?php
    include 'db.php';
    $availabilityMessage = '';
    $totalCost = 0;
    $discountPercentage = 0;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'book') {
        $Title = $_POST['Title'];
        $StartDate = $_POST['StartDate'];
        $EndDate = $_POST['EndDate'];
        $totalCost = $_POST['totalCost'];
        $discountPercentage = $_POST['discountPercentage'];

        if (!isset($_COOKIE['Username'])) {
            echo "User not logged in.";
            exit;
        }

        $Username = $_COOKIE['Username'];

        $sql = "INSERT INTO reservations (StartDate, EndDate, Amount, Username, Title) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $StartDate, $EndDate, $totalCost, $Username, $Title);

        if ($stmt->execute()) {
            echo "Booking successful.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'step2') {
        $Title = $_POST['Title'];
        $StartDate = $_POST['StartDate'];
        $EndDate = $_POST['EndDate'];

        $sql = "SELECT * FROM listings WHERE Title=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Title);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            echo "Property not found.";
            exit;
        }

        $property = $result->fetch_assoc();
        $Price = $property['Price'];

        $start = new DateTime($StartDate);
        $end = new DateTime($EndDate);
        $diffDays = $end->diff($start)->format("%a");
        $initialCost = $Price * $diffDays;
        $discountPercentage = rand(10, 30);
        $discount = ($initialCost * $discountPercentage) / 100;
        $totalCost = $initialCost - $discount;
    }
    ?>

    <main>
        <h1>Book Property</h1>
        <div id="booking-details">
        <?php
        session_start();
        if (!isset($_COOKIE['Username'])) {
            header('Location: Login.php');
            exit;
        }
        include 'db.php';
        $Title = $_GET['Title'];
        $Username = $_COOKIE['Username'];
        $sql = "SELECT * FROM users WHERE Username='$Username'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
        $sql = "SELECT * FROM listings WHERE Title='$Title'";
        $result = mysqli_query($conn, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='property-details'>";
            echo "<img src='".$row['Photo']."' alt='Property Image'>";
            echo "<h2>".$row['Title']."</h2>";
            echo "<p>Area: ".$row['Area']."</p>";
            echo "<p>Rooms: ".$row['Rooms']."</p>";
            echo "<p>Price per night: $".$row['Price']."</p>";
            echo "</div>";
        }
        ?>
        </div>

        <?php if (!isset($_POST['action']) || $_POST['action'] == 'step1'): ?>
        <form id="booking-form-step1" action="" method="post">
            <div id="step1">
                <h2>Βήμα 1: Επιλογή Ημερομηνιών</h2>
                <input type="hidden" name="Title" value="<?php echo htmlspecialchars($Title); ?>">
                <input type="hidden" name="action" value="step2">
                <label for="StartDate">Start Date:</label>
                <input type="date" id="StartDate" name="StartDate" required>
                <label for="EndDate">End Date:</label>
                <input type="date" id="EndDate" name="EndDate" required>
                <button type="submit">Συνέχεια</button>
            </div>
        </form>
        <?php endif; ?>

        <?php if (isset($_POST['action']) && $_POST['action'] == 'step2'): ?>
        <form id="booking-form-step2" action="" method="post">
            <div id="step2">
                <h2>Βήμα 2: Στοιχεία Κράτησης</h2>
                <input type="hidden" name="Title" value="<?php echo htmlspecialchars($_POST['Title']); ?>">
                <input type="hidden" name="StartDate" value="<?php echo htmlspecialchars($_POST['StartDate']); ?>">
                <input type="hidden" name="EndDate" value="<?php echo htmlspecialchars($_POST['EndDate']); ?>">
                <input type="hidden" name="totalCost" value="<?php echo htmlspecialchars($totalCost); ?>">
                <input type="hidden" name="discountPercentage" value="<?php echo htmlspecialchars($discountPercentage); ?>">
                <input type="hidden" name="action" value="book">
                <label for="FirstName">Όνομα:</label>
                <input type="text" id="FirstName" name="FirstName" value="<?php echo htmlspecialchars($_COOKIE['FirstName']); ?>" required>
                <label for="LastName">Επώνυμο:</label>
                <input type="text" id="LastName" name="LastName" value="<?php echo htmlspecialchars($_COOKIE['LastName']); ?>" required>
                <label for="Email">Email:</label>
                <input type="email" id="Email" name="Email" value="<?php echo htmlspecialchars($_COOKIE['Email']); ?>" required>
                <p id="totalCost">Τελικό Ποσό Πληρωμής: €<?php echo htmlspecialchars($totalCost); ?> (Έκπτωση: <?php echo htmlspecialchars($discountPercentage); ?>%)</p>
                <button type="submit">Κράτηση</button>
            </div>
        </form>
        <?php endif; ?>
    </main>

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


