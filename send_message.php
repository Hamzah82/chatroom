<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : 'Anonymous';
    $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

    if (!empty($message)) {
        $timestamp = date('Y-m-d H:i:s');
        $entry = "{$username}|{$message}|{$timestamp}\n";

        // Append message to messages.txt
        // Use FILE_APPEND to add to the end of the file
        // Use LOCK_EX to prevent anyone else from writing to the file at the same time
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