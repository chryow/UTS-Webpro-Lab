<?php

$stmt = $db->prepare("SELECT * FROM todo_lists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h4>Your To-Do Lists</h4>
<ul class="list-group">
    <?php while ($row = $result->fetch_assoc()): ?>
        <li class="list-group-item">
            <a href="dashboard.php?list_id=<?php echo $row['id']; ?>">
                <?php echo htmlspecialchars($row['title']); ?>
            </a>
        </li>
    <?php endwhile; ?>
</ul>
