<?php
session_start();
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---------- LOGIN ---------- */
    if (isset($_POST['login'])) {

        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conn->prepare(
            "SELECT id, username, email, password FROM users WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                // ✅ STORE SESSION
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email']    = $user['email'];

                // ✅ REDIRECT TO PROFILE
                header("Location: profile.php");
                exit;

            } else {
                $message = "Incorrect password!";
            }
        } else {
            $message = "Email not registered!";
        }
    }

    /* ---------- SIGNUP ---------- */
    elseif (isset($_POST['signup'])) {

        $username = trim($_POST['username']);
        $email    = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $message = "Email already registered!";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                $message = "Signup successful! You can now login.";
            } else {
                $message = "Signup failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login / Signup</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="login.css">
</head>
<body>

<video autoplay loop muted id="background-video">
    <source src="assets/videos/waiter-serving.mp4" type="video/mp4">
</video>

<header class="navbar">
    <div class="logo">
        <a href="LandingPage.php" style="text-decoration:none; color:inherit;">
            <b>GLOBAL PLATTER</b>
        </a>
    </div>

    <nav class="nav-links">
        <a href="Login.php" class="active">Login/Signup</a>
        <a href="Profile.php">My Profile</a>
        <a href="Chefs.php">Chefs</a>
        <a href="meme_generator.php">Make a Meme</a>
        <a href="About.php">About Us</a>
       
    </nav>
</header>

<div class="home-container">
    <div class="hero">
        <h1>Welcome!</h1>
        <p>Login or Sign Up to continue enjoying Global Platter</p>
        <div class="hero-buttons">
            <button id="login-tab" class="btn">Login</button>
            <button id="signup-tab" class="btn ghost">Sign Up</button>
        </div>
    </div>

    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- LOGIN FORM -->
    <form id="login-form" class="auth-form" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login" class="btn">Login</button>
    </form>

    <!-- SIGNUP FORM -->
    <form id="signup-form" class="auth-form" method="POST" style="display:none;">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="signup" class="btn">Sign Up</button>
    </form>
</div>

<script>
const loginTab = document.getElementById('login-tab');
const signupTab = document.getElementById('signup-tab');
const loginForm = document.getElementById('login-form');
const signupForm = document.getElementById('signup-form');

loginTab.addEventListener('click', () => {
    loginForm.style.display = 'block';
    signupForm.style.display = 'none';
});

signupTab.addEventListener('click', () => {
    signupForm.style.display = 'block';
    loginForm.style.display = 'none';
});
</script>

</body>
</html>
