$(document).ready(function(){
	
	$(".confirm").click(function(){
		
		$tmp_opr_id = $(this).parents("tr").attr("id");
		$td_elem = $(this);
		$.ajax({
	         type: "POST",
	         url: "opr_confirm.php",
	         data: "tmp_opr_id="+$tmp_opr_id,
	         success: function(msg) {
	        	 if(msg == 'confirmed'){
	        		 $td_elem.attr("disabled","disabled");
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
	
});