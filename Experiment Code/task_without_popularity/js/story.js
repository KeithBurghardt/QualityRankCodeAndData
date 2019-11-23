$(document).ready(function(){
	var storyID;
	
	// 1. animate the story list
	show_stories();

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
	$("#storylist .storyitem").hover(function() {
		$(this).children(".content").css("background","#EBEFEF");
	}, function(){
		$(this).children(".content").css("background","#FFFFFF");
	});

	// 4. click the story url, opr_number 1 denotes this is a 'URL click'
	$(".content .title a").click(function (event) {
		storyID = $(this);
		if (story_on_click($(this), "onURL"))
			popup2();
		$('#iframe-article').attr('src', $(this).attr("href"));
		//window.open($(this).attr("href"));
		return false;
	});

	// 5. click the recommendation button, opr_number 2 denotes this is a 'Recommendation'
	$("#article-yes").click(function (event) {
		story_on_click(storyID, "onButton");
		$('#dialog-overlay-article, #dialog-box-article').hide();
		return false;
	});

	// 6. add scroll to the bottom event
	$(window).scroll(function() {
        if  ($(window).scrollTop() == $(document).height() - $(window).height()){
          //alert("scroll to the bottom!");
        }
	});

	$("#article-no").click(function (event) {
		$('#dialog-overlay-article, #dialog-box-article').hide();
		return false;
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

function show_stories() {
	$('.storyitem').fadeIn(1200);
}

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