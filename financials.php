<?php include 'header.php'; ?>
<main>
    <h2>Financial Summary</h2>
<?php
include 'db.php';

$total_registration = 0;
$total_sponsorship = 0;

try {
    // Calculate Total Registration Fees
    $stmtReg = $dbh->query("SELECT SUM(payment) AS total_registration FROM Attendee");
    $resultReg = $stmtReg->fetch(PDO::FETCH_ASSOC);
    $total_registration = $resultReg['total_registration'] ?? 0;

    // Calculate Total Sponsorship Money
    $stmtSpon = $dbh->query("SELECT SUM(money) AS total_sponsorship FROM Sponsors");
    $resultSpon = $stmtSpon->fetch(PDO::FETCH_ASSOC);
    $total_sponsorship = $resultSpon['total_sponsorship'] ?? 0;

    $grand_total = $total_registration + $total_sponsorship;

    echo "<p>Total Registration Intake: $" . number_format($total_registration, 2) . "</p>";
    echo "<p>Total Sponsorship Intake: $" . number_format($total_sponsorship, 2) . "</p>";
    echo "<hr>";
    echo "<h3>Grand Total Conference Intake: $" . number_format($grand_total, 2) . "</h3>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>Error fetching financial data: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
</main>
