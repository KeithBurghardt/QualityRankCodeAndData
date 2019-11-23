<?php
// generate captcha
include("simple-php-captcha.php");
$image_src = generateCaptcha();
?>
<script	src="js/dialog.js" type="text/javascript"></script>

<div id="dialog-overlay"></div>


<div id="dialog-box">

	<div class="dialog-content">
		<?php
		include_once 'questions.php';
		$questions = get_questions();
		$answers = get_answers();
		$count = 0;
		foreach ($questions as $question) {
			echo "<fieldset id=\"$count\" class=\"part1\">\n";
			echo "<p class=\"problem-title\">$question</p>\n";
			echo "<input class=\"answer\" type=\"text\" value=\"$answers[$count]\" />\n";
			echo "</fieldset>\n";
			$count++;
		}
		?>
		<fieldset class="part2">
			<p class= "problem-title"> Please confirm you are human</p>
			<?php echo "<img class=\"captcha-img\" src=\" $image_src \" alt=\"CAPTCHA\" />\n";?>
	        <a href="#" class="genCaptcha">generate a new Captcha</a>
	        <input class="answer cap-answer" id="captcha" type="text" />
	        <input type="button" id="dialog-btn-validate" value="Validate" />
		</fieldset>
		<div class = "dialog-btn-group">
			<!--  "http://workersandbox.mturk.com/mturk/externalSubmit" -->
    	    <form id="mturk_form" method="POST" action="http://www.mturk.com/mturk/externalSubmit" >
				<input type="hidden" id="assignmentId" name="assignmentId" value="<?php echo $_SESSION['mturk']['assignmentId'];?>">
				<input type="button" id="dialog-btn-cancel" value="Close"/>
				<input type="submit" id="submitButton" name="Submit" value="Submit">
			</form>
	    </div>
	</div>

</div>
