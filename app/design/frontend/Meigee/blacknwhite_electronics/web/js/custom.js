require(['jquery', 'jquery/ui'], function($) {


		//window resize
	$(document).ready(function(){
		$(window).resize(function() {
			resizeWindow();
		});
		resizeWindow();
	})


	$('.bundle-options-container').appendTo('.product-info-main');
	

	function resizeWindow() 
	{
		if(window.innerWidth < 767) {
			$('.sidebar .inventory-section').appendTo('.product-info-main');
		}
       else if(window.innerWidth > 767)
        {
			$('.product-info-main .inventory-section').prependTo('.sidebar-additional');
	    }
	}


});