<?php include 'header.php'; ?>
<main>
    <h2>Delete Sponsoring Company</h2>
<?php
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['companyName'])) {
    $companyName = $_POST['companyName'];

    // Use a transaction for safe deletion
    $dbh->beginTransaction();
    try {
        // Delete associated sponsor representatives first
        // (This assumes ON DELETE SET NULL for Attendee.sponsorCompany)
        $stmtAttendees = $dbh->prepare("DELETE FROM Attendee WHERE sponsorCompany = :companyName AND attendeeType = 'sponsor_rep'");
        $stmtAttendees->bindParam(':companyName', $companyName);
        $stmtAttendees->execute();

        // Delete the sponsor (should cascade to Jobs, Emails, Advertise)
        $stmtSponsor = $dbh->prepare("DELETE FROM Sponsors WHERE companyName = :companyName");
        $stmtSponsor->bindParam(':companyName', $companyName);
        $deleted = $stmtSponsor->execute();

        if ($deleted && $stmtSponsor->rowCount() > 0) {
            $dbh->commit();
            $message = "<p style='color:green;'>Sponsor '" . htmlspecialchars($companyName) . "' and associated data deleted successfully.</p>";
        } else {
            // Sponsor might not have existed
            $dbh->rollBack();
            $message = "<p style='color:orange;'>Sponsor '" . htmlspecialchars($companyName) . "' not found or could not be deleted.</p>";
        }

    } catch (PDOException $e) {
        $dbh->rollBack();
        $message = "<p style='color:red;'>Database Error during deletion: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo $message;
    echo "<hr>";
}
?>
    <p>Select a sponsor company to delete. This action will also delete associated sponsor representatives, jobs, and ads. This cannot be undone.</p>
    <form method="POST" action="deleteSponsor.php" onsubmit="return confirm('Are you absolutely sure you want to delete this sponsor and all associated data?');">
        <label for="companyName">Select Sponsor:</label>
        <select name="companyName" id="companyName" required>
            <option value="">-- Select Company --</option>
            <?php
            
            try {
                $stmtSponsors = $dbh->query("SELECT companyName FROM Sponsors ORDER BY companyName");
                while ($sponsor = $stmtSponsors->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value=\"" . htmlspecialchars($sponsor['companyName']) . "\">" . htmlspecialchars($sponsor['companyName']) . "</option>";
                }
            } catch (PDOException $e) {
                echo "<option value=''>Error fetching companies</option>";
            }
            ?>
        </select>
        <input type="submit" value="Delete Selected Sponsor" style="color:red; font-weight:bold;">
    </form>
    <img src="images/collision.png" alt="Conference Image" style="max-width:100%; height:auto;">
</main>
