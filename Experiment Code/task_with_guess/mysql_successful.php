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
                <div class="logo">
                MySQL has been successfully configured!</div>
                <div class="description">
		<b>Please go to user_start.php and do the following: </b>
                        <br>
                        <br>
                        <ul style="padding-left:40px">

                                <li>Around line 20, make sure '$type = "real_popularity_high_low";'</li><br>
                                <li>Around line 25, make sure '$ConstantNumAns = 2' or '$ConstantNumAns = 2' (if you want 2 or 8 answers per question, respectively)</li><br>
                                <li>Once you make these changes do not change the .php code until the experiment finishes</li><br>
				<li><b>Please remove configure_mysql_start.php.</b> Otherwise, anyone has the potential to modify our MySQL data.</li>
                        </ul>
                </div>

</div>

		
</div>
</body>
</html>


