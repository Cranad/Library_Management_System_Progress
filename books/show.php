<?php
require_once "../database/Database.php"; 
require_once "../models/Book.php"; 
include '../layout/header.php'; 

$database = new Database();
$db = $database->getConnection();
Book::setConnection($db);  


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

$id=$_GET['id'];

if(isset($_GET['id'])){
    $book = Book::find($id);
}

if (!$book) {
    http_response_code(404);
    echo '<script>
    Swal.fire({
        title: "404 Not Found!",
        text: "The book record you are looking for is not found.",
        icon: "error",
        confirmButtonText: "Back to book list"
    }).then(function() {
        window.location.href = "index.php";
    });
  </script>';
    exit();
}
?>

<h1 class="container-xxl shadow rounder text-center p-4 mt-4 mx-auto">Book Details</h1>

<div class="container-xxl shadow rounded p-4">
    <table class="table table-striped table-hover table-bordered">
        <tr>
            <th>SKU</th>
            <td><?= $book->sku ?></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><?= $book->title ?></td>
        </tr>
        <tr>
            <th>Author</th>
            <td><?= $book->author ?></td>
        </tr>
        <tr>
            <th>Genre</th>
            <td><?= $book->genre ?></td>
        </tr>
        <tr>
            <th>Year Published</th>
            <td><?= $book->year_published ?></td>
        </tr>
        <tr>
            <th>Price</th>
            <td><?= $book->price . ' ' . $book->currency ?></td>
        </tr>
        <tr>
            <th>Stock</th>
            <td><?= $book->stock ?></td>
        </tr>
    </table>
    <a href="index.php" class="btn btn-secondary">Back to Book List</a>
</div>
<?php include "../layout/footer.php"; ?>
