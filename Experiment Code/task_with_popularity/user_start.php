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

$type = "random";//"popularity_randomized";
$guess = False;
// If $type = "real_popularity_high_low":
// This is the number of answers users will always see throughout the experiment
// This should be either 2 or 8 while the experiment is run!!!
$ConstantNumAns = 2;
$extreme_guesses = True;
$ipaddress = $_SERVER['REMOTE_ADDR'];


// get assignmentId, workerId, hitId
$_POST['assignmentId']=NULL;
$_POST['workerId']=NULL;
$_POST['hitId']=NULL;
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
$_SESSION['user']['guess'] = $guess;
$_SESSION['user']['u_opr_count'] = 0;
$QOrder = range(1,10);
shuffle($QOrder);
$_SESSION['user']['QuestionOrder'] = $QOrder;

$files = array("Perimeter1.png",
		"Line1.png",
		"Area1.png",
		"Area2.png",
		"Text1.png",
                "CountProb-fig-6.png",
                "CountProb-fig-7.png",
                "CountProb-fig-8.png",
                "CountProb-fig-9.png",
                "CountProb-fig-10.png");
$_SESSION['user']['files'] = $files;

// Number of answers per question

// Create an array of the number of answers users see for 10 questions
$_SESSION['answers'] = array();


$NumAns = array (2, 8);
$_SESSION['answers']['NumAnsRandom'] = False;
if ($type == "real_popularity_high_low" || !$guess)
{
    $_SESSION['answers']['NumAnsRandom'] = False;
}
$NumAnsRand=$_SESSION['answers']['NumAnsRandom'];


$_SESSION['answers']['NumAns'] = array();
$_SESSION['answers']['QNum'] = 0;
$_SESSION['answers']['AnsClicked'] = array();
$_SESSION['answers']['AnsOrder'] = array();
$_SESSION['answers']['Score'] = array();//Random score between 1-100
$_SESSION['answers']['RandQ'] = array();
$_SESSION['answers']['guess'] = array(); 
$_SESSION['answers']['AnsVals'] = array();
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
    array_push($_SESSION['answers']['AnsVals'],array());  
    $RandAsFile = "Rand".((string) $i).".txt";
    $lines = preg_split('/\r\n|\n|\r/', trim(file_get_contents($RandAsFile)));
    $float_log_lines = array();
    for($j=0; $j < count($lines); $j++){
	array_push($float_log_lines, log((float) $lines[$j]));
    }
    $float_log_lines = array_filter($float_log_lines);
    $float_log_lines_squared = array();
    for($j=0; $j < count($float_log_lines); $j++){
	array_push($float_log_lines_squared, $float_log_lines[$j]*$float_log_lines[$j]);
    }
    
    $log_mean =  array_sum($float_log_lines)/count($float_log_lines);
    $log_mean_x2 = array_sum($float_log_lines_squared)/count($float_log_lines_squared);
    $log_var = $log_mean_x2 - $log_mean * $log_mean;
    $log_sigma = sqrt($log_var);
    $QPlus1 = $log_mean + 1.3*$log_sigma;
    $GuessUpperBound = exp($QPlus1);
    $QMinus1 = $log_mean - 1.3*$log_sigma;
    $GuessLowerBound = exp($QMinus1);
    
    for($a=0;$a<$_SESSION['answers']['NumAns'][$i-1]; $a++){
        $answer = $lines[array_rand($lines)];
        // make one guess extreme. We choose $a=0, but guesses are shuffled
        if($extreme_guesses && $a == 0){
            // if guesses are too close to the mean, resample
	    while($GuessLowerBound < $answer && $answer < $GuessUpperBound){
	        $answer = $lines[array_rand($lines)];
	    }
	}
	array_push($_SESSION['answers']['AnsVals'][$i-1],$answer);
    }

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
