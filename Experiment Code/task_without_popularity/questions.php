<?php
	$question_array = array(
	"What is the impact of the science topics you recommended (social, economic, technological, intellectual)?",
	"Will the impact be short-term or long-term?"
	);

	$answer_array = array(
		"social",
		"long-term"
	);
	
	function get_questions(){
		global $question_array;
		return $question_array;
	}
	
	function get_answers(){
		global $answer_array;
		return $answer_array;
	}

	function validate_captcha(){

		global $question_array;
		$user_captcha = $_POST['captcha'];
		if(strtolower($user_captcha) != strtolower($_SESSION['captcha']['code'])){
			return false;
		}

		return true;
	}
?>