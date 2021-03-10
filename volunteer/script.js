$(document).ready(function() {
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

		$('.bg img').css({ 
				width : width + 'px',
				height : height + 'px',
				top : top + 'px',
				left :  left + 'px'
			});

		//console.log('width = ' + width);
		//console.log('height = ' + height);
		//console.log('screen width = ' + screen_width);
		//console.log('screen height = ' + screen_height);
		//console.log('ratio = ' + width/height);
		//console.log('-------------------------');

	}
	
	function layout_initializer()
	{
		image_fitter($(window).width(), $(window).height());
	}
	layout_initializer();

	$(window).resize(function(){
		layout_initializer();
	});
	
	/*Background fit to screen END*/
	
	$('.podio-webform-frame').css('display', 'none');
	
		$('.apply').click(function() {
			if(!$('.apply').hasClass('folded-out') ) {
				$('.podio-webform-frame').slideToggle('fast', function() {
					$('html, body').animate({scrollTop:($('.podio-webform-frame').offset().top)+25},1000);
					console.log($('.podio-webform-frame').height());
					$('.podio-webform-frame').css('max-height', '2100px');
				});
				$('.apply').addClass('folded-out');
			}
		});
		$('.apply').keyPress(function(e) {
			if(e.keyCode == 13) {
				if(!$('.apply').hasClass('folded-out') ) {
					$('.apply').trigger('click');
				}
			}
		});
});