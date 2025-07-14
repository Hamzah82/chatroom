<?php
session_start();

header('Content-Type: application/json');

// Check if user is logged in and is an admin or CEO
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (!in_array(($_SESSION['role'] ?? 'user'), ['admin', 'ceo']))) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$current_user_role = $_SESSION['role'] ?? 'user';
$current_username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_to_update = $_POST['username'] ?? '';
    $action = $_POST['action'] ?? ''; // 'ban' or 'unban'
    $new_role = $_POST['new_role'] ?? ''; // 'user' or 'admin' or 'ceo'

    if (empty($username_to_update) || (empty($action) && empty($new_role))) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }

    $users = [];
    $users_file = 'users.json';

    if (file_exists($users_file)) {
        $users_json = file_get_contents($users_file);
        $users = json_decode($users_json, true);
    }

    $user_found = false;
    $message = '';
    foreach ($users as &$user) {
        if (strtolower($user['username']) === strtolower($username_to_update)) {
            $target_user_role = $user['role'] ?? 'user';

            // --- Authorization Checks ---
            // Prevent user from modifying themselves if they are CEO
            if (strtolower($username_to_update) === strtolower($current_username) && $current_user_role === 'ceo' && ($action || $new_role)) {
                echo json_encode(['success' => false, 'message' => 'CEO cannot modify their own status or role.']);
                exit;
            }

            // Admin cannot modify CEO
            if ($current_user_role === 'admin' && $target_user_role === 'ceo') {
                echo json_encode(['success' => false, 'message' => 'Admin cannot modify CEO.']);
                exit;
            }

            // Admin cannot demote another admin to user
            if ($current_user_role === 'admin' && $target_user_role === 'admin' && $new_role === 'user') {
                echo json_encode(['success' => false, 'message' => 'Admin cannot demote another admin.']);
                exit;
            }

            // Admin cannot assign CEO role
            if ($current_user_role === 'admin' && $new_role === 'ceo') {
                echo json_encode(['success' => false, 'message' => 'Admin cannot assign CEO role.']);
                exit;
            }

            // --- Action Handling ---
            if (!empty($action)) { // Handle ban/unban action
                // CEO can ban/unban anyone except themselves
                // Admin can ban/unban users, but not other admins or CEO
                if ($current_user_role === 'admin' && $target_user_role === 'admin') {
                    echo json_encode(['success' => false, 'message' => 'Admin cannot ban/unban another admin.']);
                    exit;
                }

                if ($action === 'ban') {
                    $user['banned'] = true;
                    $message = "User '" . htmlspecialchars($username_to_update) . "' has been banned.";
                } else {
                    $user['banned'] = false;
                    $message = "User '" . htmlspecialchars($username_to_update) . "' has been unbanned.";
                }
            }

            if (!empty($new_role) && in_array($new_role, ['user', 'admin', 'ceo'])) { // Handle role assignment
                // CEO can assign any role
                // Admin can assign user/admin role, but not CEO role
                if ($current_user_role === 'admin' && $new_role === 'ceo') {
                    echo json_encode(['success' => false, 'message' => 'Admin cannot assign CEO role.']);
                    exit;
                }

                $user['role'] = $new_role;
                $message = "User '" . htmlspecialchars($username_to_update) . "' role changed to " . htmlspecialchars($new_role) . ".";
            }
            
            $user_found = true;
            break;
        }
    }

    if ($user_found) {
        if (file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT))) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to write to users.json.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>