<?php
if(isset($_POST['startDate']) && isset($_POST['endDate'])) {
	
	// should return all the hits starts between startDate and endDate
	
	require_once 'db_utility.php';
	$conn = db_connect();
	$strsql="select * from experiments";
	$result = $conn -> query($strsql);
	
	// convert starting date and ending date to a 'm-d-Y' format
	$format = 'm/d/Y H:i:s';
	$date = DateTime::createFromFormat($format, $_POST['startDate'].'00:00:00');
	$searchStart = $date->format('Y-m-d H:i:s');
	$date = DateTime::createFromFormat($format, $_POST['endDate'].'00:00:00');
	$searchEnd = $date->format('Y-m-d H:i:s');

	
		while($asst = $result->fetch_assoc()) {
			
				if($asst['end_time'] == null || strlen($asst['hit_id']) < 5) 
					continue;
				$end_time = strtotime($asst['end_time']);
				$start_search = strtotime($searchStart);
				$end_search = strtotime($searchEnd);
				if($end_time < $start_search || $end_time > $end_search)
					continue;
				
				
				echo "<tr class=\"hitRow\" id=\""; echo $asst['hit_id']; echo "\">";				
				echo "<td>"; echo $asst['hit_id']; echo "</td>";
		  		echo "<td>"; echo $asst['worker_id']; echo "</td>";
		  		echo "<td>"; echo $asst['assignment_id']; echo "</td>";
		  		echo "<td>"; 
		  			if(strcmp($asst['status'],'submitted')==0){
		  				echo "<button class='approveBtn'>Approve</button>";
		  				echo "<button class='rejectBtn'>Reject</button>";
		  			}else{
		  				echo $asst['status'];
		  			}
		  		echo "</td>";
		  		echo "<td>"; echo $asst['start_time']; echo "</td>";
		  		echo "<td>"; echo $asst['end_time']; echo "</td>";
		  		echo "<td>"; echo $asst['visibility']; echo "</td>";
		  		echo "<td>"; echo $asst['ipaddress']; echo "</td>";
		  		echo "</tr>\n";
		  		
		}
		$result->free();
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
 <h1>HITS Viewer</h1>
 	<script>
	$(function() {
		$( "#datepicker1" ).datepicker();
		$( "#datepicker2" ).datepicker();
	});
	</script>

	<div class="demo">
	  	<p>
	  		Start Date: <input type="text" id="datepicker1" />
	  		End Date: <input type="text" id="datepicker2" />
	  		<button id="searchHIT">search</button>
	   </p>
	</div>
	
<table border="1" cellpadding="1" cellspacing="2">
<tr>
	<th>HITID</th>
	<th>AssignmentId</th>
	<th>WorkerId</th>
	<th>AssignmentStatus</th>
	<th>AcceptTime</th>
	<th>SubmitTime</th>
	<th>Visibility</th>
	<th>IP</th>
</tr>
</table>


</body>
</html>