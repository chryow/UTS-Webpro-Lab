<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}


$data = json_decode(file_get_contents("php://input"), true);


if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No JSON data received']);
    exit();
}


error_log('Received data: ' . print_r($data, true));


if (isset($data['task_id']) && isset($data['completed'])) {
    $task_id = $data['task_id'];
    $completed = $data['completed'];


    if (!is_numeric($task_id) || !in_array($completed, [0, 1])) {
        echo json_encode(['success' => false, 'message' => 'Invalid task ID or completed status']);
        exit();
    }


    $query = $db->prepare("UPDATE tasks SET completed = ? WHERE id = ? AND user_id = ?");
    $query->bind_param("iii", $completed, $task_id, $_SESSION['user_id']);

    if ($query->execute()) {
        if ($query->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No task updated. Task may not exist or belong to another user.']);
        }
    } else {

        error_log('Query Error: ' . $db->error);
        echo json_encode(['success' => false, 'message' => 'Failed to update task. Please try again later.']);
    }

    $query->close();
} else {

    echo json_encode(['success' => false, 'message' => 'Invalid data received', 'data' => $data]);
}
?>
