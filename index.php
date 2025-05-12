<?php
include 'layout/header.php';
require_once 'database/Database.php';
require_once 'models/Book.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();

Book::setConnection($db);
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
        window.location.href = "auth/login.php";
    });
    </script>';
    exit();
}

$day = date('Y-m-d H:i:s', strtotime('-1 day'));

$book_count = Book::countBooks();
$user_count = User::countUsers();
$new_books = Book::booksAdded($day);
$new_users = User::usersAdded($day);
$active_users = User::userStatus('Active');
$inactive_users = User::userStatus('Inactive');
?>

<div class="container rounded bg-dark-subtle mt-5 align-items-center">
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-warning text-dark m-4">
                <div class="card-body">
                    <h5>Total Books</h5>
                    <h3><?= $book_count ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-dark m-4">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h3><?= $user_count ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-dark m-4">
                <div class="card-body">
                    <h5>Active Users</h5>
                    <h3><?= $active_users ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-warning text-dark m-4">
                <div class="card-body">
                    <h5>New Books (Last 24h)</h5>
                    <h3><?= $new_books ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-dark m-4">
                <div class="card-body">
                    <h5>New Users (Last 24h)</h5>
                    <h3><?= $new_users ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-dark m-4">
                <div class="card-body">
                    <h5>Inactive Users</h5>
                    <h3><?= $inactive_users ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'layout/footer.php'; ?>