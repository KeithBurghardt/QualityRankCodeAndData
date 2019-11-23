<?php
require_once 'amt_rest_api.php';
include_once 'db_utility.php';
//amt\balance_request::set_sandbox_mode();

$hit_id = $_GET['HITId'];
$action_type = $_GET['actionType'];

$results = new amt\results($hit_id);

if(strcmp($action_type, "Approve")==0) {
	$results->approve_all('Well done');
	// change hit status to approved
	$conn = db_connect();
	$strsql="UPDATE experiments SET status='approved' WHERE hit_id='$hit_id'";
	$result = $conn->query($strsql);
	if(!$result){
		echo "An error has occured when updating hit status[mturk_confirmHIT.php]\n";
		echo "Error:".$conn->errno;
		$conn-close();
		echo "APPROVE_FAILURE";
		exit;
	}
	$conn->close();
	echo "APPROVE_SUCCESS";
}

else if(strcmp($action_type, "Reject")==0) {
	foreach ($results as $asst) 
		$asst->reject('Not well done');
	// change hit status to rejected
	$conn = db_connect();
	$strsql="UPDATE experiments SET status='rejected' WHERE hit_id='$hit_id'";
	$result = $conn->query($strsql);
	if(!$result) {
		echo "An error has occured when updating hit status[mturk_confirmHIT.php]\n";
		echo "Error:".$conn->errno;
		$conn-close();
		echo "REJECT_FAILURE";
		exit;
	}
	$conn->close();
	echo "REJECT_SUCCESS";
}

else {
	echo "action type error";
}
?>