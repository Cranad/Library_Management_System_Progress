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

if (!$book) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Book not found.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location.href = "index.php";
            });
          </script>';
    exit();
}

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    if ($book->delete()) {
        echo '<script>
                Swal.fire({
                    title: "Deleted!",
                    text: "The book record has been deleted.",
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
                    text: "Failed to delete the book record. Please try again.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location.href = "index.php";
                });
              </script>';
    }
} else {
    echo '<script>
            Swal.fire({
                title: "Are you sure?",
                text: "You are about to delete this book record. This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "destroy.php?id=' . $id . '&confirm=yes";
                } else {
                    window.location.href = "index.php";
                }
            });
          </script>';
}
?>
<?php include "../layout/footer.php"; ?>