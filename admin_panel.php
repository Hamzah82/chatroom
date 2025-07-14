<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (!in_array(($_SESSION['role'] ?? 'user'), ['admin', 'ceo']))) {
    header("location: login.php");
    exit;
}

$users = [];
if (file_exists('users.json')) {
    $users_json = file_get_contents('users.json');
    $users = json_decode($users_json, true);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Chatroom</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #282828;
            padding: 25px;
            border-radius: 0px;
            border: 1px solid #333333;
        }
        .admin-container h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 20px;
        }
        .user-list {
            list-style: none;
            padding: 0;
        }
        .user-list li {
            background-color: #1e1e1e;
            border: 1px solid #333;
            padding: 10px 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 3px;
        }
        .user-list li span {
            color: #e0e0e0;
        }
        .user-list li .actions button {
            padding: 5px 10px;
            margin-left: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .user-list li .actions .ban-btn {
            background-color: #ff6b6b;
            color: white;
        }
        .user-list li .actions .ban-btn:hover {
            background-color: #ff4c4c;
        }
        .user-list li .actions .unban-btn {
            background-color: #6bff6b;
            color: white;
        }
        .user-list li .actions .unban-btn:hover {
            background-color: #4cff4c;
        }
        .user-list li.banned-user {
            background-color: #3a1a1a;
            border-color: #6b3b3b;
        }
        .user-list li.banned-user span {
            color: #ff8888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>Admin Panel</header>
    <div class="admin-container">
        <h2>Manage Users</h2>
        <p style="text-align: center; margin-bottom: 20px;"><a href="index.php" class="btn" style="width: auto; padding: 8px 15px;">Back to Chat</a></p>
        <ul class="user-list">
            <?php foreach ($users as $user): ?>
                <li class="<?php echo ($user['banned'] ?? false) ? 'banned-user' : ''; ?>">
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                    <div class="actions">
                        <select class="role-select" data-username="<?php echo htmlspecialchars($user['username']); ?>">
                            <option value="user" <?php echo (($user['role'] ?? 'user') === 'user') ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo (($user['role'] ?? 'user') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <?php if (($_SESSION['role'] ?? 'user') === 'ceo'): ?>
                                <option value="ceo" <?php echo (($user['role'] ?? 'user') === 'ceo') ? 'selected' : ''; ?>>CEO</option>
                            <?php endif; ?>
                        </select>
                        <button class="save-role-btn" data-username="<?php echo htmlspecialchars($user['username']); ?>">Save Role</button>
                        <?php if (($user['banned'] ?? false)): ?>
                            <button class="unban-btn" data-username="<?php echo htmlspecialchars($user['username']); ?>">Unban</button>
                        <?php else: ?>
                            <button class="ban-btn" data-username="<?php echo htmlspecialchars($user['username']); ?>">Ban</button>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userList = document.querySelector('.user-list');

            userList.addEventListener('click', function(event) {
                const target = event.target;
                if (target.classList.contains('ban-btn') || target.classList.contains('unban-btn')) {
                    const username = target.dataset.username;
                    const action = target.classList.contains('ban-btn') ? 'ban' : 'unban';

                    fetch('update_user_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `username=${encodeURIComponent(username)}&action=${action}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Reload to reflect changes
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating user status.');
                    });
                } else if (target.classList.contains('save-role-btn')) {
                    const username = target.dataset.username;
                    const roleSelect = target.previousElementSibling; // Get the select element before the button
                    const newRole = roleSelect.value;

                    fetch('update_user_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `username=${encodeURIComponent(username)}&new_role=${encodeURIComponent(newRole)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Reload to reflect changes
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating user role.');
                    });
                }
            });
        });
    </script>
</body>
</html>