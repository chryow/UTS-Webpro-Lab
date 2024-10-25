<?php

require 'db.php';


session_start();
$userId = $_SESSION['user_id'];


if (isset($_GET['query'])) {
    $query = $_GET['query'];
    

    $stmt = $db->prepare("
        SELECT tasks.*, todo_lists.title AS list_name 
        FROM tasks 
        INNER JOIN todo_lists ON tasks.todo_list_id = todo_lists.id 
        WHERE tasks.description LIKE ? AND todo_lists.user_id = ?
    ");
    $searchTerm = "%$query%";
    $stmt->bind_param("si", $searchTerm, $userId);
    

    $stmt->execute();
    $result = $stmt->get_result();
    

    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }


    echo json_encode(['tasks' => $tasks]);
} else {

    echo json_encode(['tasks' => []]);
}
?>
