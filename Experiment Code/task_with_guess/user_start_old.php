<?php session_start();

$_SESSION = array();

require_once 'visibility.php';

require_once 'db_utility.php';

// connect to database from here
$conn = db_connect();

// generate a visibility type for user
/*
Options:
    "random": list answers randomly (no scores visible)
    "popularity_high_low": list answers randomly (scores listed highest to lowest)
    "popularity_randomized": list answers and scores randomly
    "real_popularity_high_low": dynamically update scores. 
				NOTE: scores users see are based on scores BEFORE user started experiment. 
				TODO: scores before user begins question
*/

$type = "popularity_randomized";

// If $type = "real_popularity_high_low":
// This is the number of answers users will always see throughout the experiment
// This should be either 2 or 8 while the experiment is run!!!
$ConstantNumAns = 8;

$ipaddress = $_SERVER['REMOTE_ADDR'];


// get assignmentId, workerId, hitId
$assignmentId = $_POST['assignmentId'];
$workerId = $_POST['workerId'];
$hitId = $_POST['hitId'];


$_SESSION['mturk']['assignmentId'] = $assignmentId;


// set all session variables
//User ID
$_SESSION['user']['u_id'] = get_last_insert_id($conn);
$_SESSION['user']['workerId'] = $workerId;
$_SESSION['user']['surveycode']= rand(100000,10000000);
// Visability of answer (random, popular,...)
$_SESSION['user']['u_visibility'] = $type;
$_SESSION['user']['u_opr_count'] = 0;
// Number of answers per question

// Create an array of the number of answers users see for 10 questions
$_SESSION['answers'] = array();


$NumAns = array (2, 8);
$_SESSION['answers']['NumAnsRandom'] = True;
if ($type == "real_popularity_high_low")
{
    $_SESSION['answers']['NumAnsRandom'] = False;
}
$NumAnsRand=$_SESSION['answers']['NumAnsRandom'];


$_SESSION['answers']['NumAns'] = array();
$_SESSION['answers']['QNum'] = 0;
$_SESSION['answers']['AnsClicked'] = array();
$_SESSION['answers']['AnsOrder'] = array();
$_SESSION['answers']['Score'] = array();//Random score between 1-100
$_SESSION['answers']['RealScores'] = array();
$_SESSION['answers']['TimeStarted'] = array();
$_SESSION['answers']['AnswerChosen'] = array();
//$_SESSION['answers']['MouseMovementPos'] = array();
//$_SESSION['answers']['MouseMovementTime'] = array();
$_SESSION['answers']['AnsHoverIn']=array();
$_SESSION['answers']['AnsHoverOut']=array();


$NumQs = 10;
for ($i =1; $i <=$NumQs; $i ++){
    if($NumAnsRand){
	$ind = rand(1,2);
        array_push($_SESSION['answers']['NumAns'],$NumAns[$ind-1]);
    }
    else{
        array_push($_SESSION['answers']['NumAns'],$ConstantNumAns);
    }
    array_push($_SESSION['answers']['AnsOrder'],array());
    array_push($_SESSION['answers']['Score'],array());
    array_push($_SESSION['answers']['RealScores'],array());
    if ($type == "real_popularity_high_low"){
	//find the last survey completed
	// LAST SURVEY ID
	$survey_table = "survey_codes";
	$strsql_id="SELECT survey_code FROM $survey_table ORDER BY id DESC LIMIT 1";
	$result_id = $conn -> query($strsql_id);
	$row = $result_id->fetch_assoc();
	$survey_code = $row['survey_code'];
	$tb = "experiments_new";
	//$strsql="SELECT QNum,Score,AnswerOrder,AnswerChosen,survey_code FROM $tb WHERE survey_code = '$survey_code' AND QNum = '$i' AND (Score IS NOT NULL OR AnswerOrder IS NOT NULL OR AnswerChosen IS NOT NULL) ORDER BY -AnswerOrder DESC, assignment_id DESC";
        $default = "NA";
        $strsql="SELECT QNum,Score,AnswerOrder,AnswerChosen,survey_code FROM $tb WHERE survey_code = '$survey_code' AND QNum = '$i' AND (Score <> '$default' OR AnswerOrder <> '$default' OR AnswerChosen <> '$default') AND (Score <> '$default' OR AnswerOrder <> 0 OR AnswerChosen <> 0) AND (Score IS NOT NULL OR AnswerOrder IS NOT NULL OR AnswerChosen IS NOT NULL) ORDER BY -AnswerOrder DESC, assignment_id DESC";

        $result = $conn->query($strsql);

        if(!$result){
                echo "An error has occured when inserting a Q&A record to the table\n";
                echo "Error:".$conn->errno. "  ".$conn->error;
                //echo 'success';
                $conn->close();
                exit;
        }

        $scores = array();
        $ans_chosen = 0;//$row["AnswerChosen"];
        //var_dump($row = $result->fetch_assoc());
        while($row = $result->fetch_assoc()){
            if($row["Score"] !== NULL && $row["Score"] !== 'NA'){
                array_push($scores,$row["Score"]);
            }
            if($row["AnswerChosen"] !== NULL && $row["AnswerChosen"] != 0 && $row["Score"] !== 'NA'){
                $ans_chosen = (int) $row["AnswerChosen"]-1;
            }
        }

        $scores[$ans_chosen] = $scores[$ans_chosen] + 1;
        $NumAns =  $_SESSION['answers']['NumAns'][$i-1];
        for($j = 1; $j <= $NumAns; $j ++){
            array_push($_SESSION['answers']['RealScores'][$i-1],$scores[$j-1]);
        }
    }
    //array_push($_SESSION['answers']['MouseMovementPos'],array());
    //array_push($_SESSION['answers']['MouseMovementTime'],array());
    array_push($_SESSION['answers']['AnsHoverIn'],array());
    array_push($_SESSION['answers']['AnsHoverOut'],array());

} 

$conn->close();

echo 'success';
exit;

?>
