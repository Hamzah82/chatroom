<?php
session_start();

$typing_users = [];
$users = json_decode(file_get_contents('users.json'), true);

foreach ($users as $user) {
    // Only consider users who are online and typing
    $online_threshold = time() - 60; // Same threshold as online_users.php
    if (isset($user['last_active']) && $user['last_active'] >= $online_threshold && $user['is_typing'] === true && $user['username'] !== $_SESSION['username']) {
        $typing_users[] = $user['username'];
    }
}

echo json_encode($typing_users);
?>