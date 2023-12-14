<?php
if (empty($_FILES['file-1'])) {
    echo json_encode(['error'=>'No files found for upload.']); 
    // or you can throw an exception 
    return; // terminate
}
 
$images = $_FILES['file-1'];
$file_title = empty($_POST['file_title']) ? '' : $_POST['file_title'];
$company_id = empty($_POST['company_id']) ? '' : $_POST['company_id'];
$description = empty($_POST['description']) ? '' : $_POST['description'];
$success = null;
 
$paths= [];
$filenames = $images['name'];
for($i=0; $i < count($filenames); $i++){
    //$ext = explode('.', basename($filenames[$i]));
    $ext=basename($filenames[$i]);
    //$target = "uploads" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);
    $target = "uploads" . DIRECTORY_SEPARATOR . $ext;
    if(move_uploaded_file($images['tmp_name'][$i], $target)) {
        $success = true;
        $paths[] = $target;
        
    } else {
        $success = false;
        break;
    }
    
}
if ($success === true) {
    save_data($file_title, $company_id, $description, $paths);
    $output = [];
} elseif ($success === false) {
    $output = ['error'=>'Error while uploading images. Contact the system administrator'];
    foreach ($paths as $file) {
        unlink($file);
    }
} else {
    $output = ['error'=>'No files were processed.'];
}
echo json_encode($output);
?>
