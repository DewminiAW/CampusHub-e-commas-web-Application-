<?php
include 'includes/config.php';
include 'includes/header.php';

$conn = getDBConnection();

$category = $_GET['category'] ?? '';
$query = "SELECT * FROM events WHERE 1=1";
if ($category) {
    $query .= " AND category = ?";
}
$query .= " ORDER BY event_date";

$stmt = $conn->prepare($query);
if ($category) {
    $stmt->bind_param("s", $category);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="page-header">
    <h1>Events</h1>
    <p>Discover and register for upcoming events</p>
</div>

<div class="filters">
    <form method="GET" action="">
        <select name="category" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <option value="Technology" <?php echo $category === 'Technology' ? 'selected' : ''; ?>>Technology</option>
            <option value="Sports" <?php echo $category === 'Sports' ? 'selected' : ''; ?>>Sports</option>
            <option value="Cultural" <?php echo $category === 'Cultural' ? 'selected' : ''; ?>>Cultural</option>
            <option value="Workshop" <?php echo $category === 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
        </select>
    </form>
</div>

<div class="card-grid">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($event = $result->fetch_assoc()): ?>
            <div class="event-card">
                <?php if ($event['image_path']): ?>
                    <img src="<?php echo $event['image_path']; ?>" alt="<?php echo $event['event_title']; ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/400x200/2c3e7a/ffffff?text=Event" alt="Event Image">
                <?php endif; ?>
                <div class="event-info">
                    <h3><?php echo $event['event_title']; ?></h3>
                    <p><?php echo substr($event['event_description'], 0, 120); ?>...</p>
                    <div class="event-meta">
                        <span>📅 <?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                        <span>🕐 <?php echo date('h:i A', strtotime($event['event_time'])); ?></span>
                        <span>📍 <?php echo $event['venue']; ?></span>
                        <span>🏷️ <?php echo $event['category']; ?></span>
                    </div>
                    <a href="event_details.php?id=<?php echo $event['event_id']; ?>" class="btn btn-primary">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No events found.</p>
    <?php endif; ?>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>