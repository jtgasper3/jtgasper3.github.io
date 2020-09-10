<?
/*
	Runs from a directory containing files to provide an
	RSS 2.0 feed that contains the list and modification times for all the
	files.

d=1
printf -v f "%03d" $d
while wget -O USP$f.jpg "https://media.pegasuslectures.com/_site/ebooks/UPI_5e_9-1/docs/USPhysics5e_Edited.pdf_$d.jpg"; do
		((d++))
		printf -v f "%03d" $d
done

docker run --rm -it -v $(pwd):/tmper php bash
cd /tmper
apt-get update
apt-get install -y zip
curl 'http://www.fpdf.org/en/dl.php?v=182&f=zip' > fpdf.zip
unzip fpdf.zip
php -d memory_limit=-1 jpegs2ebook.php
*/
require('fpdf.php');

$allowed_ext = ".JPG";

$files = scandir("./");
sort($files);
foreach ($files as $file) {
	$path_info = pathinfo($file);
	$ext = strtoupper($path_info['extension']);

	if($file !== '.' && $file !== '..' && !is_dir($file) && strpos($allowed_ext, $ext)>0) {  
		$files[]['name'] = $file;  
		$files[]['timestamp'] = filectime($file);
	}  
}  

$pdf = new FPDF("P", "mm", "Letter"); 
$pdf->SetMargins(0, 0);
$pdf->SetTitle("Ultrasound Physics and Instrumentation, 5th Edition");
$pdf->SetAuthor("Frank R. Miele");
$pdf->SetCreator("Secret Boyfriend");
for($i=0; $i<count($files); $i++) {
	if (!empty($files[$i]['name'])) {
		print($files[$i]['name']);print("\n");
		$pdf->AddPage("P", "Letter");
		$pdf->Image($files[$i]['name'], 0, 0, -360);
	}
}

$pdf->Output('Ultrasound Physics and Instrumentation, 5th Edition.pdf', 'F');
?>
