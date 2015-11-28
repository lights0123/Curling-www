<?php
$GLOBALS['data']=json_decode(file_get_contents(SETTINGS_ROOT."/db.json"),true);
function DBConnect($db=null){
	$data=$GLOBALS['data'];
	if($db!=null){
		$conn = new mysqli($data['host'], $data['username'], $data['password'],$db);
	}else {
		$conn = new mysqli($data['host'], $data['username'], $data['password']);
	}
	return $conn;
}
function DBCheck($conn){
	return $conn->connect_errno;
}