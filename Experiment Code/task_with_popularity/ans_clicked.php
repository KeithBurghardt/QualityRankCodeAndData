<?php
  session_start();
  
$QNum = $_SESSION['answers']['QNum'];
$NumAns = $_SESSION['answers']['NumAns'][$QNum-1];  
$IsGuessing = $_SESSION['user']['guess'];

if(!$IsGuessing){
  for ($i = 0; $i < $NumAns; $i++)
  {

	$Stri = (string) ($i+1);

	$TimeInI = json_decode( $_POST['intime'.$Stri], true);

	$TimeOutI = json_decode( $_POST['outtime'.$Stri], true);

	$_SESSION['answers']['AnsHoverIn'][$QNum-1][$i] = array();

	$_SESSION['answers']['AnsHoverIn'][$QNum-1][$i] = $TimeInI;

	$_SESSION['answers']['AnsHoverOut'][$QNum-1][$i] = array();

	$_SESSION['answers']['AnsHoverOut'][$QNum-1][$i] = $TimeOutI;
  }
  $AnsClicked = (int) $_POST['AnswerChoice'];

  $_SESSION['answers']['AnsClicked'][$QNum-1] =  $AnsClicked;

}else{
  

  $Guess = $_POST["guess"]; 
  //Sanitize input
  if (!is_numeric($Guess)){//filter_var($Guess, FILTER_VALIDATE_INT) === false) {
        $_SESSION['answers']['guess'][$QNum-1] = NULL;
	$_SESSION['answers']['QNum'] = $QNum - 1;
	echo("Integer is not valid");
	exit;
  } 

  $_SESSION['answers']['guess'][$QNum-1] = $Guess;
  
}
  //less than or equal to 10 questions
  if($QNum <= 9) {
	echo "Success";
	exit;
}
 else {
	echo "Done";
	exit; 
}

?>
