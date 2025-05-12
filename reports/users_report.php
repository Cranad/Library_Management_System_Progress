
<?php
require_once '../database/Database.php';
require_once '../models/User.php';
require '../plugins/fpdf/fpdf.php';

$database = new Database();
$db = $database->getConnection();

User::setConnection($db);

$status = $_GET['status'] ?? 'all';

if ($status === 'active') {
    $users = User::findByStatus('active');
    $file_name = 'Active_Users_Report.pdf';
} elseif ($status === 'inactive') {
    $users = User::findByStatus('inactive');
    $file_name = 'Inactive_Users_Report.pdf';
} else {
    $users = User::all();
    $file_name = 'All_Users_Report.pdf';
}

//fpdf
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 20);

if ($status === 'active') {
    $pdf->Cell(0, 10, 'All Active User Report', 0, 2, 'C');
} elseif ($status === 'inactive') {
    $pdf->Cell(0, 10, 'All Inactive User Report', 0, 2, 'C');
} else {
    $pdf->Cell(0, 10, 'All User Report', 0, 2, 'C');
}

$pdf->Ln(5);

//set new format for column headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, 'No.', 1, 0, 'C');
$pdf->Cell(50, 10, 'Name', 1, 0, 'C');
$pdf->Cell(50, 10, 'Email', 1, 0, 'C');
$pdf->Cell(30, 10, 'Role', 1, 0, 'C');
$pdf->Cell(30, 10, 'Status', 1, 1, 'C');

//set new format for Contents
$pdf->SetFont('Arial', '', 10);

if (count($users) > 0) {
    $i = 1;
    foreach ($users as $user) {
        $pdf->Cell(30, 10, $i++, 1, 0, 'C');
        $pdf->Cell(50, 10, $user->first_name . ' ' . $user->last_name, 1, 0, 'C');
        $pdf->Cell(50, 10, $user->email, 1, 0, 'C');
        $pdf->Cell(30, 10, ucfirst($user->role), 1, 0, 'C');
        $pdf->Cell(30, 10, ucfirst($user->status), 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'No user record available', 1, 1, 'C');
}


$pdf->Output('I', $file_name);
?>
