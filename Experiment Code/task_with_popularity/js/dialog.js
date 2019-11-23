$(document).ready(function(){
	

	
	// Cancel dialog
	$('#dialog-btn-cancel').click(function () {
		$('#dialog-overlay, #dialog-box').hide();
	});
	

     // Submit user input answers 
	// created by Suradej Intagorn
	// Note: method for new validate button 'dialog-btn-validate-eq'
    $('#dialog-btn-validate-eq').click(function () {

    	// generate post data
    	$data_descr = '';
    	$('fieldset.part1').each(function(index, value) {
    		$answer_id = $(this).attr("id");
    		$answer = '';
    		$(this).children(".answer").each(function(){
    			$answer = $answer + $(this).val() + "   ";
    		});
    		$data_descr = $data_descr + $answer_id + '=' + $answer + '&';
    	});
    	
    	
		// check whether result is correct or not
		if($('#eq_ans').val() == $('#captcha').val()){
	        $("#dialog-btn-validate").attr("disabled", "disabled");
               // ajax codes
               $.ajax({
	         type: "POST",
	         url: "user_complete.php",
	         data: $data_descr,
	         success: function(msg) {
                if($.trim(msg) == 'yes'){
                    //redirect to story browse page
                    window.location.href= 'finish.html';
                 }
               }
	        });
            
            
		}else{ 
               alert("incorrect try again");  
               var hv = $('#eq_ans').val();
               alert(hv); 
               loadEquation();
		}
    	

        return false;
    });

    // Generate a new captcha if the old one is not clear
    $('.genCaptcha').click(function () {
    	
    	// send xhs request
    	$.ajax({
	         type: "GET",
	         url: "simple-php-captcha.php",
	         data: "_UPDATE_CAPTCHA",
	         success: function(msg) {
	        	 $(".captcha-img").attr("src", msg);
	         },
			 error: function (xhr, ajaxOptions, thrownError){
				 $('#dialog-overlay, #dialog-box').hide();  
	         }
	    });
        return false;
    });

    $('.genEq').click(function () {
    	  var hv = $('#eq_ans').val();
        alert(hv);
    	// send xhs request
    	  loadEquation();
        return false;
    });
 
    $('#finish').click(function (){
    	$.ajax({
	         type: "POST",
	         url: "track_record.php",
	         success: function(msg) {
	        	 if(msg == "yes"){
	        		 popup();
	        	 }
	        	 else{
	        		 alert("Recommend at least five stories to continue");
	        		 $('#dialog-overlay, #dialog-box').hide();
	        	 }
	         },
			 error: function (xhr, ajaxOptions, thrownError){
				 $('#dialog-overlay, #dialog-box').hide();  
	         }
	   });
    	
    });
 
    // if user resize the window, call the same function again
    // to make sure the overlay fills the screen and dialogbox aligned to center    
    $(window).resize(function () {
        //only do it if the dialog box is not hidden
        if (!$('#dialog-box').is(':hidden')) popup();       
    });

});

//Popup dialog
function popup(message) {

    // get the screen height and width  
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();

    // calculate the values for center alignment
    var dialogTop =  40;
    var dialogLeft = (maskWidth/2) - ($('#dialog-box').width()/2); 

    // assign values to the overlay and dialog box
    $('#dialog-overlay').css({height:maskHeight, width:maskWidth});
    $('#dialog-overlay').fadeIn(800);
    $('#dialog-box').css({top:dialogTop, left:dialogLeft}).show("slow");

    // display the message
    $('#dialog-message').html(message);      
}

function loadEquation()
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
    var randomnumber1=Math.floor(Math.random()*11)+1;
    var randomnumber2=Math.floor(Math.random()*11)+1;
	var res = randomnumber1 + randomnumber2;
	var res_sen = res + "";
	var sen = randomnumber1 + "+" + randomnumber2 + "=";
      
       $('#eq_ans').val(res_sen);
	 $('#myDiv').html(sen);
	     
    
  }
xmlhttp.open("GET","ajax_info.txt",true);
xmlhttp.send();
}