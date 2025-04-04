<?php include 'header.php'; ?>
<main>
  <h2>Students in a Hotel Room</h2>
  <form method="GET" action="roomStudents.php">
    <label for="room">Select Hotel Room:</label>
    <select name="room" id="room">
      <option value="101">101</option>
      <option value="102">102</option>
      <option value="103">103</option>
      <option value="104">104</option>
      <option value="105">105</option>
      <option value="106">106</option>
      <option value="107">107</option>
      <option value="108">108</option>
    </select>
    <input type="submit" value="Show Students">
  </form>
<?php
if (isset($_GET['room'])) {
    include 'db.php';
    $room = $_GET['room'];
    
    $stmt = $dbh->prepare("SELECT attendeeID, fname, lname FROM Attendee WHERE hotelRoom = :room");
    $stmt->bindParam(':room', $room);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($students) {
      echo "<table><thead><tr><th>ID</th><th>Name</th></tr></thead><tbody>";
      foreach ($students as $student) {
        echo "<tr><td>" . htmlspecialchars($student['attendeeID']) . "</td><td>" . htmlspecialchars($student['fname']) . " " . htmlspecialchars($student['lname']) . "</td></tr>";
      }
      echo "</tbody></table>";
    } else {
      echo "<p>No students found in this room.</p>";
    }
}
?>
</main>
</div>
</body>
</html>
