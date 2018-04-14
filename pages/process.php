<?php
require __DIR__ . '/vendor/autoload.php';
require_once './functions.php';

if(isset($_FILES['file'])){  
    $filePath = saveFile();
    $jsonData = convertExcelToJsonString($filePath);
    $response = sendJsonData($jsonData);
    session_start();
    if(isset($_SESSION['responses'])){
        $_SESSION['responses']='';
    }
    $_SESSION['responses'] = json_decode($response, true);
    //var_dump(json_decode($response, true));
}else{
    echo "Error uploading file";
}

header("Location: ./result.php");
exit();