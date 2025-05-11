<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Library_Management_System/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #CAE0BC 0%, #b6d7a8 100%) !important;
            min-height: 100vh;
        }
        .login-card {
            border-radius: 18px;
            box-shadow: 0 6px 32px rgba(44,62,80,0.13);
            background: #fff;
            margin-top: 5vh;
        }
        .login-header {
            font-family: 'Georgia', serif;
            color: #198754;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .login-btn {
            background-color: #198754;
            border-color: #198754;
            font-weight: 600;
            letter-spacing: 1px;
            transition: background 0.2s;
        }
        .login-btn:hover {
            background-color: #145c32;
            border-color: #145c32;
        }
        .login-label {
            color: #145c32;
            font-weight: 500;
        }
    </style>
</head>
<?php
require_once '../database/Database.php';
require_once '../models/User.php';

session_start(['cookie_lifetime' => 86400]); 
$database = new Database();
$db = $database->getConnection();

User::setConnection($db);

if (isset($_SESSION['email'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = User::findEmail($email);

    if ($user && password_verify($password, $user->password)) { 
        if ($user->account_status !== 'active') {
            $_SESSION['error'] = "Your account is inactive/deactivated. Please contact the super administrator.";
            header('Location: login.php');
            exit();
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['email'] = $user->email;
        $_SESSION['role'] = $user->role;

        header('Location: ../index.php');
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password.";
    }
}
?>
<body>
<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="login-card card shadow p-4" style="max-width: 400px; width:100%;">
        <div class="card-body">
            <div class="text-center mb-4">
                <h2 class="login-header mb-0">Library Login</h2>
                <p class="text-muted mb-2" style="font-size:1rem;">Welcome! Please sign in to continue.</p>
            </div>
            <form action="login.php" method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="email" class="login-label">Email</label>
                    <input type="email" class="form-control <?=(isset($_SESSION['error']) ? 'is-invalid' : '')?>" id="email" name="email" required autofocus>
                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="invalid-feedback">
                        <?= $_SESSION['error'] ?>
                    </div>
                    <?php
                        unset($_SESSION['error']);
                        endif;
                    ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="login-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn login-btn w-100 mb-2">Login</button>
            </form>
        </div>
    </div>
</div>
<?php include '../layout/footer.php'; ?>
</body>
</html>