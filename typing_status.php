<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_POST['is_typing'])) {
    $username = $_SESSION['username'];
    $is_typing = $_POST['is_typing'] === 'true' ? true : false;

    $users = json_decode(file_get_contents('users.json'), true);

    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            $user['is_typing'] = $is_typing;
            break;
        }
    }
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
}
?>