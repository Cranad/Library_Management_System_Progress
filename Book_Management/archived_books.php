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
    <div class="card shadow-lg p-4 rounded-4 mx-auto mb-4" style="max-width: 700px;">
        <h3 class="text-center fw-bold text-primary">Archived Books</h3>
        <?php if (!empty(Book::all())): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Year</th>
                        <th>Actions</th>
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
                                    <a href="restore.php?id=<?= $book->id ?>" class="btn btn-success btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5" viewBox="0 0 448 512" style="vertical-align: middle;"><path d="M163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3C140.6 6.8 151.7 0 163.8 0zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm192 64c-6.4 0-12.5 2.5-17 7l-80 80c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l39-39L200 408c0 13.3 10.7 24 24 24s24-10.7 24-24l0-134.1 39 39c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-80-80c-4.5-4.5-10.6-7-17-7z"/></svg>
                                    </a>
                                    <a href="destroy.php?id=<?= $book->id ?>" class="btn btn-danger btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                    </a>
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
</div>

<?php require_once '../layout/footer.php'; ?>
