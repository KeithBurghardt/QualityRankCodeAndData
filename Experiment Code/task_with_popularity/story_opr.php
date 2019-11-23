<?php session_start();

	require_once 'db_utility.php';
    require_once 'visibility.php';
	
	// if session is not set redirect the user
	if(empty($_SESSION['user']))
		header("Location:visitor.php");	

	$answer_id = $_POST['answer_id'];
	$Q_id = $_POST['Qid'];
	$position = $_POST['position'];
	$type = $_POST['click_type'];
	$user_id = $_SESSION['user']['u_id'];
	$visibility = $_SESSION['user']['u_visibility'];

    // get database connection
    $conn = db_connect();
    $tmp_opr_tb = get_temp_operation_table_name($visibility);
    
    // 1. URL Click
    if($type == 'onButton') {
	    // inset into database
	    $strsql="INSERT INTO $tmp_opr_tb(experiment_id, Q_id,answer_id, position, click_type) 
	    		 VALUES('$user_id','$Q_id','$answer_id', '$position', 'onButton')";
	    $result = $conn->query($strsql);
		if(!$result){
			echo "An error has occured when inserting a URL click to the operation table\n";
			echo "Error:".$conn->errno. "  ".$conn->error;
			exit;
		}
		
		$id = get_last_insert_id($conn);
		$_SESSION["answers"]["onButton"]["'$Q_id'"] = $id;
	}
	
    }
    
    // 2. Unknown type
    else {
        echo "Unrecognized operation type\n";
        exit;
    }
    
    // close connection
    $conn -> close();
    echo 'OPR_SUCCESS';
    
?>
