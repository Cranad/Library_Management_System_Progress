<?php
require_once '../layout/header.php';
require_once '../database/Database.php';
require_once '../models/Book.php';

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


$db = new Database();
$conn = $db->getConnection();
Book::setConnection($conn);
$id = $_GET['id'];
$bookData = Book::find($id);

if (!$bookData) {
    echo "<div class='alert alert-warning'>Book not found.</div>";
    require_once '../layout/footer.php';
    exit;
}
?>

<div class="container mt-5">
    <h2>Edit Book</h2>
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?= $bookData->title ?>" required>
        </div>
        <div class="mb-3">
            <label>Author</label>
            <input type="text" name="author" class="form-control" value="<?= $bookData->author ?>" required>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="category" class="form-control" required>
                <?php
                $categories = $conn->query("SELECT * FROM book_categories");
                foreach ($categories as $cat) {
                    $selected = $cat['id'] == $bookData->category ? 'selected' : '';
                    echo "<option value='{$cat['id']}' $selected>{$cat['category_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Published Year</label>
            <input type="number" name="published_year" class="form-control" value="<?= $bookData->published_year ?>" required>
        </div>
        <div class="mb-3">
            <label>Total Copies</label>
            <input type="number" name="total_copies" class="form-control" value="<?= $bookData->total_copies ?>" required>
        </div>
        <div class="mb-3">
            <label>Available Copies</label>
            <input type="number" name="available_copies" class="form-control" value="<?= $bookData->available_copies ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Book</button>
    </form>
</div>

<?php require_once '../layout/footer.php'; ?>
