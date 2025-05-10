<?php
require_once '../database/Database.php';
require_once '../models/Book.php';
require_once '../models/Transaction.php';
require_once '../models/User.php';
require '../plugins/fpdf/fpdf.php';


$db = new Database();
$conn = $db->getConnection();

Book::setConnection($conn);
Transaction::setConnection($conn);
User::setConnection($conn);

$books = Book::all();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, 'Books and Borrowers List', 1, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 10, '#', 1);
$pdf->Cell(60, 10, 'Title', 1);
$pdf->Cell(40, 10, 'Author', 1);
$pdf->Cell(40, 10, 'Category', 1);
$pdf->Cell(40, 10, 'Borrowers', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
if ($books) {
    $i = 1;
    foreach ($books as $book) {
        $categoryName = Book::CategoryName($book->category);
        $borrowers = Transaction::getBookId($book->id);
        $borrowerNames = [];
        foreach ($borrowers as $transaction) {
            $user = User::find($transaction->user_id);
            if ($user) {
                $borrowerNames[] = $user->name;
            }
        }
        $borrowerList = implode(', ', $borrowerNames);

        $pdf->Cell(10, 10, $i++, 1);
        $pdf->Cell(60, 10, $book->title, 1);
        $pdf->Cell(40, 10, $book->author, 1);
        $pdf->Cell(40, 10, $categoryName, 1);
        $pdf->Cell(40, 10, $borrowerList ?: 'None', 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(190, 10, 'No books found.', 1, 1, 'C');
}

$pdf->Output('I', 'Books_and_Borrowers_List.pdf');
?>
