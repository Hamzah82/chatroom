# Simple PHP Chatroom (WhatsApp-like UI)

This is a basic real-time chatroom application built with PHP, HTML, CSS, and JavaScript. It features a user interface inspired by WhatsApp and updates messages every 0.5 seconds.

## Features

*   **Real-time Messaging:** Messages are updated every 0.5 seconds using AJAX.
*   **Simple Storage:** Messages are stored in a plain text file (`messages.txt`).
*   **WhatsApp-like UI:** Basic styling to mimic the look and feel of WhatsApp chat bubbles.
*   **User Identification:** Users can enter a username before sending messages.

## Setup and Installation

To get this chatroom running on your server, follow these steps:

1.  **PHP Environment:** Ensure you have a PHP-enabled web server (e.g., Apache, Nginx with PHP-FPM) configured and running.

2.  **File Placement:** Place all three files (`index.php`, `send_message.php`, `get_messages.php`) into your web server's document root directory (e.g., `htdocs` for Apache, `www` for Nginx).

3.  **File Permissions:** The `send_message.php` script needs to write to `messages.txt`. Ensure that the web server user has write permissions to the directory where these files are located, and specifically to the `messages.txt` file. You might need to run commands like:
    ```bash
    chmod 666 messages.txt
    # Or, if messages.txt doesn't exist yet, create it and then set permissions:
    # touch messages.txt
    # chmod 666 messages.txt
    ```
    If you encounter "Permission denied" errors, check your server's user and group permissions.

4.  **Clear Old Messages (Important!):** If you were using a previous version of this chatroom, your `messages.txt` might contain old HTML-formatted messages. To avoid display issues, it's crucial to clear its content:
    ```bash
    echo '' > messages.txt
    ```
    Make sure you have the necessary permissions to do this.

## Usage

1.  Open your web browser and navigate to the URL where you placed the files (e.g., `http://localhost/index.php`).
2.  Enter your desired username in the "Your Name" field.
3.  Type your message in the "Type your message..." field.
4.  Click the "Send" button or press Enter.

Messages will appear in the chat box and will be visible to all users accessing the page.

## "My Message" Styling

The `get_messages.php` script includes a simple logic to differentiate between "my" messages and "other" messages for styling purposes. If the username entered is `Me` (case-insensitive), the message will be styled as a `my-message` (green bubble). You can modify this logic in `get_messages.php` to match a specific username you use for testing, or implement a more robust user session management in a production environment.
