<?php

// Αρχικοποίηση συνεδρίας
session_start();

// Καταστροφή όλων των cookies
if (isset($_COOKIE['FirstName'])) {
    setcookie("FirstName", "", time() - 3600, "/");
}
if (isset($_COOKIE['LastName'])) {
    setcookie("LastName", "", time() - 3600, "/");
}
if (isset($_COOKIE['Email'])) {
    setcookie("Email", "", time() - 3600, "/");
}
if (isset($_COOKIE['Username'])) {
    setcookie("Username", "", time() - 3600, "/");
}

// Καταστροφή συνεδρίας
session_unset();
session_destroy();

// Ανακατεύθυνση στη σελίδα εισόδου
header("Location: Login.php");
exit;
?>