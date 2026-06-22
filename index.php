<?php
include 'includes/config.php';
include 'includes/header.php';

$conn = getDBConnection();

// Get upcoming events
$query = "SELECT * FROM events WHERE status = 'upcoming' ORDER BY event_date LIMIT 3";
$eventsResult = $conn->query($query);

// Get announcements
$annQuery = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3";
$annResult = $conn->query($annQuery);
?>

<main class="flex-grow-1">

<style>
    body {
        margin: 0 !important;
        padding: 0 !important;
    }
    .hero {
        margin-top: 0 !important;
    }
</style>

<div class="hero">
    <h1>Welcome to CampusHub</h1>
    <p>Your gateway to student events, activities, and community engagement</p>
    <a href="events.php" class="btn">View Events</a>
</div>

<section class="features">
    <h2>What We Offer</h2>
    <div class="card-grid">
        <div class="card">
            <h3>📅 Events</h3>
            <p>Discover and register for upcoming events, workshops, and competitions</p>
        </div>
        <div class="card">
            <h3>👥 Community</h3>
            <p>Connect with students from multiple institutions and build your network</p>
        </div>
        <div class="card">
            <h3>📸 Media</h3>
            <p>View event photos and videos shared by the CampusHub community</p>
        </div>
        <div class="card">
            <h3>📢 Announcements</h3>
            <p>Stay updated with the latest news and notifications</p>
        </div>
    </div>
</section>

<section class="upcoming-events">
    <h2>Upcoming Events</h2>
    <div class="card-grid">
        <?php while ($event = $eventsResult->fetch_assoc()): ?>
            <div class="event-card">
                <?php if ($event['image_path']): ?>
                    <img src="<?php echo $event['image_path']; ?>" alt="<?php echo $event['event_title']; ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/400x200/2c3e7a/ffffff?text=Event" alt="Event Image">
                <?php endif; ?>
                <div class="event-info">
                    <h3><?php echo $event['event_title']; ?></h3>
                    <p><?php echo substr($event['event_description'], 0, 100); ?>...</p>
                    <div class="event-meta">
                        <span>📅 <?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                        <span>📍 <?php echo $event['venue']; ?></span>
                    </div>
                    <a href="event_details.php?id=<?php echo $event['event_id']; ?>" class="btn btn-primary">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<section class="announcements">
    <h2>Latest Announcements</h2>
    <div class="card-grid">
        <?php while ($ann = $annResult->fetch_assoc()): ?>
            <div class="card">
                <h3><?php echo $ann['title']; ?></h3>
                <p><?php echo substr($ann['content'], 0, 150); ?>...</p>
                <small>Posted: <?php echo date('M d, Y', strtotime($ann['created_at'])); ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php
$conn->close();
include 'includes/footer.php';
?>