
<?php 
require_once 'db_utility.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Configuring MySQL</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/msg.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="js/update_mysql.js" type="text/javascript"></script>
</head>


<body class="login">


<div>
	<input type="hidden" id="assignmentId" name="assignmentId" value=""/>
	<input type="hidden" id="workerId" name="workerId" value=""/>
	<input type="hidden" id="hitId" name="hitId" value=""/>
</div>
<?php
//Scores listed next to each answer denote the number of individuals who chose this answer in the past.
?>
<div id="login_form">
		<div class="logo">
		Configuring MySQL</div>
		<div class="description">

		Run this code first in order to configure MySQL for the experiment in which scores change dynamically.
            <br><br>
		<b>Important information: </b>
			<br>
			<br>
			<ul style="padding-left:40px">
			
				<li>You must do this before any experiment where scores dynamically update.</li><br>
				<li>You do not need to do this before an experiment in which scores update at random.</li><br>
				<li><b>Please remove configure_mysql_start.php after you click start.</b> Otherwise, anyone has the potential to modify our MySQL data.</li>
			</ul>
		</div>

    <input type="submit" id="start-btn" class="button" name="commit" value="Start" >

		<span id="msgbox" style="display:none"></span>
	
</div>


</body>
</html>


