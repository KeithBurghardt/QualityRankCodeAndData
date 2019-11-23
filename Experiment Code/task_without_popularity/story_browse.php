<?php session_start();
//var_dump($_SESSION['user']);
// if session is not set redirect the user
if(empty($_SESSION['user']))
	header("Location:visitor.php");	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Ranking Model Test</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/story.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="js/QA.js" type="text/javascript"></script>
</head>

<body id="story">
	<div id="login_form">
	<div class="Qdescription">
	<?php 	
	/*
	<div id="notify-container">
		<div id="notify--1" style="">
			<span class="notify-text">
		<font size="5">Please choose the answer closest to the correct value. Answers are ranked by popularity.</font>
		</span>
		</div>
	</div>

	*/
		echo "<span class=\"notify-text\"><center><font size=\"5\"><b>Please choose the answer closest to the correct value.</b></font></center></span>";
        	require_once('story_info_print.php');
		print_all_stories();
		//echo "<span>	TEST TEST </span>";
		
		$QNum = $_SESSION['answers']['QNum'];
		$NumLeft = (string) 10-$QNum;
		if ($NumLeft > 0){
			echo "<input type=\"submit\" id=\"answer-btn\" class=\"button\" name=\"FinalChoice\" value=\"Next Question ($NumLeft Questions Left)\" >";
		}else{
			echo "<input type=\"submit\" id=\"answer-btn\" class=\"button\" name=\"FinalChoice\" value=\"Finish\" >";
		}
	?>
		<span id="msgbox" style="display:none"></span></form>

	</div>
	</div>
</body>

</html>
