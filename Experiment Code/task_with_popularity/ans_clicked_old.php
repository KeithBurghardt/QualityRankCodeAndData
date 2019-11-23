<?php
  session_start();
  
$QNum = $_SESSION['answers']['QNum'];
  
$NumAns = $_SESSION['answers']['NumAns'][$QNum-1];

  
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

  

  $AnsClicked = $_POST['AnswerChoice'];

  $_SESSION['answers']['AnsClicked'][$QNum-1] =  $AnsClicked;


  if($QNum <= 9) {
//less than or equal to 10 questions

   echo "Success";
  exit;
}
 else {

   echo "Done";
 exit; 
}

?>
