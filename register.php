<?php
session_start();

$error = '';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users = json_decode(file_get_contents('users.json'), true);

    $user_exists = false;
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            $user_exists = true;
            break;
        }
    }

    if ($user_exists) {
        $error = "Username already exists.";
    } else {
        $users[] = ['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)];
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
        header("location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Secure Terminal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        SECURE TERMINAL ACCESS
    </header>

    <div class="container">
        <div class="form-wrapper">
            <h2>Create New Agent ID</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Agent ID</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn" value="Register">
                </div>
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login here</a>.</p>
                </div>
            </form>
            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Secure Terminal. All rights reserved.</p>
    </footer>
</body>
</html>