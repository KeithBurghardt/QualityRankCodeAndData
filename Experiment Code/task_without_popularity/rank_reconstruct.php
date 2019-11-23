<?php


if(isset($_POST['uptoDate'])) {

	require_once 'db_utility.php';
	require_once 'visibility.php';
	
	$conn = db_connect();
	$type = 'random';
	$rank = array();
	
	// 1. get story information
	$story_table_name = get_story_table_name($type);
	$strsql="select story_id from ".$story_table_name;
	$result = $conn -> query($strsql);
	
	while($asst = $result->fetch_assoc()){
		$story_id = $asst['story_id'];
		$rank[$story_id] = array();
		$rank[$story_id]['activity'] = 0;
		$rank[$story_id]['popularity'] = 0;
	}
	
	$result->free();

	// 2. get each operation for current visibility and reconstruct the ranks
	$opr_table_name = get_operation_table_name($type);


	$strsql="select * from ".$opr_table_name;
	$result = $conn -> query($strsql);

	$format = 'm/d/Y H:i:s';
	$date = DateTime::createFromFormat($format, $_POST['uptoDate'].'00:00:00');
	$uptoDate = $date->format('Y-m-d H:i:s');

	while($asst = $result->fetch_assoc()) {
		$time1 = strtotime($uptoDate);
		$time2 = strtotime($asst['opr_time']);
		if($time2 >= $time1) continue;
		
		$id = $asst['story_id'];
		if( strcmp($asst['click_type'], "onButton") == 0 ) {
			$rank[$id]['activity'] ++;
			$rank[$id]['popularity'] ++;
		}
		else if (strcmp($asst['click_type'], "onURL") == 0){
			$rank[$id]['activity'] ++;
		}
		else {
			// no suck type
		}
	}
	$result->free();
	
	foreach ($rank as $storyId => $pair) {
		echo "<tr class=\"row\" >";
		echo "<td>"; echo $storyId; echo "</td>";
		echo "<td>"; echo $pair['activity']; echo "</td>";
		echo "<td>"; echo $pair['popularity']; echo "</td>";
		echo "</tr>\n";
	}
	
	$conn -> close();
	exit;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Reviewable Hits</title>
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/jquery-ui-1.8.20.custom.min.js"></script>
	<script src="js/mturk_confirm.js" type="text/javascript"></script>
	<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
</head>

<body>
 <h1>Rank Viewer</h1>
 	<script>
	$(function() {
		$( "#datepicker" ).datepicker();
	});
	</script>

	<div class="demo">
	   <p>
	  		Up-to Date: <input type="text" id="datepicker" />
	  		<button id="reconstruct">demo</button>
	   </p>
	</div>
	
<table border="1" cellpadding="1" cellspacing="2">
<tr>
	<th>storyId</th>
	<th>activity</th>
	<th>popularity</th>
</tr>
</table>


</body>
</html>