<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp-like PHP Chatroom</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #ece5dd; /* WhatsApp background color */
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        header {
            background-color: #075e54; /* WhatsApp header green */
            color: white;
            padding: 15px 20px;
            width: 100%;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            font-size: 1.5em;
            font-weight: bold;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 15px;
            width: 95%;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex-grow: 1;
        }
        #chat-box {
            width: 100%;
            height: 60vh; /* Use viewport height for better responsiveness */
            border: none;
            padding: 10px;
            overflow-y: auto;
            background-image: url('https://www.transparenttextures.com/patterns/diagmonds-light.png'); /* Optional: WhatsApp-like background texture */
            background-color: #e5ddd5; /* Fallback for texture */
            border-radius: 5px;
            display: flex;
            flex-direction: column;
        }
        .message-container {
            display: flex;
            margin-bottom: 8px;
        }
        .message-container.my-message {
            justify-content: flex-end;
        }
        .message-container.other-message {
            justify-content: flex-start;
        }
        .message-bubble {
            max-width: 75%;
            padding: 8px 12px;
            border-radius: 7px;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }
        .my-message .message-bubble {
            background-color: #dcf8c6; /* My message green */
            margin-left: auto;
        }
        .other-message .message-bubble {
            background-color: #ffffff; /* Other message white */
            margin-right: auto;
        }
        .message-sender {
            font-weight: bold;
            font-size: 0.9em;
            margin-bottom: 2px;
            color: #075e54; /* Dark green for sender */
        }
        .my-message .message-sender {
            color: #128c7e; /* Lighter green for my sender */
        }
        .message-content {
            font-size: 1em;
            margin-bottom: 2px;
        }
        .message-time {
            font-size: 0.7em;
            color: #888;
            text-align: right;
            margin-top: 5px;
        }
        #message-form {
            display: flex;
            gap: 10px;
            padding: 10px 0;
            background-color: #f0f0f0; /* Input bar background */
            border-radius: 8px;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.05);
        }
        #username-input, #message-input {
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 20px; /* Rounded input fields */
            font-size: 1em;
            background-color: #fff;
        }
        #username-input {
            width: 100px;
            flex-shrink: 0;
            margin-left: 10px;
        }
        #message-input {
            flex-grow: 1;
        }
        #send-button {
            padding: 10px 20px;
            background-color: #128c7e; /* WhatsApp send button green */
            color: white;
            border: none;
            border-radius: 20px; /* Rounded send button */
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.2s ease;
            margin-right: 10px;
        }
        #send-button:hover {
            background-color: #075e54;
        }
        footer {
            margin-top: 20px;
            padding: 10px;
            color: #666;
            font-size: 0.8em;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        WhatsApp Chatroom
    </header>

    <div class="container">
        <div id="chat-box"></div>

        <form id="message-form">
            <input type="text" id="username-input" placeholder="Your Name" required>
            <input type="text" id="message-input" placeholder="Type your message..." required>
            <button type="submit" id="send-button">Send</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 WhatsApp-like Chatroom. All rights reserved.</p>
    </footer>

    <script>
        const chatBox = document.getElementById('chat-box');
        const messageForm = document.getElementById('message-form');
        const usernameInput = document.getElementById('username-input');
        const messageInput = document.getElementById('message-input');

        function fetchMessages() {
            fetch('get_messages.php')
                .then(response => response.text())
                .then(data => {
                    chatBox.innerHTML = data;
                    chatBox.scrollTop = chatBox.scrollHeight; // Scroll to bottom
                })
                .catch(error => console.error('Error fetching messages:', error));
        }

        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = usernameInput.value.trim();
            const message = messageInput.value.trim();

            if (username && message) {
                const formData = new FormData();
                formData.append('username', username);
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
    </script>
</body>
</html>