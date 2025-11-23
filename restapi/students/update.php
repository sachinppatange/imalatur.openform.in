<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once '../config/config.php';
include_once '../class/Students.php';
 
$database = new Database();
$db = $database->getConnection();
 
$Students = new Students($db);
 
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->studid) && !empty($data->pstatus) && 
!empty($data->pdate)){ 
	
	$Students->studid = $data->studid; 
	$Students->pamount = $data->pamount;
    $Students->pstatus = $data->pstatus;
    $Students->pdate = $data->pdate;
    
	if($Students->update()){     
		http_response_code(200);   
		echo json_encode(array("message" => "Student was updated."));
	}else{    
		http_response_code(503);     
		echo json_encode(array("message" => "Unable to update Student."));
	}
	
} else {
	http_response_code(400);    
    echo json_encode(array("message" => "Unable to update Student. Data is incomplete."));
}
?>