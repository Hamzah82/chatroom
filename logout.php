<?php
session_start();

$users = json_decode(file_get_contents('users.json'), true);

foreach ($users as &$user) {
    if ($user['username'] === $_SESSION['username']) {
        $user['last_active'] = 0;
        break;
    }
}

file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));

$_SESSION = [];
session_destroy();

header("location: login.php");
exit;
?>