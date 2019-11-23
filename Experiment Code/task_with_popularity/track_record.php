<?php session_start();
	if($_SESSION['user']['u_opr_count'] < 5){
		echo "LESS_THAN_FIVE ".$_SESSION['u_opr_count'];
	}
	else {
		echo 'yes';
	}
?>