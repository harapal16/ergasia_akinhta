<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ergasia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>DS Estate - Create Listing</title>
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
        <h1>Create Listing</h1>
        <form action="Create.php" method="post" enctype="multipart/form-data">
            <label for="Title">Title:</label>
            <input type="text" id="Title" name="Title" required>
            <label for="Area">Area:</label>
            <input type="text" id="Area" name="Area" required>
            <label for="Rooms">Number of Rooms:</label>
            <input type="number" id="Rooms" name="Rooms" required>
            <label for="Price">Price per Night:</label>
            <input type="number" step="0.01" id="Price" name="Price" required>
            <label for="Photo">Property Image:</label>
            <input type="file" id="Photo" name="Photo" required>
            <button type="submit">Create Listing</button>
        </form>
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



<?php
session_start();
include 'db.php';
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $Title = $_POST['Title'];
    $Area = $_POST['Area'];
    $Rooms = $_POST['Rooms'];
    $Price = $_POST['Price'];
    $Photo = $_FILES['Photo'];

    $target_dir = "images/";
    $target_file = $target_dir . basename($Photo["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($Photo["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($Photo["tmp_name"], $target_file)) {
            $sql = "INSERT INTO listings (Title, Area, Rooms, Price, Photo) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiis", $Title, $Area, $Rooms, $Price, $target_file);

            if ($stmt->execute()) {
                echo "Listing created successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "File is not an image.";
    }

    $conn->close();

}
?>
