<?php   require_once "../database/Database.php"; 
        require_once "../models/Book.php"; 
        include '../layout/header.php'; 

        $database= new Database(); // create object
        $conn = $database->getConnection(); //establish a connection
        Book::setConnection($conn);  //PDO connection passed to Book class using this line of code

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
         
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$data = [
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'genre' => $_POST['genre'],
        'year_published' =>$_POST['year_published'], 
        'sku' => $_POST['sku'],
        'price' => $_POST['price'],
        'currency' => $_POST['currency'],
        'stock' => $_POST['stock'],
    ];
    $book=Book::create($data);
    if ($book) {
        echo "<script>
        Swal.fire({
            title: 'Successful!',
            text: 'Book has been created.',
            icon: 'success',
            confirmButtonText: 'Ok'
        }).then(function() {
            window.location = 'index.php';
        });
    </script>";
    } else {
            echo "<script>
            Swal.fire({
                title: 'Failed!',
                text: 'Failed to create book, try again.',
                icon: 'error',
                confirmButtonText: 'Ok'
            }).then(function() {
                window.location = 'create.php';
            });
        </script>";
        }
    }
?>
<?php include "../layout/footer.php"; ?>