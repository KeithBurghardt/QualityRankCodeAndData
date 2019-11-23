<?php
require_once 'amt_rest_api.php';
include_once 'db_utility.php';
//amt\balance_request::set_sandbox_mode();


function approveHit($hit_id){
	
	$results = new amt\results($hit_id);
	$results->approve_all('Well done');
	
	// change hit status to approved
	$conn = db_connect();
	$strsql="UPDATE experiments SET status='approved' WHERE hit_id='$hit_id'";
	$result = $conn->query($strsql);
	if(!$result){
		echo "An error has occured when updating hit status[mturk_func.php]\n";
		echo "Error:".$conn->errno;
		$conn-close();
		echo "APPROVE_FAILURE";
		exit;
	}
	$conn->close();	
}

function rejectHit($hitid) {
	
	$results = new amt\results($hit_id);
	foreach ($results as $asst) {
		$asst->reject('Not well done');
	}
	// change hit status to rejected
	$conn = db_connect();
	$strsql="UPDATE experiments SET status='rejected' WHERE hit_id='$hit_id'";
	$result = $conn->query($strsql);
	if(!$result) {
		echo "An error has occured when updating hit status[mturk_func.php]\n";
		echo "Error:".$conn->errno;
		$conn-close();
		echo "REJECT_FAILURE";
		exit;
	}
	$conn->close();
}

date_default_timezone_set('America/Los_Angeles');
$conn = db_connect();
$strsql="select * from experiments";
$result = $conn -> query($strsql);

while($asst = $result->fetch_assoc()) {
	if($asst['end_time'] == null || strlen($asst['hit_id']) < 5)
		continue;
	approveHit($asst['hit_id']);
	echo $asst['experiment_id'].'   '.$asst['hit_id'].'   '.$asst['status']."\n";
}

$result->free();
$conn -> close();
