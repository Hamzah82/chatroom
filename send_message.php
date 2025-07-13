<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    die("Please log in to send messages.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username']; // Get username from session
    $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

    if (!empty($message)) {
        $timestamp = date('Y-m-d H:i:s');
        $entry = "{$username}|{$message}|{$timestamp}\n";

        // Create messages.txt if it doesn't exist
        if (!file_exists('messages.txt')) {
            file_put_contents('messages.txt', '');
        }

        // Append message to messages.txt
        if (file_put_contents('messages.txt', $entry, FILE_APPEND | LOCK_EX) !== false) {
            echo 'success';
        } else {
            echo 'error: Could not write to file.';
        }
    } else {
        echo 'error: Message cannot be empty.';
    }
} else {
    echo 'error: Invalid request method.';
}
?>