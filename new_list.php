<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit();
}


$list_name = isset($_POST['list_name']) ? $_POST['list_name'] : '';


if (!empty($list_name)) {
    $user_id = $_SESSION['user_id'];


    $query = $db->prepare("INSERT INTO todo_lists (title, user_id) VALUES (?, ?)");
    $query->bind_param("si", $list_name, $user_id);


    if ($query->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
