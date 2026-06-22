<?php
include 'includes/config.php';
include 'includes/header.php';

$eventId = $_GET['id'] ?? 0;
$conn = getDBConnection();

$query = "SELECT * FROM events WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

// Get registration count
$countQuery = "SELECT COUNT(*) as count FROM registrations WHERE event_id = ?";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("i", $eventId);
$countStmt->execute();
$countResult = $countStmt->get_result();
$regCount = $countResult->fetch_assoc()['count'] ?? 0;

if (!$event) {
    echo "<p>Event not found.</p>";
    include 'includes/footer.php';
    exit();
}
?>

<div class="event-detail">
    <div class="event-header">
        <h1><?php echo $event['event_title']; ?></h1>
        <span class="event-status <?php echo $event['status']; ?>"><?php echo ucfirst($event['status']); ?></span>
    </div>
    
    <div class="event-info-grid">
        <div class="event-detail-image">
            <?php if ($event['image_path']): ?>
                <img src="<?php echo $event['image_path']; ?>" alt="<?php echo $event['event_title']; ?>">
            <?php endif; ?>
        </div>
        
        <div class="event-detail-info">
            <p><strong>Description:</strong></p>
            <p><?php echo nl2br($event['event_description']); ?></p>
            
            <div class="detail-meta">
                <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($event['event_date'])); ?></p>
                <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                <p><strong>Venue:</strong> <?php echo $event['venue']; ?></p>
                <p><strong>Category:</strong> <?php echo $event['category']; ?></p>
                <p><strong>Registered:</strong> <?php echo $regCount; ?> / <?php echo $event['max_participants'] ?? 'Unlimited'; ?></p>
            </div>
            
            <?php if (isset($_SESSION['student_id']) && $event['status'] === 'upcoming'): ?>
                <?php
                // Check if already registered
                $checkQuery = "SELECT registration_id FROM registrations WHERE student_id = ? AND event_id = ?";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param("ii", $_SESSION['student_id'], $eventId);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                $alreadyRegistered = $checkResult->num_rows > 0;
                ?>
                
                <?php if ($alreadyRegistered): ?>
                    <div class="alert alert-success">✅ You are already registered for this event!</div>
                <?php elseif ($regCount < $event['max_participants'] || !$event['max_participants']): ?>
                    <form method="POST" action="register_event.php">
                        <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
                        <button type="submit" class="btn">Register Now</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-error">❌ This event is fully booked.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>