<?php
session_start();
if(empty($_SESSION['user']))
	header("Location:visitor.php");	
/*elseif($_SESSION['answers']['QNum'] != 10)
	header("Location:visitor.php");*/	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Ranking Test</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/msg.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="js/visitor.js" type="text/javascript"></script>
</head>


<body class="login">


<div>
	<input type="hidden" id="assignmentId" name="assignmentId" value=""/>
	<input type="hidden" id="workerId" name="workerId" value=""/>
	<input type="hidden" id="hitId" name="hitId" value=""/>
</div>

<div id="login_form">
		
<div class="description">
<div class="logo">
		<center>Thank you for your time!<br><br> Your data has been recorded.</center>
</div><br>
<?php 

include_once 'db_utility.php';
include_once 'visibility.php';
// if session is not set redirect the user

	$survey_id = $_SESSION['user']['surveyid'];
	$survey_id_str = (string) $survey_id;
	echo "<br><br><center><h3>Survey code: ";
	echo $survey_id_str." </h3></center>";
	//$conn = db_connect();
	//$tb = "experiments_new";
	//$strsql="INSERT INTO $tb (survey_code)
	//		VALUES('$survey_id')";
        //    $result = $conn->query($strsql);
        //    if(!$result){
        //        echo "An error has occured when inserting a Q&A record to the table\n";
        //        echo "Error:".$conn->errno. "  ".$conn->error;
        //        $conn->close();
        //        exit;
        //    }

    // 4. destroy user session
    $result_dest = session_destroy();
?>
<br>
</div>


</div>
</body>
</html>


