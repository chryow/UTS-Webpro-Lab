<?php
session_start();
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}


if (isset($_GET['list_id'])) {
    $list_id = $_GET['list_id'];


    $task_query = $db->prepare("SELECT * FROM tasks WHERE todo_list_id = ?");
    $task_query->bind_param("i", $list_id);
    $task_query->execute();
    $tasks_result = $task_query->get_result();


    $list_query = $db->prepare("SELECT title FROM todo_lists WHERE id = ?");
    $list_query->bind_param("i", $list_id);
    $list_query->execute();
    $list_result = $list_query->get_result();
    

    $response = [];
    if ($list_result->num_rows > 0) {
        $list = $list_result->fetch_assoc();
        $response['list_name'] = $list['title'];
    } else {
        $response['list_name'] = 'Unknown List';
    }


    while ($task = $tasks_result->fetch_assoc()) {
        $response['tasks'][] = $task;
    }


    echo json_encode($response);
} else {
    echo json_encode(['error' => 'List ID not provided.']);
}
?>
