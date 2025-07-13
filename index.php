<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypted Terminal Chat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            background-color: #1a1a1a; /* Dark background */
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            height: 100vh; /* Ensure body takes full viewport height */
            color: #e0e0e0; /* Light grey text */
        }
        header {
            background-color: #222222; /* Dark grey header */
            color: #ffffff; /* White text */
            padding: 15px 20px;
            width: 100%;
            text-align: center;
            box-shadow: none; /* No shadow */
            margin-bottom: 20px;
            font-size: 1.5em;
            font-weight: bold;
            text-transform: uppercase;
        }
        .container {
            background-color: #282828; /* Slightly lighter dark grey container */
            border-radius: 0px; /* Sharp corners */
            box-shadow: none; /* No shadow */
            padding: 15px;
            width: 95%;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex-grow: 1; /* Allow container to grow */
            border: 1px solid #333333; /* Subtle dark border */
        }
        #chat-box {
            width: 100%;
            height: 60vh; /* Use viewport height for better responsiveness */
            min-height: 300px; /* Minimum height for chat box */
            flex-grow: 1; /* Allow chat-box to grow */
            border: 1px solid #333333; /* Subtle dark border */
            padding: 10px; /* Uniform padding */
            overflow-y: auto;
            background-color: #1e1e1e; /* Dark grey chat background */
            border-radius: 0px;
            display: flex;
            flex-direction: column;
            box-shadow: none; /* No glow */
        }
        /* General message container */
        .message-container {
            display: flex; /* Keep as flex to allow positioning of bubble inside */
            margin-bottom: 10px; /* Space between messages */
        }

        /* Message from the user (aligned right) */
        .message-container.user {
            justify-content: flex-end; /* Push bubble to the right within its container */
        }

        /* Message from the agent (aligned left) */
        .message-container.agent {
            justify-content: flex-start; /* Push bubble to the left within its container */
        }

        /* Styling for the actual message bubble */
        .message-bubble {
            max-width: 75%; /* Limit bubble width */
            padding: 10px 15px; /* Good padding */
            border-radius: 4px; /* Subtle rounding */
            word-wrap: break-word; /* Ensures text wraps */
            box-shadow: none; /* No glow */
            position: relative; /* For potential future elements like read receipts */
        }

        /* Specific styles for user's message bubble */
        .message-container.user .message-bubble {
            background-color: #333333; /* Darker grey for user messages */
            color: #e0e0e0; /* Light grey text */
            border: 1px solid #444444; /* Subtle border */
            /* NO margin-left or margin-right here */
        }

        /* Specific styles for agent's message bubble */
        .message-container.agent .message-bubble {
            background-color: #222222; /* Even darker grey for agent messages */
            color: #e0e0e0; /* Light grey text */
            border: 1px solid #333333; /* Subtle border */
            /* NO margin-left or margin-right here */
        }

        /* Sender name styling */
        .message-sender {
            font-weight: bold;
            font-size: 0.85em; /* Slightly smaller */
            margin-bottom: 4px; /* Space between sender and content */
            color: #999999; /* Subtle grey */
        }

        /* Message content styling */
        .message-content {
            font-size: 0.95em; /* Slightly smaller for elegance */
            margin-bottom: 4px; /* Space between content and time */
            color: #e0e0e0;
        }

        /* Message time styling */
        .message-time {
            font-size: 0.65em; /* Smaller for subtlety */
            color: #777777; /* Even darker grey for time */
            text-align: right;
        }
        #message-form {
            display: flex;
            gap: 10px;
            padding: 10px 0;
            background-color: #282828; /* Dark input bar background */
            border-radius: 0px;
            box-shadow: none; /* No shadow */
            border: 1px solid #333333;
        }
        #message-input {
            padding: 10px 15px;
            border: 1px solid #444444; /* Darker subtle border */
            border-radius: 3px;
            font-size: 1em;
            background-color: #1e1e1e; /* Dark input fields */
            color: #e0e0e0;
            box-shadow: none; /* No inner glow */
        }
        #message-input {
            flex-grow: 1;
            margin-left: 10px;
        }
        #send-button {
            padding: 10px 20px;
            background-color: #444444; /* Dark grey send button */
            color: #ffffff; /* White text */
            border: 1px solid #555555;
            border-radius: 3px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
            margin-right: 10px;
            box-shadow: none; /* No glow */
        }
        #send-button:hover {
            background-color: #555555; /* Slightly lighter grey on hover */
            box-shadow: none; /* No glow */
        }
        footer {
            margin-top: 20px;
            padding: 10px;
            color: #888888; /* Muted grey for footer */
            font-size: 0.8em;
            text-align: center;
            width: 100%;
            text-shadow: none; /* Remove glow */
        }
    </style>
</head>
<body>
    <header>
        SECURE TERMINAL ACCESS
    </header>

    <div class="container">
        <div class="online-users-display">
            Online Users: <span id="online-users-count">0</span>
        </div>
        <div id="chat-box"></div>

        <form id="message-form">
            <input type="text" id="message-input" placeholder="Enter encrypted message..." required>
            <button type="submit" id="send-button">Send</button>
        </form>
    </div>

    <footer>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>! <a href="logout.php">Logout</a></p>
        <p>&copy; 2025 Secure Terminal. All rights reserved.</p>
    </footer>

    <script>
        const chatBox = document.getElementById('chat-box');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');

        function fetchMessages() {
            let url = 'get_messages.php';
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    const isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 1; // Add a small buffer
                    chatBox.innerHTML = data;
                    if (isScrolledToBottom) {
                        chatBox.scrollTop = chatBox.scrollHeight; // Only scroll to bottom if user was already there
                    }
                })
                .catch(error => console.error('Error fetching messages:', error));
        }

        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();

            if (message) {
                const formData = new FormData();
                formData.append('message', message);

                fetch('send_message.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        messageInput.value = ''; // Clear input
                        fetchMessages(); // Refresh messages
                    } else {
                        alert('Failed to send message: ' + data);
                    }
                })
                .catch(error => console.error('Error sending message:', error));
            }
        });

        // Fetch messages every 0.5 seconds (500 milliseconds)
        setInterval(fetchMessages, 500);

        // Initial fetch
        fetchMessages();

        const onlineUsersCountElement = document.getElementById('online-users-count');

        function fetchOnlineUsers() {
            let url = 'online_users.php';
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    onlineUsersCountElement.textContent = data;
                })
                .catch(error => console.error('Error fetching online users:', error));
        }

        // Fetch online users every 5 seconds
        setInterval(fetchOnlineUsers, 5000);

        // Initial fetch for online users
        fetchOnlineUsers();

        function sendHeartbeat() {
            fetch('heartbeat.php', {
                method: 'POST'
            })
            .then(response => {
                if (!response.ok) {
                    console.error('Heartbeat failed:', response.statusText);
                }
            })
            .catch(error => console.error('Error sending heartbeat:', error));
        }

        // Send heartbeat every 30 seconds
        setInterval(sendHeartbeat, 30000);

        // Initial heartbeat
        sendHeartbeat();
    </script>
</body>
</html>