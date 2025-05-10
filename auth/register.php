<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<?php
require_once '../models/User.php';
require_once '../database/Database.php';

session_start(['cookie_lifetime' => 86400]); 
$database = new Database();
$db = $database->getConnection();
User::setConnection($db);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'borrower'; 
    $account_status = $_POST['account_status'] ?? 'active';

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userData = [
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone_number,
            'password' => $hashedPassword,
            'role' => $role,
            'account_status' => $account_status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $existingUser = User::findEmail($email);
            if ($existingUser) {
                $error = "Email is already registered.";
            } else {
                $newUser = User::create($userData);
                if ($newUser) {
                    $success = "Registration successful. You can now log in.";
                } else {
                    $error = "Failed to register. Please try again.";
                }
            }
        } catch (Exception $e) {
            $error = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<body>
<div class="container-xxl d-flex align-items-center vh-100">
    <div class="card shadow mx-auto w-50 p-2" style="max-width: 500px;">
        <div class="card-body">
            <form action="register.php" method="POST">
                <h1 class="text-center">Register</h1>
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center">
                        <?= $error ?>
                    </div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success text-center">
                        <?= $success ?>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="" selected hidden>Select: </option>
                            <option value="superadmin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="borrower">Borrower</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="account_status">Account Status</label>
                        <select class="form-control" id="account_status" name="account_status" required>
                            <option value="" selected hidden>Select: </option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
