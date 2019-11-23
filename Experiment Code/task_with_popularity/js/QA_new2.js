$(document).ready(function(){
	//var storyID;
	
	// 1. animate the story list
	//show_stories();

	// 2. change the attributes of a recommendation button
	// $(".recbutton").hover(function() {
	// 	$(this).css("background-color","#8FC88B");
	// 	$(this).css("border-color","#8FC88B");
	// 	$(this).css("cursor","pointer");
	// }, function() {
	// 	$(this).css("background-color","#B5CBE2");
	// 	$(this).css("border-color","#B5CBE2");
	// });

	// 3. highlight the selected story item
	/*$("#storylist .storyitem").hover(function() {
		$(this).children(".content").css("background","#EBEFEF");
	}, function(){
		$(this).children(".content").css("background","#FFFFFF");
	});*/

	// 4. click the story url, opr_number 1 denotes this is a 'URL click'
	/*$("input:radio[name=option]").click(function() {
            var value = $(this).val();
            var image_name;
            if(value == ''){
                image_name = "formula_gdp.gif";
            }else{
                if(value == 'Population'){
                    image_name = "formula_pop.gif";
                }else{
                    image_name = "formula_none.gif";
                }
            }
             $('#formula').attr('src', image_name);
        });*/
	/*$(".content .title a").click(function (event) {
		storyID = $(this);
		if (story_on_click($(this), "onURL"))
			popup2();
		$('#iframe-article').attr('src', $(this).attr("href"));
		//window.open($(this).attr("href"));
		return false;
	});*/

	// 5. click the recommendation button, opr_number 2 denotes this is a 'Recommendation'
	/*$("#article-yes").click(function (event) {
		story_on_click(storyID, "onButton");
		$('#dialog-overlay-article, #dialog-box-article').hide();
		return false;
	});*/

	// 6. add scroll to the bottom event
	/*$(window).scroll(function() {
        if  ($(window).scrollTop() == $(document).height() - $(window).height()){
          //alert("scroll to the bottom!");
        }
	});*/

	/*$("#article-no").click(function (event) {
		$('#dialog-overlay-article, #dialog-box-article').hide();
		return false;
	});*/
        $("#answer-btn").click(function() {

                alert("TEST");

		if(!$.isnumeric($("#guess").attr('value'))){//!all
                        alert("Please guess the best value. Fractions, commas, letters, or blank answers will not be accepted.");
                }

		/*if(!$("input[name='AnswerChoice']:checked").val()) { 
			alert("Please choose the best answer to the question from the list."); 
		}*/
		else
		{
		    var AjaxDataStr = "";
		    // save clicked 
		    //alert($("#guess").attr('value'));
		    // Make string for each in/out array of times
		    //var MouseHoverIn, MouseHoverOut;

		    // convert ratio into number 
		    var AnsChosen = $("#guess").attr('value');
		    var AnsChosenStr = 'guess=' + AnsChosen;

		    AjaxDataStr = AnsChosenStr;

		    var i;
		    //if(NumAns > 0){
		    /*for (i = 1; i<=NumAns;i++){
			// SEE HOW TO USE JSON STRINGIFY
		    	MouseHoverIn = "intime" + i.toString() + "=" + JSON.stringify(eval("intime" + i.toString()));
		    	MouseHoverOut = "outtime" + i.toString() + "=" + JSON.stringify(eval("outtime" + i.toString()));
			AjaxDataStr += "&" + MouseHoverIn + "&" + MouseHoverOut;
		    }*/
		    //alert(AjaxDataStr);

                    //remove all the class add the messagebox classes and start fading
                    $("#msgbox").removeClass().addClass('messagebox').text('Loading Data...').fadeIn(100);
                    $.ajax({
                     type: "POST",
                     url: "ans_clicked.php",
                     data: AjaxDataStr,//AnsClickedStr,
                     success: function(msg) {
			    if(msg == "Success")
 			    {
                                $("#msgbox").fadeTo(100,0.1,function()  //start fading the messagebox
                                {
  					  
                                          //add message and change the class of the box and start fading
                                          $(this).html('Loaded successfully!').addClass('messageboxok').fadeTo(100,1,
                                          function()
                                          {
					    
                                            //redirect to story browse page
                                            window.location.href = 'story_browse.php';
                                          });
                                });
			    }
			    else if(msg == "Done"){
				        // we are done
                                        $("#msgbox").fadeTo(100,0.1,function()  //start fading the messagebox
                                        {

					    $(this).html('Finished!').addClass('messageboxok').fadeTo(100,1,
                                              function()
                                              {
                                                //redirect to story browse page
                                                window.location.href = 'user_complete.php';
                                              });
				        });

				}
				else{
					window.location.href = 'visitor.php';
				}

                        },
                                    error: function (xhr, ajaxOptions, thrownError){
                                    alert(xhr.status+" @visitor.php");
                                    alert(thrownError+" @visitor.php");
                                 }
                 });//ajax
		//}
		}
                       return false;//not to post the form physically
                });
});

//Popup dialog
function popup2(message) {

    // get the screen height and width  
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();

    // calculate the values for center alignment
    var dialogTop =  40;
    var dialogLeft = (maskWidth/2) - ($('#dialog-box-article').width()/2); 

    // assign values to the overlay and dialog box
    $('#dialog-overlay-article').css({height:maskHeight, width:maskWidth});
    $('#dialog-overlay-article').fadeIn(800);
    $('#dialog-box-article').css({top:dialogTop, left:dialogLeft}).show("slow");    
}

/*function show_stories() {
	$('.storyitem').fadeIn(1200);
}*/

function story_on_click ($elem, $click_type){
	
	$li = $elem.parents(".storyitem")
	$story_id = $li.attr("id");
	$position = $li.index();
	$opr_url = "story_opr.php";

	// Forbid user to recommend a story twice
	$pic = $li.children(".recbutton").children("img").attr("src");
	if($pic=="images/thumb2.png")
	{
		alert("You have already recommended this story, please select another one!");
		return false;
	}

	$.ajax({
         type: "POST",
         url: $opr_url,
         data: "story_id=" + $story_id + "&position=" + $position + "&click_type="+$click_type,
         success: function(msg){
            if(msg == "OPR_SUCCESS"){
            	// successful
            	if($click_type == 'onButton'){
            		$number = $li.children(".recbutton").children(".rank").text();	
            		$number++;
            		$li.children(".recbutton").children(".rank").text($number);
            		$li.children(".recbutton").children("img").attr("src","images/thumb2.png");
            	}
            }
            else{
            	alert(msg);
            }
         },
		 error: function (xhr, ajaxOptions, thrownError){
            alert(xhr.status+" @story_opr.js");
            alert(thrownError+" @story_opr.js");
         }

    });
    return true;
}
   // $("#AnswerChoice").click(function() {

   //}
