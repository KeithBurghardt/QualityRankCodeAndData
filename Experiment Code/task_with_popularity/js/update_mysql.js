$(document).ready(function() {
	
        $mturk_descriptor = 'assignmentId=' + document.getElementById('assignmentId').value +
                                                '&hitId=' + document.getElementById('hitId').value +
                                                '&workerId=' + document.getElementById('workerId').value;
    $("#start-btn").click(function() {

                //remove all the class add the messagebox classes and start fading
                $("#msgbox").removeClass().addClass('messagebox').text('Configuring MySQL...').fadeIn(30);
                $.ajax({
                     type: "POST",
                     url: "configure_mysql.php",
                     data: $mturk_descriptor,
                     success: function(msg) {
                        if(msg == "success") 
                        {
                        	$("#msgbox").fadeTo(30,0.1,function()  //start fading the messagebox
			        {
			                  //add message and change the class of the box and start fading
			                  $(this).html('MySQL configured successfully!').addClass('messageboxok').fadeTo(10,.1,
			                  function()
			                  {
			                    //redirect to story browse page
			                    window.location.href = 'mysql_successful.php';
			                  });
			        });
			}
		        else{window.alert(msg);
			            	//start fading the messagebox
                            $("#msgbox").fadeTo(30,0.1,function() {
                                //add message and change the class of the box and start fading
                                $(this).html('MySQL configuration failed...').addClass('messageboxerror').fadeTo(30,1);
	    	            });
			}
			            
		     },
		     error: function (xhr, ajaxOptions, thrownError){
		     alert(xhr.status+" @configure_mysql_start.php");
		     alert(thrownError+" @configure_mysql_start.php");
		   }
		 });//ajax
			    
		       return false;//not to post the form physically
		});


});

