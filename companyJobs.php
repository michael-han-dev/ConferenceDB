<?php include 'header.php'; ?>
<main>
    <h2>Jobs by Company</h2>
    <form method="GET" action="companyJobs.php">
        <label for="company">Select Company:</label>
        <select name="company" id="company" required>
            <option value="">-- Select Company --</option>
            <?php
            include 'db.php';
            try {
                $stmtSponsors = $dbh->query("SELECT companyName FROM Sponsors ORDER BY companyName");
                while ($sponsor = $stmtSponsors->fetch(PDO::FETCH_ASSOC)) {
                    $selected = (isset($_GET['company']) && $_GET['company'] == $sponsor['companyName']) ? ' selected' : '';
                    echo "<option value=\"" . htmlspecialchars($sponsor['companyName']) . "\"" . $selected . ">" . htmlspecialchars($sponsor['companyName']) . "</option>";
                }
            } catch (PDOException $e) {
                echo "<option value=''>Error fetching companies</option>";
            }
            ?>
        </select>
        <input type="submit" value="Show Jobs">
    </form>

<?php
if (isset($_GET['company'])) {
    $company = $_GET['company'];
    echo "<h3>Jobs Available at " . htmlspecialchars($company) . "</h3>";
    try {
        $stmtJobs = $dbh->prepare("SELECT title, description FROM Jobs WHERE companyName = :company ORDER BY title");
        $stmtJobs->bindParam(':company', $company);
        $stmtJobs->execute();
        $jobs = $stmtJobs->fetchAll(PDO::FETCH_ASSOC);

        if ($jobs) {
            echo "<table><thead><tr><th>Title</th><th>Description</th></tr></thead><tbody>";
            foreach ($jobs as $job) {
                echo "<tr><td>" . htmlspecialchars($job['title']) . "</td><td>" . htmlspecialchars($job['description']) . "</td></tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No jobs found for this company.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error fetching jobs: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
</main>
