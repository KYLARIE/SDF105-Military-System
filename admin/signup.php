<?php
// admin/signup.php
session_start();
require '../config/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if username exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $errors[] = "Username already exists";
    }

    // If no errors, create new admin
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $hashed_password])) {
            $success = "Registration successful! You can now login.";
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Sign Up</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>

<body>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <p><a href="login.php">Go to Login</a></p>
    <?php else: ?>
        <form method="post" action="signup.php">
            <h2>Admin Sign Up</h2>

            <label>Username:</label><br>
            <input type="text" name="username" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <label>Confirm Password:</label><br>
            <input type="password" name="confirm_password" required><br><br>

            <button type="submit">Sign Up</button>
            <a href="login.php" class="button">Back to Login</a>
        </form>
    <?php endif; ?>
</body>

</html>