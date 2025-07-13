<?php
session_start();

// Debug log function
function debug_log($message) {
    file_put_contents('delete_debug.log', date('Y-m-d H:i:s') . ' - ' . $message . "\n", FILE_APPEND);
}

debug_log('delete_message.php accessed.');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    debug_log('User not logged in.');
    die("Please log in to delete messages.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_user = $_SESSION['username'];
    $message_id_to_delete = isset($_POST['message_id']) ? $_POST['message_id'] : '';

    debug_log("Current User: " . $current_user);
    debug_log("Message ID to Delete: " . $message_id_to_delete);

    if (empty($message_id_to_delete)) {
        debug_log('Error: Message ID is missing.');
        echo 'error: Message ID is missing.';
        exit;
    }

    $messages = [];
    if (file_exists('messages.txt')) {
        $file_content = file_get_contents('messages.txt');
        $lines = explode("\n", $file_content);
        foreach ($lines as $line) {
            if (!empty($line)) {
                $decoded_message = json_decode($line, true);
                if ($decoded_message !== null) {
                    $messages[] = $decoded_message;
                } else {
                    debug_log('Warning: Could not decode JSON line: ' . $line);
                }
            }
        }
    } else {
        debug_log('messages.txt does not exist.');
    }

    $message_found = false;
    foreach ($messages as &$message) {
        debug_log("Comparing: ID='" . $message['id'] . "' (target: '" . $message_id_to_delete . "') | User='" . $message['username'] . "' (target: '" . $current_user . "')");
        if ($message['id'] === $message_id_to_delete && strtolower($message['username']) === strtolower($current_user)) {
            $message['deleted'] = true;
            $message_found = true;
            debug_log('Message found and authorized. Marking as deleted.');
            break;
        }
    }

    if ($message_found) {
        $file_write_success = true;
        $file_handle = fopen('messages.txt', 'w');
        if ($file_handle) {
            foreach ($messages as $msg) {
                if (fwrite($file_handle, json_encode($msg) . "\n") === false) {
                    $file_write_success = false;
                    debug_log('Error writing message to file.');
                    break;
                }
            }
            fclose($file_handle);
        } else {
            $file_write_success = false;
            debug_log('Error opening messages.txt for writing.');
        }

        if ($file_write_success) {
            echo 'success';
            debug_log('Deletion successful.');
        } else {
            echo 'error: Could not write to file.';
            debug_log('Error: Could not write to file after modification.');
        }
    } else {
        echo 'error: Message not found or you are not authorized to delete this message.';
        debug_log('Message not found or unauthorized.');
    }
} else {
    echo 'error: Invalid request method.';
    debug_log('Invalid request method.');
}
?>