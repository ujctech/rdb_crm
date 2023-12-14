<?php
require('newpdf/WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('newpdf/logo.png',18,13,33);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>Techzax Programming Blog, Tutorials, jQuery, Ajax, PHP, MySQL and Demos</h1><br>
Website: <u>www.techzax.com</u></para><br><br>How to Convert HTML to PDF with fpdf example');

$pdf->SetFont('Arial','B',7); 
$htmlTable='<TABLE>
<TR>
<TD>Name:</TD>
<TD>abcd</TD>
</TR>
<TR>
<TD>Email:</TD>
<TD>a@b.com</TD>
</TR>
<TR>
<TD>URl:</TD>
<TD>www.kswebapps.com</TD>
</TR>
<TR>
<TD>Comment:</TD>
<TD>abc</TD>
</TR>
</TABLE>';
$pdf->WriteHTML2("<br><br><br>$htmlTable");
$pdf->SetFont('Arial','B',6);
$ds          = DIRECTORY_SEPARATOR;
$filename = "tofilename.pdf";
//$target = dirname( __FILE__ ).$ds.'uploads'.$ds.'reports'.$ds.$filename;
$target = 'uploads'.$ds.'reports'.$ds.$filename;
echo $target;
$pdf->Output($target,'F'); 

//$pdf->Output(); 
?>