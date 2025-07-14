<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    die("Please log in to see the messages.");
}

$current_user = $_SESSION['username'];
$current_user_role = $_SESSION['role'] ?? 'user'; // Get current user's role

// Load user data to check banned status
$users_data_full = [];
if (file_exists('users.json')) {
    $users_json_full = file_get_contents('users.json');
    $users_array_full = json_decode($users_json_full, true);
    if (is_array($users_array_full)) {
        foreach ($users_array_full as $user_entry) {
            $users_data_full[strtolower($user_entry['username'])] = $user_entry;
        }
    }
}

// Check if current user is banned
if (isset($users_data_full[strtolower($current_user)]) && ($users_data_full[strtolower($current_user)]['banned'] ?? false)) {
    die("You are banned and cannot view messages.");
}

// Create messages.txt if it doesn't exist
if (!file_exists('messages.txt')) {
    file_put_contents('messages.txt', '');
}

// Load user roles from users.json (for display purposes)
$users_data = [];
if (file_exists('users.json')) {
    $users_json = file_get_contents('users.json');
    $users_array = json_decode($users_json, true);
    if (is_array($users_array)) {
        foreach ($users_array as $user) {
            $users_data[strtolower($user['username'])] = $user['role'] ?? 'user';
        }
    }
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
        $deleted_by_role = $message_data['deleted_by_role'] ?? null; // New field

        // Determine message type based on current user
        $message_class = (strtolower($username) === strtolower($current_user)) ? 'user' : 'agent';

        // Get sender's role
        $sender_role = $users_data[strtolower($username)] ?? 'user';
        $sender_class = '';
        if ($sender_role === 'admin') {
            $sender_class = ' admin-sender'; // Add a class for admin users
        } else if ($sender_role === 'ceo') {
            $sender_class = ' ceo-sender'; // Add a class for CEO users
        }

        // Using explicit concatenation for clarity and to avoid interpolation issues
        $output .= "<div class=\"message-container " . $message_class . "\" data-message-id=\"" . $message_id . "\" data-sender-username=\"" . $username . "\">";
        $output .= "    <div class=\"message-bubble\">";
        $output .= "        <div class=\"message-sender" . $sender_class . "\">" . $username . "</div>"; // Apply admin class here
        if ($is_deleted) {
            $deleted_message_text = 'This message has been deleted.';
            if ($deleted_by_role === 'admin') {
                $deleted_message_text = 'This message has been deleted by Admin.';
            } else if ($deleted_by_role === 'ceo') {
                $deleted_message_text = 'This message has been deleted by CEO.';
            }
            $output .= "        <div class=\"message-content deleted-message\">" . $deleted_message_text . "</div>";
        } else {
            $output .= "        <div class=\"message-content\">" . $message_content . "</div>";
        }
        $output .= "        <div class=\"message-time\">" . $timestamp . "</div>";

        // Show delete button if current user is admin OR CEO OR if it's their own message, AND the message is NOT deleted
        if (((in_array($current_user_role, ['admin', 'ceo'])) || (strtolower($username) === strtolower($current_user))) && !$is_deleted) {
            $output .= "        <button class=\"delete-message-btn\" data-message-id=\"" . $message_id . "\">Delete</button>";
        }
        $output .= "    </div>";
        $output .= "</div>";
    }
}

echo $output;
?>