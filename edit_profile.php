<?php

require 'db.php';


session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}


$user_id = $_SESSION['user_id'];
$result = $db->query("SELECT username, email, favorite_thing FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();


$error_message = '';
$success_message = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current_password = $_POST['current_password'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $favorite_thing = $_POST['favorite_thing'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();


    if (password_verify($current_password, $hashed_password)) {

        if (!empty($new_password) && $new_password === $confirm_password) {

            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, favorite_thing = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $username, $email, $favorite_thing, $hashed_new_password, $user_id);
        } else {

            $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, favorite_thing = ? WHERE id = ?");
            $stmt->bind_param("sssi", $username, $email, $favorite_thing, $user_id);
        }


        if ($stmt->execute()) {
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Error updating profile: " . htmlspecialchars($stmt->error);
        }


        $stmt->close();
    } else {
        $error_message = "Current password is incorrect! You cannot update your profile.";
    }
}


$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="edit_profile.css"> 
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Profile</h2>


        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>


        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Enter your current password" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="favorite_thing">Favorite Thing</label>
                <input type="text" class="form-control" name="favorite_thing" id="favorite_thing" value="<?php echo htmlspecialchars($user['favorite_thing']); ?>" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Leave blank to keep current password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Leave blank to keep current password">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="profile.php" class="btn btn-secondary">Back to Profile Information</a>
        </form>
    </div>
</body>
</html>
