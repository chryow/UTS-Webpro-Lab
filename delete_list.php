<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo 'not_logged_in';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $list_id = $_POST['list_id'];


    $query = $db->prepare("DELETE FROM todo_lists WHERE id = ? AND user_id = ?");
    $user_id = $_SESSION['user_id'];
    $query->bind_param("ii", $list_id, $user_id);
    
    if ($query->execute()) {

        $task_query = $db->prepare("DELETE FROM tasks WHERE todo_list_id = ?");
        $task_query->bind_param("i", $list_id);
        $task_query->execute();
        
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'invalid_request';
}
?>
