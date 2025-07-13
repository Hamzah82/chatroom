<?php
$users = json_decode(file_get_contents('users.json'), true);

$online_users = 0;
foreach ($users as $user) {
    // Consider a user online if their last_active timestamp is within the last 60 seconds
    $online_threshold = time() - 60; // 60 seconds ago
    if (isset($user['last_active']) && $user['last_active'] >= $online_threshold) {
        $online_users++;
    }
}

echo $online_users;
?>