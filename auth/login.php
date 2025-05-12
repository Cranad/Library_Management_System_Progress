<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        if ($user->status !== 'active') {
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
<div class="container-xxl d-flex align-items-center vh-100">
    <div class="card shadow mx-auto w-50 p-2" style="max-width: 500px;">
        <div class="card-body">
            <form action="login.php" method="POST">
                <h1 class="text-center">LOGIN</h1>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control <?=(isset($_SESSION['error']) ? 'is-invalid' : '')?>" id="email" name="email" required>
                        <?php if (isset($_SESSION['error'])): ?>
                        <div class="invalid-feedback">
                            <?= $_SESSION['error'] ?>
                        </div>
                        <?php
                            unset($_SESSION['error']);
                            endif;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
<?php include '../layout/footer.php'; ?>