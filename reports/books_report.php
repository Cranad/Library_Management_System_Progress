<?php 
require_once '../database/Database.php'; 
require_once '../models/Book.php';
require '../plugins/fpdf/fpdf.php';

$database= new Database();
$db = $database->getConnection();

Book::setConnection($db);

if (isset($_GET['year']) && !empty($_GET['year'])) {
    $year_published = $_GET['year'];
    $books = Book::booksAddedbyyear($year_published);
    $file_name = 'Books added in ' . $year_published . ".pdf";
} else {
    $books = Book::all();
    $file_name = 'books.pdf';
}

//fpdf
$pdf = new FPDF();
//Adds a page
$pdf->AddPage();

//Font family, style(bold, italicize, underlined, unit)
$pdf->SetFont('Arial', 'B', 20);

    //if year is inputted
if (isset($year_published)) {
    $pdf->Cell(0,10, 'Books published in ' . $year_published, 0, 2, 'C');
} else {
    //Cell title for all books
    $pdf->Cell(0,10, 'All Books', 0, 2, 'C');
}
//line Break
$pdf->Ln(5);

//set new format for column headers
$pdf->SetFont('Arial', 'B', 10);
//Columns
$pdf->Cell(10,10, 'No.', 1, 0, 'C');
$pdf->Cell(15,10, 'SKU', 1, 0, 'C');
$pdf->Cell(50,10, 'Title', 1, 0, 'C');
$pdf->Cell(30,10, 'Author', 1, 0, 'C');
$pdf->Cell(20,10, 'Genre', 1, 0, 'C');
$pdf->Cell(20,10, 'Year', 1, 0, 'C');
$pdf->Cell(20,10, 'Price', 1, 0, 'C');
$pdf->Cell(20,10, 'Curr.', 1, 0, 'C');
$pdf->Cell(10,10, 'Stock', 1, 1, 'C');

//set new format for Contents
$pdf->SetFont('Arial', '', 10);

//Actual content
if (count($books) > 0) { 
    $i = 1;
    foreach ($books as $book) {
        $pdf->Cell(10,10,$i++, 1, 0, 'C');
        $pdf->Cell(15,10,$book->sku, 1, 0, 'C');
        $pdf->Cell(50,10,$book->title, 1, 0, 'C');
        $pdf->Cell(30,10,$book->author, 1, 0, 'C');
        $pdf->Cell(20,10,$book->genre, 1, 0, 'C');
        $pdf->Cell(20,10,$book->year_published, 1, 0, 'C');
        $pdf->Cell(20,10,$book->price, 1, 0, 'C');
        $pdf->Cell(20,10,$book->currency, 1, 0, 'C');
        $pdf->Cell(10,10,$book->stock, 1, 1, 'C');
    }
} else {
    $pdf->Cell(0,10, 'No book record available', 0, 1, 'C');
}

//output 
$pdf->Output('I', $file_name);

?>
