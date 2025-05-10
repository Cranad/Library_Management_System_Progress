<?php 
    require_once '../database/Database.php';
    require_once '../models/User.php';
    include '../layout/header.php'; 

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
    
    if ($_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'admin') {
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
    
    $id = $_GET['id'] ?? null;


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $hasEmail = User::findEmail($_POST['email']);
        $hasPhoneNumber = User::findPhone( $_POST['phone_number']); 
    
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
        } elseif ($hasPhoneNumber && $hasPhoneNumber ->id != $id) { // Check if phone number belongs to another user
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
        } elseif($_POST['password']!== $_POST['confirm_password']){
            echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Password do not match, please try again',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
          </script>";
        }else {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone_number' => $_POST['phone_number'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'role' => $_POST['role'],
                'account_status' => $_POST['account_status'],
            ];
    
            $newUser = User::create($data);
    
            if ($newUser) {
                echo '<script>
                        Swal.fire({
                            title: "Success!",
                            text: "User has been created!",
                            icon: "success"
                        }).then(function() {
                            window.location = "index.php";
                        });
                      </script>';
            } else {
                echo '<script>
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to create User, try again.",
                            icon: "error"
                        }).then(function() {
                            window.location = "create.php";
                        });
                      </script>';
            }
        }
    }
    ?>
<?php include '../layout/footer.php';