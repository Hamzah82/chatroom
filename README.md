# Encrypted Terminal Chat (Dark Minimalist UI)

This is a real-time chatroom application built with PHP, HTML, CSS, and JavaScript. It features a dark, minimalist user interface inspired by hacker terminals and includes advanced moderation capabilities.

## Features

*   **Real-time Messaging:** Messages are updated every 0.5 seconds using AJAX.
*   **Simple Storage:** Messages are stored in a plain text file (`messages.txt`). User data (including roles and banned status) is stored in `users.json`.
*   **Dark Minimalist UI:** Elegant dark mode styling with a terminal-like aesthetic.
*   **User/Agent Differentiation:** Messages are styled differently based on whether they are sent by the active user or another participant (agent).
*   **Responsive Layout:** The chat interface adapts to different screen sizes for a consistent experience.
*   **User Roles (User, Admin, CEO):**
    *   **User:** Standard chat participant.
    *   **Admin:** Can delete any message, ban/unban users, and assign 'user' or 'admin' roles. Admin display names are gold.
    *   **CEO:** The highest authority. Can delete any message (with "deleted by CEO" tag for others' messages), ban/unban any user (including admins), and assign any role (user, admin, CEO). CEOs cannot demote themselves or be demoted/banned by admins. CEO display names are vibrant red.
*   **Admin Panel:** A dedicated interface for users with 'admin' or 'ceo' roles to manage other users.
    *   View all registered users.
    *   Ban and unban users.
    *   Assign user roles (User, Admin, CEO).
    *   "Save Role" button for explicit role changes.
    *   "Back to Chat" button for easy navigation.
*   **Banned User Restrictions:** Banned users are prevented from:
    *   Logging in.
    *   Sending messages.
    *   Viewing chat messages.
*   **Session Synchronization:** User roles and banned status are automatically synchronized from `users.json` to the user's session upon loading `index.php`, allowing immediate application of new privileges/restrictions without requiring a manual re-login.
*   **Enhanced Message Deletion:** Messages deleted by an Admin or CEO will indicate who deleted them (e.g., "This message has been deleted by Admin."). Self-deleted messages will simply show "This message has been deleted."

## Setup and Installation

To get this chatroom running on your server, follow these steps:

1.  **PHP Environment:** Ensure you have a PHP-enabled web server (e.g., Apache, Nginx with PHP-FPM) configured and running.

2.  **File Placement:** Place all application files (`.php`, `.css`, `.txt`, `.json`) into your web server's document root directory (e.g., `htdocs` for Apache, `www` for Nginx).

3.  **File Permissions:** The application needs write permissions to `messages.txt` and `users.json`. Ensure that the web server user has write permissions to the directory where these files are located, and specifically to the files themselves. You might need to run commands like:
    ```bash
    chmod 666 messages.txt users.json
    # Or, if files don't exist yet, create them and then set permissions:
    # touch messages.txt users.json
    # chmod 666 messages.txt users.json
    ```
    If you encounter "Permission denied" errors, check your server's user and group permissions.

4.  **Initial User Setup (for Admin/CEO access):**
    To get an 'admin' or 'ceo' account, you will need to manually edit the `users.json` file.
    *   Register a new user through the `register.php` page.
    *   Open `users.json` in a text editor.
    *   Find the entry for the registered user and change their `"role"` field from `"user"` to `"admin"` or `"ceo"`.
    *   Ensure the `"banned"` field is set to `false` (it should be by default).
    Example for setting a user as CEO:
    ```json
    [
        {
            "username": "your_username",
            "password": "your_hashed_password",
            "last_active": 1234567890,
            "is_typing": false,
            "role": "ceo", // Change this line
            "banned": false,
            "last_login_time": 1234567890
        }
    ]
    ```

5.  **Clear Old Messages (Important!):** If you were using a previous version of this chatroom, your `messages.txt` might contain old HTML-formatted messages. To avoid display issues, it's crucial to clear its content:
    ```bash
    echo '' > messages.txt
    ```
    Make sure you have the necessary permissions to do this.

## Usage

1.  Open your web browser and navigate to the URL where you placed the files (e.g., `http://localhost/index.php`).
2.  Register a new account or log in with an existing one.
3.  If you have an 'admin' or 'ceo' role, a "Admin Panel" link will appear in the footer. Click it to manage users.
4.  Type your message in the input field and press Enter or click "Send".

Messages will appear in the chat box and will be visible to all authorized users accessing the page.

## Message Styling

The `get_messages.php` script now differentiates messages based on the `Agent ID` entered by the current user and their assigned role. Messages sent by the active user will be styled with the `.message-container.user` class (aligned right), while messages from other users will be styled with the `.message-container.agent` class (aligned left). Admin and CEO messages will have distinct display name colors and effects.
