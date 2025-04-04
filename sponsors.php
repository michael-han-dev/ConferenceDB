<?php include 'header.php'; ?>
<main>
  <h2>Sponsors</h2>
  <?php
  include 'db.php';
  $stmt = $dbh->prepare("SELECT companyName, money FROM Sponsors");
  $stmt->execute();
  $sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if ($sponsors) {
      echo "<table><thead><tr><th>Company Name</th><th>Sponsorship Amount</th></tr></thead><tbody>";
      foreach ($sponsors as $sponsor) {
          echo "<tr><td>" . htmlspecialchars($sponsor['companyName']) . "</td><td>$" . number_format($sponsor['money'], 2) . "</td></tr>";
      }
      echo "</tbody></table>";
  } else {
      echo "<p>No sponsors found.</p>";
  }
  ?>
</main>
</div>
</body>
</html>
