<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ergasia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>DS Estate - Feed</title>
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

    <main>
        <h1>Available Properties</h1>
        <div id="property-list">
            <?php
            include 'db.php';
            $sql = "SELECT * FROM listings";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='listing'>";
                echo "<img src='".$row['Photo']."' alt='Property Image'>";
                echo "<h2>".$row['Title']."</h2>";
                echo "<p>Area: ".$row['Area']."</p>";
                echo "<p>Rooms: ".$row['Rooms']."</p>";
                echo "<p>Price per night: $".$row['Price']."</p>";
                echo "<a href='Book.php?Title=".$row['Title']."' class='btn'>Book Now</a>";
                echo "</div>";
            }
            ?>
        </div>
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
