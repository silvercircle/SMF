/*
 * this integrates
 * a) colorbox.js
 * b) syntax highlighter
 * c) site-specific jQuery code.
 * 
 * This script is loaded asynchronously in the footer of the theme
 */
// ColorBox v1.3.17.1 - a full featured, light-weight, customizable lightbox based on jQuery 1.3+
// Copyright (c) 2011 Jack Moore - jack[AT]colorpowered[DOT]com
// Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
(function(a,b,c){function bc(b){if(!T){O=b,_(a.extend(J,a.data(O,e))),x=a(O),P=0,J.rel!=="nofollow"&&(x=a("."+X).filter(function(){var b=a.data(this,e).rel||this.rel;return b===J.rel}),P=x.index(O),P===-1&&(x=x.add(O),P=x.length-1));if(!R){R=S=!0,q.show();if(J.returnFocus)try{O.blur(),a(O).one(k,function(){try{this.focus()}catch(a){}})}catch(c){}p.css({opacity:+J.opacity,cursor:J.overlayClose?"pointer":"auto"}).show(),J.w=Z(J.initialWidth,"x"),J.h=Z(J.initialHeight,"y"),W.position(0),n&&y.bind("resize."+o+" scroll."+o,function(){p.css({width:y.width(),height:y.height(),top:y.scrollTop(),left:y.scrollLeft()})}).trigger("resize."+o),ba(g,J.onOpen),I.add(C).hide(),H.html(J.close).show()}W.load(!0)}}function bb(){var a,b=f+"Slideshow_",c="click."+f,d,e,g;J.slideshow&&x[1]?(d=function(){E.text(J.slideshowStop).unbind(c).bind(i,function(){if(P<x.length-1||J.loop)a=setTimeout(W.next,J.slideshowSpeed)}).bind(h,function(){clearTimeout(a)}).one(c+" "+j,e),q.removeClass(b+"off").addClass(b+"on"),a=setTimeout(W.next,J.slideshowSpeed)},e=function(){clearTimeout(a),E.text(J.slideshowStart).unbind([i,h,j,c].join(" ")).one(c,d),q.removeClass(b+"on").addClass(b+"off")},J.slideshowAuto?d():e()):q.removeClass(b+"off "+b+"on")}function ba(b,c){c&&c.call(O),a.event.trigger(b)}function _(b){for(var c in b)a.isFunction(b[c])&&c.substring(0,2)!=="on"&&(b[c]=b[c].call(O));b.rel=b.rel||O.rel||"nofollow",b.href=b.href||a(O).attr("href"),b.title=b.title||O.title,typeof b.href=="string"&&(b.href=a.trim(b.href))}function $(a){return J.photo||/\.(gif|png|jpg|jpeg|bmp)(?:\?([^#]*))?(?:#(\.*))?$/i.test(a)}function Z(a,b){b=b==="x"?y.width():y.height();return typeof a=="string"?Math.round(/%/.test(a)?b/100*parseInt(a,10):parseInt(a,10)):a}function Y(c,d){var e=b.createElement("div");c&&(e.id=f+c),e.style.cssText=d||"";return a(e)}var d={transition:"elastic",speed:300,width:!1,initialWidth:"600",innerWidth:!1,maxWidth:!1,height:!1,initialHeight:"450",innerHeight:!1,maxHeight:!1,scalePhotos:!0,scrolling:!0,inline:!1,html:!1,iframe:!1,fastIframe:!0,photo:!1,href:!1,title:!1,rel:!1,opacity:.9,preloading:!0,current:"image {current} of {total}",previous:"previous",next:"next",close:"close",open:!1,returnFocus:!0,loop:!0,slideshow:!1,slideshowAuto:!0,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",onOpen:!1,onLoad:!1,onComplete:!1,onCleanup:!1,onClosed:!1,overlayClose:!0,escKey:!0,arrowKey:!0,top:!1,bottom:!1,left:!1,right:!1,fixed:!1,data:!1},e="colorbox",f="cbox",g=f+"_open",h=f+"_load",i=f+"_complete",j=f+"_cleanup",k=f+"_closed",l=f+"_purge",m=a.browser.msie&&!a.support.opacity,n=m&&a.browser.version<7,o=f+"_IE6",p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J={},K,L,M,N,O,P,Q,R,S,T,U,V,W,X=f+"Element";W=a.fn[e]=a[e]=function(b,c){var f=this,g;if(!f[0]&&f.selector)return f;b=b||{},c&&(b.onComplete=c);if(!f[0]||f.selector===undefined)f=a("<a/>"),b.open=!0;f.each(function(){a.data(this,e,a.extend({},a.data(this,e)||d,b)),a(this).addClass(X)}),g=b.open,a.isFunction(g)&&(g=g.call(f)),g&&bc(f[0]);return f},W.init=function(){y=a(c),q=Y().attr({id:e,"class":m?f+(n?"IE6":"IE"):""}),p=Y("Overlay",n?"position:absolute":"").hide(),r=Y("Wrapper"),s=Y("Content").append(z=Y("LoadedContent","width:0; height:0; overflow:hidden"),B=Y("LoadingOverlay").add(Y("LoadingGraphic")),C=Y("Title"),D=Y("Current"),F=Y("Next"),G=Y("Previous"),E=Y("Slideshow").bind(g,bb),H=Y("Close")),r.append(Y().append(Y("TopLeft"),t=Y("TopCenter"),Y("TopRight")),Y(!1,"clear:left").append(u=Y("MiddleLeft"),s,v=Y("MiddleRight")),Y(!1,"clear:left").append(Y("BottomLeft"),w=Y("BottomCenter"),Y("BottomRight"))).children().children().css({"float":"left"}),A=Y(!1,"position:absolute; width:9999px; visibility:hidden; display:none"),a("body").prepend(p,q.append(r,A)),s.children().hover(function(){a(this).addClass("hover")},function(){a(this).removeClass("hover")}).addClass("hover"),K=t.height()+w.height()+s.outerHeight(!0)-s.height(),L=u.width()+v.width()+s.outerWidth(!0)-s.width(),M=z.outerHeight(!0),N=z.outerWidth(!0),q.css({"padding-bottom":K,"padding-right":L}).hide(),F.click(function(){W.next()}),G.click(function(){W.prev()}),H.click(function(){W.close()}),I=F.add(G).add(D).add(E),s.children().removeClass("hover"),p.click(function(){J.overlayClose&&W.close()}),a(b).bind("keydown."+f,function(a){var b=a.keyCode;R&&J.escKey&&b===27&&(a.preventDefault(),W.close()),R&&J.arrowKey&&x[1]&&(b===37?(a.preventDefault(),G.click()):b===39&&(a.preventDefault(),F.click()))})},W.remove=function(){q.add(p).remove(),a("."+X).removeData(e).removeClass(X)},W.position=function(a,c){function g(a){t[0].style.width=w[0].style.width=s[0].style.width=a.style.width,B[0].style.height=B[1].style.height=s[0].style.height=u[0].style.height=v[0].style.height=a.style.height}var d,e=0,f=0;q.hide(),J.fixed&&!n?q.css({position:"fixed"}):(e=y.scrollTop(),f=y.scrollLeft(),q.css({position:"absolute"})),J.right!==!1?f+=Math.max(y.width()-J.w-N-L-Z(J.right,"x"),0):J.left!==!1?f+=Z(J.left,"x"):f+=Math.max(y.width()-J.w-N-L,0)/2,J.bottom!==!1?e+=Math.max(b.documentElement.clientHeight-J.h-M-K-Z(J.bottom,"y"),0):J.top!==!1?e+=Z(J.top,"y"):e+=Math.max(b.documentElement.clientHeight-J.h-M-K,0)/2,q.show(),d=q.width()===J.w+N&&q.height()===J.h+M?0:a,r[0].style.width=r[0].style.height="9999px",q.dequeue().animate({width:J.w+N,height:J.h+M,top:e,left:f},{duration:d,complete:function(){g(this),S=!1,r[0].style.width=J.w+N+L+"px",r[0].style.height=J.h+M+K+"px",c&&c()},step:function(){g(this)}})},W.resize=function(a){if(R){a=a||{},a.width&&(J.w=Z(a.width,"x")-N-L),a.innerWidth&&(J.w=Z(a.innerWidth,"x")),z.css({width:J.w}),a.height&&(J.h=Z(a.height,"y")-M-K),a.innerHeight&&(J.h=Z(a.innerHeight,"y"));if(!a.innerHeight&&!a.height){var b=z.wrapInner("<div style='overflow:auto'></div>").children();J.h=b.height(),b.replaceWith(b.children())}z.css({height:J.h}),W.position(J.transition==="none"?0:J.speed)}},W.prep=function(b){function h(b){W.position(b,function(){function o(){m&&q[0].style.removeAttribute("filter")}var b,d,g,h,j=x.length,k,n;!R||(n=function(){clearTimeout(V),B.hide(),ba(i,J.onComplete)},m&&Q&&z.fadeIn(100),C.html(J.title).add(z).show(),j>1?(typeof J.current=="string"&&D.html(J.current.replace(/\{current\}/,P+1).replace(/\{total\}/,j)).show(),F[J.loop||P<j-1?"show":"hide"]().html(J.next),G[J.loop||P?"show":"hide"]().html(J.previous),b=P?x[P-1]:x[j-1],g=P<j-1?x[P+1]:x[0],J.slideshow&&E.show(),J.preloading&&(h=a.data(g,e).href||g.href,d=a.data(b,e).href||b.href,h=a.isFunction(h)?h.call(g):h,d=a.isFunction(d)?d.call(b):d,$(h)&&(a("<img/>")[0].src=h),$(d)&&(a("<img/>")[0].src=d))):I.hide(),J.iframe?(k=a("<iframe/>").addClass(f+"Iframe")[0],J.fastIframe?n():a(k).one("load",n),k.name=f+ +(new Date),k.src=J.href,J.scrolling||(k.scrolling="no"),m&&(k.frameBorder=0,k.allowTransparency="true"),a(k).appendTo(z).one(l,function(){k.src="//about:blank"})):n(),J.transition==="fade"?q.fadeTo(c,1,o):o(),y.bind("resize."+f,function(){W.position(0)}))})}function g(){J.h=J.h||z.height(),J.h=J.mh&&J.mh<J.h?J.mh:J.h;return J.h}function d(){J.w=J.w||z.width(),J.w=J.mw&&J.mw<J.w?J.mw:J.w;return J.w}if(!!R){var c=J.transition==="none"?0:J.speed;y.unbind("resize."+f),z.remove(),z=Y("LoadedContent").html(b),z.hide().appendTo(A.show()).css({width:d(),overflow:J.scrolling?"auto":"hidden"}).css({height:g()}).prependTo(s),A.hide(),a(Q).css({"float":"none"}),n&&a("select").not(q.find("select")).filter(function(){return this.style.visibility!=="hidden"}).css({visibility:"hidden"}).one(j,function(){this.style.visibility="inherit"}),J.transition==="fade"?q.fadeTo(c,0,function(){h(0)}):h(c)}},W.load=function(b){var c,d,g=W.prep;S=!0,Q=!1,O=x[P],b||_(a.extend(J,a.data(O,e))),ba(l),ba(h,J.onLoad),J.h=J.height?Z(J.height,"y")-M-K:J.innerHeight&&Z(J.innerHeight,"y"),J.w=J.width?Z(J.width,"x")-N-L:J.innerWidth&&Z(J.innerWidth,"x"),J.mw=J.w,J.mh=J.h,J.maxWidth&&(J.mw=Z(J.maxWidth,"x")-N-L,J.mw=J.w&&J.w<J.mw?J.w:J.mw),J.maxHeight&&(J.mh=Z(J.maxHeight,"y")-M-K,J.mh=J.h&&J.h<J.mh?J.h:J.mh),c=J.href,V=setTimeout(function(){B.show()},100),J.inline?(Y().hide().insertBefore(a(c)[0]).one(l,function(){a(this).replaceWith(z.children())}),g(a(c))):J.iframe?g(" "):J.html?g(J.html):$(c)?(a(Q=new Image).addClass(f+"Photo").error(function(){J.title=!1,g(Y("Error").text("This image could not be loaded"))}).load(function(){var a;Q.onload=null,J.scalePhotos&&(d=function(){Q.height-=Q.height*a,Q.width-=Q.width*a},J.mw&&Q.width>J.mw&&(a=(Q.width-J.mw)/Q.width,d()),J.mh&&Q.height>J.mh&&(a=(Q.height-J.mh)/Q.height,d())),J.h&&(Q.style.marginTop=Math.max(J.h-Q.height,0)/2+"px"),x[1]&&(P<x.length-1||J.loop)&&(Q.style.cursor="pointer",Q.onclick=function(){W.next()}),m&&(Q.style.msInterpolationMode="bicubic"),setTimeout(function(){g(Q)},1)}),setTimeout(function(){Q.src=c},1)):c&&A.load(c,J.data,function(b,c,d){g(c==="error"?Y("Error").text("Request unsuccessful: "+d.statusText):a(this).contents())})},W.next=function(){!S&&x[1]&&(P<x.length-1||J.loop)&&(P=P<x.length-1?P+1:0,W.load())},W.prev=function(){!S&&x[1]&&(P||J.loop)&&(P=P?P-1:x.length-1,W.load())},W.close=function(){R&&!T&&(T=!0,R=!1,ba(j,J.onCleanup),y.unbind("."+f+" ."+o),p.fadeTo(200,0),q.stop().fadeTo(300,0,function(){q.add(p).css({opacity:1,cursor:"auto"}).hide(),ba(l),z.remove(),setTimeout(function(){T=!1,ba(k,J.onClosed)},1)}))},W.element=function(){return a(O)},W.settings=d,U=function(a){a.button!==0&&typeof a.button!="undefined"||a.ctrlKey||a.shiftKey||a.altKey||(a.preventDefault(),bc(this))},a.fn.delegate?a(b).delegate("."+X,"click",U):a("."+X).live("click",U),a(W.init)})(jQuery,document,this);

/**
 * site specific jQuery code
 */
 
function share_popup (url_add, width, height)  {
	var left = (screen.width/2)-(width/2);
	var top = (screen.height/2)-(height/2);	
	window.open (url_add, 'Popup','toolbar=no,locationbar=no,directories=no,titlebar=no,status=no,menubar=no,scrollbars=no,resizable=no,width='
				 +width+',height='+height+',left='+left+',top='+top);
};

var _timer = null;
var req = null;
var _is_locked = false;

function stopEventPropagation(_e)
{
   if (_e.stopPropagation) {
       _e.stopPropagation();
   }
   else if(window.event){
      window.event.cancelBubble = true;
   }
}

function setBusy(mode)
{
	var el = $('#ajaxbusy');
	
    if(mode) {
    	_is_locked = true;
    	el.css('position','fixed');
    	el.css('z-index', '100');
		//el.css("top", $(window).scrollTop() + "px");
		el.css('top', '0');
		//el.css("left", (($(window).width() - el.outerWidth()) / 2) + $(window).scrollLeft() + "px");
		el.css('right', '0px');
		el.show();
	}
	else {
		_is_locked = false;
		el.hide();
	}
	
}

jQuery(document).ready(function() {
	
	// this kills the pure CSS hover effect from the dropdown menus so they will
	// only react on DOM events.
	$('html > head').append('<style>.dropmenu li:hover ul { display: none; }</style>');

	// add the dropdown arrows to each toplevel menu that actually has sub items.	
	jQuery('#menu_nav li').each(function() {
		if (jQuery(this).children('ul').length > 0) {
			var id;
			id = jQuery(this).attr('id');
			if (id == '' || id == undefined) {
				var classNames = jQuery(this).attr('class').split(' ');
				id = classNames[1];
			}
			jQuery(this).children('a').after('<span id="' + id + '" class="m_downarrow">&nbsp;</span>');
		}
	});
	// arrow clicked -> open the menu
	jQuery('.m_downarrow').click(function() {
		var id = jQuery(this).attr('id');
		jQuery('#' + id).children('ul:first').fadeIn();
		menu_active = true;
	});
	jQuery('#menu_nav ul').hover(function() {
	}, function() {
		jQuery(this).hide();
	});
	jQuery('.dropmenu').hover(function() {
	}, function() {
		jQuery('#menu_nav ul').fadeOut();
		menu_active = false;
	});
	jQuery('#menu_nav li').hover(function() {
		if (menu_active) {
			jQuery(this).children('ul').show();
		}
	}, function() {
		if (menu_active) {
			jQuery(this).children('ul').hide();
		}
	});
	$('a.vbox').colorbox({
		iframe : true,
		innerWidth : 855,
		innerHeight : 510,
		opacity : 0.5,
		transition : 'fade'
	});
	$('a.bbc_img, img.bbc_img').colorbox({
		maxWidth : '100%',
		maxHeight : '100%',
		opacity : 0.6,
		scalePhotos : false,
		transition : 'fade'
	});
	$('.whoposted').click(function() {
		var t = $(this).attr('data-topic');
		if(t) {
			sendRequest(smf_scripturl, 'action=xmlhttp;sa=whoposted;t=' + t, $(this));
		}
		return(false);
	});
	$('.share_button').click(function() {
		share_popup($(this).attr('href'), 700, 400);
		return(false);
	});
	// initiate a ajax request to open a member card
	$('.mcard').click(function() {
		var uid = $(this).attr('data-id');
		if(uid > 0) {
			sendRequest(smf_scripturl, 'action=xmlhttp;sa=mcard;u=' + parseInt(uid), $(this));
		}
		return(false);
		});
	// close the member card
	$('#mcard_close').click(function() {
		$('#mcard').hide();
		$('#mcard_inner').html('');
		$('#wrap').css('opacity', '1.0');
		return(false);
	});
	$('.givelike').click(function() {
		var mid = parseInt($(this).attr('data-id'));
		if(mid > 0) {
			switch($(this).attr('data-fn')) {
				case 'give':
					sendRequest(smf_scripturl, 'action=xmlhttp;sa=givelike;m=' + mid, $(this));
				    break;
				case 'remove':
					sendRequest(smf_scripturl, 'action=xmlhttp;sa=givelike;remove=1;m=' + mid, $(this));
				    break;
				case 'repair':
					sendRequest(smf_scripturl, 'action=xmlhttp;sa=givelike;repair=1;m=' + mid, $(this));
					break;
				default:
					break;
			}
		}
		return(false);
	});
	// handle the topic preview functionality in MessageIndex
	// fade in the preview link when hovering the subject
	$('span.tpeek').hover(
		function() {
			$('#tpeekresult').remove();
			$(this).append('<a style="display:none;" href="#" onclick="firePreview(' + $(this).attr('data-id') +', $(this)); return(false);" class="tpeek">Preview topic</a>');
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
});

function submitTagForm(ele)
{
	sendRequest(smf_scripturl, 'action=xmlhttp;sa=tags;submittag=1;topic=' + $('#tagtopic').val() + ';tag=' + encodeURIComponent($('#newtags').val()), ele);
	$('#tagform').remove();
}
// submit ajax request for a topic preview
function firePreview(topic_id, ele)
{
	sendRequest(smf_scripturl, 'action=xmlhttp;sa=mpeek;t=' + topic_id, ele);
}

function timeOutError() 
{
	if(req) {
		req.abort();
		req = null;
	}
	setBusy(0);
	alert('Error: Connection has timed out.');
};

function setTimeOut(t) {
	_timer = window.setTimeout(function(){ timeOutError(); },t);
};
	
function clearTimeOut()
{
	if(_timer) {
		clearTimeout(_timer);
		_timer = null;
	}
};

function sendRequest(uri, request, anchor_element)
{
	if(_is_locked)
		return;
		
	var xmlrequest = new XMLHttpRequest();
	if(xmlrequest) {
		if(typeof(sSessionVar) == 'undefined')
			sSessionVar = 'sesc';
		
		request = request + ';' + sSessionVar + '='	+ sSessionId + ';xml';
		var sUrl = uri + '?' + request;
		setTimeOut(10000);
		req = xmlrequest;
		xmlrequest.onreadystatechange = function() { response(anchor_element) };
		xmlrequest.open('GET', sUrl, true);
		setBusy(1);
		xmlrequest.send(null);
	}
};

function openResult(html)
{
	$('#wrap').css('opacity', '0.6');
	$('#mcard_inner').html(req.responseText);
	var el = $('#mcard');
   	el.css("position","fixed");
	el.css("top", (($(window).height() - el.outerHeight()) / 2) -200 + "px");
	el.css("left", (($(window).width() - el.outerWidth()) / 2) + $(window).scrollLeft() + "px");
	el.fadeIn();
	el.css('z-index', '100');
}
/*
 * generic handler for XMLHttp response. Determines its origin by observing ele
 */
function response(ele)
{
	var com, srch, err;
	try {
		clearTimeOut();
		if(req.readyState == 4) {
			if(req.status == 200) {
				setBusy(0);
				if(ele.attr('class') == 'whoposted') {
					openResult(req.responseText);
					return;
				}
				if(ele.attr('class') == 'tpeek') {
					openResult(req.responseText);
					return;
				}
				if(ele.attr('class') == 'givelike') {
					var id = '#likers_msg_' + ele.attr('data-id');
					$(id).html(req.responseText);
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
					$('#addtag').before(req.responseText);
					return;
				}
				if(ele.attr('id') == 'tags') {
					ele.html(req.responseText);
					return;
				}
				openResult(req.responseText);
    			$('div#mcard_inner abbr.timeago').timeago();
			} else if(req.status == 500) {
				setBusy(0);
				clearTimeOut();
				err = req.responseText || req.statusText;
			}
		}
	} catch(e) {
		clearTimeOut();
		setBusy(0);
	}
};

function openAdvSearch(e)
{
	$('#adv_search').fadeIn();
	//stopEventPropagation(e);
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
	$('#adv_search').hide();
});