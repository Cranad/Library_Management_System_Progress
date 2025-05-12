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

$id = $_GET['id'];
$book = Book::find($id); 

if (!$id) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Invalid book ID.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location.href = "index.php";
            });
          </script>';
    exit();
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        "sku" => $_POST['sku'],
        "title" => $_POST['title'],
        "author" => $_POST['author'],
        "genre" => $_POST['genre'],
        "year_published" => $_POST['year_published'],
        "price" => $_POST['price'],
        "currency" => $_POST['currency'],
        "stock" => $_POST['stock']
    ];

    if ($book->update($data)) {
        echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "Book updated successfully.",
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
                    text: "Failed to update the book. Please try again.",
                    icon: "error",
                    confirmButtonText: "Ok"
                });
              </script>';
    }
}

?>

<div class="container-xxl p-4 shadow rounded mt-4">
    <h1 class="text-center mb-4">Edit Book</h1>
    <form action="update.php?id=<?= $book->id ?>" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="sku">SKU</label>
                <input type="text" class="form-control" id="sku" name="sku" value="<?= $book->sku ?>" required>
            </div>
            <div class="col-md-6">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= $book->title ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="author">Author</label>
                <input type="text" class="form-control" id="author" name="author" value="<?= $book->author ?>" required>
            </div>
            <div class="col-md-6">
                <label for="genre">Genre</label>
                <input type="text" class="form-control" id="genre" name="genre" value="<?= $book->genre ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="year_published">Year Published</label>
                <input type="number" class="form-control" id="year_published" name="year_published" value="<?= $book->year_published ?>" required>
            </div>
            <div class="col-md-6">
                <label for="price">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $book->price ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="currency">Currency</label>
                <input type="text" class="form-control" id="currency" name="currency" value="<?= $book->currency ?>" required>
            </div>
            <div class="col-md-6">
                <label for="stock">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?= $book->stock ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary w-100">Update</button>
            </div>
        </div>
    </form>
</div>

<?php include '../layout/footer.php'; ?>

