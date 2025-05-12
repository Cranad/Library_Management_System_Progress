<?php   require_once "../database/Database.php"; 
        require_once "../models/Book.php"; 
        include '../layout/header.php'; 

        $database= new Database();
        $db = $database->getConnection();
        Book::setConnection($db);
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
?>

<h1 class="container-xxl shadow rounder text-center p-4 mt-4 mx-auto">Input a book</h1>

<div class="container-xxl p-4 shadow rounded">
    <form action="store.php" method="POST">
        <div class="row gx-3">
            <div class="col-md-6 mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" name="author" id="author" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="genre" class="form-label">Genre</label>
                <input type="text" name="genre" id="genre" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="year_published" class="form-label">Year Published</label>
                <select name="year_published" id="year_published" class="form-control" required>
                <?php for ($year = date('Y'); $year >= date('Y') - 100; $year--){ ?>
                        <option value="" selected hidden>----</option>
                        <option value="<?= $year ?>"><?= $year ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" name="sku" id="sku" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="currency" class="form-label">Currency</label>
                <select name="currency" id="currency" class="form-control" required>
                    <option value="" selected hidden>(PHP,USD,EUR)</option>
                    <option value="PHP">PHP</option>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" name="stock" id="stock" class="form-control" required>
            </div>
        </div>
        <a class="btn btn-danger" href="index.php">Cancel</a>
        <button class="d-inline btn btn-primary">Store</button>
    </form>
</div>
<?php include '../layout/footer.php'; ?>