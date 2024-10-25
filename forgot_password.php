<?php
session_start();
include 'db.php';

$error = "";
$successMessage = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $favorite_thing = isset($_POST['favorite_thing']) ? trim($_POST['favorite_thing']) : '';


    $stmtCheck = $db->prepare("SELECT * FROM users WHERE email = ? AND favorite_thing = ?");
    $stmtCheck->bind_param("ss", $email, $favorite_thing);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {

        header("Location: reset_password.php?email=" . urlencode($email));
        exit();
    } else {
        $error = "Email tidak ada atau favorite thing tidak cocok.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="credentials.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="favorite_thing">Favorite Thing:</label>
                <input type="text" class="form-control" id="favorite_thing" name="favorite_thing" required>
            </div>
            <button type="submit" class="btn btn-primary">Cek</button>
        </form>


        <?php if ($error): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="index.php" class="text-link">Kembali ke Login</a>
        </div>
    </div>
</body>
</html>
