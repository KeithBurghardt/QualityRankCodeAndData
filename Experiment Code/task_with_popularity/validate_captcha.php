<?php

validate_captcha(){
	$rand_num1 = rand(1,100);
	$rand_num2 = rand(1,100);
	$answer = $rand_num1 + $rand_num2;
	$_POST['CAPTCHA'] = $answer;
	echo ((string)$rand_num1)." + ".((string)$rand_num1)." = ?";
	echo "<input type=\"text\" name=\"TESTval\">";
	echo "<input type=\"submit\" name=\"SubmitCAPTCHA\">";
}

?>
