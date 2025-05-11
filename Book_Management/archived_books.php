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

$db = new Database();
$conn = $db->getConnection();
Book::setConnection($conn);
$books = Book::all();
?>

<div class="container mt-4">
    <h3>Archived Books</h3>

    <?php if (!empty(Book::all())): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th><th>Author</th><th>Category</th><th>Year</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $hasArchived = false;
                foreach ($books as $book): ?>
                    <?php if ($book->status === 'archived'): ?>
                        <?php $hasArchived = true; ?>
                        <tr>
                            <td><?= $book->title ?></td>
                            <td><?= $book->author ?></td>
                            <td><?= Book::CategoryName($book->category) ?></td>
                            <td><?= $book->published_year ?></td>
                            <td>
                                <a href="restore.php?id=<?= $book->id ?>" class="btn btn-success btn-sm">Restore</a>
                                <a href="destroy.php?id=<?= $book->id ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (!$hasArchived): ?>
                    <tr>
                        <td colspan="5" class="text-center">No archived books found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>
    <?php endif; ?> 

</div>

<?php require_once '../layout/footer.php'; ?>
