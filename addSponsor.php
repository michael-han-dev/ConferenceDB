<?php include 'header.php'; ?>
<main>
    <h2>Add New Sponsoring Company</h2>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php';
    $companyName = $_POST['companyName'];
    $money = $_POST['money'];

    if (!empty($companyName) && is_numeric($money) && $money >= 0) {
        try {
            $stmt = $dbh->prepare("INSERT INTO Sponsors (companyName, money) VALUES (:companyName, :money)");
            $stmt->bindParam(':companyName', $companyName);
            $stmt->bindParam(':money', $money);

            if ($stmt->execute()) {
                echo "<p style='color:green;'>Sponsor '" . htmlspecialchars($companyName) . "' added successfully.</p>";
            } else {
                echo "<p style='color:red;'>Error adding sponsor (execute failed).</p>";
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "<p style='color:red;'>Error: Sponsor company name already exists.</p>";
            } else {
                echo "<p style='color:red;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>Invalid input. Please provide a valid company name and non-negative sponsorship amount.</p>";
    }
    echo "<hr>";
}
?>
    <form method="POST" action="addSponsor.php">
        <label for="companyName">Company Name:</label>
        <input type="text" name="companyName" id="companyName" required><br>

        <label for="money">Sponsorship Amount:</label>
        <input type="number" step="0.01" name="money" id="money" min="0" required><br>

        <input type="submit" value="Add Sponsor">
    </form>
</main>
