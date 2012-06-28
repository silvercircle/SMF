/* 
 * This script is loaded asynchronously in the footer of the theme
 */
 
/* ------------------------------------------------------------------------
	Class: prettyPhoto
	Use: Lightbox clone for jQuery
	Author: Stephane Caron (http://www.no-margin-for-errors.com)
	Version: 3.1.3
------------------------------------------------------------------------- */


(function($) {
	$.prettyPhoto = {version: '3.1.3'};
	
	$.fn.prettyPhoto = function(pp_settings) {
		pp_settings = jQuery.extend({
			animation_speed: 'fast', /* fast/slow/normal */
			slideshow: 5000, /* false OR interval time in ms */
			autoplay_slideshow: false, /* true/false */
			opacity: 0.8, /* Value between 0 and 1 */
			show_title: false, /* true/false */
			allow_resize: true, /* Resize the photos bigger than viewport. true/false */
			default_width: 500,
			default_height: 344,
			counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
			theme: 'pp_default', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
			horizontal_padding: 20, /* The padding on each side of the picture */
			hideflash: false, /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
			wmode: 'opaque', /* Set the flash wmode attribute */
			autoplay: true, /* Automatically start videos: True/False */
			modal: false, /* If set to true, only the close button will close the window */
			deeplinking: true, /* Allow prettyPhoto to update the url to enable deeplinking. */
			overlay_gallery: true, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
			keyboard_shortcuts: true, /* Set to false if you open forms inside prettyPhoto */
			changepicturecallback: function(){}, /* Called everytime an item is shown/changed */
			callback: function(){}, /* Called when prettyPhoto is closed */
			ie6_fallback: false,
			markup: '<div class="pp_pic_holder"> \
						<div class="ppt">&nbsp;</div> \
						<div class="pp_top"> \
							<div class="pp_left"></div> \
							<div class="pp_middle"></div> \
							<div class="pp_right"></div> \
						</div> \
						<div class="pp_content_container"> \
							<div class="pp_left"> \
							<div class="pp_right"> \
								<div class="pp_content"> \
									<div class="pp_loaderIcon"></div> \
									<div class="pp_fade"> \
										<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
										<div class="pp_hoverContainer"> \
											<a class="pp_next" href="#">next</a> \
											<a class="pp_previous" href="#">previous</a> \
										</div> \
										<div id="pp_full_res"></div> \
										<div class="pp_details"> \
											<div class="pp_nav"> \
												<a href="#" class="pp_arrow_previous">Previous</a> \
												<p class="currentTextHolder">0/0</p> \
												<a href="#" class="pp_arrow_next">Next</a> \
											</div> \
											<p class="pp_description"></p> \
											<div class="pp_social">{pp_social}</div> \
											<a class="pp_close" href="#">Close</a> \
										</div> \
									</div> \
								</div> \
							</div> \
							</div> \
						</div> \
						<div class="pp_bottom"> \
							<div class="pp_left"></div> \
							<div class="pp_middle"></div> \
							<div class="pp_right"></div> \
						</div> \
					</div> \
					<div class="pp_overlay"></div>',
			gallery_markup: '<div class="pp_gallery"> \
								<a href="#" class="pp_arrow_previous">Previous</a> \
								<div> \
									<ul> \
										{gallery} \
									</ul> \
								</div> \
								<a href="#" class="pp_arrow_next">Next</a> \
							</div>',
			image_markup: '<img id="fullResImage" src="{path}" />',
			flash_markup: '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',
			quicktime_markup: '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="{height}" width="{width}"><param name="src" value="{path}"><param name="autoplay" value="{autoplay}"><param name="type" value="video/quicktime"><embed src="{path}" height="{height}" width="{width}" autoplay="{autoplay}" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>',
			iframe_markup: '<iframe src ="{path}" width="{width}" height="{height}" frameborder="no"></iframe>',
			inline_markup: '<div class="pp_inline">{content}</div>',
			custom_markup: '',
			social_tools: '<div class="twitter"><a href="http://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div><div class="facebook"><iframe src="http://www.facebook.com/plugins/like.php?locale=en_US&href={location_href}&amp;layout=button_count&amp;show_faces=true&amp;width=500&amp;action=like&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:500px; height:23px;" allowTransparency="true"></iframe></div>' /* html or false to disable */
		}, pp_settings);
		
		// Global variables accessible only by prettyPhoto
		var matchedObjects = this, percentBased = false, pp_dimensions, pp_open,
		
		// prettyPhoto container specific
		pp_contentHeight, pp_contentWidth, pp_containerHeight, pp_containerWidth,
		
		// Window size
		windowHeight = $(window).height(), windowWidth = $(window).width(),

		// Global elements
		pp_slideshow;
		
		doresize = true, scroll_pos = _get_scroll();
	
		// Window/Keyboard events
		$(window).unbind('resize.prettyphoto').bind('resize.prettyphoto',function(){_center_overlay();_resize_overlay();});
		
		if(pp_settings.keyboard_shortcuts) {
			$(document).unbind('keydown.prettyphoto').bind('keydown.prettyphoto',function(e){
				if(typeof $pp_pic_holder != 'undefined'){
					if($pp_pic_holder.is(':visible')){
						switch(e.keyCode){
							case 37:
								$.prettyPhoto.changePage('previous');
								e.preventDefault();
								break;
							case 39:
								$.prettyPhoto.changePage('next');
								e.preventDefault();
								break;
							case 27:
								if(!settings.modal)
								$.prettyPhoto.close();
								e.preventDefault();
								break;
						};
						// return false;
					};
				};
			});
		};
		
		/**
		* Initialize prettyPhoto.
		*/
		$.prettyPhoto.initialize = function() {
			
			settings = pp_settings;
			
			if(settings.theme == 'pp_default') settings.horizontal_padding = 16;
			if(settings.ie6_fallback && $.browser.msie && parseInt($.browser.version) == 6) settings.theme = "light_square"; // Fallback to a supported theme for IE6
			
			// Find out if the picture is part of a set
			theRel = $(this).attr('rel');
			galleryRegExp = /\[(?:.*)\]/;
			isSet = (galleryRegExp.exec(theRel)) ? true : false;
			
			// Put the SRCs, TITLEs, ALTs into an array.
			pp_images = (isSet) ? jQuery.map(matchedObjects, function(n, i){if($(n).attr('rel').indexOf(theRel) != -1) return $(n).attr('href');}) : $.makeArray($(this).attr('href'));
			pp_titles = (isSet) ? jQuery.map(matchedObjects, function(n, i){if($(n).attr('rel').indexOf(theRel) != -1) return ($(n).find('img').attr('alt')) ? $(n).find('img').attr('alt') : "";}) : $.makeArray($(this).find('img').attr('alt'));
			pp_descriptions = (isSet) ? jQuery.map(matchedObjects, function(n, i){if($(n).attr('rel').indexOf(theRel) != -1) return ($(n).attr('title')) ? $(n).attr('title') : "";}) : $.makeArray($(this).attr('title'));
			
			if(pp_images.length > 30) settings.overlay_gallery = false;
			
			set_position = jQuery.inArray($(this).attr('href'), pp_images); // Define where in the array the clicked item is positionned
			rel_index = (isSet) ? set_position : $("a[rel^='"+theRel+"']").index($(this));
			
			_build_overlay(this); // Build the overlay {this} being the caller
			
			if(settings.allow_resize)
				$(window).bind('scroll.prettyphoto',function(){_center_overlay();});
			
			
			$.prettyPhoto.open();
			
			return false;
		};


		/**
		* Opens the prettyPhoto modal box.
		* @param image {String,Array} Full path to the image to be open, can also be an array containing full images paths.
		* @param title {String,Array} The title to be displayed with the picture, can also be an array containing all the titles.
		* @param description {String,Array} The description to be displayed with the picture, can also be an array containing all the descriptions.
		*/
		$.prettyPhoto.open = function(event) {
			if(typeof settings == "undefined"){ // Means it's an API call, need to manually get the settings and set the variables
				settings = pp_settings;
				if($.browser.msie && $.browser.version == 6) settings.theme = "light_square"; // Fallback to a supported theme for IE6
				pp_images = $.makeArray(arguments[0]);
				pp_titles = (arguments[1]) ? $.makeArray(arguments[1]) : $.makeArray("");
				pp_descriptions = (arguments[2]) ? $.makeArray(arguments[2]) : $.makeArray("");
				isSet = (pp_images.length > 1) ? true : false;
				set_position = 0;
				_build_overlay(event.target); // Build the overlay {this} being the caller
			}

			if($.browser.msie && $.browser.version == 6) $('select').css('visibility','hidden'); // To fix the bug with IE select boxes
			
			if(settings.hideflash) $('object,embed,iframe[src*=youtube],iframe[src*=vimeo]').css('visibility','hidden'); // Hide the flash

			_checkPosition($(pp_images).size()); // Hide the next/previous links if on first or last images.
		
			$('.pp_loaderIcon').show();
		
			if(settings.deeplinking)
				setHashtag();
		
			// Rebuild Facebook Like Button with updated href
			if(settings.social_tools){
				facebook_like_link = settings.social_tools.replace('{location_href}', encodeURIComponent(location.href)); 
				$pp_pic_holder.find('.pp_social').html(facebook_like_link);
			}
			
			// Fade the content in
			if($ppt.is(':hidden')) $ppt.css('opacity',0).show();
			$pp_overlay.show().fadeTo(settings.animation_speed,settings.opacity);

			// Display the current position
			$pp_pic_holder.find('.currentTextHolder').text((set_position+1) + settings.counter_separator_label + $(pp_images).size());

			// Set the description
			/*
			if(pp_descriptions[set_position] != ""){
				$pp_pic_holder.find('.pp_description').show().html(unescape(pp_descriptions[set_position]));
			}else{
				$pp_pic_holder.find('.pp_description').hide();
			}*/
			
			// Get the dimensions
			movie_width = ( parseFloat(getParam('width',pp_images[set_position])) ) ? getParam('width',pp_images[set_position]) : settings.default_width.toString();
			movie_height = ( parseFloat(getParam('height',pp_images[set_position])) ) ? getParam('height',pp_images[set_position]) : settings.default_height.toString();
			
			// If the size is % based, calculate according to window dimensions
			percentBased=false;
			if(movie_height.indexOf('%') != -1) {movie_height = parseFloat(($(window).height() * parseFloat(movie_height) / 100) - 150);percentBased = true;}
			if(movie_width.indexOf('%') != -1) {movie_width = parseFloat(($(window).width() * parseFloat(movie_width) / 100) - 150);percentBased = true;}
			
			// Fade the holder
			$pp_pic_holder.fadeIn(function(){
				// Set the title
				(settings.show_title && pp_titles[set_position] != "" && typeof pp_titles[set_position] != "undefined") ? $ppt.html(unescape(pp_titles[set_position])) : $ppt.html('&nbsp;');
				
				imgPreloader = "";
				skipInjection = false;
				
				// Inject the proper content
				switch(_getFileType(pp_images[set_position])){
					case 'image':
						imgPreloader = new Image();

						// Preload the neighbour images
						nextImage = new Image();
						if(isSet && set_position < $(pp_images).size() -1) nextImage.src = pp_images[set_position + 1];
						prevImage = new Image();
						if(isSet && pp_images[set_position - 1]) prevImage.src = pp_images[set_position - 1];

						$pp_pic_holder.find('#pp_full_res')[0].innerHTML = settings.image_markup.replace(/{path}/g,pp_images[set_position]);

						imgPreloader.onload = function(){
							// Fit item to viewport
							pp_dimensions = _fitToViewport(imgPreloader.width,imgPreloader.height);

							_showContent();
						};

						imgPreloader.onerror = function(){
							alert('Image cannot be loaded. Make sure the path is correct and image exist.');
							$.prettyPhoto.close();
						};
					
						imgPreloader.src = pp_images[set_position];
					break;
				
					case 'youtube':
						pp_dimensions = _fitToViewport(movie_width,movie_height); // Fit item to viewport
						
						// Regular youtube link
						movie_id = getParam('v',pp_images[set_position]);
						
						// youtu.be link
						if(movie_id == ""){
							movie_id = pp_images[set_position].split('youtu.be/');
							movie_id = movie_id[1];
							if(movie_id.indexOf('?') > 0)
								movie_id = movie_id.substr(0,movie_id.indexOf('?')); // Strip anything after the ?

							if(movie_id.indexOf('&') > 0)
								movie_id = movie_id.substr(0,movie_id.indexOf('&')); // Strip anything after the &
						}

						movie = 'http://www.youtube.com/embed/'+movie_id;
						(getParam('rel',pp_images[set_position])) ? movie+="?rel="+getParam('rel',pp_images[set_position]) : movie+="?rel=1";
							
						if(settings.autoplay) movie += "&autoplay=1";
					
						toInject = settings.iframe_markup.replace(/{width}/g,pp_dimensions['width']).replace(/{height}/g,pp_dimensions['height']).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,movie);
					break;
				
					case 'vimeo':
						pp_dimensions = _fitToViewport(movie_width,movie_height); // Fit item to viewport
					
						movie_id = pp_images[set_position];
						var regExp = /http:\/\/(www\.)?vimeo.com\/(\d+)/;
						var match = movie_id.match(regExp);
						
						movie = 'http://player.vimeo.com/video/'+ match[2] +'?title=0&amp;byline=0&amp;portrait=0';
						if(settings.autoplay) movie += "&autoplay=1;";
				
						vimeo_width = pp_dimensions['width'] + '/embed/?moog_width='+ pp_dimensions['width'];
				
						toInject = settings.iframe_markup.replace(/{width}/g,vimeo_width).replace(/{height}/g,pp_dimensions['height']).replace(/{path}/g,movie);
					break;
				
					case 'quicktime':
						pp_dimensions = _fitToViewport(movie_width,movie_height); // Fit item to viewport
						pp_dimensions['height']+=15;pp_dimensions['contentHeight']+=15;pp_dimensions['containerHeight']+=15; // Add space for the control bar
				
						toInject = settings.quicktime_markup.replace(/{width}/g,pp_dimensions['width']).replace(/{height}/g,pp_dimensions['height']).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,pp_images[set_position]).replace(/{autoplay}/g,settings.autoplay);
					break;
				
					case 'flash':
						pp_dimensions = _fitToViewport(movie_width,movie_height); // Fit item to viewport
					
						flash_vars = pp_images[set_position];
						flash_vars = flash_vars.substring(pp_images[set_position].indexOf('flashvars') + 10,pp_images[set_position].length);

						filename = pp_images[set_position];
						filename = filename.substring(0,filename.indexOf('?'));
					
						toInject =  settings.flash_markup.replace(/{width}/g,pp_dimensions['width']).replace(/{height}/g,pp_dimensions['height']).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,filename+'?'+flash_vars);
					break;
				
					case 'iframe':
						pp_dimensions = _fitToViewport(movie_width,movie_height); // Fit item to viewport
				
						frame_url = pp_images[set_position];
						frame_url = frame_url.substr(0,frame_url.indexOf('iframe')-1);

						toInject = settings.iframe_markup.replace(/{width}/g,pp_dimensions['width']).replace(/{height}/g,pp_dimensions['height']).replace(/{path}/g,frame_url);
					break;
					
					case 'ajax':
						doresize = false; // Make sure the dimensions are not resized.
						pp_dimensions = _fitToViewport(movie_width,movie_height);
						doresize = true; // Reset the dimensions
					
						skipInjection = true;
						$.get(pp_images[set_position],function(responseHTML){
							toInject = settings.inline_markup.replace(/{content}/g,responseHTML);
							$pp_pic_holder.find('#pp_full_res')[0].innerHTML = toInject;
							_showContent();
						});
						
					break;
					
					case 'custom':
						pp_dimensions = _fitToViewport(movie_width,movie_height); // Fit item to viewport
					
						toInject = settings.custom_markup;
					break;
				
					case 'inline':
						// to get the item height clone it, apply default width, wrap it in the prettyPhoto containers , then delete
						myClone = $(pp_images[set_position]).clone().append('<br clear="all" />').css({'width':settings.default_width}).wrapInner('<div id="pp_full_res"><div class="pp_inline"></div></div>').appendTo($('body')).show();
						doresize = false; // Make sure the dimensions are not resized.
						pp_dimensions = _fitToViewport($(myClone).width(),$(myClone).height());
						doresize = true; // Reset the dimensions
						$(myClone).remove();
						toInject = settings.inline_markup.replace(/{content}/g,$(pp_images[set_position]).html());
					break;
				};

				if(!imgPreloader && !skipInjection){
					$pp_pic_holder.find('#pp_full_res')[0].innerHTML = toInject;
				
					// Show content
					_showContent();
				};
			});

			return false;
		};

	
		/**
		* Change page in the prettyPhoto modal box
		* @param direction {String} Direction of the paging, previous or next.
		*/
		$.prettyPhoto.changePage = function(direction){
			currentGalleryPage = 0;
			
			if(direction == 'previous') {
				set_position--;
				if (set_position < 0) set_position = $(pp_images).size()-1;
			}else if(direction == 'next'){
				set_position++;
				if(set_position > $(pp_images).size()-1) set_position = 0;
			}else{
				set_position=direction;
			};
			
			rel_index = set_position;

			if(!doresize) doresize = true; // Allow the resizing of the images
			$('.pp_contract').removeClass('pp_contract').addClass('pp_expand');

			_hideContent(function(){$.prettyPhoto.open();});
		};


		/**
		* Change gallery page in the prettyPhoto modal box
		* @param direction {String} Direction of the paging, previous or next.
		*/
		$.prettyPhoto.changeGalleryPage = function(direction){
			if(direction=='next'){
				currentGalleryPage ++;

				if(currentGalleryPage > totalPage) currentGalleryPage = 0;
			}else if(direction=='previous'){
				currentGalleryPage --;

				if(currentGalleryPage < 0) currentGalleryPage = totalPage;
			}else{
				currentGalleryPage = direction;
			};
			
			slide_speed = (direction == 'next' || direction == 'previous') ? settings.animation_speed : 0;

			slide_to = currentGalleryPage * (itemsPerPage * itemWidth);

			$pp_gallery.find('ul').animate({left:-slide_to},slide_speed);
		};


		/**
		* Start the slideshow...
		*/
		$.prettyPhoto.startSlideshow = function(){
			if(typeof pp_slideshow == 'undefined'){
				$pp_pic_holder.find('.pp_play').unbind('click').removeClass('pp_play').addClass('pp_pause').click(function(){
					$.prettyPhoto.stopSlideshow();
					return false;
				});
				pp_slideshow = setInterval($.prettyPhoto.startSlideshow,settings.slideshow);
			}else{
				$.prettyPhoto.changePage('next');	
			};
		};


		/**
		* Stop the slideshow...
		*/
		$.prettyPhoto.stopSlideshow = function(){
			$pp_pic_holder.find('.pp_pause').unbind('click').removeClass('pp_pause').addClass('pp_play').click(function(){
				$.prettyPhoto.startSlideshow();
				return false;
			});
			clearInterval(pp_slideshow);
			pp_slideshow=undefined;
		};


		/**
		* Closes prettyPhoto.
		*/
		$.prettyPhoto.close = function(){
			if($pp_overlay.is(":animated")) return;
			
			$.prettyPhoto.stopSlideshow();
			
			$pp_pic_holder.stop().find('object,embed').css('visibility','hidden');
			
			$('div.pp_pic_holder,div.ppt,.pp_fade').fadeOut(settings.animation_speed,function(){$(this).remove();});
			
			$pp_overlay.fadeOut(settings.animation_speed, function(){
				if($.browser.msie && $.browser.version == 6) $('select').css('visibility','visible'); // To fix the bug with IE select boxes
				
				if(settings.hideflash) $('object,embed,iframe[src*=youtube],iframe[src*=vimeo]').css('visibility','visible'); // Show the flash
				
				$(this).remove(); // No more need for the prettyPhoto markup
				
				$(window).unbind('scroll.prettyphoto');
				
				clearHashtag();
				
				settings.callback();
				
				doresize = true;
				
				pp_open = false;
				
				delete settings;
			});
		};
	
		/**
		* Set the proper sizes on the containers and animate the content in.
		*/
		function _showContent(){
			$('.pp_loaderIcon').hide();

			// Calculate the opened top position of the pic holder
			projectedTop = scroll_pos['scrollTop'] + ((windowHeight/2) - (pp_dimensions['containerHeight']/2));
			if(projectedTop < 0) projectedTop = 0;

			$ppt.fadeTo(settings.animation_speed,1);

			// Resize the content holder
			$pp_pic_holder.find('.pp_content')
				.animate({
					height:pp_dimensions['contentHeight'],
					width:pp_dimensions['contentWidth']
				},settings.animation_speed);
			
			// Resize picture the holder
			$pp_pic_holder.animate({
				'top': projectedTop,
				'left': (windowWidth/2) - (pp_dimensions['containerWidth']/2),
				width:pp_dimensions['containerWidth']
			},settings.animation_speed,function(){
				$pp_pic_holder.find('.pp_hoverContainer,#fullResImage').height(pp_dimensions['height']).width(pp_dimensions['width']);

				$pp_pic_holder.find('.pp_fade').fadeIn(settings.animation_speed); // Fade the new content

				// Show the nav
				if(isSet && _getFileType(pp_images[set_position])=="image") {$pp_pic_holder.find('.pp_hoverContainer').show();}else{$pp_pic_holder.find('.pp_hoverContainer').hide();}
			
				if(pp_dimensions['resized']){ // Fade the resizing link if the image is resized
					$('a.pp_expand,a.pp_contract').show();
				}else{
					$('a.pp_expand').hide();
				}
				
				if(settings.autoplay_slideshow && !pp_slideshow && !pp_open) $.prettyPhoto.startSlideshow();
				
				settings.changepicturecallback(); // Callback!
				
				pp_open = true;
			});
			
			_insert_gallery();
		};
		
		/**
		* Hide the content...DUH!
		*/
		function _hideContent(callback){
			// Fade out the current picture
			$pp_pic_holder.find('#pp_full_res object,#pp_full_res embed').css('visibility','hidden');
			$pp_pic_holder.find('.pp_fade').fadeOut(settings.animation_speed,function(){
				$('.pp_loaderIcon').show();
				
				callback();
			});
		};
	
		/**
		* Check the item position in the gallery array, hide or show the navigation links
		* @param setCount {integer} The total number of items in the set
		*/
		function _checkPosition(setCount){
			(setCount > 1) ? $('.pp_nav').show() : $('.pp_nav').hide(); // Hide the bottom nav if it's not a set.
		};
	
		/**
		* Resize the item dimensions if it's bigger than the viewport
		* @param width {integer} Width of the item to be opened
		* @param height {integer} Height of the item to be opened
		* @return An array containin the "fitted" dimensions
		*/
		function _fitToViewport(width,height){
			resized = false;

			_getDimensions(width,height);
			
			// Define them in case there's no resize needed
			imageWidth = width, imageHeight = height;

			if( ((pp_containerWidth > windowWidth) || (pp_containerHeight > windowHeight)) && doresize && settings.allow_resize && !percentBased) {
				resized = true, fitting = false;
			
				while (!fitting){
					if((pp_containerWidth > windowWidth)){
						imageWidth = (windowWidth - 200);
						imageHeight = (height/width) * imageWidth;
					}else if((pp_containerHeight > windowHeight)){
						imageHeight = (windowHeight - 200);
						imageWidth = (width/height) * imageHeight;
					}else{
						fitting = true;
					};

					pp_containerHeight = imageHeight, pp_containerWidth = imageWidth;
				};
			
				_getDimensions(imageWidth,imageHeight);
				
				if((pp_containerWidth > windowWidth) || (pp_containerHeight > windowHeight)){
					_fitToViewport(pp_containerWidth,pp_containerHeight)
				};
			};
			
			return {
				width:Math.floor(imageWidth),
				height:Math.floor(imageHeight),
				containerHeight:Math.floor(pp_containerHeight),
				containerWidth:Math.floor(pp_containerWidth) + (settings.horizontal_padding * 2),
				contentHeight:Math.floor(pp_contentHeight),
				contentWidth:Math.floor(pp_contentWidth),
				resized:resized
			};
		};
		
		/**
		* Get the containers dimensions according to the item size
		* @param width {integer} Width of the item to be opened
		* @param height {integer} Height of the item to be opened
		*/
		function _getDimensions(width,height){
			width = parseFloat(width);
			height = parseFloat(height);
			
			// Get the details height, to do so, I need to clone it since it's invisible
			$pp_details = $pp_pic_holder.find('.pp_details');
			$pp_details.width(width);
			detailsHeight = parseFloat($pp_details.css('marginTop')) + parseFloat($pp_details.css('marginBottom'));
			
			$pp_details = $pp_details.clone().addClass(settings.theme).width(width).appendTo($('body')).css({
				'position':'absolute',
				'top':-10000
			});
			detailsHeight += $pp_details.height();
			detailsHeight = (detailsHeight <= 34) ? 36 : detailsHeight; // Min-height for the details
			if($.browser.msie && $.browser.version==7) detailsHeight+=8;
			$pp_details.remove();
			
			// Get the titles height, to do so, I need to clone it since it's invisible
			$pp_title = $pp_pic_holder.find('.ppt');
			$pp_title.width(width);
			titleHeight = parseFloat($pp_title.css('marginTop')) + parseFloat($pp_title.css('marginBottom'));
			$pp_title = $pp_title.clone().appendTo($('body')).css({
				'position':'absolute',
				'top':-10000
			});
			titleHeight += $pp_title.height();
			$pp_title.remove();
			
			// Get the container size, to resize the holder to the right dimensions
			pp_contentHeight = height + detailsHeight;
			pp_contentWidth = width;
			pp_containerHeight = pp_contentHeight + titleHeight + $pp_pic_holder.find('.pp_top').height() + $pp_pic_holder.find('.pp_bottom').height();
			pp_containerWidth = width;
		};
	
		function _getFileType(itemSrc){
			if (itemSrc.match(/youtube\.com\/watch/i) || itemSrc.match(/youtu\.be/i)) {
				return 'youtube';
			}else if (itemSrc.match(/vimeo\.com/i)) {
				return 'vimeo';
			}else if(itemSrc.match(/\b.mov\b/i)){ 
				return 'quicktime';
			}else if(itemSrc.match(/\b.swf\b/i)){
				return 'flash';
			}else if(itemSrc.match(/\biframe=true\b/i)){
				return 'iframe';
			}else if(itemSrc.match(/\bajax=true\b/i)){
				return 'ajax';
			}else if(itemSrc.match(/\bcustom=true\b/i)){
				return 'custom';
			}else if(itemSrc.substr(0,1) == '#'){
				return 'inline';
			}else{
				return 'image';
			};
		};
	
		function _center_overlay(){
			if(doresize && typeof $pp_pic_holder != 'undefined') {
				scroll_pos = _get_scroll();
				contentHeight = $pp_pic_holder.height(), contentwidth = $pp_pic_holder.width();

				projectedTop = (windowHeight/2) + scroll_pos['scrollTop'] - (contentHeight/2);
				if(projectedTop < 0) projectedTop = 0;
				
				if(contentHeight > windowHeight)
					return;

				$pp_pic_holder.css({
					'top': projectedTop,
					'left': (windowWidth/2) + scroll_pos['scrollLeft'] - (contentwidth/2)
				});
			};
		};
	
		function _get_scroll(){
			if (self.pageYOffset) {
				return {scrollTop:self.pageYOffset,scrollLeft:self.pageXOffset};
			} else if (document.documentElement && document.documentElement.scrollTop) { // Explorer 6 Strict
				return {scrollTop:document.documentElement.scrollTop,scrollLeft:document.documentElement.scrollLeft};
			} else if (document.body) {// all other Explorers
				return {scrollTop:document.body.scrollTop,scrollLeft:document.body.scrollLeft};
			};
		};
	
		function _resize_overlay() {
			windowHeight = $(window).height(), windowWidth = $(window).width();
			
			if(typeof $pp_overlay != "undefined") $pp_overlay.height($(document).height()).width(windowWidth);
		};
	
		function _insert_gallery(){
			if(isSet && settings.overlay_gallery && _getFileType(pp_images[set_position])=="image" && (settings.ie6_fallback && !($.browser.msie && parseInt($.browser.version) == 6))) {
				itemWidth = 52+5; // 52 beign the thumb width, 5 being the right margin.
				navWidth = (settings.theme == "facebook" || settings.theme == "pp_default") ? 50 : 30; // Define the arrow width depending on the theme
				
				itemsPerPage = Math.floor((pp_dimensions['containerWidth'] - 100 - navWidth) / itemWidth);
				itemsPerPage = (itemsPerPage < pp_images.length) ? itemsPerPage : pp_images.length;
				totalPage = Math.ceil(pp_images.length / itemsPerPage) - 1;

				// Hide the nav in the case there's no need for links
				if(totalPage == 0){
					navWidth = 0; // No nav means no width!
					$pp_gallery.find('.pp_arrow_next,.pp_arrow_previous').hide();
				}else{
					$pp_gallery.find('.pp_arrow_next,.pp_arrow_previous').show();
				};

				galleryWidth = itemsPerPage * itemWidth;
				fullGalleryWidth = pp_images.length * itemWidth;
				
				// Set the proper width to the gallery items
				$pp_gallery
					.css('margin-left',-((galleryWidth/2) + (navWidth/2)))
					.find('div:first').width(galleryWidth+5)
					.find('ul').width(fullGalleryWidth)
					.find('li.selected').removeClass('selected');
				
				goToPage = (Math.floor(set_position/itemsPerPage) < totalPage) ? Math.floor(set_position/itemsPerPage) : totalPage;

				$.prettyPhoto.changeGalleryPage(goToPage);
				
				$pp_gallery_li.filter(':eq('+set_position+')').addClass('selected');
			}else{
				$pp_pic_holder.find('.pp_content').unbind('mouseenter mouseleave');
				// $pp_gallery.hide();
			}
		};
	
		function _build_overlay(caller){
			// Inject Social Tool markup into General markup
			if(settings.social_tools)
				facebook_like_link = settings.social_tools.replace('{location_href}', encodeURIComponent(location.href)); 

			settings.markup=settings.markup.replace('{pp_social}',(settings.social_tools)?facebook_like_link:''); 
			
			$('body').append(settings.markup); // Inject the markup
			
			$pp_pic_holder = $('.pp_pic_holder') , $ppt = $('.ppt'), $pp_overlay = $('div.pp_overlay'); // Set my global selectors
			
			// Inject the inline gallery!
			if(isSet && settings.overlay_gallery) {
				currentGalleryPage = 0;
				toInject = "";
				for (var i=0; i < pp_images.length; i++) {
					if(!pp_images[i].match(/\b(jpg|jpeg|png|gif)\b/gi)){
						classname = 'default';
						img_src = '';
					}else{
						classname = '';
						img_src = pp_images[i];
					}
					toInject += "<li class='"+classname+"'><a href='#'><img src='" + img_src + "' width='50' alt='' /></a></li>";
				};
				
				toInject = settings.gallery_markup.replace(/{gallery}/g,toInject);
				
				$pp_pic_holder.find('#pp_full_res').after(toInject);
				
				$pp_gallery = $('.pp_pic_holder .pp_gallery'), $pp_gallery_li = $pp_gallery.find('li'); // Set the gallery selectors
				
				$pp_gallery.find('.pp_arrow_next').click(function(){
					$.prettyPhoto.changeGalleryPage('next');
					$.prettyPhoto.stopSlideshow();
					return false;
				});
				
				$pp_gallery.find('.pp_arrow_previous').click(function(){
					$.prettyPhoto.changeGalleryPage('previous');
					$.prettyPhoto.stopSlideshow();
					return false;
				});
				
				$pp_pic_holder.find('.pp_content').hover(
					function(){
						$pp_pic_holder.find('.pp_gallery:not(.disabled)').fadeIn();
					},
					function(){
						$pp_pic_holder.find('.pp_gallery:not(.disabled)').fadeOut();
					});

				itemWidth = 52+5; // 52 beign the thumb width, 5 being the right margin.
				$pp_gallery_li.each(function(i){
					$(this)
						.find('a')
						.click(function(){
							$.prettyPhoto.changePage(i);
							$.prettyPhoto.stopSlideshow();
							return false;
						});
				});
			};
			
			
			// Inject the play/pause if it's a slideshow
			if(settings.slideshow){
				$pp_pic_holder.find('.pp_nav').prepend('<a href="#" class="pp_play">Play</a>')
				$pp_pic_holder.find('.pp_nav .pp_play').click(function(){
					$.prettyPhoto.startSlideshow();
					return false;
				});
			}
			
			$pp_pic_holder.attr('class','pp_pic_holder ' + settings.theme); // Set the proper theme
			
			$pp_overlay
				.css({
					'opacity':0,
					'height':$(document).height(),
					'width':$(window).width()
					})
				.bind('click',function(){
					if(!settings.modal) $.prettyPhoto.close();
				});

			$('a.pp_close').bind('click',function(){$.prettyPhoto.close();return false;});

			$('a.pp_expand').bind('click',function(e){
				// Expand the image
				if($(this).hasClass('pp_expand')){
					$(this).removeClass('pp_expand').addClass('pp_contract');
					doresize = false;
				}else{
					$(this).removeClass('pp_contract').addClass('pp_expand');
					doresize = true;
				};
			
				_hideContent(function(){$.prettyPhoto.open();});
		
				return false;
			});
		
			$pp_pic_holder.find('.pp_previous, .pp_nav .pp_arrow_previous').bind('click',function(){
				$.prettyPhoto.changePage('previous');
				$.prettyPhoto.stopSlideshow();
				return false;
			});
		
			$pp_pic_holder.find('.pp_next, .pp_nav .pp_arrow_next').bind('click',function(){
				$.prettyPhoto.changePage('next');
				$.prettyPhoto.stopSlideshow();
				return false;
			});
			
			_center_overlay(); // Center it
		};

		if(!pp_alreadyInitialized && getHashtag()){
			pp_alreadyInitialized = true;
			
			// Grab the rel index to trigger the click on the correct element
			hashIndex = getHashtag();
			hashRel = hashIndex;
			hashIndex = hashIndex.substring(hashIndex.indexOf('/')+1,hashIndex.length-1);
			hashRel = hashRel.substring(0,hashRel.indexOf('/'));

			// Little timeout to make sure all the prettyPhoto initialize scripts has been run.
			// Useful in the event the page contain several init scripts.
			setTimeout(function(){$("a[rel^='"+hashRel+"']:eq("+hashIndex+")").trigger('click');},50);
		}
		
		return this.unbind('click.prettyphoto').bind('click.prettyphoto',$.prettyPhoto.initialize); // Return the jQuery object for chaining. The unbind method is used to avoid click conflict when the plugin is called more than once
	};
	
	function getHashtag(){
		url = location.href;
		hashtag = (url.indexOf('#!') != -1) ? decodeURI(url.substring(url.indexOf('#!')+2,url.length)) : false;
		return hashtag;
	};
	
	function setHashtag(){
		if(typeof theRel == 'undefined') return; // theRel is set on normal calls, it's impossible to deeplink using the API
		location.hash = '!' + theRel + '/'+rel_index+'/';
	};
	
	function clearHashtag(){
		// Clear the hashtag only if it was set by prettyPhoto
		url = location.href;
		hashtag = (url.indexOf('#!prettyPhoto')) ? true : false;
		if(hashtag) location.hash = "!prettyPhoto";
	};
	
	function getParam(name,url){
	  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	  var regexS = "[\\?&]"+name+"=([^&#]*)";
	  var regex = new RegExp( regexS );
	  var results = regex.exec( url );
	  return ( results == null ) ? "" : results[1];
	};
	
})(jQuery);

var pp_alreadyInitialized = false; // Used for the deep linking to make sure not to call the same function several times.

/*
 * jquery.socialshareprivacy.js | 2 Klicks fuer mehr Datenschutz
 *
 * http://www.heise.de/extras/socialshareprivacy/
 * http://www.heise.de/ct/artikel/2-Klicks-fuer-mehr-Datenschutz-1333879.html
 *
 * Copyright (c) 2011 Hilko Holweg, Sebastian Hilbig, Nicolas Heiringhoff, Juergen Schmidt,
 * Heise Zeitschriften Verlag GmbH & Co. KG, http://www.heise.de
 *
 * is released under the MIT License http://www.opensource.org/licenses/mit-license.php
 *
 * Spread the word, link to us if you can.
 */
(function($){
    $.fn.socialSharePrivacy = function(options){
        var defaults = {
            'services' : {
                'facebook' : {
                    'status'            : 'on',
                    'app_id'            : fb_appid,
                    'dummy_img'         : ssp_imgpath + '/dummy_facebook_en.png',
                    'txt_info'          : '2 clicks for more privacy: The first click activates the Facebook connection and allows you to submit your recommendation.',
                    'txt_fb_off'        : 'not connected to Facebook',
                    'txt_fb_on'         : 'connected to Facebook',
                    'perma_option'      : 'on',
                    'display_name'      : 'Facebook',
                    'referrer_track'    : '',
                    'language'          : 'en_US'
                }, 
                'twitter' : {
                    'status'            : 'on', 
                    'dummy_img'         : ssp_imgpath + '/dummy_twitter.png',
                    'txt_info'          : '2 clicks for more privacy: The first click activates the Twitter connection and allows you to submit the tweet.',
                    'txt_twitter_off'   : 'not connected to Twitter',
                    'txt_twitter_on'    : 'connected to Twitter',
                    'perma_option'      : 'on',
                    'display_name'      : 'Twitter',
                    'referrer_track'    : '', 
                    'tweet_text'        : getTweetText
                },
                'gplus' : {
                    'status'            : 'on',
                    'dummy_img'         : ssp_imgpath + '/dummy_gplus.png',
                    'txt_info'          : '2 clicks for more privacy: The first click activates the connection to Google and allows you to +1 this page.',
                    'txt_gplus_off'     : 'not connected to Google',
                    'txt_plus_on'       : 'connected to Google',
                    'perma_option'      : 'on',
                    'display_name'      : 'Google+',
                    'referrer_track'    : '',
                    'language'          : 'en'
                }
            },
            'info_link'         : 'http://www.heise.de/ct/artikel/2-Klicks-fuer-mehr-Datenschutz-1333879.html',
            'txt_help'          : 'Whenever you activate one of the social media buttons, information will be submitted to Facebook, Twitter or Google.',
            'settings_perma'    : 'Permanently activate social media connections for this site (opt-in). Your choice will be saved to a cookie and you can revert it at any time.',
            'cookie_path'       : '/',
            'cookie_domain'     : document.location.host,
            'cookie_expires'    : '365',
            'css_path'          : 'socialshareprivacy/socialshareprivacy.css'
        };

        // Standardwerte des Plug-Ings mit den vom User angegebenen Optionen ueberschreiben
        var options = $.extend(true, defaults, options);

        if((options.services.facebook.status == 'on' && options.services.facebook.app_id != '__FB_APP-ID__') || options.services.twitter.status == 'on' || options.services.gplusone.status == 'on'){
            //$('head').append('<link rel="stylesheet" type="text/css" href="'+options.css_path+'" />');
            $(this).prepend('<ul class="social_share_privacy_area"></ul>');
            var context = $('.social_share_privacy_area', this);
            // als URL wird erstmal die derzeitige Dokument-URL angenommen
            var uri = document.location.href;
            // ist eine kanonische URL hinterlegt wird diese verwendet
            var canonical = $("link[rel=canonical]").attr("data-href");
            if(canonical){
            	/*
                if(canonical.indexOf("http") <= 0){
                    canonical = document.location.protocol+"//"+document.location.host+document.location.port+canonical;
                    alert(canonical);
                }
                */
                uri = canonical.replace(/PHPSESSID=.*&/g, '');
            }
        }

        // Text kuerzen und am Ende mit … versehen, sofern er abgekuerzt werden musste
        function abbreviateText(text, length){
            var abbreviated = decodeURIComponent(text);
            if(abbreviated.length <= length){
                return text;
            }

            var lastWhitespaceIndex = abbreviated.substring(0, length - 1).lastIndexOf(' ');
            abbreviated = encodeURIComponent(abbreviated.substring(0, lastWhitespaceIndex)) + "…";

            return abbreviated;
        }

        // Meta-Wert abfragen
        function getMeta(name){
            var metaContent = jQuery('meta[name="' + name + '"]').attr('content');
            return metaContent ? metaContent : '';
        }
        
        // Tweet-Text
        function getTweetText(){
            // Titel aus <meta name="DC.title"> und <meta name="DC.creator"> wenn vorhanden, sonst <title>
            var title = getMeta('DC.title');
            var creator = getMeta('DC.creator');
            if(title.length > 0){
                if(creator.length > 0){
                    title = title+' - '+creator;
                }
            }
            else{
                title = $('title').text();
            }
            return encodeURIComponent(title);
        }

        return this.each(function(){
            // Facebook
            if(options.services.facebook.status == 'on'){
                // Kontrolle ob Facebook App-ID hinterlegt ist, da diese noetig fuer den Empfehlen-Button ist
                if(options.services.facebook.app_id != '__FB_APP-ID__'){
                    var fb_enc_uri = encodeURIComponent(uri+options.services.facebook.referrer_track);
                    var fb_code = '<iframe src="http://www.facebook.com/plugins/like.php?locale='+options.services.facebook.language+'&amp;app_id='+options.services.facebook.app_id+'&amp;href='+fb_enc_uri+'&amp;send=false&amp;layout=button_count&amp;width=240&amp;show_faces=false&amp;action=recommend&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden;" allowTransparency="true"></iframe>';
                    var fb_dummy_btn = '<img src="'+options.services.facebook.dummy_img+'" alt="Facebook &quot;Like&quot;-Dummy" class="fb_like_privacy_dummy" />';

                    context.append('<li class="facebook help_info"><span class="info">'+options.services.facebook.txt_info+'</span><span class="switch off">'+options.services.facebook.txt_fb_off+'</span><div class="fb_like dummy_btn">'+fb_dummy_btn+'</div></li>');

                    var $container_fb = $('li.facebook', context);

                    $('li.facebook div.fb_like img.fb_like_privacy_dummy,li.facebook span.switch', context).live('click', function(){
                        if($container_fb.find('span.switch').hasClass('off')){
                            $container_fb.addClass('info_off');
                            $container_fb.find('span.switch').addClass('on').removeClass('off').html(options.services.facebook.txt_fb_on);
                            $container_fb.find('img.fb_like_privacy_dummy').replaceWith(fb_code);
                        }
                        else{
                            $container_fb.removeClass('info_off');
                            $container_fb.find('span.switch').addClass('off').removeClass('on').html(options.services.facebook.txt_fb_off);
                            $container_fb.find('.fb_like').html(fb_dummy_btn);
                        }
                    });
                }
                else{
                    try{
                        console.log('Fehler: Es ist keine Facebook App-ID hinterlegt.');
                    }
                    catch(e){ }
                }
            }

            // Twitter
            if(options.services.twitter.status == 'on'){
                // 120 = Restzeichen-Anzahl nach automatischem URL-Kuerzen durch Twitter mit t.co
                var text = options.services.twitter.tweet_text;
                if(typeof(text) == 'function'){
                    text = text();
                }
                text = abbreviateText(text,'120');
                
                var twitter_enc_uri = encodeURIComponent(uri+options.services.twitter.referrer_track);
                var twitter_count_url = encodeURIComponent(uri);
                var twitter_code = '<iframe allowtransparency="true" frameborder="0" scrolling="no" src="http://platform.twitter.com/widgets/tweet_button.html?url='+twitter_enc_uri+'&amp;counturl='+twitter_count_url+'&amp;text='+text+'&amp;count=horizontal"></iframe>';
                var twitter_dummy_btn = '<img src="'+options.services.twitter.dummy_img+'" alt="&quot;Tweet this&quot;-Dummy" class="tweet_this_dummy" />';
                
                context.append('<li class="twitter help_info"><span class="info">'+options.services.twitter.txt_info+'</span><span class="switch off">'+options.services.twitter.txt_twitter_off+'</span><div class="tweet dummy_btn">'+twitter_dummy_btn+'</div></li>');

                var $container_tw = $('li.twitter', context);
                
                $('li.twitter div.tweet img,li.twitter span.switch', context).live('click', function(){
                    if($container_tw.find('span.switch').hasClass('off')){
                        $container_tw.addClass('info_off');
                        $container_tw.find('span.switch').addClass('on').removeClass('off').html(options.services.twitter.txt_twitter_on);
                        $container_tw.find('img.tweet_this_dummy').replaceWith(twitter_code);
                    }
                    else{
                        $container_tw.removeClass('info_off');
                        $container_tw.find('span.switch').addClass('off').removeClass('on').html(options.services.twitter.txt_twitter_off);
                        $container_tw.find('.tweet').html(twitter_dummy_btn);
                    }
                });
            }

            // Google+
            if(options.services.gplus.status == 'on'){
                // fuer G+ wird die URL nicht encoded, da das zu einem Fehler fuehrt
                var gplus_uri = uri+options.services.gplus.referrer_track;
                var gplus_code = '<div class="g-plusone" data-size="medium" data-href="'+gplus_uri+'"></div><script type="text/javascript">window.___gcfg = {lang: "'+options.services.gplus.language+'"}; (function(){ var po = document.createElement("script"); po.type = "text/javascript"; po.async = true; po.src = "https://apis.google.com/js/plusone.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s); })(); </script>';
                var gplus_dummy_btn = '<img src="'+options.services.gplus.dummy_img+'" alt="&quot;Google+1&quot;-Dummy" class="gplus_one_dummy" />';

                context.append('<li class="gplus help_info"><span class="info">'+options.services.gplus.txt_info+'</span><span class="switch off">'+options.services.gplus.txt_gplus_off+'</span><div class="gplusone dummy_btn">'+gplus_dummy_btn+'</div></li>');

                var $container_gplus = $('li.gplus', context);

                $('li.gplus div.gplusone img,li.gplus span.switch', context).live('click', function(){
                    if($container_gplus.find('span.switch').hasClass('off')){
                        $container_gplus.addClass('info_off');
                        $container_gplus.find('span.switch').addClass('on').removeClass('off').html(options.services.gplus.txt_gplus_on);
                        $container_gplus.find('img.gplus_one_dummy').replaceWith(gplus_code);
                    }
                    else{
                        $container_gplus.removeClass('info_off');
                        $container_gplus.find('span.switch').addClass('off').removeClass('on').html(options.services.gplus.txt_gplus_off);
                        $container_gplus.find('.gplusone').html(gplus_dummy_btn);
                    }
                });
            }

            // Der Info/Settings-Bereich wird eingebunden
            context.append('<li class="settings_info"><div class="settings_info_menu off perma_option_off"><a href="'+options.info_link+'"><span class="help_info icon"><span class="info">'+options.txt_help+'</span></span></a></div></li>');

            // Info-Overlays mit leichter Verzoegerung einblenden
            // modified for jQuery hoverIntent plugin 
            $('.help_info:not(.info_off)', context).hoverIntent(function() {
            	$(this).addClass('display');
            },
            function() {
            	if($(this).hasClass('display'))
            		$(this).removeClass('display');
            },
            500);

            // Menue zum dauerhaften Einblenden der aktiven Dienste via Cookie einbinden
            // Die IE7 wird hier ausgenommen, da er kein JSON kann und die Cookies hier ueber JSON-Struktur abgebildet werden
            if(((options.services.facebook.status == 'on' && options.services.facebook.perma_option == 'on' && options.services.facebook.app_id != '__FB_APP-ID__') || (options.services.twitter.status == 'on' && options.services.twitter.perma_option == 'on') || (options.services.gplus.status == 'on' && options.services.gplus.perma_option == 'on')) && (($.browser.msie && $.browser.version > 7.0) || !$.browser.msie)){
                // Cookies abrufen
                var cookie_list = document.cookie.split(';');
                var cookies = '{';
                for(var i = 0; i < cookie_list.length; i++){
                    var foo = cookie_list[i].split('=');
                    cookies+='"'+$.trim(foo[0])+'":"'+$.trim(foo[1])+'"';
                    if(i < cookie_list.length-1){
                        cookies += ',';
                    }
                }
                cookies += '}';
                cookies = JSON.parse(cookies);
				

                // Cookie setzen
                function cookieSet(name,value,days,path,domain){
                    var expires = new Date();
                    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
                    document.cookie = name+'='+value+'; expires='+expires.toUTCString()+'; path='+path+'; domain='+domain;
                }

                // Cookie loeschen
                function cookieDel(name,value){
                    var expires = new Date();
                    expires.setTime(expires.getTime() - 100);
                    document.cookie = name+'='+value+'; expires='+expires.toUTCString()+'; path='+options.cookie_path+'; domain='+options.cookie_domain;
                }

                // Container definieren
                var $container_settings_info = $('li.settings_info', context);

                // Klasse entfernen, die das i-Icon alleine formatiert, da Perma-Optionen eingeblendet werden
                $container_settings_info.find('.settings_info_menu').removeClass('perma_option_off');

                // Perma-Optionen-Icon (.settings) und Formular (noch versteckt) einbinden
                $container_settings_info.find('.settings_info_menu').append('<span class="settings">Settings</span><form><fieldset><legend>'+options.settings_perma+'</legend></fieldset></form>');

                // Die Dienste mit <input> und <label>, sowie checked-Status laut Cookie, schreiben
                if(options.services.facebook.status == 'on' && options.services.facebook.perma_option == 'on' && options.services.facebook.app_id != '__FB_APP-ID__'){
                    var perma_status_facebook = '';
                    cookies.socialSharePrivacy_facebook == 'perma_on' ? perma_status_facebook = ' checked="checked"' : perma_status_facebook = '';
                    $container_settings_info.find('form fieldset').append('<input type="checkbox" name="perma_status_facebook" id="perma_status_facebook"'+perma_status_facebook+' /><label for="perma_status_facebook">'+options.services.facebook.display_name+'</label>');
                }
                if(options.services.twitter.status == 'on' && options.services.twitter.perma_option == 'on'){
                    var perma_status_twitter = '';
                    cookies.socialSharePrivacy_twitter == 'perma_on' ? perma_status_twitter = ' checked="checked"' : perma_status_twitter = '';
                    $container_settings_info.find('form fieldset').append('<input type="checkbox" name="perma_status_twitter" id="perma_status_twitter"'+perma_status_twitter+' /><label for="perma_status_twitter">'+options.services.twitter.display_name+'</label>');
                }
                if(options.services.gplus.status == 'on' && options.services.twitter.perma_option == 'on'){
                    var perma_status_gplus = '';
                    cookies.socialSharePrivacy_gplus == 'perma_on' ? perma_status_gplus = ' checked="checked"' : perma_status_gplus = '';
                    $container_settings_info.find('form fieldset').append('<input type="checkbox" name="perma_status_gplus" id="perma_status_gplus"'+perma_status_gplus+' /><label for="perma_status_gplus">'+options.services.gplus.display_name+'</label>');
                }

                // Cursor auf Pointer setzen fuer das Zahnrad
                $container_settings_info.find('span.settings').css('cursor','pointer');

                // Einstellungs-Menue bei mouseover ein-/ausblenden
                $($container_settings_info.find('span.settings'), context).hoverIntent(function() {
                	$container_settings_info.find('.settings_info_menu').removeClass('off').addClass('on');
                },
                function() {
                	$container_settings_info.find('.settings_info_menu').removeClass('on').addClass('off');
                },
                300);

                // Klick-Interaktion auf <input> um Dienste dauerhaft ein- oder auszuschalten (Cookie wird gesetzt oder geloescht)
                $($container_settings_info.find('fieldset input')).live('click', function(event){
                    var value;
                    var click = event.target.id;
                    service = click.substr(click.lastIndexOf('_')+1, click.length);

                    $('#'+event.target.id+':checked').length ? value = 'on' : value = 'off';

                    var cookie_name = 'socialSharePrivacy_'+service;

                    if(value == 'on'){
                        // Cookie setzen
                        cookieSet(cookie_name,'perma_on',options.cookie_expires,options.cookie_path,options.cookie_domain);
                        $('form fieldset label[for='+click+']', context).addClass('checked');
                    }
                    else{
                        // Cookie loeschen
                        cookieDel(cookie_name,'perma_on');
                        $('form fieldset label[for='+click+']', context).removeClass('checked');
                    }
                });

                // Dienste automatisch einbinden, wenn entsprechendes Cookie vorhanden ist
                if(options.services.facebook.status == 'on' && options.services.facebook.perma_option == 'on' && cookies.socialSharePrivacy_facebook == 'perma_on' && options.services.facebook.app_id != '__FB_APP-ID__'){
                    $('li.facebook span.switch', context).click();
                }
                if(options.services.twitter.status == 'on' && options.services.twitter.perma_option == 'on' && cookies.socialSharePrivacy_twitter == 'perma_on'){
                    $('li.twitter span.switch', context).click();
                }
                if(options.services.gplus.status == 'on' && options.services.gplus.perma_option == 'on' && cookies.socialSharePrivacy_gplus == 'perma_on'){
                    $('li.gplus span.switch', context).click();
                }
            }
        });
    }
})(jQuery);

function share_popup (url_add, width, height)  {
	var left = (screen.width/2)-(width/2);
	var top = (screen.height/2)-(height/2);	
	window.open (url_add, 'Popup','toolbar=no,locationbar=no,directories=no,titlebar=no,status=no,menubar=no,scrollbars=no,resizable=no,width='
				 +width+',height='+height+',left='+left+',top='+top);
};

var _timer = null;
var _is_locked = false;

Array.prototype.remove = function(from, to) {
   var rest = this.slice((to || from) + 1 || this.length);
   this.length = from < 0 ? this.length + from : from;
   return this.push.apply(this, rest);
};

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
};

function setDimmed(mode)
{
	if(mode) {
		if($('#pagedimmer').length > 0)
			return;
		var _e = $('<div id="pagedimmer" style="position:fixed;left:0;top:0;z-index:2999;width:100%;height:100%;"></div>');
		_e.prependTo('body');
	}
	else if($('#pagedimmer').length > 0)
		$('#pagedimmer').remove();
}
function setBusy(mode)
{
	var el = $('#ajaxbusy');
	
    if(mode) {
    	_is_locked = true;
    	el.css('position','fixed');
    	el.css('z-index', '10000');
		el.css('top', '0');
		el.css('right', '0');
		el.show();
	}
	else {
		_is_locked = false;
		el.hide();
		el.css('position','static');
	}
}

var menu_active = false;

jQuery(document).ready(function() {
	
	// this kills the pure CSS hover effect from the dropdown menus so they will
	// only react on DOM events.
	$('html > head').append('<style>.menu li:hover ul { display: none; }</style>');
	// pull down menu handlers
	jQuery('.menu ul').hover(function() {
	}, function() {
		jQuery(this).hide();
	});
	jQuery('.dropmenu').hover(function() {
	}, function() {
		jQuery('#menu_nav ul').hide();
		menu_active = false;
	});
	jQuery('.menu li').hover(function() {
		if (menu_active) {
			jQuery(this).children('ul').show();
		}
	}, function() {
		if (menu_active) {
			jQuery(this).children('ul').hide();
		}
	});
	$('img.resize_1').live('mouseenter', function(event) {
		var resizer = $(this).prev();
		resizer.css({'position':'absolute', 'width': $(this).width(), 'left': $(this).position().left + 3, 'top': $(this).position().top + 3});
		resizer.show();
	});
	$('div.bbc_img_resizer').click(function() {
		var url = $(this).next().attr('src');
		$.prettyPhoto.open(url);
		return(false);
	});
	// bbcode img tag and attachment handlers
	$('a.attach_thumb').prettyPhoto({social_tools:'', deeplinking:false, animation_speed:0});
	// passive share button (for sharing a topic)
	$('.share_this').click(function() {
		$('#share_bar').hide();
		share_popup($(this).attr('data-target'), 700, 400);
		return(false);
	});
	$('.givelike').click(function() {
		var mid = parseInt($(this).attr('data-id'));
		if(mid > 0) {
			switch($(this).attr('data-fn')) {
				case 'give':
					sendRequest('action=xmlhttp;sa=givelike;m=' + mid, $(this));
				    break;
				case 'remove':
					sendRequest('action=xmlhttp;sa=givelike;remove=1;m=' + mid, $(this));
				    break;
				case 'repair':
					sendRequest('action=xmlhttp;sa=givelike;repair=1;m=' + mid, $(this));
					break;
				default:
					break;
			}
		}
		return(false);
	});
	$('table.table_grid td .input_check, table.topic_table td .input_check').change(function() {
		var cbox = this;
		$(this).parent().parent().children('td').each(function() {
			if($(cbox).is(':checked'))
				$(this).addClass('inline_highlight');
			else
				$(this).removeClass('inline_highlight');
		});
		return(false);
	});
	
	$('table.table_grid th .input_check, table.topic_table th .input_check').change(function() {
		var cbox = this;
		$(this).parent().parent().parent().parent().find('tbody').find('input.input_check').each(function() {
			$(this).prop('checked', $(cbox).is(':checked') ? true : false);
			$(this).parent().parent().children('td').each(function() {
				if($(cbox).is(':checked'))
					$(this).addClass('inline_highlight');
				else
					$(this).removeClass('inline_highlight');
			});
		});
		return(false);
	});
	
	// handle the topic preview functionality in MessageIndex
	// fade in the preview link when hovering the subject
	$('span.tpeek').hover(
		function() {
			$('#tpeekresult').remove();
			$(this).append('<a style="display:none;-lineheight:100%;" href="#" onclick="firePreview(' + $(this).attr('data-id') +', $(this)); return(false);" class="tpeek">Preview topic</a>');
			$(this).children('a[class=tpeek]:first').fadeIn();
		},
		function() {
	});
	// fade it out when leaving the table cell
	$('td.subject').hover(
		function() {},
		function() {
			$(this).find('a[class=tpeek]:first').remove();			
	});
	// convert all time stamps to relative 
	
	if(!disableDynamicTime)
		$('abbr.timeago').timeago();
		
	if($('#socialshareprivacy'))
    	$('#socialshareprivacy').socialSharePrivacy();
	
	$('div.spoiler.head').click(function() {
        var content = $(this).next();
		if(content.css('display') != 'none')
			content.hide();
		else
			content.show();
        return(false);
	});
	$('a.bbc_link').each(function() {
		if($(this).html().match(/<img .*>/))
			$(this).css('border', 'none');
	});
	$('#jsconfirm').jqm({overlay: 50, modal: true, trigger: false, center:true});

	$('a.easytip').easyTooltip( {parentData: true} );
	$('div.iconlegend_container').hover(function() {
		$(this).css('opacity', '1.0');
	},
	function() {
		$(this).css('opacity', '0.4');
	});
});

/*
 * all "onclick" handlers
 */

function singlePost(id) 
{
	sendRequest('msg=' + id + ';perma;xml', null);
}
function getNotifications(el)
{
	if($('#notify_wrapper').length > 0)		// it already exists in the dom tree
		return;
	sendRequest('action=astream;sa=notifications', null);
}

function getAStream(el)
{
	var _el = el.children('a:first');
	if(el.attr('data-board') == 'all')
		sendRequest('action=astream;sa=get;all;xml', null);
	else
		sendRequest('action=astream;sa=get;b=' + parseInt(el.attr('data-board')) + ';xml', null);
}
function getMcard(uid)
{
	if(uid > 0)
		sendRequest('action=xmlhttp;sa=mcard;u=' + parseInt(uid), null);
	return(false);
}

function getLikes(mid)
{
	sendXMLDocument(smf_prepareScriptUrl(smf_scripturl) + 'action=like;sa=getlikes;m=' + mid + ';xml', '', response_xml);
}

function brdModeratorsPopup(el)
{
	el.children('.brd_moderators_chld').show();
	return(false);
}

function sharePost(el)
{
	var permalink = el.parent().find('a:first');
	var subject = el.parent().parent().find('h5:first').html().trim();
	$('#share_bar').insertBefore(el.parent());
	$('#share_bar span.share_this').each(function() {
		var href = $(this).attr('data-href').replace(/%%uri%%/g, permalink.attr('href'));
		var new_href = href.replace(/%%txt%%/g, encodeURIComponent(subject));
		$(this).attr('data-target', new_href);
	});
	$('#share_bar').fadeIn();
	
}
function mcardClose()
{
	$('#mcard').hide();
	$('#mcard').css('position', 'static');
	$('#mcard_inner').html('');
	setDimmed(0);
	return(false);
}

function onMenuArrowClick(el) 
{
	var id = 'button' + el.attr('id');
	$('#' + id).children('ul:first').show();
	menu_active = true;
	return(false);
}

function whoPosted(el)
{
	var t = el.attr('data-topic');
	if(t)
		sendRequest('action=xmlhttp;sa=whoposted;t=' + t, null);
	return(false);
}

// collapse / expand a board index category
function catCollapse(el)
{
	var img = el.find('img:first');
	var is_collapsed = (img.hasClass('_expand')) ? 1 : 0;
	var request = 'action=xmlhttp;sa=collapse;c=' + el.attr('data-id') + (is_collapsed ? ';expand=1' : ';collapse=1');
	sendRequest(request, el);
	var id = '#category_' + el.attr('data-id') + '_boards';
	var src = img.attr('src');
	if(is_collapsed) {
		$(id).fadeIn();
		img.removeClass('_expand');
		img.addClass('_collapse');
	}
	else {
		$(id).fadeOut();
		img.removeClass('_collapse');
		img.addClass('_expand');
	}
	return(false);
}

// toggle the side bar
function sbToggle(el)
{
	// side bar (toggle, animate, load content via ajax request)
	var is_visible = ($('#sidebar').css('display') == 'none' ? false : true);
	$('#sbtoggle').removeClass('expand collapse');
	if(is_visible) {
		$('#sidebar').fadeOut(100, function() {
			$('#container').animate({marginRight: '0'}, 350);
		});
		createCookie('smf_sidebar_disabled', 1, 300);
		$('#sbtoggle').addClass('expand');
	}
	else {
		$('#container').animate({marginRight: sideBarWidth + 20 + 'px'}, 350, function() {
			$('#sidebar').fadeIn(100);
		});
		createCookie('smf_sidebar_disabled', 0, 300);
		$('#sbtoggle').addClass('collapse');
	}
	return(false);
}

function cContainer(el)
{
	var raw_id = el.attr('id');
	var id = '#' + raw_id + '_body';
	var is_visible = ($(id).css('display') == 'none' ? false : true);
	if(is_visible) {
		$(id).fadeOut(500);
		el.removeClass('_collapse');
		el.addClass('_expand');
	}
	else {
		var height = $(id).attr('data-height');
		$(id).fadeIn(500);
		el.removeClass('_expand');
		el.addClass('_collapse');
	}
	var cookie = readCookie('SF_collapsed') || '';
	if(cookie.length > 1)
		var _s = cookie.split(',');
	if(is_visible)
		cookie += (',' + raw_id);
	else {
		var n;
		for(n = 0; n < _s.length; n++) {
			if(_s[n] == raw_id)
				_s.remove(n, n);
		}
		cookie = _s.join(',');
	}
	createCookie('SF_collapsed', cookie, 360);
	return(false);
}

function submitTagForm(ele)
{
	sendRequest('action=xmlhttp;sa=tags;submittag=1;topic=' + $('#tagtopic').val() + ';tag=' + encodeURIComponent($('#newtags').val()), ele);
	$('#tagform').remove();
}
// submit ajax request for a topic preview
function firePreview(topic_id, ele)
{
	sendRequest('action=xmlhttp;sa=mpeek;t=' + topic_id, null);
}

function sendRequest(request, anchor_element)
{
	if(_is_locked)
		return;
		
	request = request + ';' + sSessionVar + '='	+ sSessionId + ';xml';
	var sUrl = smf_prepareScriptUrl(smf_scripturl) + request;
	setBusy(1);
	if(anchor_element == null)
		sendXMLDocument(sUrl, '', response_xml);
	else
		sendXMLDocumentWithAnchor(sUrl, '', response, anchor_element);
}

function openResult(html, width, offset)
{
	var el = $('#mcard');

	setDimmed(1);
	var windowheight = $(window).height();
	$('#mcard_inner').html(html);
	if($('#mcard_content').length)
		$('#mcard_content').css({'max-height': windowheight * 0.9 + 'px', 'overflow': 'auto'});

	el.css({'width': parseInt(width) > 0 ? width : 'auto', "position": 'fixed', 'z-index': '10000'});
	centerElement(el, offset);
	$('div#mcard_inner abbr.timeago').timeago();
	el.show();
}

/**
 * generic handler for XML response from ajax requests.
 * analyzes the <response> element to determine how to open the <content> part.
 */
function response_xml(responseXML)
{
	try {
		setBusy(0);

		var data = $(responseXML);
		var _r = data.find('response');
		if(_r) {
			var _error = _r.attr('error') || 0;
			if(_error) {
				// TODO this *should* be translatable!
				var title = _r.find('title').text() || 'XML response error';
				var msg = _r.find('message').text() || 'Unknown or unspecified error.';
				Eos_Alert(title, msg);
				return(false);
			}
			var content = data.find('content').text();
			/*
			 * how private handlers work:
			 *
			 * the response must define a script in the <handler> element (separated from <content>).
			 * This *has* to be a function and the <handler> must NOT contain <script> tags, just the
			 * code.
			 * The entry function must accept a single parameter (the <content>) and its name must
			 * be defined in the fn attribute of the <response> element.
			 * this callback function is then responsible for all further DOM manipulations to
			 * display the response content.
			 */
			if(_r.attr('open') == 'private_handler') {
				var handler = data.find('handler').text();
				var fn = _r.attr('fn');
				$('#__t_script').html('<script>' + handler + '</script>');
				window[fn](content);
				return(false);
			}
			var width = _r.attr('width') || '300px';
			var offset = parseInt(_r.attr('offset')) || 0;
			openResult(content, width, offset);
			return(false);
		}
		return(false);
	} catch(e) {
		Eos_Alert('XmlHTTP Request', 'Unknown or unspecified error in response document.');
		return(false);
	}
}
/*
 * generic handler for XMLHttp response. Determines its origin by observing ele
 */
function response(ele, responseText)
{
	try {
		setBusy(0);
		if(ele.attr('class') == 'givelike') {
			var id = '#likers_msg_' + ele.attr('data-id');
			$(id).html(responseText);
			if(ele.attr('data-fn') == 'give') {
				ele.attr('data-fn', 'remove');
				ele.html(smf_unlikelabel);
			}
			else if(ele.attr('data-fn') == 'remove'){
				ele.attr('data-fn', 'give');
				ele.html(smf_likelabel);
			}
			return;
		}
		if(ele.attr('id') == 'addtag') {
			$('#addtag').before(responseText);
			return;
		}
		if(ele.attr('id') == 'tags') {
			ele.html(responseText);
			return;
		}
		if(ele.hasClass('collapse'))
			return;
		if(ele.attr('id') == 'sidebar') {
			ele.html(responseText);
			sidebar_content_loaded = true;
			return;
		}
		openResult(responseText, '500px', 0);
		$('div#mcard_inner abbr.timeago').timeago();
	} catch(e) {
		setBusy(0);
	}
}

function openAdvSearch(e)
{
	$('#search_form').css({overflow: 'auto', height: 'auto', 'padding-bottom': '10px'});
	$('#search_form').addClass('search_form_active');
}

function submitSearchBox()
{
	if($('#search_form #i_topic').is(':checked')) {
		$('#search_form #s_board').remove();
	}
	else if($('#search_form #i_board').is(':checked')) {
		$('#search_form #s_topic').remove();
	}
	else {
		$('#search_form #s_board').remove();
		$('#search_form #s_topic').remove();
	}
}

$('#adv_search').live('mouseleave',function(event) {
	$('#search_form').css({overflow: 'hidden', height: '24px', 'padding-bottom': '0'});
	$('#search_form').removeClass('search_form_active');
});
$('.brd_moderators_chld, #share_bar').live('mouseleave',function(event) {
	$(this).hide();
});
