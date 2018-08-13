function customHomeSlider() {
	slider = jQuery("#home-slider");
	navigation = slider.data('navigation');
	pagination = slider.data('pagination');
	slideSpeed = slider.data('speed');
	auto_play = slider.data('autoplay');
	rewind_speed = slider.data('rewindspeed');
	stop_on_hover = slider.data('stoponhover');
	navigation ? navigation = true : navigation = false;
	pagination ? pagination = true : pagination = false;
	jQuery('body').css('direction') == 'rtl' ? isRtl = true : isRtl = false;
	auto_play ? auto_play = true : auto_play = false;
	rewind_speed ? rewind_speed : rewind_speed = 1000;
	stop_on_hover ? stop_on_hover = true : stop_on_hover = false;
	slider.owlCarousel({
		items : 1,
		nav : navigation,
		navSpeed : slideSpeed,
		dots: pagination,
		dotsSpeed : 400,
		rtl: isRtl,
		autoplaySpeed : rewind_speed,
		autoplay : auto_play,
		autoplayHoverPause : stop_on_hover
	});
}

function titleDivider(){
	setTimeout(function(){
		jQuery('.widget-title, .footer-block-title, .block-layered-nav .filter-label, .sidebar .block.filter .filter-options-title, .block-layered-nav dl#narrow-by-list2 dt, .sidebar .block-title, .block-vertical-nav .block-title, header.rating-title, .box-reviews .rating-subtitle, .block.related .block-title, .block.upsell .block-title, .product-options-title, #login-holder .page-title, .opc-block-title, .quick-view-title').each(function(){
			title_container_width = jQuery(this).width();
			title_width = jQuery(this).find('h1, h2, h3, h5, strong').innerWidth();
			divider_width = ((title_container_width - title_width-2)/2);
			full_divider_width = (title_container_width - title_width-2);
			if ((jQuery(this).hasClass('widget-title')) || (jQuery(this).hasClass('filter-label')) || (jQuery(this).hasClass('filter-options-title')) || (jQuery(this).parent().attr('id') == 'narrow-by-list2') || (jQuery(this).hasClass('block-title') && !jQuery(this).hasClass('product-options-title')) || (jQuery(this).hasClass('rating-title')) || (jQuery(this).hasClass('rating-subtitle')) || (jQuery(this).hasClass('page-title')) || (jQuery(this).hasClass('opc-block-title')) || (jQuery(this).hasClass('quick-view-title')) || (jQuery(this).hasClass('title'))) {
				if (divider_width > 15) {
					if(!jQuery(this).find('.right-divider').length){
						jQuery(this).append('<div class="right-divider" />');
					}
					jQuery(this).find('.right-divider').css('width', divider_width);
				} else {
					jQuery(this).find('.right-divider').remove();
				}
				if (divider_width > 15) {
					if(!jQuery(this).find('.left-divider').length) {
						jQuery(this).prepend('<div class="left-divider" />');
					}
					jQuery(this).find('.left-divider').css('width', divider_width);
				} else {
					jQuery(this).find('.left-divider').remove();
				}
			} else {
				if(!jQuery(this).find('.right-divider').length) {
					jQuery(this).append('<div class="right-divider" />');
				}
				jQuery(this).find('.right-divider').css('width', full_divider_width);
			}
		});
	}, 250);
}



function pageNotFound() {
	if(jQuery('.not-found-bg').data('bgimg')){
		var bgImg = jQuery('.not-found-bg').data('bgimg');
		jQuery('.not-found-bg').attr('style', bgImg);
	}
}

function accordionNav(){
	if(jQuery('.block.filter').length){
		jQuery('.filter-options-title').off().on('click', function(){
			jQuery(this).parents('.filter-options-item').toggleClass('active').children('.filter-options-content').slideToggle();
		});
		if(jQuery(document.body).width() < 767 && jQuery('body').hasClass('page-layout-1column')){
			jQuery('#layered-filter-block .filter-title').on('click', function(){
				if(!jQuery('#layered-filter-block').hasClass('active')) {
					jQuery('#layered-filter-block').addClass('active');
				} else {
					jQuery('#layered-filter-block').removeClass('active');
				}
			});

		}
	}
}

function backgroundWrapper(){
	if(jQuery('.background-wrapper').length){
		jQuery('.background-wrapper').each(function(){
			var thisBg = jQuery(this);
			if(jQuery(document.body).width() < 768){
				thisBg.attr('style', '');
				if(thisBg.parent().hasClass('text-banner') || thisBg.find('.text-banner').length || thisBg.find('.fullwidth-text-banners').length){
					bgHeight = thisBg.parent().outerHeight();
					thisBg.parent().css('height', bgHeight - 2);
				}
				if(jQuery('body').hasClass('boxed-layout')){
					bodyWidth = thisBg.parents('.container').outerWidth();
					bgLeft = (bodyWidth - thisBg.parents('.container').width())/2;
				} else {
					bgLeft = thisBg.parent().offset().left;
					bodyWidth = jQuery(document.body).width();
				}
				if(thisBg.data('bgColor')){
					bgColor = thisBg.data('bgColor');
					thisBg.css('background-color', bgColor);
				}
				setTimeout(function(){
					thisBg.css({
						'position' : 'absolute',
						'left' : -bgLeft,
						'width' : bodyWidth
					}).parent().css('position', 'relative');
				}, 300);
			} else {
				thisBg.attr('style', '');
				if(jQuery('body').hasClass('boxed-layout')){
					bodyWidth = thisBg.parents('.container').outerWidth();
					bgLeft = (bodyWidth - thisBg.parents('.container').width())/2;
				} else {
					bgLeft = thisBg.parent().offset().left;
					bodyWidth = jQuery(document.body).width();
				}
				thisBg.css({
					'position' : 'absolute',
					'left' : -bgLeft,
					'width' : bodyWidth
				}).parent().css('position', 'relative');
				if(thisBg.data('bgColor')){
					bgColor = thisBg.data('bgColor');
					thisBg.css('background-color', bgColor);
				}
				if(thisBg.parent().hasClass('text-banner') || thisBg.find('.text-banner').length || thisBg.find('.fullwidth-text-banners').length){
					bgHeight = thisBg.children().innerHeight();
					thisBg.parent().css('height', bgHeight - 2);
				}
			}
			
			if(thisBg.parents('.parallax-content')){
				jQuery('body').addClass('parallax');
			}
			if(thisBg.parent().hasClass('parallax-banners-wrapper')) {
					jQuery('.parallax-banners-wrapper').each(function(){
						block = jQuery(this).find('.text-banner');
						var wrapper = jQuery(this);
						var fullHeight = 0;
						var imgCount = block.size();
						var currentIndex = 0;
						block.each(function(){
							imgUrl = jQuery(this).css('background-image').replace(/url\(|\)|\"/ig, '');
							if(imgUrl.indexOf('none')==-1){
								img = new Image;
								img.src = imgUrl;
								img.setAttribute("name", jQuery(this).attr('id'));
								img.onload = function(){
									imgName = '#' + jQuery(this).attr('name');
									if(wrapper.data('fullscreen')){
										windowHeight = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
										jQuery(imgName).css({
											'height' : windowHeight+'px',
											'background-size' : 'cover'
										});
										fullHeight += windowHeight;
									} else {
										jQuery(imgName).css('height', this.height+'px');
										jQuery(imgName).css('height', (this.height - 200)+'px');
										fullHeight += this.height - 200;
										// if (pixelRatio > 1) {
											// jQuery(imgName).css('background-size', this.width+'px' + ' ' + this.height+'px');
										// }
									}
									wrapper.css('height', fullHeight);
									currentIndex++;
									if(!jQuery('body').hasClass('mobile-device')){
										if(currentIndex == imgCount){
											if(jQuery(document.body).width() > 1278) {
												jQuery('#parallax-banner-1').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-2').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-3').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-4').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-5').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-6').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-7').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-8').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-9').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-10').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-11').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-12').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-13').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-14').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-15').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-16').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-17').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-18').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-19').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-20').parallax("60%", 0.7, false);
											} else if(jQuery(document.body).width() > 977) {
												jQuery('#parallax-banner-1').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-2').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-3').parallax("60%", 0.9, false);
												jQuery('#parallax-banner-4').parallax("60%", 0.85, false);
												jQuery('#parallax-banner-5').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-6').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-7').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-8').parallax("60%", 0.9, false);
												jQuery('#parallax-banner-9').parallax("60%", 0.85, false);
												jQuery('#parallax-banner-10').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-11').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-12').parallax("60%", 0.9, false);
												jQuery('#parallax-banner-13').parallax("60%", 0.85, false);
												jQuery('#parallax-banner-14').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-15').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-16').parallax("60%", 0.9, false);
												jQuery('#parallax-banner-17').parallax("60%", 0.85, false);
												jQuery('#parallax-banner-18').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-19').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-20').parallax("60%", 0.9, false);
											} else if(jQuery(document.body).width() > 767) {
												jQuery('#parallax-banner-1').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-2').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-3').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-4').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-5').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-6').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-7').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-8').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-9').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-10').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-11').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-12').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-13').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-14').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-15').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-16').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-17').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-18').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-19').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-20').parallax("60%", 0.8, false);
											} else {
												jQuery('#parallax-banner-1').parallax("30%", 0.5, true);
												jQuery('#parallax-banner-2').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-3').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-4').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-5').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-6').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-7').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-8').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-9').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-10').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-11').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-12').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-13').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-14').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-15').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-16').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-17').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-18').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-19').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-20').parallax("60%", 0.1, false);
											}
										}
									}
								}
							}
							bannerText = jQuery(this).find('.banner-content');
							if(bannerText.data('top')){
								bannerText.css('top', bannerText.data('top'));
							}
							if(bannerText.data('left')){
								if(!bannerText.data('right')){
									bannerText.css({
										'left': bannerText.data('left'),
										'right' : 'auto'
									});
								} else {
									bannerText.css('left', bannerText.data('left'));
								}
							}
							if(bannerText.data('right')){
								if(!bannerText.data('left')){
									bannerText.css({
										'right': bannerText.data('right'),
										'left' : 'auto'
									});
								} else {
									bannerText.css('right', bannerText.data('right'));
								}
							}
						});
					});
					jQuery(window).scroll(function() {
						jQuery('.parallax-banners-wrapper').each(function(){
							block = jQuery(this).find('.text-banner');
							block.each(function(){
								var imagePos = jQuery(this).offset().top;
								var topOfWindow = jQuery(window).scrollTop();
								if (imagePos < topOfWindow+600) {
									jQuery(this).addClass("slideup");
								} else {
									jQuery(this).removeClass("slideup");
								}
							});
						});
					});
					setTimeout(function(){
						jQuery('#parallax-loading').fadeOut(200);
					}, 1000);
				}
				thisBg.animate({'opacity': 1}, 200)
		});
	}
}

var bsModal;

require(['jquery'], function ($)
{
	
	/* Product Timer */
	productTimer = {
		init: function(secondsDiff, id){
			daysHolder = jQuery('.timer-'+id+' .days span');
			hoursHolder = jQuery('.timer-'+id+' .hours span');
			minutesHolder = jQuery('.timer-'+id+' .minutes span');
			secondsHolder = jQuery('.timer-'+id+' .seconds span');
			var firstLoad = true;
			productTimer.timer(secondsDiff, daysHolder, hoursHolder, minutesHolder, secondsHolder, firstLoad);
			setTimeout(function(){
				jQuery('.timer-box').css('display', 'block');
			}, 1100);
		},
		timer: function(secondsDiff, daysHolder, hoursHolder, minutesHolder, secondsHolder, firstLoad){
			setTimeout(function(){
				days = Math.floor(secondsDiff/86400);
				hours = Math.floor((secondsDiff/3600)%24);
				minutes = Math.floor((secondsDiff/60)%60);
				seconds = secondsDiff%60;
				secondsHolder.html(seconds);
				if(secondsHolder.text().length == 1){
					secondsHolder.html('0'+seconds);
				} else if (secondsHolder.text()[0] != 0) {
					secondsHolder.html(seconds);
				}
				if(firstLoad == true){
					daysHolder.html(days);
					hoursHolder.html(hours);
					minutesHolder.html(minutes);
					if(minutesHolder.text().length == 1){
						minutesHolder.html('0'+minutes);
					}
					if(hoursHolder.text().length == 1){
						hoursHolder.html('0'+hours);
					}
					if(daysHolder.text().length == 1){
						daysHolder.html('0'+days);
					}
					firstLoad = false;
				}
				if(seconds >= 59){
					if(minutesHolder.text().length == 1 || minutesHolder.text()[0] == 0 && minutesHolder.text() != 00){
						minutesHolder.html('0'+minutes);
					} else {
						minutesHolder.html(minutes);
					}
					if(hoursHolder.text().length == 1 || hoursHolder.text()[0] == 0 && hoursHolder.text() != 00){
						hoursHolder.html('0'+hours);
					} else {
						hoursHolder.html(hours);
					}
					if(daysHolder.text().length == 1 || daysHolder.text()[0] == 0 && daysHolder.text() != 00){
						daysHolder.html('0'+days);
					} else {
						daysHolder.html(days);
					}
				}

				secondsDiff--;
				productTimer.timer(secondsDiff, daysHolder, hoursHolder, minutesHolder, secondsHolder, firstLoad);
			}, 1000);
		}
	}
	
	if(jQuery('#gift-options-cart').length) {
		jQuery(window).load(function(){
			setTimeout(function(){
				
				titleDivider();
			}, 2500);
			console.log('1');
		});
	}

	
	require(["MeigeeBootstrap", "meigeeCookies"], function(modal, cookie)
	{
		if(jQuery('#popup-block').length){
			// "use strict";
			function popupBlock() {
				jQuery('#popup-block').modal({
					show: true
				});
			}
			subscribeFlag = jQuery.cookie('blacknwhitePopupFlag');
			
			
			jQuery('#popup-block .action.subscribe').on('click', function(){
				if(jQuery('#popup-block').find('.mage-error').length == 0 && !jQuery('#subscribecheck').attr('aria-invalid')) {
					jQuery.cookie('blacknwhitePopupFlag2', 'true', {
						expires: '30',
						path: '/'
					});
				} else {
					jQuery.removeCookie('blacknwhitePopupFlag2');
				}
			});
			
			expires = jQuery('#popup-block').data('expires');
			function subsSetcookie(){
				jQuery.cookie('blacknwhitePopup', 'true', {
					expires: ''+expires+'',
					path: '/'
				});
			}
			if(!(subscribeFlag) && !jQuery.cookie('blacknwhitePopupFlag2')){
				popupBlock();
			}else{
				jQuery.removeCookie('blacknwhitePopupFlag', { path: '/' });
				subsSetcookie();
			}
			jQuery('#popup-block').parents('body').css({
				'padding' : 0,
				'overflow' : 'visible'
			});
			jQuery('#popup-block .popup-bottom input').on('click', function(){
				if(jQuery(this).parent().find('input:checked').length){
					subsSetcookie();
				} else {
					jQuery.removeCookie('blacknwhitePopup', { path: '/' });
				}
			});
			setTimeout(function(){
				jQuery('#popup-block button.close').on('click', function(){
					jQuery.cookie('blacknwhitePopup', 'true');
				});
			}, 1000);
			
			if((jQuery('#popup-block .popup-content-wrapper').data('bgimg')) && (jQuery('#popup-block .popup-content-wrapper').data('bgcolor'))) {
				var bgImg = jQuery('#popup-block .popup-content-wrapper').data('bgimg');
				var bgColor = jQuery('#popup-block .popup-content-wrapper').data('bgcolor');
				jQuery('#popup-block .popup-content-wrapper').attr('style', bgImg + bgColor);
			}else{
				if(jQuery('#popup-block .popup-content-wrapper').data('bgimg')){
					var bgImg = jQuery('#popup-block .popup-content-wrapper').data('bgimg');
					jQuery('#popup-block .popup-content-wrapper').attr('style', bgImg);
				}
				if(jQuery('#popup-block .popup-content-wrapper').data('bgcolor')){
					jQuery('#popup-block .popup-content-wrapper').addClass('no-bgimg');
					var bgColor = jQuery('#popup-block .popup-content-wrapper').data('bgcolor');
					jQuery('#popup-block .popup-content-wrapper').attr('style', bgColor);
				}
			}
		}
	});
	
	require(['MeigeeBootstrap', 'MeigeeCarousel'], function(mb,mc)
    {
       bsModal = $.fn.modal.noConflict();

		jQuery(document).ready(function(){
			customHomeSlider();
			titleDivider();
			productHoverItems();
			/* Mobile Devices */
			if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))){
			/* Mobile Devices Class */
			jQuery('body').addClass('mobile-device');
				var mobileDevice = true;
			}else if(!navigator.userAgent.match(/Android/i)){
				var mobileDevice = false;
			}

			/* Responsive */
			var responsiveflag = false;
			var topSelectFlag = false;
			var menu_type = jQuery('#nav').attr('class');




			jQuery('#sticky-header .search-button').on('click', function(){
				jQuery(this).toggleClass('active');
				jQuery('#sticky-header .block-search form.minisearch').slideToggle();
			});

			jQuery('.page-header.header-3 .block-search .block-title, .page-header.header-3 + #sticky-header .block-search .block-title').on('click', function(){
				jQuery(this).parent().toggleClass('active');
			});

			jQuery('.page-header.header-3 .block-search .block-content button.close, .page-header.header-3 + #sticky-header .block-search .block-content button.close').on('click', function(){
				jQuery(this).closest('.block-search').toggleClass('active');
			});

			var isApple = false;
		/* apple position fixed fix */
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))){
			isApple = true;
			function stickyPosition(clear){
				items = jQuery('.header, .backstretch');
				if(clear == false){
					topIndent = jQuery(window).scrollTop();
					items.css({
						'position': 'absolute',
						'top': topIndent
					});
				}else{
					items.css({
						'position': 'fixed',
						'top': '0'
					});
				}
			}
			jQuery('#sticky-header .form-search input').on('focusin focusout', function(){
				jQuery(this).toggleClass('focus');
				if(jQuery('header.header').hasClass('floating')){
					if(jQuery(this).hasClass('focus')){
						setTimeout(function(){
							stickyPosition(false);
						}, 500);
					}else{
						stickyPosition(true);
					}
				}
			});
		}


			/* sticky header */
			if(jQuery('#sticky-header').length){
				var headerHeight = jQuery('.page-header').height();
				sticky = jQuery('#sticky-header');
				jQuery(window).on('scroll', function(){
					if(jQuery(document.body).width() > 977){
						if(!isApple){
							heightParam = headerHeight;
						}else{
							heightParam = headerHeight*2;
						}
						if(jQuery(this).scrollTop() >= heightParam){
							sticky.stop().slideDown(250);
							if (jQuery('.page-header.header-3 .settings-wrapper.show').length != 0) {
								jQuery('.page-header.header-3 .settings-wrapper').removeClass('show');
								document.removeEventListener('touchstart', settingsListener, false);
								jQuery('html body').animate({
								    'margin-left': '0',
								    'margin-right': '0'
							  	}, 300);
							}
						}
						if(jQuery(this).scrollTop() < headerHeight ){
							sticky.stop().hide();
						}
						//

					} 
				});
			}
			pageNotFound();
			accordionNav();
			
			jQuery(window).load(function(){
				backgroundWrapper();
			});
			jQuery(window).resize(function(){
				titleDivider();
				pageNotFound();
				accordionNav();
				backgroundWrapper();
				productHoverItems();
			});

			if(document.URL.indexOf("#product_tabs_reviews") != -1) {
				$('#tabs a[href="#product_tabs_reviews"]').tab('show')
			}
			$.fn.scrollTo = function (speed) {
				if (typeof(speed) === 'undefined')
					speed = 1000;
				$('html, body').animate({
					// scrollTop: parseInt($(this).offset().top)
					scrollTop: parseInt($('#tabs').offset().top - 100)
				}, speed);
			};
			$('.product-info-main .product-reviews-summary a.action').on('click', function(){
				$(this).scrollTo('#tabs');
				$('#tabs a[href="#product_tabs_reviews"]').tab('show');
			});


		});
	});

    require(['jquery/ui', 'MeigeeBootstrap', 'lightBox'], function(ui, lb)
    {
        // $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event)
        // {
            // event.preventDefault();
            // $(this).ekkoLightbox();
            // return false;
        // });
    });

    function settingsListener(e){
		var touch = e.touches[0];
		if(jQuery(touch.target).parents('.page-header.header-3 .settings-wrapper').length == 0){
			jQuery('.page-header.header-3 .settings-wrapper').removeClass('show');
			document.removeEventListener('touchstart', settingsListener, false);
			jQuery('html body').animate({
			    'margin-left': '0',
			    'margin-right': '0'
		  	}, 300);
		}
	}
	jQuery('.page-header.header-3 .settings-wrapper .settings-btn').on('click', function(event){
		event.stopPropagation();
		jQuery('.page-header.header-3 .settings-wrapper').toggleClass('show');
		document.addEventListener('touchstart', settingsListener, false);
		if (jQuery('.page-header.header-3 .settings-wrapper').hasClass('show')) {
			if (!jQuery('.page-header.header-3').hasClass('rtl')) {
				jQuery('html body').animate({
				    'margin-left': '-300px',
				    'margin-right': '300px'
			  	}, 300);
			} else {
				jQuery('html body').animate({
				    'margin-left': '300px',
				    'margin-right': '-300px'
			  	}, 300);
			}
		} else {
			jQuery('html body').animate({
			    'margin-left': '0',
			    'margin-right': '0'
		  	}, 300);
		}
		
		jQuery(document).on('click.searchEvent', function(e) {
			if (jQuery(e.target).parents('.page-header.header-3 .settings-wrapper').length == 0) {
				jQuery('.page-header.header-3 .settings-wrapper').removeClass('show');
				jQuery(document).off('click.searchEvent');
				jQuery('html body').animate({
				    'margin-left': '0',
				    'margin-right': '0'
			  	}, 300);
			}
		});
	});

	function productHoverItems() {
		if (jQuery('.products-grid .item .info-item-1').length) {
			jQuery('.products-grid .item').each(function() {
				jQuery(this).find('[class*="info-item-"]').each(function() {
					if (!jQuery(this).children().length) {
						jQuery(this).remove();
					}
					if (jQuery(this).find('.product-reviews-summary.no-rating').length) {
						jQuery(this).remove();
					}
				});
				var productInfoItems = jQuery(this).find('[class*="info-item-"]').length;
				jQuery(this).find('.product-item-details').addClass('info-items-' + productInfoItems);
			});
		}
	}


});










