<?php
require_once 'db_utility.php';
require_once 'visibility.php';


/*
	Read Qids off sequentially
	Create NumAns ($pos = rand(1,3), $val[$pos])
	Find answers
	Create answers from 1 - NumAns
	Sort:
		Random (shuffle)
		popularity (rand (1,100) score, sort by score (array_multisort([score, Aid]))
		actual_popularity (scores made by real users, sort by score)
	Record: Qid, NumAns, positions of ALL Aid, position of clicked Aid
	
*/
function print_stories_in_order($result, $show_number){
	echo "<div id=\"storylist\">\n";
      $conn2 = db_connect();
      $story_order = 0;
	while ($row = $result->fetch_assoc()) {
		print_one_story($row, $show_number);
            insert_story_order($conn2,$row, $story_order);
            $story_order = $story_order+1;
	}
      $conn2 -> close();
	echo "</div>\n";
}
function print_q($result,$QNumStr){
	$row = $result->fetch_assoc();
	//echo "<div id="Question">";
	if ($QNumStr!="8"){
	    echo "<h1> ";
	}
	else
	{
  	    echo " <h2>";
	}
	
	
	echo $row['Title'.$QNumStr];

	if ($QNumStr!="8"){
	    echo "</h1> ";
	}
	else
	{
  	    echo " </h2>";
	}
  	echo " <br><br> <font size=\"5\">";
	echo str_replace(">"," target=\"_blank\">",$row['Body'.$QNumStr]);
	//echo $row['Body'.$QNumStr];
	echo "</font>";
	echo " <br> ";
	echo " <br> ";
	//echo " <br> ";
	echo "<hr><hr>";
	//echo " <br> ";
}

function print_answers($result, $ordering,$NumAns,$QNum,$NotReloaded,$operation_table_name,$type)
{
	$row = $result->fetch_assoc();
	if($NotReloaded == 1){

	    //$answer_order = array_fill(0,(int)$NumAns,0);
	    $answer_order = array();
           if ($type == "random"){
		$answer_order = range(1,(int)$NumAns);
		shuffle($answer_order);
		//shuffle($answer_order);
		$_SESSION['answers']['Score'][((int)$QNum)-1] = array_fill(0,(int)$NumAns,0);

	    }
	    else{// answer scores are visible
    		    
		    $_SESSION['answers']['Score'][((int)$QNum)-1] = array();
		    //$high=1;
		    if ($type =="real_popularity_high_low")
		    {
			for($i = 0; $i < (int)$NumAns; $i++)
                        {
			    $score = $_SESSION['answers']['RealScores'][((int)$QNum)-1][$i]; 
   			    array_push($_SESSION['answers']['Score'][((int)$QNum)-1],$score);
			}
		    }
 		    else{
 		        for($i = 0; $i < (int)$NumAns; $i++)
		        {
			
			    if((int)$NumAns==2)
			    {
				$score = rand(0,100);
			    }
			    else{
				$score=rand(0,25);
			    }
		    
			    array_push($_SESSION['answers']['Score'][((int)$QNum)-1],$score);
			}
		    }
		//}
		$popularity = $_SESSION['answers']['Score'][((int)$QNum)-1];
		if ($ordering == "order by popularity asc"){
			sort($popularity);
		}elseif($ordering == "order by popularity desc"){
			rsort($popularity);
		}elseif($ordering == "random" && $NotReloaded == 1){
			shuffle($popularity);
		}
		// record score order
		$_SESSION['answers']['Score'][((int)$QNum)-1] = $popularity;
		if ($type == "popularity_randomized"){
		    $answer_order = range(1,$NumAns);
		    shuffle($answer_order);	
		}
		else{
		    //echo "THIS SHOULD NOT BE HAPPENING~";
	 	    $unique_scores = array_unique($popularity);
		    foreach ($unique_scores as $score)
		    {
			$score_array = array();
			$score_array[0] = $score;
			// all answers with the same score
			$a_pos_array = array_keys(array_intersect($_SESSION['answers']['Score'][((int)$QNum)-1],$score_array));
			// shuffle these orders
			shuffle($a_pos_array);
			// add shuffled order to array
			foreach($a_pos_array as $pos){
			    array_push($answer_order,1+$pos);
			}
		    }
		}
		unset($popularity);
	    }
	}

	// display score, record score on MySQL	
	echo "<br><br>";
	echo "<style class=\"cp-pen-styles\">div {
  			margin: 0 0 0.75em 0;
			}

			input[type=\"radio\"] {
			  display: none;
			}
	
			input[type=\"radio\"] + label{
			  color: #292321;
			  font-family: Arial, sans-serif;
			  font-size: 14px;
			}

			input[type=\"radio\"] + label span{

			  background-color: #7e7e7e;
			  border-bottom: 7px solid;
			  border-right: 7px solid; 
			  border-color: #FFFFFF #FFFFFF;
			    transform: rotate(0deg);
			  display: inline-block;

			  width: 20px;
			  height: 20px;
			  margin: -1px 9px -10px -20px;
			  vertical-align: middle;
			  cursor: pointer;
			  -moz-border-radius: 00%;
			  border-radius: 00%;
			}


			input[type=\"radio\"] + label span{
			  background-color: #7e7e7e;
			  border-color: #7e7e7e #7e7e7e;
			}


			input[type=\"radio\"]:checked + label span{

			  border-right: 7px solid; 
			  border-bottom: 7px solid;  
			  background-color: #FFFFFF;
			  border-color: #3cd024 #3cd024;
			  transform: rotate(35deg);
			  display: inline-block;
			  width: 12px;
			  height: 25px;
			  margin: -7px 10px -10px -13px;
			  vertical-align: middle;
			  cursor: pointer;
			  -moz-border-radius: 00%;
			  border-radius: 00%;

			}


			input[type=\"radio\"] + label span,
			input[type=\"radio\"]:checked + label span,
			input[type=\"radio\"]:hover + label span{
			  -webkit-transition: background-color 0.2s linear;
			  -o-transition: background-color 0.2s linear;
			  -moz-transition: background-color 0.2s linear;
			  transition: background-color 0.2s linear;
			}

		</style></head>";
	if ($NotReloaded == 1){
            $count = 0;
            foreach ($answer_order as $Ans){
                if($type != "random"){//display score
                        echo "<h3>".(string) $_SESSION['answers']['Score'][((int)$QNum)-1][$count]."</h3>";
                        $count = $count + 1; 
                }
		print_one_answer($row["A$Ans"],$Ans,$QNum,$guess,$new_pop);
 		$_SESSION['answers']['AnsOrder'][((int)$QNum)-1][] = $Ans;
	    }
	}
	else{
	    $answer_order = $_SESSION['answers']['AnsOrder'][((int)$QNum)-1];
	    $count = 0;
	    foreach ($answer_order as $Ans){
		if($type != "random"){//display score
			echo "<h3>".(string) $_SESSION['answers']['Score'][((int)$QNum)-1][$count]."</h3>";
			$count = $count + 1;
		}
		print_one_answer($row["A$Ans"],$Ans,$QNum,$guess,$new_pop);
	    }

	}
}
function print_rand_answers($AnsArray,$ordering,$NumAns,$QNum,$NotReloaded,$type)
{
	if($NotReloaded == 1){

	   //$answer_order = array_fill(0,(int)$NumAns,0);
	   $answer_order = array();
           if ($type == "random"){
		$answer_order = range(1,(int)$NumAns);
		shuffle($answer_order);
		//shuffle($answer_order);
		$_SESSION['answers']['Score'][((int)$QNum)-1] = array_fill(0,(int)$NumAns,0);

	    }
	    else{// answer scores are visible
    		    
		    $_SESSION['answers']['Score'][((int)$QNum)-1] = array();
		    //$high=1;
		    if ($type =="real_popularity_high_low")
		    {
			for($i = 0; $i < (int)$NumAns; $i++)
                        {
			    $score = $_SESSION['answers']['RealScores'][((int)$QNum)-1][$i]; 
   			    array_push($_SESSION['answers']['Score'][((int)$QNum)-1],$score);
			}
		    }
 		    else{
 		        for($i = 0; $i < (int)$NumAns; $i++)
		        {
			
			    if((int)$NumAns==2)
			    {
				$score = rand(0,100);
			    }
			    else{
				$score=rand(0,25);
			    }
		    
			    array_push($_SESSION['answers']['Score'][((int)$QNum)-1],$score);
			}
		    }
		//}
		$popularity = $_SESSION['answers']['Score'][((int)$QNum)-1];
		if ($ordering == "order by popularity asc"){
			sort($popularity);
		}elseif($ordering == "order by popularity desc"){
			rsort($popularity);
		}elseif($ordering == "random" && $NotReloaded == 1){
			shuffle($popularity);
		}
		// record score order
		$_SESSION['answers']['Score'][((int)$QNum)-1] = $popularity;
		if ($type == "popularity_randomized"){
		    $answer_order = range(1,$NumAns);
		    shuffle($answer_order);	
		}
		else{
		    //echo "THIS SHOULD NOT BE HAPPENING~";
	 	    $unique_scores = array_unique($popularity);
		    foreach ($unique_scores as $score)
		    {
			$score_array = array();
			$score_array[0] = $score;
			// all answers with the same score
			$a_pos_array = array_keys(array_intersect($_SESSION['answers']['Score'][((int)$QNum)-1],$score_array));
			// shuffle these orders
			shuffle($a_pos_array);
			// add shuffled order to array
			foreach($a_pos_array as $pos){
			    array_push($answer_order,1+$pos);
			}
		    }
		}
		unset($popularity);
	    }
	}

	// display score, record score on MySQL	
	//echo "<br><br>";
	echo "<style class=\"cp-pen-styles\">div {
  			margin: 0 0 0.75em 0;
			}

			input[type=\"radio\"] {
			  display: none;
			}
	
			input[type=\"radio\"] + label{
			  color: #292321;
			  font-family: Arial, sans-serif;
			  font-size: 14px;
			}

			input[type=\"radio\"] + label span{

			  background-color: #7e7e7e;
			  border-bottom: 7px solid;
			  border-right: 7px solid; 
			  border-color: #FFFFFF #FFFFFF;
			    transform: rotate(0deg);
			  display: inline-block;

			  width: 20px;
			  height: 20px;
			  margin: -1px 9px -10px -20px;
			  vertical-align: middle;
			  cursor: pointer;
			  -moz-border-radius: 00%;
			  border-radius: 00%;
			}


			input[type=\"radio\"] + label span{
			  background-color: #7e7e7e;
			  border-color: #7e7e7e #7e7e7e;
			}


			input[type=\"radio\"]:checked + label span{

			  border-right: 7px solid; 
			  border-bottom: 7px solid;  
			  background-color: #FFFFFF;
			  border-color: #3cd024 #3cd024;
			  transform: rotate(35deg);
			  display: inline-block;
			  width: 12px;
			  height: 25px;
			  margin: -7px 10px -10px -13px;
			  vertical-align: middle;
			  cursor: pointer;
			  -moz-border-radius: 00%;
			  border-radius: 00%;

			}


			input[type=\"radio\"] + label span,
			input[type=\"radio\"]:checked + label span,
			input[type=\"radio\"]:hover + label span{
			  -webkit-transition: background-color 0.2s linear;
			  -o-transition: background-color 0.2s linear;
			  -moz-transition: background-color 0.2s linear;
			  transition: background-color 0.2s linear;
			}

		</style></head>";
	$pop = array("<i>Most popular answer:</i> ","<i>Least popular answer:</i> ");
	$new_pop = "";

	if ($NotReloaded == 1){
            $count = 0;
            foreach ($answer_order as $Ans){
                if($type != "random"){//display score
                        echo "<h3>".(string) $_SESSION['answers']['Score'][((int)$QNum)-1][$count]."</h3>";
                        $count = $count + 1; 
                }else if (!$guess){
                        //echo "<h3>".$pop[$count]."</h3>";
                        //echo "<span font-size: 20px;>".$pop[$count]."</span>";
			$new_pop = $pop[$count];
                        $count = $count + 1; 
		}
		print_one_answer((string)$AnsArray[$Ans-1],$Ans,$QNum,$guess,$new_pop);
 		$_SESSION['answers']['AnsOrder'][((int)$QNum)-1][] = $Ans;
	    }
	}
	else{
	    $answer_order = $_SESSION['answers']['AnsOrder'][((int)$QNum)-1];
	    $count = 0;
	    foreach ($answer_order as $Ans){
		if($type != "random"){//display score
			echo "<h3>".(string) $_SESSION['answers']['Score'][((int)$QNum)-1][$count]."</h3>";
			$count = $count + 1;
		}else if (!$guess){
                        //echo "<span font-size: 20px;>".$pop[$count]."</span>";//
			//echo "<h3>".$pop[$count]."</h3>";
			$new_pop = $pop[$count];
                        $count = $count + 1; 
		}

		//print_one_answer($row["A$Ans"],$Ans,$QNum);
		print_one_answer((string)$AnsArray[$Ans-1],$Ans,$QNum,$guess,$new_pop);
	    }

	}

}

/*function print_rand_answer($randAns){
	echo "<div class=\"AnswerInput\">";
        //echo "<div id = \"$story_id\" class = \"storyitem\">";
        echo "<div class=\"Answer".$strAns."\">";

        //      echo "  <img src=\"images/thumb2.png\" />\n";
        //else echo "  <img src=\"images/thumb.png\"  />\n";


        //<input type="submit" id="start-btn" class="button" name="commit" value="Start" >
        //$strAns = (string)$Ans;
        echo "<input type=\"radio\" id=\"radio".$strAns."\" name=\"AnswerChoice\" value=$randAns/>
          <label for=\"radio".$strAns."\"><span></span></label>";

        //echo "<input type=\"radio\" id=\"rad\".$Ans name=\"AnswerChoice\" value=$Ans >";
        echo "<span class=\"answer\">";
        echo "<label for=\"rad\".$Ans >";
	echo $randAns;
	echo "</label></span></div></div>";
}*/

function print_one_answer($string,$Ans,$QNum,$guess,$new_pop){
	$strAns = (string)$Ans;
        //echo $strAns;
	// script below ammended from https://api.jquery.com/mouseover/

	echo "<div class=\"AnswerInput\">";
	//echo "<div id = \"$story_id\" class = \"storyitem\">\n";
	echo "<div class=\"Answer".$strAns."\">";

	//<input type="submit" id="start-btn" class="button" name="commit" value="Start" >
	//$strAns = (string)$Ans;
	echo "<input type=\"radio\" id=\"radio".$strAns."\" name=\"AnswerChoice\" value=$Ans/>
	  <label for=\"radio".$strAns."\"><span></span></label>";

	//echo "<input type=\"radio\" id=\"rad\".$Ans name=\"AnswerChoice\" value=$Ans >";
	echo "<span class=\"answer\">";


	echo "<label for=\"rad\".$Ans >";

	//$html_in_paren = 
	$NumMatchs = preg_match_all("((mailto\:|(news|(ht|f)tp(s?))\://){1}\S+)",$string,$matches);
	
	//if($html_in_paren){
	if($NumMatchs > 0 && $QNum < 5)
	{
	    foreach($matches[0] as $url)
	    {
		$url = explode("<br>",$url);
		$url = trim($url[0]," (),.;:");

		$string_vec = explode(' ',$string);
		$url_pos = array_search($url,$string_vec);
		if (!$url_pos){//if url is not found..., assume it is hidden within "()"
			$url_pos = array_search("(".$url.")",$string_vec);
		}
		$delimiter = "";
		if (!$url_pos){//if url is not found..., assume it is hidden within "()"
			$delimiter = ",";
			$url_pos = array_search("(".$url.")".$delimiter,$string_vec);
		}
		if (!$url_pos){//if url is not found..., assume it is hidden within "()"
			$delimiter = ".";
			$url_pos = array_search("(".$url.")".$delimiter,$string_vec);
		}
		if (!$url_pos){//if url is not found..., assume it is hidden within "()"
			$delimiter = ":";
			$url_pos = array_search("(".$url.")".$delimiter,$string_vec);
		}
		if (!$url_pos){//if url is not found..., assume it is hidden within "()"
			$delimiter = ";";
			$url_pos = array_search("(".$url.")".$delimiter,$string_vec);
		}
		if (!$url_pos){//if url is not found..., assume it is hidden within "()"

			$delimiter = ")<br><br>That";
			$url_pos = array_search("(".$url.$delimiter,$string_vec);
			$url = str_replace($delimiter,"",$url);
			$delimiter = "<br><br>That";
			//echo (string)$url;
		}
		if (!$url_pos){//if url is not found..., assume it is hidden within "()"

			$delimiter = ").<br><br>Therefore,";
			$url_pos = array_search("(".$url.$delimiter,$string_vec);
			$url = str_replace($delimiter,"",$url);
			$delimiter = ".<br><br>Therefore,";
			//echo (string)$url;
		}
		if (!$url_pos){//if url is not found..., assume it is hidden within "()"

			$delimiter = "<br><br>they";
			$url_pos = array_search($url,$string_vec);
			$url = str_replace($delimiter,"",$url);
			//echo (string)$url;
		}
		if($delimiter === "<br><br>they")

		{
			$hyperlinked_word = " <a href=$url>".$url."</a>";
			$string_vec[$url_pos+1] =  "here,<br><br>".$hyperlinked_word.$delimiter;
			//$string_vec[$url_pos + 1] =  $delimiter.($string_vec[$url_pos - 1]);
			$string = join(" ",$string_vec);
		} else
		{
			//echo "<br>delimiter:<br>";
			//echo $delimiter;
			//hyperlink previous word with url
			$word = $string_vec[$url_pos-1];
			$hyperlinked_word = " <a href=$url>".$word."</a>";
			$string_vec[$url_pos - 1] =  $hyperlinked_word.$delimiter;
			//$string_vec[$url_pos + 1] =  ($string_vec[$url_pos + 1]);

			if($url_pos > 0)
			{
			    unset($string_vec[$url_pos]);
			}
			$string = join(" ",$string_vec);
		}
	    }
	}
	if(!$guess){
            echo "<span font-size: 20px;>".$new_pop."</span><b>";
	    echo str_replace("''","\"",str_replace(">"," target=\"_blank\">",$string));
	    echo "</b>";
	}
	else{
	    echo str_replace("''","\"",str_replace(">"," target=\"_blank\">",$string));
	}
	//echo "$string";
	echo "</label>";
	echo "</span>";
	echo "</div>";
	echo "</div>";
	
	//echo "<script>
	//var intime".$strAns." = [];
	//var outtime".$strAns." = [];
	//    document.getElementById(\"demo\").innerHTML = len;
	////var Enter".$strAns." = document.getElementsByClassName(\"Answer".$strAns."\")[0];

	//(document.getElementsByClassName(\"Answer".$strAns."\")[0]).onmouseenter = function() {mouseEnter".$strAns."()};

	////Enter".$strAns.".onmouseenter = function() {mouseEnter".$strAns."()};
	//(document.getElementsByClassName(\"Answer".$strAns."\")[0]).onmouseleave = function() {mouseLeave".$strAns."()};

	////var Exit".$strAns." = document.getElementsByClassName(\"Answer".$strAns."\")[0];
	////Exit".$strAns.".onmouseleave = function() {mouseLeave".$strAns."()};

	//function mouseEnter".$strAns."() {
	//    var d = new Date();
	//    intime".$strAns.".push(d.getTime());
	//    //document.getElementById(\"demo".$strAns."\").innerHTML = len;
	//  }

	//function mouseLeave".$strAns."() {
	//    var d = new Date();
	//    outtime".$strAns.".push(d.getTime());
	//  }

	//function myFunction".$strAns."() {
	//    var len = outtime".$strAns.".length;
	//    document.getElementById(\"demo".$strAns."\").innerHTML = len;
	//}
	//</script>";
//MUST BE LISTED LAST
if(1){
	//(document.getElementsByClassName(\"Answer".((string)$Ans)."\")[0]).style.color = \"gray\";
	echo "
<script>
    
    (document.getElementsByClassName(\"Answer".((string)$Ans)."\")[0]).onmouseenter = function() {mouseEnter".((string)$Ans)."()};
    (document.getElementsByClassName(\"Answer".((string)$Ans)."\")[0]).onmouseleave = function() {mouseLeave".((string)$Ans)."()};
    var count = [];
    var intime".$strAns." = [];
    var outtime".$strAns." = [];
    function mouseEnter".((string)$Ans)."() {
    	
    	var d = new Date();
    	count.push(d.getTime());
    	intime".$strAns.".push(d.getTime());

    }
    function mouseLeave".((string)$Ans)."() {
    	(document.getElementsByClassName(\"Answer".((string)$Ans)."\")[0]).style.color = \"black\";
    	var d = new Date();
    	outtime".$strAns.".push(d.getTime());

    }
    function myFunction".((string)$Ans)."() {
        var len = outtime".((string)$Ans).".length;
        var val = intime".((string)$Ans)."[0];
        document.getElementById(\"demo".((string)$Ans)."\").innerHTML = len;
    }

</script>";
} 

	echo "<hr>";
}

function print_stories_in_random($result, $show_number){
	$arr = array();
	while ($row = $result->fetch_assoc()) {
		array_push($arr, $row);
	}
	shuffle($arr);
	echo "<div id=\"storylist\">\n";
      $conn2 = db_connect();
      $story_order = 0;
	foreach ($arr as $row){
		print_one_story($row, $show_number);
            insert_story_order($conn2,$row, $story_order);
            $story_order = $story_order+1;
	}

      $conn2 -> close();
      echo "</div>\n";
}

function insert_story_order($conn2,$row, $story_order)
{
    $experiment_id = $_SESSION['user']['u_id'];
    $story_id = $row['story_id'];

    $result = $conn2->query("replace INTO experiment_story_order (experiment_id, story_id, story_order, insert_time)
		values ($experiment_id, $story_id, $story_order, now())");

    if(!$result){
	   echo 'no';
	   $conn2->close();
	   exit;
     }

}

function get_domain($url)
{
	//$nowww = ereg_replace('www\.','',$url);
	$domain = parse_url($url);
	if(!empty($domain["host"]))
	{
		return $domain["host"];
	}
	else
	{
		return $domain["path"];
	}
}

function print_all_stories(){
    if(!isset($_SESSION['user']['u_visibility'])){
        return;
    }

    //OVERVIEW: print_all_stories: 
    //		run function "print_one_story/quesion" once. 
    //		echo "<input type="submit"...>" etc.
    //		
    //once we submit first question, go back to page

    //record Q num
    $QNum = (int) $_SESSION['answers']['QNum'];
    $QNumTest =count($_SESSION['answers']['AnsClicked']); // number of answers made
    //echo "<br><br><br>";
	//count($_SESSION['answers']['guess']);
    //$NumAnsRandom = $_SESSION['answers']['NumAnsRandom']; 

    // this condition ensures that reloading the webpage doesn't make the user skip a question
    // This also ensures that those in the "low number of answers" stay in that world and
    // vice versa 
    if($QNum === $QNumTest && $QNum < 10){
	$NotReloaded = 1;
    	$QNum++;
	if($NumAnsRandom)
	{
	    // $NumAns = 2 + rand(0,1) * 6;
	    // IF WE WANT BOTH 2 AND 8 ANSWERS VISIBLE AT RANDOM
	    // $NumAns = 2; OR 8;
	    // IF WE WANT ONLY 2 OR ONLY 8 ANSWERS
            $NumAns = 2 + rand(0,1) * 6;//2 or 8 answers
	}
	else{
	    $NumAns = $_SESSION['answers']['NumAns'][(int)$QNum - 1];
	}
    }
    else{
        $NumAns=$_SESSION['answers']['NumAns'][$QNum-1];
	$NotReloaded = 0;
    }

    $_SESSION['answers']['QNum'] = $QNum;
    $type = $_SESSION['user']['u_visibility'];
    $guess = $_SESSION['user']['guess'];
    
    $_SESSION['answers']['TimeStarted'][$QNum-1] = time();


    $_SESSION['answers']['NumAns'][$QNum-1] = $NumAns;
    echo "<script> var NumAns = ".strval($_SESSION['answers']['NumAns'][$QNum-1])."; </script>";
    $question_table_name = "Questions";
    $answer_table_name = "Answers".(string)$QNum;
   
    $operation_table_name = get_answer_order_name($type);
    $conn = db_connect();

    $QRand = $_SESSION['user']['QuestionOrder'][$QNum-1];
    $files = $_SESSION['user']['files'];
    $file = $files[$QRand-1];
    //echo "<br><br>";
    echo "<style>
	//div {
	//text-align: center;
	//}
	div.center {
	 //display: block;
	 //margin-left: auto;
	 //margin-right: auto;
	text-align: center
	}

	img {vertical-align: middle}
	</style>	
	";
    echo "<div class=\"center\">";
    //if(strpos($file,'Text') !== false){
    echo "<img src='$file' height=\"300\"/>";
    //}
    //else{ echo "<img src='$file' width=\"300\"/>";}
    echo "</div>";
    $description = "";
    if(strpos($file,'Ratio') !== false){
	$description = "Length ratio of the longer and shorter curves:";
    } elseif(strpos($file,'Line') !== false){
	$description ="Length ratio of the longer and shorter curves:";
    } elseif(strpos($file,'Perimeter') !== false){
	$description ="Perimeter ratio of the larger and smaller shapes:";
    } elseif(strpos($file,'Area') !== false){
	$description ="Area ratio of the larger and smaller shapes:";
    } elseif(strpos($file,'Text') !== false){
	$description ="The number of times the letter 'r' appears:";
    } elseif(strpos($file,'CountProb-fig-10.png') !== false){
	$description = "Number of pink circles:";
    } elseif(strpos($file,'CountProb-fig-7.png') !== false){
        $description = "Number of blue dots:";
    } elseif(strpos($file,'CountProb-fig-6.png') !== false){
        $description = "Number of dots (any shape or size):";
    } elseif(strpos($file,'Count') !== false){
	$description = "Number of dots (all shapes and sizes):";
    }
    //echo "<script type=\"text/javascript\">document.theFormID.theFieldID.focus();</script>";
    echo "<script>";
    if($guess){
        echo "var guess = true;";
    }else{
        echo "var guess = false;";
    }
    echo "</script>";

    if($guess){
        echo ("<form onkeypress=\"return event.keyCode != 13;\" method=\"post\"> ".$description.": <input type=\"text\" id=\"guess\" name=\"guess\" value=\"\" /><br>");
        if(!(strpos($file,'Count') !== false || strpos($file,'Lincoln') !== false)){
            echo "<font size=4><i>Decimal answers only (not fractions).</i></font>";
        }
        echo "<script> document.getElementById(\"guess\").focus() </script>";
    }else{

	//title
    	echo " <h3>";
    	echo $description."<br>";
    	echo " </h3>";
	//print_answers($result_ans, $operation_table_name,"$NumAns","$QNum",$NotReloaded,$operation_table_name,$type);
	//for ($ans=0; $ans<$_SESSION['answers']['NumAns'][$QRand-1]; $ans++){
	//    $randAns = $_SESSION['answers']['AnsVals'][$QRand-1][$ans];
	//    print_rand_answer($randAns);
	//}
	//var_dump($_SESSION['answers']['AnsVals']);
	print_rand_answers($_SESSION['answers']['AnsVals'][$QRand-1],$ordering,"$NumAns","$QNum",$NotReloaded,$type);

    }
    $conn -> close();

}
?> 

