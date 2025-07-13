<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $username = $_SESSION['username'];
    $users = json_decode(file_get_contents('users.json'), true);

    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            $user['last_active'] = 0; // Mark as offline immediately
            break;
        }
    }
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
}
?>