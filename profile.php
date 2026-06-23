<?php
include 'includes/config.php';
include 'includes/header.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$studentId = $_SESSION['student_id'];
$conn = getDBConnection();

$query = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Get registered events
$eventsQuery = "SELECT e.*, r.registration_date, r.attendance_status 
                FROM registrations r 
                JOIN events e ON r.event_id = e.event_id 
                WHERE r.student_id = ? 
                ORDER BY e.event_date";
$eventsStmt = $conn->prepare($eventsQuery);
$eventsStmt->bind_param("i", $studentId);
$eventsStmt->execute();
$eventsResult = $eventsStmt->get_result();

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<div class="profile-container">
    <h1>My Profile</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="profile-info card">
        <h2>Personal Information</h2>
        <p><strong>Name:</strong> <?php echo $student['first_name'] . ' ' . $student['last_name']; ?></p>
        <p><strong>Email:</strong> <?php echo $student['email']; ?></p>
        <p><strong>Phone:</strong> <?php echo $student['phone'] ?? 'Not provided'; ?></p>
        <p><strong>Institution:</strong> <?php echo $student['institution'] ?? 'Not provided'; ?></p>
        <p><strong>Course:</strong> <?php echo $student['course'] ?? 'Not provided'; ?></p>
    </div>
    
    <div class="my-events">
        <h2>My Registered Events</h2>
        <?php if ($eventsResult->num_rows > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Status</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = $eventsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $event['event_title']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                <td><?php echo $event['venue']; ?></td>
                                <td><?php echo ucfirst($event['attendance_status']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($event['registration_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>You haven't registered for any events yet.</p>
            <a href="events.php" class="btn btn-primary">Browse Events</a>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>