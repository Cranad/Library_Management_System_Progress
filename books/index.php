<?php
include '../layout/header.php';
require_once '../database/Database.php'; 
require_once '../models/Book.php'; 

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
$database= new Database();
$conn = $database->getConnection();

Book::setConnection($conn);

$books = Book::all();
?>

<h1 class="container-xxl shadow rounder text-center p-4 mt-4 mx-auto">Book List</h1>

<div class="container-xxl p-4 shadow rounded mt-4">
    <a href="create.php" class="btn btn-success mb-3 ">Create</a> 
    <a href="../reports/GenerateBooks.php" class="btn btn-success mb-3 ">Generate Books</a> 

    <table id="booksTable" class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th style="background-color:#FFFACD;">ID</th>
                <th style="background-color:#FFFACD;">SKU</th>
                <th style="background-color:#FFFACD;">Title</th>
                <th style="background-color:#FFFACD;">Author</th>
                <th style="background-color:#FFFACD;">Genre</th>
                <th style="background-color:#FFFACD;">Year published</th>
                <th style="background-color:#FFFACD;">Action</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php
                $i = 1;
                foreach ($books as $book): ?>
                <tr>
                    <td style="background-color: #ADD8E6"><?= $i++ ?></td>
                    <td style="background-color: #ADD8E6"><?= $book->sku ?></td>
                    <td style="background-color: #ADD8E6"><?= $book->title ?></td>
                    <td style="background-color: #ADD8E6"><?= $book->author ?></td>
                    <td style="background-color: #ADD8E6"><?= $book->genre ?></td>
                    <td style="background-color: #ADD8E6"><?= $book->year_published ?></td>
                    <td class="text-center" style="background-color:#ADD8E6">
                        <a href="show.php?id=<?= $book->id ?>" class="btn btn-warning">View</a>
                        <a href="edit.php?id=<?= $book->id ?>" class="btn btn-primary">Edit</a>
                        <?php if ($_SESSION['role'] == 'superadmin' || $_SESSION['role'] == 'admin'): ?>
                            <a href="destroy.php?id=<?= $book->id ?>" class="btn btn-danger">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include "../layout/footer.php"; ?>
