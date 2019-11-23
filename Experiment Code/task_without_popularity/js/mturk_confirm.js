$(document).ready(function(){
	
	// search hits function
	$("#searchHIT").click(function(){

		$startDate = $("#datepicker1").val();
		$endDate = $("#datepicker2").val();
		
		// validate date variable
		if($startDate == '' || $startDate == null) return;
		if($endDate == '' || $endDate == null) return;
		if(new Date($startDate).getTime() > new Date($endDate).getTime()) {
			alert("the end date is before start date");
			return;
		}
		
		$.ajax({
	         type: "POST",
	         url:  "mturk_viewer.php",
	         data: "startDate="+$startDate+"&endDate="+$endDate,
	         success: function(msg) {
	     			// retrieve all hits
	        	    $('.hitRow').remove();
	        	 	$('table').append(msg);
	        	 
	        	 	// add approve function
	        		$(".approveBtn").click(function(){

	        			$tr_elem = $(this).parents("tr");
	        			$hit_id = $tr_elem.attr("id");
	        			$.ajax({
	        		         type: "GET",
	        		         url: "mturk_confirmHIT.php",
	        		         data: "HITId="+$hit_id+"&actionType=Approve",
	        		         success: function(msg) {
	        		        	 if(msg == 'APPROVE_SUCCESS'){
	        		        		$("#"+$hit_id).find('.approveBtn').remove();
	        		        		$("#"+$hit_id).find('.rejectBtn').remove();
	        		         	 }
	        		        	 else{
	        		        		alert(msg);
	        		        	 }
	        		         },
	        				 error: function (xhr, ajaxOptions, thrownError){
	        					 alert(thrownError);
	        		         }
	        		    });
	        			return false;
	        		});

	        		// add rejection function
	        		$(".rejectBtn").click(function(){

	        			$tr_elem = $(this).parents("tr");
	        			$hit_id = $tr_elem.attr("id");
	        			
	        			$.ajax({
	        		         type: "GET",
	        		         url: "mturk_confirmHIT.php",
	        		         data: "HITId="+$hit_id+"&actionType=Reject",
	        		         success: function(msg) {
	        		        	 if(msg == 'REJECT_SUCCESS'){
	        		        		$("#"+$hit_id).find('.approveBtn').remove();
	        		        		$("#"+$hit_id).find('.rejectBtn').remove();
	        		        	 }
	        		        	 else {
	        		        		alert(msg); 
	        		        	 }
	        		         },
	        				 error: function (xhr, ajaxOptions, thrownError){
	        					 alert(thrownError);
	        		         }
	        		    });
	        			return false;
	        		});

	         },
			 error: function (xhr, ajaxOptions, thrownError){
				 alert(thrownError);
	         }
		});
		return false;
	});
	
	// rank reconstruct button
	$("#reconstruct").click(function(){	
		$uptoDate = $("#datepicker").val();
		
		//validate date variable
		if($uptoDate == '' || $uptoDate == null) return;
		$.ajax({
	         type: "POST",
	         url:  "rank_reconstruct.php",
	         data: "uptoDate="+$uptoDate,
	         success: function(msg) {
	        	    $('.row').remove();
	        	 	$('table').append(msg);
	         },
			 error: function (xhr, ajaxOptions, thrownError){
				 alert(thrownError);
		     }
		});
		return false;

	});
}); 