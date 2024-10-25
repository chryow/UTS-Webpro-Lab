<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $task_id = $data['task_id'];

    if ($task_id) {

        $stmt = $db->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $task_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete task.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid task ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
