<?php
// Access the $_FILES global variable for this specific file being uploaded
// and create local PHP variables from the $_FILES array of information


$wmax = 640;
$hmax = 640;
$fileName = $_FILES["motif-image"]["name"]; // The file name
$fileTmpLoc = $_FILES["motif-image"]["tmp_name"]; // File in the PHP tmp folder
$fileType = $_FILES["motif-image"]["type"]; // The type of file it is
$fileSize = $_FILES["motif-image"]["size"]; // File size in bytes
$fileErrorMsg = $_FILES["motif-image"]["error"]; // 0 for false... and 1 for true
$kaboom = explode(".", $fileName); // Split file name into an array using the dot
$fileName = strtolower($fileName);
$fileExt = end($kaboom); // Now target the last array element to get the file extension





// START PHP Image Upload Error Handling --------------------------------------------------
if (!$fileTmpLoc) { // if file not chosen
    echo "Acho que talvez você tenha esquecido de selecionar uma imagem para enviar";
    exit();
} else if($fileSize > 3145728) { // if file size is larger than 3 Megabytes
    echo "Calma, calma! Consigo processar imagens com tamanho até 3 megabytes.";
    unlink($fileTmpLoc); // Remove the uploaded file from the PHP temp folder
    exit();
} else if (!preg_match("/.(gif|jpg|png)$/i", $fileName) ) {
     // This condition is only if you wish to allow uploading of specific file types    
     echo "Aceitamos apenas imagens (jpg, gif e png)";
     unlink($fileTmpLoc); // Remove the uploaded file from the PHP temp folder
     exit();
} else if ($fileErrorMsg == 1) { // if file upload error key is equal to 1
    echo "Ops! Algo não saiu como esperado. Tente de novo, por favor.";
    exit();
}
// END PHP Image Upload Error Handling ----------------------------------------------------



// Place it into your "uploads" folder mow using the move_uploaded_file() function
$fileName = "$motif_id." . strtolower($fileExt);
$moveResult = move_uploaded_file($fileTmpLoc, "uploads/$fileName");
// Check to make sure the move result is true before continuing
if ($moveResult != true) {
    echo "Ocorreu um erro no upload do arquivo.";
    unlink($fileTmpLoc); // Remove the uploaded file from the PHP temp folder
    exit();
}
//if($fileTmpLoc) unlink($fileTmpLoc); // Remove the uploaded file from the PHP temp folder



function ak_img_resize($target, $newcopy, $w, $h, $ext) {
    list($w_orig, $h_orig) = getimagesize($target);
    $scale_ratio = $w_orig / $h_orig;
    if (($w / $h) > $scale_ratio) { $w = $h * $scale_ratio; }
	    else { $h = $w / $scale_ratio; }
    $img = "";
    $ext = strtolower($ext);
    if ($ext == "gif"){ $img = imagecreatefromgif($target); }
	    else if($ext =="png"){ $img = imagecreatefrompng($target); }
	    else { $img = imagecreatefromjpeg($target); }
    $tci = imagecreatetruecolor($w, $h);
    // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
    imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
    if($h_orig > $h || $w_orig > $w) imagejpeg($tci, $newcopy, 85);
}

header("location: " . $_SESSION['user'] . "/motifs");
?>