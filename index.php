<?php
session_start();
include 'db.php';

$secretKey = "6LfUeWkqAAAAAH7jlkaOVFXt0I5ih52l6r-sDzwu";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $recaptchaResponse = $_POST['g-recaptcha-response'];


    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verifyUrl . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
    $responseKeys = json_decode($response, true);

    if ($responseKeys["success"]) {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                setcookie("user", $user['username'], time() + (10 * 60), "/");
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Email not found.";
        }
    } else {
        $error = "reCAPTCHA verification failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="credentials.css">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="g-recaptcha" data-sitekey="6LfUeWkqAAAAAO9zqeyUd1vD9wAmUWC7rFAAMMPd"></div>
            <button type="submit" class="btn btn-primary">Login</button>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
            <?php endif; ?>
        </form>
        <p class="mt-3"><a href="register.php">Register</a> | <a href="forgot_password.php">Forgot Password?</a></p>
    </div>
</body>
</html>
