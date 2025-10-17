<?php
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $username = $_POST['username'];
    $password = $_POST['password'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $conn = getDBConnection();
    
    if ($user_id) {
        // Update existing user
        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, full_name = ?, email = ?, role = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("sssssii", $username, $password, $full_name, $email, $role, $is_active, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, role = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("ssssii", $username, $full_name, $email, $role, $is_active, $user_id);
        }
    } else {
        // Create new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $username, $password, $full_name, $email, $role, $is_active);
    }
    
    if ($stmt->execute()) {
        header("Location: ../pages/users.php?message=User saved successfully");
    } else {
        header("Location: ../pages/users.php?error=Error saving user");
    }
    
    $stmt->close();
    $conn->close();
}
?>