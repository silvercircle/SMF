/* 
 * This script is loaded asynchronously in the footer of the theme
 */
 

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
                    'dummy_img'         : ssp_imgpath + '/dummy_facebook_en.png',
                    'txt_info'          : '2 clicks for more privacy: The first click activates the Facebook connection and allows you to submit your recommendation.',
                    'txt_fb_off'        : 'not connected to Facebook',
                    'txt_fb_on'         : 'connected to Facebook',
                    'perma_option'      : 'on',
                    'display_name'      : 'Facebook',
                    'referrer_track'    : '',
                    'language'          : 'en_US',
                    'action'            : 'recommend'
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

        if(options.services.facebook.status == 'on' || options.services.twitter.status == 'on' || options.services.gplusone.status == 'on'){
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
                var fb_enc_uri = encodeURIComponent(uri + options.services.facebook.referrer_track);
                var fb_code = '<iframe src="http://www.facebook.com/plugins/like.php?locale=' + options.services.facebook.language + '&amp;href=' + fb_enc_uri + '&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=false&amp;action=' + options.services.facebook.action + '&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:145px; height:21px;" allowTransparency="true"></iframe>';
                var fb_dummy_btn = '<img src="' + options.services.facebook.dummy_img + '" alt="Facebook &quot;Like&quot;-Dummy" class="fb_like_privacy_dummy" />';

                context.append('<li class="facebook help_info"><span class="info">' + options.services.facebook.txt_info + '</span><span class="switch off">' + options.services.facebook.txt_fb_off + '</span><div class="fb_like dummy_btn">' + fb_dummy_btn + '</div></li>');

                var $container_fb = $('li.facebook', context);

                $('li.facebook div.fb_like img.fb_like_privacy_dummy,li.facebook span.switch', context).live('click', function () {
                    if ($container_fb.find('span.switch').hasClass('off')) {
                        $container_fb.addClass('info_off');
                        $container_fb.find('span.switch').addClass('on').removeClass('off').html(options.services.facebook.txt_fb_on);
                        $container_fb.find('img.fb_like_privacy_dummy').replaceWith(fb_code);
                    } else {
                        $container_fb.removeClass('info_off');
                        $container_fb.find('span.switch').addClass('off').removeClass('on').html(options.services.facebook.txt_fb_off);
                        $container_fb.find('.fb_like').html(fb_dummy_btn);
                    }
                });
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
            if(((options.services.facebook.status == 'on' && options.services.facebook.perma_option == 'on') || (options.services.twitter.status == 'on' && options.services.twitter.perma_option == 'on') || (options.services.gplus.status == 'on' && options.services.gplus.perma_option == 'on')) && (($.browser.msie && $.browser.version > 7.0) || !$.browser.msie)){
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
                if(options.services.facebook.status == 'on' && options.services.facebook.perma_option == 'on'){
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
                if(options.services.facebook.status == 'on' && options.services.facebook.perma_option == 'on' && cookies.socialSharePrivacy_facebook == 'perma_on'){
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
		_e.click(function() {
			mcardClose();
			if($('#tagform').length > 0)
				$('#tagform').remove();
		});
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

function giveLike(_el)
{
	var r, mid;
	switch(_el.attr('data-fn')) {
		case 'give':
			r = parseInt(_el.attr('data-rtype'));
			mid = parseInt(_el.parent().attr('data-likebarid'));
			if(mid > 0 && r > 0)
				sendRequest('action=xmlhttp;sa=givelike;m=' + mid + ';r=' + r, null);
		    break;
		case 'remove':
			mid = parseInt(_el.attr('data-id'));
			if(mid > 0)
				sendRequest('action=xmlhttp;sa=givelike;remove=1;m=' + mid, null);
		    break;
		case 'repair':
			mid = parseInt(_el.attr('data-id'));
			if(mid > 0)
				sendRequest('action=xmlhttp;sa=givelike;repair=1;m=' + mid, null);
			break;
		default:
			break;
	}
	return(false);
}
function ratingWidgetInvoke(_el)
{
	var 	id, ctype;
	id = parseInt(_el.parent().attr('data-likebarid'));
	ctype = parseInt(_el.parent().attr('data-ctype'));

	if(id > 0 && ctype > 0)
		sendRequest('action=like;sa=widget;id=' + id + ';c=' + ctype, null);
	return(false);
}

function bbc_refresh()
{
	$('img.resize_1').on('mouseenter', function(event) {
		var resizer = $(this).prev();
		resizer.css({'position':'absolute', 'width': $(this).width(), 'left': $(this).position().left + 3, 'top': $(this).position().top + 3});
		resizer.show();
	});
	$('div.bbc_img_resizer').on('mouseleave', function(event) {
		$(this).hide();
		return(false);
	})
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
}
jQuery(document).ready(function() {
	
	// this kills the pure CSS hover effect from the dropdown menus so they will
	// only react on DOM events.
	$('html > head').append('<style>ul.dropmenu li:hover ul { display: none; }</style>');
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
	// passive share button (for sharing a topic)
	$('.share_this').click(function() {
		$('#share_bar').hide();
		share_popup($(this).attr('data-target'), 700, 400);
		return(false);
	});
	$('.givelike').click(function() {
		giveLike($(this));
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
		return(true);
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
		return(true);
	});

	$('input.it_check').change(function() {
		var cbox = this;
		var id = $(this).val();
		$('div.post_wrapper[data-mid=' + id + ']').each(function() {
			if($(cbox).is(':checked')) {
				$(this).addClass('inline_highlight');
				$(this).find('div.post_content:first').addClass('inline_highlight');
			}
			else {
				$(this).removeClass('inline_highlight');
				$(this).find('div.post_content:first').removeClass('inline_highlight');
			}
		});
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
	
	$('#jsconfirm').jqm({overlay: 50, modal: true, trigger: false, center:true});

	$('a.easytip, span.easytip').easyTooltip( {parentData: true} );
	$('div.iconlegend_container').hover(function() {
		$(this).css('opacity', '1.0');
	},
	function() {
		$(this).css('opacity', '0.4');
	});
	$('span.ratings span.number').click(function() {
		sendRequest('action=like;sa=getlikes;m=' + parseInt($(this).parent().attr('data-mid'))  + ';r=' + parseInt($(this).attr('data-rtype')), null);
		return(false);
	});
	bbc_refresh();
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
    $('#mcard').css('max-height', 'none');
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
	//var is_visible = ($('#sidebar').css('display') == 'none' ? false : true);
	var is_visible = !sidebar_disabled;
	$('#sbtoggle').removeClass('expand collapse');
	if(is_visible) {
		$('#sidebar').fadeOut(100, function() {
			$('#container').animate({marginRight: '0'}, 350);
		});
		//createCookie('smf_sidebar_disabled', 1, 300);
		$('#sbtoggle').addClass('expand');
	}
	else {
		$('#container').animate({marginRight: sideBarWidth + 20 + 'px'}, 350, function() {
			$('#sidebar').fadeIn(100);
		});
		//createCookie('smf_sidebar_disabled', 0, 300);
		$('#sbtoggle').addClass('collapse');
	}
	sidebar_disabled = !sidebar_disabled;
	sendRequest('action=xmlhttp;sa=togglesb;class=' + $('#sbtoggle').attr('data-class'), $('#sidebar'));
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
	setDimmed(0);
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
		
	request = request + ';' + sSessionVar + '='	+ sSessionId + ';xml' + sSID;
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
			var content = data.find('content').text() || '';
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
				var handler = data.find('handler').text() || '';
				var json_data = new Array();
				var param = data.find('data').text() || '';
				if(param.length > 0)
					json_data = $.parseJSON(param);
				var fn = _r.attr('fn');
				$('#__t_script').html('<script>' + handler + '</script>');
				window[fn](content, json_data);
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
		if(ele.attr('id') == 'sidebar')
			return;
		if(ele.attr('id') == 'addtag') {
			$('#addtag').before(responseText);
			setDimmed(1);
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
    $('#search_form input.default').show();
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
    $('#search_form input.default').hide();
	$('#search_form').css({overflow: 'hidden', height: '26px', 'padding-bottom': '0'});
	$('#search_form').removeClass('search_form_active');
});
$('.brd_moderators_chld, #share_bar').live('mouseleave',function(event) {
	$(this).hide();
});

var clicks = 0;
var singleClickTimer = false;
$('a.member').on('click',function(event) {
    var mid = $(this).attr('attr-mid');
    if(typeof(mid) !== "undefined") {
        // Already got a click? This is dblclick then, just pass through
        if(clicks == 1) {
            if(singleClickTimer)
                clearTimeout(singleClickTimer);
            return true;
        }
        // Show mcard with a slight delay
        singleClickTimer = setTimeout(function(){
            getMcard(mid);
            clicks = 0;
        }, 250);
        clicks = 1;
        return false;
    }
});
