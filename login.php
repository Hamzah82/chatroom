<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

$error = '';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users = json_decode(file_get_contents('users.json'), true);

    $login_success = false;
    foreach ($users as &$user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $user['role'] ?? 'user'; // Store role in session, default to 'user'
            $user['last_active'] = time();
            $login_success = true;
            break;
        }
    }

    if($login_success){
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
        header("location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Secure Terminal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        SECURE TERMINAL ACCESS
    </header>

    <div class="container">
        <div class="form-wrapper">
            <h2>User Login</h2>
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
                    <input type="submit" class="btn" value="Login">
                </div>
                <div class="form-footer">
                    <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
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