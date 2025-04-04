<?php include 'header.php'; ?>
<main>
  <h2>Add New Attendee</h2>
  <form method="POST" action="addAttendee.php">
    <label for="fname">First Name:</label>
    <input type="text" name="fname" id="fname" required><br>
    
    <label for="lname">Last Name:</label>
    <input type="text" name="lname" id="lname" required><br>
    
    <label for="attendeeType">Attendee Type:</label>
    <select name="attendeeType" id="attendeeType" required>
      <option value="">-- Select Type --</option>
      <option value="student">Student</option>
      <option value="professional">Professional</option>
      <option value="sponsor_rep">Sponsor Representative</option>
    </select><br>
    
    <label for="attendeeID">Attendee ID:</label>
    <input type="number" name="attendeeID" id="attendeeID" required><br>
    
    <label for="payment">Registration Payment:</label>
    <input type="number" step="0.01" name="payment" id="payment" required><br>
    
    <div id="student_fields">
      <label for="hotelRoom">Hotel Room (if student):</label>
      <input type="number" name="hotelRoom" id="hotelRoom"><br>
    </div>
    
    <div id="sponsor_fields">
      <label for="sponsorCompany">Sponsoring Company (if sponsor rep):</label>
      <select name="sponsorCompany" id="sponsorCompany">
        <option value="">-- Select Company --</option>
        <?php
            
            include_once 'db.php';
            try {
                $stmtSponsors = $dbh->query("SELECT companyName FROM Sponsors ORDER BY companyName");
                while ($sponsor = $stmtSponsors->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value=\"" . htmlspecialchars($sponsor['companyName']) . "\">" . htmlspecialchars($sponsor['companyName']) . "</option>";
                }
            } catch (PDOException $e) {
                echo "<option value=''>Error fetching sponsors</option>";
            }
        ?>
      </select><br>
    </div>
    
    <input type="submit" value="Add Attendee">
  </form>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process form data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $attendeeType = $_POST['attendeeType'];
    $attendeeID = $_POST['attendeeID'];
    $payment = $_POST['payment'];

    $hotelRoom = null;
    if ($attendeeType === 'student' && !empty($_POST['hotelRoom'])) {
        $hotelRoom = $_POST['hotelRoom'];
    } elseif ($attendeeType === 'student' && empty($_POST['hotelRoom'])) {
        // Student without room is allowed
    }

    $sponsorCompany = null;
    if ($attendeeType === 'sponsor_rep') {
        if (!empty($_POST['sponsorCompany'])) {
            $sponsorCompany = $_POST['sponsorCompany'];
        } else {
             echo "<p style='color:red;'>Error: Sponsor representative must be associated with a company.</p>";
             exit();
        }
    }

    try {
        $stmt = $dbh->prepare("INSERT INTO Attendee (fname, lname, attendeeType, attendeeID, hotelRoom, payment, sponsorCompany) VALUES (:fname, :lname, :attendeeType, :attendeeID, :hotelRoom, :payment, :sponsorCompany)");
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':attendeeType', $attendeeType);
        $stmt->bindParam(':attendeeID', $attendeeID);
        $stmt->bindParam(':payment', $payment);
        $stmt->bindParam(':hotelRoom', $hotelRoom);
        $stmt->bindParam(':sponsorCompany', $sponsorCompany);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Attendee added successfully.</p>";
        } else {
            echo "<p style='color:red;'>Error adding attendee (execute failed).</p>";
        }
    } catch (PDOException $e) {
         // Handle potential errors like duplicate ID
         if ($e->errorInfo[1] == 1062) { // MySQL duplicate entry code
            echo "<p style='color:red;'>Error: Attendee ID already exists.</p>";
         } else {
            echo "<p style='color:red;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
         }
    }
}
?>
</main>
</div>
</body>
</html>
