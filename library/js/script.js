$(document).ready(function() {
	// New submission
	$(".new-submission").submit(function(e) {
		
		var itu_email = $(".itu-mail").val(),
		description = $(".description").val(),
		headline = $(".headline").val(),
		image_link = $(".image_link").val();
		
		console.log(itu_email + "x" + description);
		
		$.post(
			"new_submission.php",
			{
				itu_mail : itu_email,
				description : description,
				headline : headline,
				image_link : image_link
			}
		).done(function(data) {
			// When done with submission
			$(".new-submission, .idea-header").slideToggle(500, function() {
				$(".new-submission, .idea-header").remove();
			});
			console.log(data);
			description = description.replace(/\n/g, "<br>");
			var idea_elem = "<li id='" + data + "'><span class='points_result'>0</span><h3 class='headline_result'>" + headline + "</h3><span class='descrip_result'>" + description + "</span>";
			// If image link is empty, don't add it to the idea
			if(image_link == "") {
				idea_elem = idea_elem + "<div class='byline'>by <span class='itu_mail_result'>" +
				 itu_email + "</span></div><div class='fb-share-button' data-href='http://cafeanalog.dk/library/?iid=" + data + "' data-layout='button'></div></li>";
			}
			else {
				idea_elem = idea_elem + "<div class='image_link_result'><a href='" + image_link + "' alt='Link to idea image'>Link to idea image</a></div><div class='byline'>by <span class='itu_mail_result'>" +
				 itu_email + "</span></div><div class='fb-share-button' data-href='http://cafeanalog.dk/library/index.php?iid=" + data + "' data-layout='button'></div></li>";
			}
			
			$(".ideas-list").prepend(idea_elem);
			 
			 // Initiate Upvote mechanism
			 // This is obsolete in library version
			 $(".upvote").click(function(e) {
			 	$(this).remove();
			 	var this_mail = $(this).parent("li").find(".itu_mail_result").text(),
			 	this_descrip = $(this).parent("li").find(".descrip_result").text();
			 	console.log(this_mail, this_descrip);
			 	
			 	var this_idea_id = $(this).parent("li").attr("id");
			 	UpvoteThis(this_idea_id);
			 	
			 	MakeActivated($(this).find("i"));
    		IncreaseCount($(this).find("i"));
			 	
			 	$.post(
			 		"set_cookie.php",
			 		{
			 			itu_mail : this_mail,
			 			description : this_descrip
			 		}
			 	).done(function(data) {
			 		console.log(data);
			 	});
			 	
			 	e.preventDefault();
			 });
		});
		e.preventDefault();
	});
	
	// List all ideas
	function ListAllIdeas() {
		$.ajax("get_ideas.php").done(function(data) {
	    	$(".ideas-list").prepend(data);
	    	
	    	// Scroll to idea, if it's in the parameter
	    	var query = getQueryParams(document.location.search);
			$('html, body').animate({scrollTop:($('.ideas-list #' + query.iid).offset().top)},1000);
	    	
	    	
   			GetCookie();
	    	$(".upvote").click(function(e) {
	    		var this_mail = $(this).parent("li").find(".itu_mail_result").text(),
	    		this_descrip = $(this).parent("li").find(".descrip_result").text();
	    		
	    		var this_idea_id = $(this).parent("li").attr("id");
	    		UpvoteThis(this_idea_id);
	    		
	    		MakeActivated($(this).find("i"));
	    		IncreaseCount($(this).find("i"));
	    		
	    		
	    		$.post(
	    			"set_cookie.php",
	    			{
	    				itu_mail : this_mail,
	    				description : this_descrip
	    			}
	    		).done(function(data) {
	    			console.log(data);
	    		});
 				 	$(this).remove();
	    		e.preventDefault();
	    	});
		});
	}
	ListAllIdeas();
	
	function MakeActivated(elem) {
		console.log(elem);
		elem.removeClass("fa-plus-square-o");
		elem.addClass("fa-plus-square");
	}
	
	function IncreaseCount(elem) {
			elem.parent("a").parent("li").find(".points_result").text(parseInt(elem.parent("a").parent("li").find(".points_result").text()) + 1);
	}
	
	function UpvoteThis(idea_id) {
		$.post(
			"upvote.php",
			{
				idea_id : idea_id
			}
		).done(function(data) {
			GetCookie();
			console.log(data);
		});
	}
	
	var theCookie;
	function GetCookie() {
		$.ajax("get_cookie.php").done(function(data) {
			theCookie = data;
			var theCookieArray = data.split(", ");
			$(".ideas-list li").each(function() {
				for(var i = 0; i < theCookieArray.length; i++) {
					if($(this).attr("id") == parseInt(theCookieArray[i])) {
						// Mark this as voted
						console.log($(this).find(".upvote"));
						$(this).find(".upvote").remove();
					}
				}
			});
		});
	}
	
	// Get URL Query Parameters
	function getQueryParams(qs) {
	    qs = qs.split('+').join(' ');
	
	    var params = {},
	        tokens,
	        re = /[?&]?([^=]+)=([^&]*)/g;
	
	    while (tokens = re.exec(qs)) {
	        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
	    }
	
	    return params;
	}
	
	/*Background fit to screen*/
	function image_fitter(screen_width, screen_height)
	{
		// Image resolutions
		var img_width = 1680, img_height = 1050;
		var ratio = img_width/img_height;

		var head_n_foot = 0;

		// bg visible height
		var bg_vis_height = screen_height - head_n_foot;

		var width, height;
		width = screen_width;
		height = width/ratio;
		if(height - head_n_foot < bg_vis_height)
		{
			height = screen_height;
			width = height * ratio;
		}

		set_bg_image_size(width, height, screen_width, screen_height);
	}

	function set_bg_image_size(width, height, screen_width, screen_height)
	{
		var top = 0, left = 0;
		if(width > screen_width)
			left = -(width - screen_width)/2;
		if(height > screen_height)
			top = -(height - screen_height)/2;

		$('.background img').css({ 
				width : width + 'px',
				height : height + 'px',
				top : top + 'px',
				left :  left + 'px'
			});
	}
	
	function layout_initializer()
	{
		image_fitter($(window).width(), $(window).height());
	}
	layout_initializer();
	
	//When window is resized, recalculate bg size and position
	$(window).resize(function(){
		layout_initializer();
	});
	/*Background fit to screen END*/
});