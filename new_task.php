<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$task_name = $_POST['task_name'];
$list_id = $_POST['list_id'];

$query = $db->prepare("INSERT INTO tasks (description, todo_list_id, user_id) VALUES (?, ?, ?)");
$query->bind_param("sii", $task_name, $list_id, $user_id);
$success = $query->execute();

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding task']);
}
?>