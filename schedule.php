<?php include 'header.php'; ?>
<main>
  <h2>Conference Schedule</h2>
  <form method="GET" action="schedule.php">
    <label for="schedule_date">Select Date:</label>
    <input type="date" name="schedule_date" id="schedule_date" required>
    <input type="submit" value="Show Schedule">
  </form>
<?php
if (isset($_GET['schedule_date'])) {
    include 'db.php';
    $date = $_GET['schedule_date'];
    $stmt = $dbh->prepare("SELECT s.ID, s.name, s.start_time, s.end_time, str.location, sp.fname AS speakerFirst, sp.lname AS speakerLast
                           FROM Sessions s
                           JOIN SessionTalkRoom str ON s.roomNumber = str.roomID
                           JOIN Speaker sp ON s.speaker = sp.speakerID
                           WHERE DATE(s.start_time) = :date");
    $stmt->bindParam(':date', $date);
    $stmt->execute();
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($sessions) {
      echo "<table><thead><tr><th>ID</th><th>Session Name</th><th>Start Time</th><th>End Time</th><th>Location</th><th>Speaker</th></tr></thead><tbody>";
      foreach ($sessions as $session) {
        echo "<tr>
                <td>" . htmlspecialchars($session['ID']) . "</td>
                <td>" . htmlspecialchars($session['name']) . "</td>
                <td>" . htmlspecialchars($session['start_time']) . "</td>
                <td>" . htmlspecialchars($session['end_time']) . "</td>
                <td>" . htmlspecialchars($session['location']) . "</td>
                <td>" . htmlspecialchars($session['speakerFirst']) . " " . htmlspecialchars($session['speakerLast']) . "</td>
              </tr>";
      }
      echo "</tbody></table>";
    } else {
      echo "<p>No sessions scheduled for this date.</p>";
    }
}
?>
</main>
</div>
</body>
</html>
