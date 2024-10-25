<?php
session_start();
include 'db.php';

$successMessage = "";
$error = "";


if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Akun berhasil dibuat! Silakan login.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $favorite_thing = trim($_POST['favorite_thing']);


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    $stmtCheck = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {

        $error = "Email sudah terdaftar, silakan gunakan email lain.";
    } else {

        $stmt = $db->prepare("INSERT INTO users (username, email, password, favorite_thing) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $favorite_thing);

        if ($stmt->execute()) {

            header("Location: register.php?success=1");
            exit();
        } else {
            $error = "Registration failed: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="credentials.css">
    <style>
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .text-link {
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }
        .text-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="favorite_thing">Favorite Thing:</label>
                <input type="text" class="form-control" id="favorite_thing" name="favorite_thing" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>


        <?php if ($successMessage): ?>
            <div class="alert alert-success mt-3"><?php echo $successMessage; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>


        <div class="mt-3">
            <a href="index.php" class="text-link">Kembali ke Login</a>
        </div>
    </div>
</body>
</html>
