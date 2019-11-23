
<?php 
require_once 'db_utility.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Guess Task</title>
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
<?php
//Scores listed next to each answer denote the number of individuals who chose this answer in the past.
//		Please estimate the correct value to the following questions. 
//		We may ask you to explain why you chose each value at the end of the task.<br><br>

//In the following we will show you ten questions with two possible answers each. We ask you to choose the answer that is the closest to the correct value. To help your decision, we will tell you which answer was more popular with previous participants.
?>
<div id="login_form">
		<div class="logo">
		Choosing The Best Values</div>
		<div class="description">
Please estimate the correct value to the following questions.
We may ask you to explain why you chose each value at the end of the task.<br><br>
 <div style="text-align: center;">
   <input type="submit" id="start-btn" class="button" name="commit" onclick="myfun()" value="Start">
		<span id="msgbox" style="display:none"></span>
</div>		
            <br><br>
		<b>Consent Information:</b>
			<br>
			<br>
			<ul style="padding-left:40px">
			<i>Introduction</i><br><br>
			<ul style="padding-left:40px">
			<li>You are being asked to be in a research study on how people answer survey questions.</li><br>
			<li>We ask that you read this form and ask any questions that you may have before agreeing to be in the study.</li><br>
                        <li>Participation is voluntary but you can only complete this survey once.</li><br>
			</ul>
                        <i>Purpose of Study</i><br><br>
			<ul style="padding-left:40px">
			<li>The purpose of the study is to determine the best answers to questions listed in the following pages. </li><br>
                        <li>Ultimately, this research may be published, but your identity will remain confidential</li><br>
			</ul>
			<i>Description of the Study Procedures</i><br><br>
			<ul style="padding-left:40px">If you agree to be in this study, you will be asked to do the following things: You will find the best answer to a few questions. We may ask you to explain why you chose each value at the end of the task. Expect the experiment to take about 10 minutes or less.<br></ul>
			<br><i>Risks and Discomforts of Being in this Study</i><br><br>
                        <ul style="padding-left:40px">There are no reasonable foreseeable (or expected) risks. <br></ul>
                        <br><i>Benefits of Being in the Study</i><br><br>
			<ul style="padding-left:40px">You will receive compensation for your time. <br></ul>
 			<br><i>Confidentiality</i><br><br>
                        <ul style="padding-left:40px">This study is anonymous.  We will not be collecting or retaining any information about your identity.<br></ul>
			<br><i>Right to Refuse or Withdraw</i><br><br>
			<ul style="padding-left:40px">The decision to participate in this study is entirely up to you.  You may refuse to take part in the study at any time without affecting your relationship with the investigators of this study.  Your decision will not result in any loss or benefits to which you are otherwise entitled.  You have the right to withdraw completely from the survey at any point during the process.<br></ul>
			<br><i>Right to Ask Questions and Report Concerns</i><br><br>
			<ul style="padding-left:40px">You have the right to ask questions about this research study and to have those questions answered by me before or after the research.  If you have any further questions about the study, at any time feel free to contact Raissa D’Souza at raissa@cs.ucdavis.edu. 
			This research has been reviewed and approved by an Institutional Review Board (“IRB”). Information to help you understand research is on-line at http://www.research.ucdavis.edu/policiescompliance/irb-admin/.You may talk to a IRB staff member at (916) 703-9151, hs-irbadmin@ucdavis.edu, or 2921 Stockton Blvd, Suite 1400, Room 1429, Sacramento, CA 95817 for any of the following:</li><br><br>
                        <ul style="padding-left:40px">
			<li>Your questions, concerns, or complaints are not being answered by the research team.</li><br>
                        <li>You cannot reach the research team.</li><br>
                        <li>You want to talk to someone besides the research team.</li><br>
                        <li>You have questions about your rights as a research subject.</li><br></ul></ul>
                        <i>Consent</i><br><br>
			<ul style="padding-left:40px">Clicking “Start” above indicates that you have decided to volunteer as a research participant for this study, and that you have read and understood the information provided above. </ul>

		</div>
	
</div>


</body>
</html>


