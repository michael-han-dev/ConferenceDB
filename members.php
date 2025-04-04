<?php include 'header.php'; ?>
<main>
  <h2>Committee Members</h2>
  <form method="GET" action="members.php">
    <label for="committee">Select Committee:</label>
    <select name="committee" id="committee">
      <option value="Logistics">Logistics</option>
      <option value="Sponsorship">Sponsorship</option>
      <option value="Technology">Technology</option>
      <option value="Hospitality">Hospitality</option>
      <option value="Marketing">Marketing</option>
      <option value="Operations">Operations</option>
      <option value="Registration">Registration</option>
      <option value="Security">Security</option>
    </select>
    <input type="submit" value="Show Members">
  </form>
<?php
if (isset($_GET['committee'])) {
    include 'db.php';
    $committee = $_GET['committee'];
    // Fetch members for the selected committee
    $stmt = $dbh->prepare("SELECT m.memberID, m.fname, m.lname, m.email 
                           FROM Members m 
                           JOIN CommitteeMembers cm ON m.memberID = cm.memID 
                           WHERE cm.committeeID = :committee");
    $stmt->bindParam(':committee', $committee);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
      echo "<table><thead><tr><th>ID</th><th>Name</th><th>Email</th></tr></thead><tbody>";
      foreach ($results as $row) {
        echo "<tr><td>" . htmlspecialchars($row['memberID']) . "</td><td>" . htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['lname']) . "</td><td>" . htmlspecialchars($row['email']) . "</td></tr>";
      }
      echo "</tbody></table>";
    } else {
      echo "<p>No members found for this committee.</p>";
    }
}
?>
</main>
</div>
</body>
</html>
