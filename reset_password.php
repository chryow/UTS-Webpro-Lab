<?php
session_start();
include 'db.php';

$error = "";
$successMessage = "";

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);


        if ($new_password === $confirm_password) {

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);


            $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                $successMessage = "Password berhasil direset! Silakan login.";

                header("Refresh:3; url=index.php");
            } else {
                $error = "Gagal mengupdate password: " . $stmt->error;
            }
        } else {
            $error = "Password baru dan konfirmasi password tidak sama.";
        }
    }
} else {

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="credentials.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="new_password">Password Baru:</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>


        <?php if ($error): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php elseif ($successMessage): ?>
            <div class="alert alert-success mt-3"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="index.php" class="text-link">Kembali ke Login</a>
        </div>
    </div>
</body>
</html>
