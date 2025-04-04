<?php include 'header.php'; ?>
<main>
    <h2>All Available Jobs</h2>
<?php
include 'db.php';
try {
    $stmtJobs = $dbh->query("SELECT j.title, j.description, s.companyName
                           FROM Jobs j
                           JOIN Sponsors s ON j.companyName = s.companyName
                           ORDER BY s.companyName, j.title");
    $jobs = $stmtJobs->fetchAll(PDO::FETCH_ASSOC);

    if ($jobs) {
        echo "<table><thead><tr><th>Company</th><th>Job Title</th><th>Description</th></tr></thead><tbody>";
        foreach ($jobs as $job) {
            echo "<tr><td>" . htmlspecialchars($job['companyName']) . "</td><td>" . htmlspecialchars($job['title']) . "</td><td>" . htmlspecialchars($job['description']) . "</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No jobs currently available.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Error fetching jobs: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
</main>
