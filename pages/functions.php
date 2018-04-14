<?php

    function saveFile(){
        $errors = array();
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_type = $_FILES['file']['type'];
        $file_ext = strtolower(end(explode('.',$file_name)));
        
        $expensions = array("csv","xls","xlsx");
        
        if(in_array($file_ext,$expensions)=== false){
            $errors[]= "extension not allowed, please choose a EXCEL file.";
        }
        
        //if($file_size > 2097152){
            //$errors[]='File size must be excately 2 MB';
        //}
        
        if(empty($errors)==true){
            if(!file_exists('file_uploads/'))
                mkdir('file_uploads');
                
            move_uploaded_file($file_tmp,"file_uploads/".$file_name);
            //getFileContents('file_uploads/'.$fileName);
            return "file_uploads/".$file_name;
        }else{
            print_r($errors);
            return false;
        }
    }

    function convertExcelToJsonString($filePath){
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($filePath);

        $worksheet = $spreadsheet->getActiveSheet();
        $dataArray = collect([]);
        foreach ($worksheet->getRowIterator() as $row) 
        {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $data = collect([]);
            foreach ($cellIterator as $cell) 
            {
                $data->push($cell->getValue());
            }
            $dataArray->push((object)$data->all());
        }
        return $dataArray->toJson();
    }

    function sendJsonData($valueofjsonstring){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://gvivegh.com:1352/VotersService");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "jsonstring=$valueofjsonstring");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return $server_output;
    }