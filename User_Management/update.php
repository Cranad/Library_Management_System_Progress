<?php
require_once '../database/Database.php';
require_once '../models/User.php';
require_once '../layout/header.php';


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


$db = new Database();
$conn = $db->getConnection();
User::setConnection($conn);

$id = $_GET['id'] ?? null;
$user = User::find($id);

if (!$user) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "User not found.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location.href = "index.php";
            });
          </script>';
    exit();
}

$status = $_GET['status'] ?? null;

if ($status === 'reactivate') {
    if ($user->reactivate()) {
        echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "User has been reactivated.",
                    icon: "success",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
              </script>';
    } else {
        echo '<script>
                Swal.fire({
                    title: "Error!",
                    text: "Failed to reactivate user. Please try again.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
              </script>';
    }
    exit();
} elseif ($status === 'deactivate') {
    if ($user->deactivate()) {
        echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "User has been deactivated.",
                    icon: "success",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
              </script>';
    } else {
        echo '<script>
                Swal.fire({
                    title: "Error!",
                    text: "Failed to deactivate user. Please try again.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
              </script>';
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone_number' => $_POST['phone_number']
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        if (!empty($_POST['role'])) {
            $data['role'] =$_POST['role'];
        }

        $hasEmail = User::findEmail($_POST['email']);
        $hasPhoneNumber = User::findPhone($_POST['phone_number']);

        if ($hasEmail && $hasEmail->id != $id) {
            echo "<script>
                 Swal.fire({
                     title: 'Error!',
                     text: 'This email is already in use. Please use a different one.',
                     icon: 'error',
                     confirmButtonText: 'OK'
                 }).then(() => {
                     window.history.back();
                 });
               </script>";
            exit();
        }

        if ($hasPhoneNumber && $hasPhoneNumber->id != $id) {
            echo "<script>
                 Swal.fire({
                     title: 'Error!',
                     text: 'This phone number is already in use. Please use a different one.',
                     icon: 'error',
                     confirmButtonText: 'OK'
                 }).then(() => {
                     window.history.back();
                 });
               </script>";
            exit();
        }

        if ($user->update($data)) {
            echo '<script>
                    Swal.fire({
                        title: "Success!",
                        text: "User updated successfully.",
                        icon: "success",
                        confirmButtonText: "Ok"
                    }).then(function() {
                        window.location.href = "index.php";
                    });
                  </script>';
        } else {
            echo '<script>
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to update user. Please try again.",
                        icon: "error",
                        confirmButtonText: "Ok"
                    });
                  </script>';
        }
    }

?>

<?php include '../layout/footer.php'; ?>
