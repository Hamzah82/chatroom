<?php
$messages_raw = file_get_contents('messages.txt');
$messages_array = explode("\n", $messages_raw);

$output = '';
foreach ($messages_array as $message_line) {
    $message_line = trim($message_line);
    if (!empty($message_line)) {
        list($username, $message, $timestamp) = explode('|', $message_line, 3);
        
        // Simple logic to differentiate sender for styling (e.g., if username is 'Me')
        // In a real app, you'd use session/user ID to determine 'my' messages
        $is_my_message = (strtolower($username) === strtolower('Me')); // You can change 'Me' to a specific username for testing
        $message_class = $is_my_message ? 'my-message' : 'other-message';

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