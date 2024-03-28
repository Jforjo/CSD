<?php
require_once('tcpdf/tcpdf.php');
session_start();
$userName = $_SESSION['userName'];
$completedTests = $_SESSION['completedTests'];

//Check to see if user is logged in, if not then redirect to login page
if (!isset($_SESSION['userName']))
{
    header("Location: /login");
    exit();    
}

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setPrintHeader(false);

// Set document information
$pdf->SetAuthor($userName);
$pdf->SetTitle($userName . "'s Quiz Results");

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font
$pdf->SetFont('helvetica', '', 12);

$highestPercentageQuiz = null;
$lowestPercentageQuiz = null;
foreach ($completedTests as $key => $test) {
    if (isset($test['correctCount'], $test['questionCount']) && $test['questionCount'] != 0) {
        $completedTests[$key]['percentage'] = $test['correctCount'] / $test['questionCount'] * 100;
        if ($highestPercentageQuiz === null || $completedTests[$key]['percentage'] > $completedTests[$highestPercentageQuiz]['percentage']) {
            $highestPercentageQuiz = $key;
        }
        if ($lowestPercentageQuiz === null || $completedTests[$key]['percentage'] < $completedTests[$lowestPercentageQuiz]['percentage']) {
            $lowestPercentageQuiz = $key;
        }
    }
}

// Add a page
$pdf->AddPage();

//$pdf->SetFillColor(24, 24, 24);
//$pdf->SetTextColor(255, 255, 255);

//$pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'F');

// Set font for the heading
$pdf->SetFont('helvetica', '', 20);

// Write the main heading that says "Stats for [name]"
$pdf->Write(0, "Stats for " . $userName, '', 0, 'L', true, 0, false, false, 0);

// Add a gap
$pdf->Ln(10); // Add a 10mm gap

//Set font back to 12 for the table
$pdf->SetFont('helvetica', '', 12);

// Insert table
$html = '<table border="1" cellspacing="3" cellpadding="4">
    <tr>
        <th>Test Name</th>
        <th>Subject</th>
        <th>Date Completed</th>
        <th>Score</th>
        <th>Percentage</th>
        <th>Points Earned</th>
    </tr>';
    foreach ($completedTests as $key => $test) {
        // Check if this quiz has the highest or lowest percentage
        if ($key == $highestPercentageQuiz) {
            // Set cell background colour to green
            $colour = 'bgcolor="#008000"';
        } elseif ($key == $lowestPercentageQuiz) {
            // Set cell background colour to red
            $colour = 'bgcolor="#FF0000"';
        } else {
            // Set cell background colour to white for all other quizzes
            $colour = 'bgcolor="#FFFFFF"';
        }

    $html .= '<tr>
        <td ' . $colour . '>' . $test['quiz'] . '</td>
        <td ' . $colour . '>' . $test['subject'] . '</td>
        <td ' . $colour . '>' . date('d/m/Y', strtotime($test['dateCompleted'])) . '</td>
        <td ' . $colour . '>' . $test['correctCount'] . '/' . $test['questionCount'] . '</td>
        <td ' . $colour . '>' . round(($test['correctCount'] / $test['questionCount']) * 100) . '%</td>
        <td ' . $colour . '>' . $test['points'] . '</td>
    </tr>';
}
$html .= '</table>';
$pdf->writeHTML($html, true, false, true, false, '');

$date = date('dMY'); //Get current date in the format dMY (e.g. 24Mar2024)
$filename = $userName . "_Stats_" . $date . ".pdf"; // Generate the filename


// Close and output PDF document
$pdf->Output($filename, 'I');
?>