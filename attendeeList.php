<?php include 'header.php'; ?>
<main>
    <h2>Attendee Lists</h2>
<?php
include 'db.php';

// Function to display a table of attendees for a given type
function displayAttendees($dbh, $attendeeType, $title, $showCompany = false) {
    echo "<h3>" . htmlspecialchars($title) . "</h3>";
    try {
        $columns = "fname, lname, attendeeID";
        if ($showCompany) {
            $columns .= ", sponsorCompany";
        }
        $sql = "SELECT $columns FROM Attendee WHERE attendeeType = :type ORDER BY ";
        $sql .= ($showCompany ? "sponsorCompany, " : "") . "lname, fname";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':type', $attendeeType);
        $stmt->execute();
        $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($attendees) {
            echo "<table><thead><tr><th>ID</th><th>Name</th>";
            if ($showCompany) {
                echo "<th>Sponsor Company</th>";
            }
            echo "</tr></thead><tbody>";
            foreach ($attendees as $attendee) {
                echo "<tr><td>" . htmlspecialchars($attendee['attendeeID']) . "</td><td>" . htmlspecialchars($attendee['fname']) . " " . htmlspecialchars($attendee['lname']) . "</td>";
                if ($showCompany) {
                    echo "<td>" . htmlspecialchars($attendee['sponsorCompany'] ?? 'N/A') . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No attendees found for this category.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error fetching " . htmlspecialchars($title) . ": " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo "<br>"; // Add some space between tables
}

// Display each category
displayAttendees($dbh, 'student', 'Students');
displayAttendees($dbh, 'professional', 'Professionals');
displayAttendees($dbh, 'sponsor_rep', 'Sponsor Representatives', true);

?>
</main>
