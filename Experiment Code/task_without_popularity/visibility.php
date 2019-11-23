<?php 
    
    // Associate each visibility type to its corresponding tables    
    $visibility_types = array(
                                       // answer_table_name            "answers_popularity"                    /           "answers_random"
                                        // operation_table_name         "operations_popularity"                 /           "operations_random"
                                        // answer_order_by             "order by popularity desc"               /           "random"
                                        //                              (assume we create random percentages)   /           (assume popularity is unknown)
                              
    "popularity_high_low"           => array(   "answer_table_name"       =>     "answers_popularity",
                                       "operation_table_name"   =>     "operations_popularity",
                                       "answer_order_by"         =>     "order by popularity desc",
					//"popularity_rank"	=>	array(1,2,3,4,5,6,7,8,9,10),// 10 answers total
                                       "is_show_number"         =>     false),

    "popularity_randomized"           => array(   "answer_table_name"       =>     "answers_popularity",
                                       "operation_table_name"   =>     "operations_popularity",
                                       "answer_order_by"         =>     "random",
                                        //"popularity_rank"     =>      array(1,2,3,4,5,6,7,8,9,10),// 10 answers total
                                       "is_show_number"         =>     false),

    "popularity_low_high"           => array(   "answer_table_name"       =>     "answers_popularity",
                                       "operation_table_name"   =>     "operations_popularity",
                                       "answer_order_by"         =>     "order by popularity asc",
					//"popularity_rank"	=>	array(1,2,3,4,5,6,7,8,9,10),// 10 answers total
                                       "is_show_number"         =>     false),

    "real_popularity_high_low" => array(   "answer_table_name"       =>     "real_answers_popularity",
                                       "operation_table_name"   =>     "operations_popularity",
                                       "answer_order_by"         =>     "order by popularity desc",
                                        //"popularity_rank"     =>      array(1,2,3,4,5,6,7,8,9,10),// 10 answers total
                                       "is_show_number"         =>     false),
                                       
    "random"               => array(   "answer_table_name"       =>     "answers_random",
                                       "operation_table_name"   =>     "operations_random",
                                       "answer_order_by"         =>     "random",
                                       "is_show_number"         =>     false),
                              
    );

function get_answer_table_name($type) {
    global $visibility_types;
    return $visibility_types[$type]["answer_table_name"];// change when we know what we're doing
}

function get_temp_operation_table_name($type){
    global $visibility_types;
    return 'tmp_'.$visibility_types[$type]["operation_table_name"];
}

function get_operation_table_name($type) {
    global $visibility_types;
    return $visibility_types[$type]["operation_table_name"];
}

function get_answer_order_name($type) {
    global $visibility_types;
    return $visibility_types[$type]["answer_order_by"];
}

function is_show_number($type) {
    global $visibility_types;
    return $visibility_types[$type]["is_show_number"];
}

function gen_visibility_type() {
    global $visibility_types;
    $keys = array_keys($visibility_types);
    //currently we want to hide recommendation numbers - $keys[rand(0,3)] rand(4,7)
    return $keys[0];
}

?>
