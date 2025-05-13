<?php
include '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/Book.php';
require_once '../models/Category.php';



$db = new Database();
$conn = $db->getConnection();

Book::setConnection($conn);
Category::setConnection($conn);
$categories = Category::all();

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


?>

<div class="container mt-5 d-flex justify-content-center align-items-center">
    <div class="card shadow-lg p-4" style="width: 50%; ">
        <div class="card-body">
            <h2 class="text-center fw-bold text-primary">Add New Book</h2>
            <form method="POST" action="store.php">
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Author</label>
                    <input type="text" name="author" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Category</label>
                    <select name="category" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $category) {
                            echo "<option value='{$category->id}'>{$category->category_name}</option>";
                        } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="published_year" class="form-label">Year Published</label>
                    <select name="published_year" id="published_year" class="form-control" required>
                        <option value="" selected hidden>-Select Year-</option>
                        <?php for ($year = date('Y'); $year >= date('Y') - 100; $year--){ ?>
                            <option value="<?= $year ?>"><?= $year ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Total Copies</label>
                    <input type="number" name="total_copies" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Available Copies</label>
                    <input type="number" name="available_copies" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-2">Save Book</button>
                <a href="index.php" class="btn btn-outline-secondary w-100">Back</a>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
