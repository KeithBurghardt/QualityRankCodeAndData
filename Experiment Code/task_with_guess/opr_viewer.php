<?php session_start();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Ranking Test</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/opr_confirm.js" type="text/javascript"></script>
</head>
<body>
<?php
    // if 'visability' condition exists
    if(isset($_GET['visibility'])) 	
    {
	// find session visability. THIS STAYS THE SAME THROUGHOUT
        $_SESSION['visibility'] = $_GET['visibility'];
    	ini_set('error_log', 'script_errors.log');
    	ini_set('log_errors', 'On');
    	ini_set('display_errors', 'Off');
    	
    	require_once('db_utility.php');
        require_once ('visibility.php');
        $tableName = get_temp_operation_table_name($_GET['visibility']);
        
        // SQL statement for data query
        $strsql="select * from ".$tableName;
        
        // get database connection
        $conn = db_connect();
        
        // execute SQL query
        $result = $conn -> query($strsql);
        
        echo "<font face=\"verdana\">\n";
        echo '<table border="1" cellpadding="1" cellspacing="2">';
    
        // show the column fields
        echo "\n<tr>\n";
        $fields = $result -> fetch_fields();
        foreach($fields as $fi => $f) 
    	{
    	  echo '<td bgcolor="#FFF5500"><b>'.
          $f->name;
          echo "</b></td>\n";
    	}
    	echo "<td bgcolor=\"#FFF5500\"><b>confirm</b></td>\n";
        echo "</tr>\n";
        
        // show data fields
        $fieldC = $result -> field_count;
        while ($row = $result->fetch_row()) {
        	echo "<tr id=\"$row[0]\">\n";
        	for($j=0; $j < $fieldC; $j++)
        	{
    	          echo '<td bgcolor="#4CB749">';
    	          echo $row[$j];
    	          echo "</td>\n";
        	}
        	echo '<td bgcolor="#4CB749">';
        	echo '<button class="confirm" type="button">confirm</button></td>';
        	echo "\n</tr>\n";
        }
     
        echo "</table>\n";
        echo "</font>";
        
        // release resource
        $result -> free();
        // close connection
        $conn -> close();  
    }
    else 
    {?>
        <ul>
    		<li><a href="opr_viewer.php?visibility=popularity">popularity</a></li>
    		<li><a href="opr_viewer.php?visibility=random">random</a></li>
        </ul>
    <?php
    }
?>
</body>
</html>
