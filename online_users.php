<?php
session_start();

$users = json_decode(file_get_contents('users.json'), true);

$online_usernames = [];
$online_threshold = time() - 60; // 60 seconds ago

foreach ($users as $user) {
    if (isset($user['last_active']) && $user['last_active'] >= $online_threshold) {
        $online_usernames[] = $user['username'];
    }
}

echo json_encode($online_usernames);
?>