<?php
function db_connect() {
		
		$result = new mysqli('localhost','rank','hello','rank2');
		//username rank
		//password hello
		//database rank2
		/* check connection */
		if (mysqli_connect_errno()) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    exit();
		}

		$result->autocommit(TRUE);
			return $result;
}

function get_last_insert_id($conn){
    $strsql="SELECT last_insert_id()";
    $result = $conn->query($strsql);
    if($row = $result->fetch_array()){
        $id = $row[0];
        return $id;
    }
    return false;
}

?>
