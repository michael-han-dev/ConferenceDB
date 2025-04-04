<?php
// db.php - establishes a connection to the conferenceDB database using PDO.
try {
    $dbh = new PDO('mysql:host=localhost;dbname=conferenceDB', 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error!: " . $e->getMessage();
    die();
}
?>
