$(document).ready(function() {
	
	// Check if the worker is PREVIEWING the HIT or if they've ACCEPTED the HIT
	if (gup('assignmentId') == "ASSIGNMENT_ID_NOT_AVAILABLE")
	{
    	// If we're previewing, disable the button and give it a helpful message
	    document.getElementById('start-btn').disabled = true;
	    document.getElementById('start-btn').value = "Accept First";
	}
	else if(gup('assignmentId') != null && gup('assignmentId') != "") 
	{
			document.getElementById('assignmentId').value = gup('assignmentId');
			document.getElementById('workerId').value = gup('workerId');
			document.getElementById('hitId').value = gup('hitId');
			
			// Check if this user has recently visited our website
			$req = 'CHECK_VISIT_TWICE=' + document.getElementById('workerId').value;
			
                        
			$.ajax({
                type: "POST",
                url: "visitor.php",
                data: $req,
                success: function(msg) {
                    if(msg == "SUCCESS") 
                    {
                          
                    	// user passed the twice-visit check, no changes here
		            }
		            else
			        {
						 document.getElementById('start-btn').value = "Finished";
						 document.getElementById('start-btn').disabled = true;
		            }
		         },
				 error: function (xhr, ajaxOptions, thrownError){
					 document.getElementById('start-btn').value = thrownError;
					 document.getElementById('start-btn').disabled = true;
		         }
		    });

	}

	$mturk_descriptor = 'assignmentId=' + document.getElementById('assignmentId').value +
						'&hitId=' + document.getElementById('hitId').value +
						'&workerId=' + document.getElementById('workerId').value;
	//alert($mturk_descriptor);
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
		          else
			 {//window.alert(msg);
			            	//start fading the messagebox
                            $("#msgbox").fadeTo(30,0.1,function() {
                              //add message and change the class of the box and start fading
                              $(this).html('MySQL configuration failed...').addClass('messageboxerror').fadeTo(30,1);
	    	         	});
			 }
			            
			},
				    error: function (xhr, ajaxOptions, thrownError){
			            alert(xhr.status+" @visitor.php");
			            alert(thrownError+" @visitor.php");
			         }
		 });//ajax
			    
		       return false;//not to post the form physically
		});


  $("#answer-btn").click(function() {

                //remove all the class add the messagebox classes and start fading
                $("#msgbox").removeClass().addClass('messagebox').text('Loading Stories...').fadeIn(30);
                $.ajax({
                     type: "POST",
                     url: "story_browse.php",
                     data: $mturk_descriptor,
                     success: function(msg) {
                        if(msg == "success")
                        {
                                $("#msgbox").fadeTo(30,0.1,function()  //start fading the messagebox
                                {
                                          //add message and change the class of the box and start fading
                                          $(this).html('Loading successfully....').addClass('messageboxok').fadeTo(30,1,
                                          function()
                                          {
                                            //redirect to story browse page
                                            window.location.href = 'story_browse.php';
                                          });
                                });
                        }
			else
                         {//window.alert(msg);
                                        //start fading the messagebox
                            $("#msgbox").fadeTo(30,0.1,function() {
                              //add message and change the class of the box and start fading
                              $(this).html('Loading failed...').addClass('messageboxerror').fadeTo(30,1);
                                });
                         }

                        },
                                    error: function (xhr, ajaxOptions, thrownError){
                                    alert(xhr.status+" @visitor.php");
                                    alert(thrownError+" @visitor.php");
                                 }
                 });//ajax

                       return false;//not to post the form physically
                });
    
});

//
// This method Gets URL Parameters (GUP)
//
function gup( name )
{
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var tmpURL = window.location.href;
  var results = regex.exec( tmpURL );
  if( results == null )
    return "";
  else
    return results[1];
}

//
// This method decodes the query parameters that were URL-encoded
//
function decode(strToDecode)
{
  var encoded = strToDecode;
  return unescape(encoded.replace(/\+/g,  " "));
}
