<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    die("Please log in to send messages.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username']; // Get username from session
    $message_content = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

    if (!empty($message_content)) {
        $timestamp = date('Y-m-d H:i:s');
        $message_id = uniqid(); // Generate a unique ID for the message

        $message_data = [
            'id' => $message_id,
            'username' => $username,
            'message' => $message_content,
            'timestamp' => $timestamp,
            'deleted' => false
        ];

        // Read existing messages
        $messages = [];
        if (file_exists('messages.txt')) {
            $file_content = file_get_contents('messages.txt');
            $lines = explode("\n", $file_content);
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $messages[] = json_decode($line, true);
                }
            }
        }

        // Add new message
        $messages[] = $message_data;

        // Write all messages back to the file
        $file_write_success = true;
        $file_handle = fopen('messages.txt', 'w');
        if ($file_handle) {
            foreach ($messages as $msg) {
                if (fwrite($file_handle, json_encode($msg) . "\n") === false) {
                    $file_write_success = false;
                    break;
                }
            }
            fclose($file_handle);
        } else {
            $file_write_success = false;
        }

        if ($file_write_success) {
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