<?php
session_start();

require_once 'db_utility.php';
require_once 'visibility.php';

// Handler of opr_viewer.php
if(isset($_POST['tmp_opr_id'])){
    if(confirm_one_operation($_SESSION['visibility'], $_POST['tmp_opr_id']))
        echo 'confirmed';
    else
        echo 'error_confirmed';
    exit;
}

// Transfer all operations from a temporary table to a permanent table
function confirm_all_operations($visibility, 
				array $tmp_num_ans_arr
				//, array $tmp_ans_choice_arr
				) {
  $tmp_num_ans_arr = array_values($tmp_num_ans_arr);
  //$tmp_ans_choice_arr = array_values($tmp_ans_choice_arr);

  // KL - this would be the place to check that the user made at most 10 recommendations
  // if more than X recommendations, do not update story_tbl
  //if (count($tmp_rec_arr)>20) return true;

  for($i=0; $i<count($tmp_num_ans_arr); $i++){
      if(!confirm_one_operation($visibility, $tmp_num_ans_arr[$i]))
      return false;
  }

  /*for($i=0; $i<count($tmp_rec_arr); $i++){
     if(!confirm_one_operation($visibility, $tmp_rec_arr[$i]))
     return false;
  }*/

  return true;
}

function confirm_one_operation($visibility, $tmp_opr_id){

        $story_tb      = get_story_table_name($visibility);
        $opr_tb        = get_operation_table_name($visibility);
        $tmp_opr_tb    = get_temp_operation_table_name($visibility);

        // establish db connection
        $conn = db_connect();

        // find temporary operation record that has not been confirmed with operation id equal to 'tmp_opr_id'

        $strsql="SELECT * FROM $tmp_opr_tb WHERE opr_id = '$tmp_opr_id'";
        $result = $conn->query($strsql);
        if(!$result){
            echo "An error has occured when finding operation record in tmp_opr_table\n";
            echo "Error:".$conn->errno. "  ".$conn->error;
            $conn->close();
            return false;
        }

        if($result->num_rows == 0)
        	return true;

        if($row = $result->fetch_assoc()){
            $user_id = $row['experiment_id'];
            $story_id = $row['story_id'];
            $position = $row['position'];
            $type = $row['click_type'];
            $opr_time = $row['opr_time'];
        }
        $result->close();

        // begin a transaction
        $conn->autocommit(FALSE);

        // a. insert a click record to operation table
        $strsql="INSERT INTO $opr_tb(experiment_id, story_id, position, click_type, opr_time)
        	    		 VALUES('$user_id','$story_id', '$position', '$type', '$opr_time')";
        $result = $conn->query($strsql);
        if(!$result){
            $conn->rollback();
            $conn->autocommit(TRUE);
            echo "An error has occured when inserting a new operation[opr_confirm.php]\n";
            echo "Error:".$conn->errno. "  ".$conn->error;
            $conn->close();
            return false;
        }

        // b. select story activity
        $strsql="SELECT * FROM $story_tb WHERE story_id = '$story_id'";
        $result = $conn->query($strsql);
        $row = $result->fetch_assoc();
        if(!$row){
            $conn->rollback();
            $conn->autocommit(TRUE);
            echo "An error has occured when select story item[opr_confirm.php]\n";
            echo "Error:".$conn->errno. "  ".$conn->error;
            $conn->close();
            return false;
        }

        // c. URL Click
        if($type == 'onURL'){
            $activity = $row['activity'];
            $activity++;
            $result->close();
            $strsql="UPDATE $story_tb SET activity = '$activity' WHERE story_id = '$story_id'";
            $result = $conn->query($strsql);
            if(!$result){
                $conn->rollback();
                $conn->autocommit(TRUE);
                echo "An error has occured when $type [opr_confirm.php]\n";
                echo "Error:".$conn->errno. "  ".$conn->error;
                $conn->close();
                return false;
            }
        }

        // c. Recommendation Click
        if ($type == 'onButton'){
            $activity = $row['activity'];
            $activity++;
            $popularity = $row['popularity'];
            $popularity++;
            $result->close();
//            $strsql="UPDATE $story_tb SET activity = '$activity', popularity = '$popularity' WHERE story_id = '$story_id'";
// KL - changed this link to write recommendation time into submission_time (to be used by activity interface)
            $strsql="UPDATE $story_tb SET  submission_time='$opr_time', activity = '$activity', popularity = '$popularity' WHERE story_id = '$story_id'";
            $result = $conn->query($strsql);
            if(!$result){
                $conn->rollback();
                $conn->autocommit(TRUE);
                echo "An error has occured when $type [opr_confirm.php]\n";
                echo "Error:".$conn->errno. "  ".$conn->error;
                $conn->close();
                return false;
            }
        }


        // d. delete temporary operation record
        $strsql="DELETE FROM $tmp_opr_tb WHERE opr_id = '$tmp_opr_id'";
        $result = $conn->query($strsql);
        if(!$result){
            $conn->rollback();
            $conn->autocommit(TRUE);
            echo "An error has occured when delete temporary operation[opr_confirm.php]\n";
            echo "Error:".$conn->errno. "  ".$conn->error;
            $conn->close();
            return false;
        }

        // destroy db connection
        $conn->commit();
        $conn->autocommit(TRUE);
        $conn -> close();
        return true;
}

?> 
