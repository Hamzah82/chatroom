<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    die("Please log in to see the messages.");
}

$current_user = $_SESSION['username'];

// Create messages.txt if it doesn't exist
if (!file_exists('messages.txt')) {
    file_put_contents('messages.txt', '');
}

$messages_raw = file_get_contents('messages.txt');
$lines = explode("\n", $messages_raw);

$output = '';
// DEBUG_IDENTIFIER_V1.0
foreach ($lines as $line) {
    $line = trim($line);
    if (!empty($line)) {
        $message_data = json_decode($line, true);
        
        if ($message_data === null) {
            // Skip invalid JSON lines
            continue;
        }

        $message_id = htmlspecialchars($message_data['id']);
        $username = htmlspecialchars($message_data['username']);
        $timestamp = htmlspecialchars($message_data['timestamp']);
        $is_deleted = $message_data['deleted'];
        $message_content = htmlspecialchars($message_data['message']);

        // Determine message type based on current user
        $message_class = (strtolower($username) === strtolower($current_user)) ? 'user' : 'agent';

        // Using explicit concatenation for clarity and to avoid interpolation issues
        $output .= "<div class=\"message-container " . $message_class . "\" data-message-id=\"" . $message_id . "\" data-sender-username=\"" . $username . "\">";
        $output .= "    <div class=\"message-bubble\">";
        $output .= "        <div class=\"message-sender\">" . $username . "</div>";
        if ($is_deleted) {
            $output .= "        <div class=\"message-content deleted-message\">This message has been deleted.</div>";
        } else {
            $output .= "        <div class=\"message-content\">" . $message_content . "</div>";
        }
        $output .= "        <div class=\"message-time\">" . $timestamp . "</div>";
        if (strtolower($username) === strtolower($current_user) && !$is_deleted) {
            $output .= "        <button class=\"delete-message-btn\" data-message-id=\"" . $message_id . "\">Delete</button>";
        }
        $output .= "    </div>";
        $output .= "</div>";
    }
}

echo $output;
?>