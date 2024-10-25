<?php
include 'db.php';

$list_id = $_POST['list_id'];
$filter = $_POST['filter'];

if ($filter === 'complete') {
    $query = "SELECT * FROM tasks WHERE todo_list_id = ? AND completed = 1";
} elseif ($filter === 'incomplete') {
    $query = "SELECT * FROM tasks WHERE todo_list_id = ? AND completed = 0";
} else {
    $query = "SELECT * FROM tasks WHERE todo_list_id = ?";
}

$stmt = $db->prepare($query);
$stmt->bind_param("i", $list_id);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

echo json_encode(['tasks' => $tasks]);
?>  