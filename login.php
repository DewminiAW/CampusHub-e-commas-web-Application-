<?php
include 'includes/config.php';
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $conn = getDBConnection();
        
        $query = "SELECT student_id, first_name, last_name, email, password, institution FROM students WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // For admin (demo purposes)
            if ($email === 'admin@campushub.com' && $password === 'admin123') {
                $_SESSION['student_id'] = $user['student_id'];
                $_SESSION['student_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = 'admin';
                redirect('admin/dashboard.php');
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['student_id'] = $user['student_id'];
                $_SESSION['student_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = 'student';
                redirect('index.php');
            } else {
                $error = 'Invalid password.';
            }
        } else {
            $error = 'Email not found. Please register.';
        }
        $conn->close();
    }
}
?>

<div class="form-container">
    <h2>Login to CampusHub</h2>
    <p>Welcome back! Login to access your dashboard.</p>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>">
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    
    <p style="margin-top: 1rem;">Don't have an account? <a href="register.php">Register here</a></p>
    <p><small>Demo admin: admin@campushub.com / admin123</small></p>
</div>

<?php include 'includes/footer.php'; ?>