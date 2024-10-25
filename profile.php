<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}


$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, favorite_thing FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="profile.css">
    <title>Profile Info</title>
</head>
<body>

<div class="container">
    <h2>Profile Information</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Favorite Thing:</strong> <?php echo htmlspecialchars($user['favorite_thing']); ?></p>
    <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

</body>
</html>
