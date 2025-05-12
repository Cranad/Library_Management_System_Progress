<?php   require_once "../database/Database.php"; 
        require_once "../models/Book.php"; 
        include '../layout/header.php';

        $database= new Database();
        $conn = $database->getConnection();
        Book::setConnection($conn);  

if (!isset($_SESSION['email']))  {
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
// $sql = "SELECT * FROM books WHERE id = ?";
// $stmt = mysqli_prepare($conn, $sql);

// if ($stmt) {
//     $id = $_GET['id'];
//     mysqli_stmt_bind_param($stmt, "i", $id);
//     mysqli_stmt_execute($stmt);
//     $result = mysqli_stmt_get_result($stmt);
//     $book = mysqli_fetch_assoc($result);
// }
?>
    <h1 class="container-xxl shadow rounder text-center p-4 mt-4 mx-auto">Edit book details</h1>
<div class="container-xxl p-4 shadow rounded">
    <form action="update.php?id=<?= $book->id ?>" method="POST">
        <div class="row gx-3">
            <div class="col-md-6 mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= $book->title ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" name="author" id="author" class="form-control" value="<?= $book->author ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="genre" class="form-label">Genre</label>
                <input type="text" name="genre" id="genre" class="form-control" value="<?= $book->genre ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="year_published" class="form-label">Year Published</label>
                <select name="year_published" id="year_published" class="form-control" required>
                    <?php for ($year = date('Y'); $year >= date('Y') - 100; $year--){ ?>
                        <option value="<?= $year ?>" <?php if ($year == $book->year_published) echo 'selected'; ?>><?= $year ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" name="sku" id="sku" class="form-control" value="<?= $book->sku ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?= $book->price ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="currency" class="form-label">Currency</label>
                <select name="currency" id="currency" class="form-control" required>
                    <option value="PHP" <?php if ($book->currency == 'PHP') echo 'selected'; ?>>PHP</option>
                    <option value="USD" <?php if ($book->currency == 'USD') echo 'selected'; ?>>USD</option>
                    <option value="EUR" <?php if ($book->currency == 'EUR') echo 'selected'; ?>>EUR</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" name="stock" id="stock" class="form-control" value="<?= $book->stock ?>" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
<?php include "../layout/footer.php"; ?>

