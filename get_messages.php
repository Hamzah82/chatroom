<?php
$current_user = isset($_GET['current_user']) ? $_GET['current_user'] : '';

$messages_raw = file_get_contents('messages.txt');
$messages_array = explode("\n", $messages_raw);

$output = '';
foreach ($messages_array as $message_line) {
    $message_line = trim($message_line);
    if (!empty($message_line)) {
        list($username, $message, $timestamp) = explode('|', $message_line, 3);
        
        // Determine message type based on current user
        $message_class = (strtolower($username) === strtolower($current_user)) ? 'user' : 'agent';

        $output .= "<div class=\"message-container {$message_class}\">";
        $output .= "    <div class=\"message-bubble\">";
        $output .= "        <div class=\"message-sender\">" . htmlspecialchars($username) . "</div>";
        $output .= "        <div class=\"message-content\">" . htmlspecialchars($message) . "</div>";
        $output .= "        <div class=\"message-time\">" . htmlspecialchars($timestamp) . "</div>";
        $output .= "    </div>";
        $output .= "</div>";
    }
}

echo $output;
?>