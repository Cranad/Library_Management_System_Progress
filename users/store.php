<?php
include '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();
User::setConnection($db);

if (!isset($_SESSION['email'])) {
    http_response_code(404);
    echo '<script>
    Swal.fire({
        title: "Error!",
        text: "Login first!",
        icon: "error",
        confirmButtonText: "Ok"
    }).then(function() {
        window.location.href = "../auth/login.php";
    });
    </script>';
    exit();
}

if ($_SESSION['role'] !== 'superadmin') {
    http_response_code(403);
    echo '<script>
    Swal.fire({
        title: "Error!",
        text: "No user access.",
        icon: "error",
        confirmButtonText: "Ok"
    }).then(function() {
        window.location.href = "../index.php";
    });
    </script>';
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), // Hash the password
        'role' => $_POST['role'],
        'status' => $_POST['status']
    ];

    $user = User::create($data);

    if ($user) {
        echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "User created successfully.",
                    icon: "success",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
              </script>';
        exit(); 
    } else {
        echo '<script>
                Swal.fire({
                    title: "Error!",
                    text: "Failed to create user. Please try again.",
                    icon: "error",
                    confirmButtonText: "Ok"
                });
              </script>';
    }
}

include '../layout/footer.php';
?>

