<?php session_start();
//include_once 'questions.php';
include_once 'db_utility.php';
include_once 'visibility.php';
//include_once 'validate_captcha.php';

//if(validate_captcha()) {
  if(1){  
    // 1. confirm all temporary operations user has done
    /*if(!confirm_all_operations ($_SESSION['user']['u_visibility'], 
       $_SESSION['answers']['NumAns']
	//, $_SESSION['answers']['AnswerChoice']
	))
    {
        echo "An error has occured when confirming operations [user_complete.php]\n";
        exit;
    }*/

    $conn = db_connect();
    // 2. add exp data
    $survey_code = $_SESSION['user']['surveycode'];
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    $type = $_SESSION['user']['u_visibility'];
    // get assignmentId, workerId, hitId
    //$assignmentId = $_POST['assignmentId'];
    //$workerId = $_POST['workerId'];
    //$hitId = $_POST['hitId'];
    //var_dump($result);
    //var_dump($ipaddress);
    //var_dump($type);
    $tb = "experiments_new";

    $result = $conn->query("INSERT INTO $tb (visibility, ipaddress, survey_code)
                values ('$type','$ipaddress','$survey_code')");
    if(!$result){
        echo 'no';
        $conn->close();
        exit;
	}



    // 3. collect user answers and store them in db
    $old_user = $_SESSION['user']['u_id'];
    $tb = "experiments_new";
    /*
    //foreach   ($_POST   as   $key   =>   $value) {
    foreach ($_SESSION['answers']['NumAns'] as $NumAns){
        //if(strval($key) != 'captcha') {
            //$question = $question_array[$key];
            //$answer = $value;
            $strsql="INSERT INTO $tb (NumAns,survey_code)
			VALUES('$NumAns','$survey_code')";
            $result = $conn->query($strsql);
            if(!$result){
                echo "An error has occured when inserting a Q&A record to the table\n";
                echo "Error:".$conn->errno. "  ".$conn->error;
                $conn->close();
                exit;
            }
        //}
    }*/
    $visibility = $_SESSION['user']['u_visibility'];
    $strsql="INSERT INTO $tb (visibility,survey_code)
                        VALUES('$visibility','$survey_code')";
    $result = $conn->query($strsql);
    if(!$result){
         echo "An error has occured when inserting a Q&A record to the table\n";
         echo "Error:".$conn->errno. "  ".$conn->error;
         $conn->close();
         exit;
    }

    for ($i=0; $i < count($_SESSION['answers']['NumAns']); ++$i){
    	$QNum = $i+1;
    	$NumAns = $_SESSION['answers']['NumAns'][$i];
    	$TimeStarted = $_SESSION['answers']['TimeStarted'][$i];
	$TimeStarted = date('YmdHis',$TimeStarted);
	$AnswerChosen = $_SESSION['answers']['AnsClicked'][$i];
        //if(strval($key) != 'captcha') {
            //$question = $question_array[$key];
            //$answer = $value;
            $strsql="INSERT INTO $tb (QNum,NumAns,TimeStarted,AnswerChosen,survey_code)
			VALUES('$QNum','$NumAns','$TimeStarted','$AnswerChosen','$survey_code')";
            $result = $conn->query($strsql);
            if(!$result){
                echo "An error has occured when inserting a Q&A record to the table\n";
                echo "Error:".$conn->errno. "  ".$conn->error;
                $conn->close();
                exit;
            }
	$TimeInArray = array();
	$TimeOutArray = array();
	$popularity = $_SESSION['answers']['Score'][$i];
	if ($visibility == "popularity_high_low"||$visibility == "real_popularity_high_low"){
                rsort($popularity);
        }//else{
        //      sort($popularity);
        //}
	for ($j=0; $j < $NumAns; ++$j){
	    $Answer = $_SESSION['answers']['AnsOrder'][$i][$j]; 
	    $Score = $popularity[$j];
	    $strsql="INSERT INTO $tb (QNum,AnswerOrder,Score,survey_code)
                       VALUES('$QNum','$Answer','$Score','$survey_code')";

	    $result = $conn->query($strsql);
            if(!$result){
                echo "An error has occured when inserting a Q&A record to the table\n";
                echo "Error:".$conn->errno. "  ".$conn->error;
                $conn->close();
                exit;
            }
	    $TimeInArray = $_SESSION['answers']['AnsHoverIn'][$QNum-1][$j];
            $TimeOutArray = $_SESSION['answers']['AnsHoverOut'][$QNum-1][$j];

	    foreach($TimeInArray as $TimeIn){
	    	$strsql="INSERT INTO $tb (TimeInArray_".((string)$j).",survey_code)
                       VALUES('$TimeIn','$survey_code')";
            	$result = $conn->query($strsql);
            	if(!$result){
            	    echo "An error has occured when inserting a Q&A record to the table\n";
            	    echo "Error:".$conn->errno. "  ".$conn->error;
            	    $conn->close();
                    exit;
            	}

	    }
	    foreach($TimeOutArray as $TimeOut){
		//echo (string) $TimeOut;
	    	$strsql="INSERT INTO $tb (TimeOutArray_".((string)$j).",survey_code)
                       VALUES('$TimeOut','$survey_code')";
            	$result = $conn->query($strsql);
            	if(!$result){
            	    echo "An error has occured when inserting a Q&A record to the table\n";
            	    echo "Error:".$conn->errno. "  ".$conn->error;
            	    $conn->close();
                    exit;
            	}

	    }

        }
	//}
    }
	/*
        //if(strval($key) != 'captcha') {
            //$question = $question_array[$key];
            //$answer = $value;
            $strsql="INSERT INTO $tb (NumAns)
			VALUES('$NumAns')";
            $result = $conn->query($strsql);
            if(!$result){
                echo "An error has occured when inserting a Q&A record to the table\n";
                echo "Error:".$conn->errno. "  ".$conn->error;
                $conn->close();
                exit;
            }
        //}*/
    //}


    // 4. insert finish event to experiment table
    $now = time();
    $now = date('YmdHis',$now);
    $tb = "experiments_new";

    $strsql="INSERT INTO $tb (end_time,status,survey_code) VALUES('$now','submitted','$survey_code')";// WHERE experiment_id='$old_user'

    $result = $conn->query($strsql);
    if(!$result){
        echo "An error has occured when adding an end_timestamp to experiment table[opr_complete.php]\n";
        echo "Error:".$conn->errno. "  ".$conn->error;
        $conn->close();
        exit;
    }

    // 5. add survey code to survey code table
    $survey_table = "survey_codes";
    $strsql="INSERT INTO $survey_table (survey_code)
                       VALUES('$survey_code')";
    $result = $conn->query($strsql);
    if(!$result){
	echo "An error has occured when inserting a Q&A record to the table\n";
	echo "Error:".$conn->errno. "  ".$conn->error;
        $conn->close();
        exit;
    }

    $conn -> close();
    
    


    if (!empty($old_user)) {
        if ($result_dest) {
            // if they were logged in and are now logged out
            echo "yes";
        } 
        else {
            // they were logged in and could not be logged out
            echo "session can not be destroyed";
        }
    }
    else {
        // if they weren't logged in but came to this page somehow
        echo "user has not been created";
    }
}
else {
    echo "captcha is incorrect";
}
header("Location: survey.php"); /* Redirect browser */
//header("Location: thank_you.php"); /* Redirect browser */
//exit();
?>
