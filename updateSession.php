<?php include 'header.php'; ?>
<main>
    <h2>Update Session Time/Location</h2>
<?php
include 'db.php';
$message = '';
$selected_session = null;

// --- Process Update Request (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sessionID'])) {
    $sessionID = $_POST['sessionID'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $roomNumber = $_POST['roomNumber'];

    // Basic validation (e.g., ensure start is before end)
    if (!empty($start_time) && !empty($end_time) && !empty($roomNumber) && strtotime($start_time) < strtotime($end_time)) {
        try {
            $stmtUpdate = $dbh->prepare("UPDATE Sessions SET start_time = :start_time, end_time = :end_time, roomNumber = :roomNumber WHERE ID = :sessionID");
            $stmtUpdate->bindParam(':start_time', $start_time);
            $stmtUpdate->bindParam(':end_time', $end_time);
            $stmtUpdate->bindParam(':roomNumber', $roomNumber);
            $stmtUpdate->bindParam(':sessionID', $sessionID);

            if ($stmtUpdate->execute()) {
                $message = "<p style='color:green;'>Session updated successfully.</p>";
            } else {
                $message = "<p style='color:red;'>Error updating session.</p>";
            }
        } catch (PDOException $e) {
            $message = "<p style='color:red;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $message = "<p style='color:red;'>Invalid input. Please ensure all fields are filled and start time is before end time.</p>";
        
        $_GET['session_id'] = $sessionID;
    }
    echo $message;
    echo "<hr>";
}

// --- Session Selection Dropdown (GET) ---
?>
    <form method="GET" action="updateSession.php">
        <label for="session_id">Select Session to Update:</label>
        <select name="session_id" id="session_id" required onchange="this.form.submit()"> <!-- Auto-submit on change -->
            <option value="">-- Select Session --</option>
            <?php
            try {
                
                $stmtSessions = $dbh->query("SELECT ID, name, DATE_FORMAT(start_time, '%Y-%m-%d %H:%i') as startTimeFormatted FROM Sessions ORDER BY start_time, name");
                while ($session = $stmtSessions->fetch(PDO::FETCH_ASSOC)) {
                    
                    $selected_attr = (isset($_GET['session_id']) && $_GET['session_id'] == $session['ID']) ? ' selected' : '';
                    echo "<option value=\"" . $session['ID'] . "\"" . $selected_attr . ">" . htmlspecialchars($session['name']) . " (" . $session['startTimeFormatted'] . ")" . "</option>";
                }
            } catch (PDOException $e) {
                echo "<option value=''>Error fetching sessions</option>";
            }
            ?>
        </select>
        
    </form>
    <br>
<?php
// --- Display Edit Form (if session selected via GET) ---
if (isset($_GET['session_id']) && !empty($_GET['session_id'])) {
    $selected_session_id = $_GET['session_id'];

    try {
        // Fetch details for selected session
        $stmtDetails = $dbh->prepare("SELECT ID, name, start_time, end_time, roomNumber FROM Sessions WHERE ID = :id");
        $stmtDetails->bindParam(':id', $selected_session_id);
        $stmtDetails->execute();
        $selected_session = $stmtDetails->fetch(PDO::FETCH_ASSOC);

        if ($selected_session) {
            // Format datetime for input fields
            $start_time_formatted = date('Y-m-d\TH:i', strtotime($selected_session['start_time']));
            $end_time_formatted = date('Y-m-d\TH:i', strtotime($selected_session['end_time']));
?>
            <h3>Update Details for: <?php echo htmlspecialchars($selected_session['name']); ?></h3>
            <form method="POST" action="updateSession.php">
                <input type="hidden" name="sessionID" value="<?php echo $selected_session['ID']; ?>">

                <label for="start_time">Start Time:</label>
                <input type="datetime-local" id="start_time" name="start_time" value="<?php echo $start_time_formatted; ?>" required><br>

                <label for="end_time">End Time:</label>
                <input type="datetime-local" id="end_time" name="end_time" value="<?php echo $end_time_formatted; ?>" required><br>

                <label for="roomNumber">Location (Room):</label>
                <select name="roomNumber" id="roomNumber" required>
                    <option value="">-- Select Room --</option>
                    <?php
                    try {
                        
                        $stmtRooms = $dbh->query("SELECT roomID, location FROM SessionTalkRoom ORDER BY location");
                        while ($room = $stmtRooms->fetch(PDO::FETCH_ASSOC)) {
                            
                            $selected_attr = ($room['roomID'] == $selected_session['roomNumber']) ? ' selected' : '';
                            echo "<option value=\"" . $room['roomID'] . "\"" . $selected_attr . ">" . htmlspecialchars($room['location']) . "</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<option value=''>Error fetching rooms</option>";
                    }
                    ?>
                </select><br>

                <input type="submit" value="Update Session Details">
            </form>
<?php
        } else {
            echo "<p style='color:red;'>Selected session not found.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error fetching session details: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
</main>
