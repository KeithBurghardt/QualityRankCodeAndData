
<?php session_start();

//session_start();
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
                <center>Thank you for your time!<br><br> Your answers have been recorded.</center>
</div>
<?php
include_once 'db_utility.php';
include_once 'visibility.php';
// if session is not set redirect the user

        $survey_code = $_SESSION['user']['surveycode'];
        $survey_code_str = (string) $survey_code;
	echo "<br><br><div style=\"border: 2px solid green;\">";
        echo "<br><center><font color=\"green\"><h3>Survey code: ";
        echo $survey_code_str." </h3></font></center>";
	echo "<br></div>";

    // 4. destroy user session
    $result_dest = session_destroy();
/*
include_once 'db_utility.php';
include_once 'visibility.php';

// ASK MULTIPLE CHOICE QUESTIONS HERE
// Include "other" in gender
//demographic information and why answers were chosen at the very end.

echo 'Please state the reason(s) for choosing the answers you did.<br>';
echo 'For example, did you tend to choose the simplest answer, the most descriptive answer, the most accurate answer, etc.?<br>';

echo 'What is your gender?<br>';

$Ans = 1;
$strAns = (string) $Ans;
echo "<input type=\"radio\" id=\"radio".$strAns."\" name=\"Male\" value=$Ans/>
          <label for=\"radio".$strAns."\"><span></span></label>";

$Ans = 2;
$strAns = (string) $Ans;
echo "<input type=\"radio\" id=\"radio".$strAns."\" name=\"Female\" value=$Ans/>
          <label for=\"radio".$strAns."\"><span></span></label>";

$Ans = 3;
$strAns = (string) $Ans;
echo "<input type=\"radio\" id=\"radio".$strAns."\" name=\"Non\-binary or choose not to say\" value=$Ans/>
          <label for=\"radio".$strAns."\"><span></span></label>";


echo 'What is your highest education? <br>';

$Ans = 1;
$strAns = (string) $Ans;
echo "<input type=\"radio\" id=\"radio".$strAns."\" name=\"Highschool\" value=$Ans/>
          <label for=\"radio".$strAns."\"><span></span></label>";

$Ans = 2;
$strAns = (string) $Ans;
echo "<input type=\"radio\" id=\"radio".$strAns."\" name=\"Some college\" value=$Ans/>
          <label for=\"radio".$strAns."\"><span></span></label>";

$Ans = 3;
$strAns = (string) $Ans;
echo "<input type=\"radio\" id=\"radio".$strAns."\" name=\"College Degree\" value=$Ans/>
          <label for=\"radio".$strAns."\"><span></span></label>";

$Ans = 3;
$strAns = (string) $Ans;
echo "<input type=\"radio\" id=\"radio".$strAns."\" name=\"Post-graduate\" value=$Ans/>
          <label for=\"radio".$strAns."\"><span></span></label>";
*/

?>
<br><br>
<h3>Debriefing:</h3><br>
<ul style="padding-left:40px">Our goal in this survey is to study factors affecting choices of answers to questions. For the purposes of the study, we have recorded what answer you chose and when, how answers are ranked, as well as which answers you moused over. We did not detail the nature of the data collection ahead of time, because we did not want to affect your decision process. Please contact Raissa D’Souza at raissa@cs.ucdavis.edu if you have any questions or concerns.</ul>

</div>

</div>
</body>
</html>

