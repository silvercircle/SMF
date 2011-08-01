// The purpose of this code is to fix the height of overflow: auto blocks, because some browsers can't figure it out for themselves.
function smf_codeBoxFix()
{
	var codeFix = document.getElementsByTagName('code');
	for (var i = codeFix.length - 1; i >= 0; i--)
	{
		if (is_webkit && codeFix[i].offsetHeight < 20)
			codeFix[i].style.height = (codeFix[i].offsetHeight + 20) + 'px';

		else if (is_ff && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0))
			codeFix[i].style.overflow = 'scroll';

		else if ('currentStyle' in codeFix[i] && codeFix[i].currentStyle.overflow == 'auto' && (codeFix[i].currentStyle.height == '' || codeFix[i].currentStyle.height == 'auto') && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0) && (codeFix[i].offsetHeight != 0))
			codeFix[i].style.height = (codeFix[i].offsetHeight + 24) + 'px';
	}
}

// Add a fix for code stuff?
if ((is_ie && !is_ie4) || is_webkit || is_ff)
	addLoadEvent(smf_codeBoxFix);

// Toggles the element height and width styles of an image.
function smc_toggleImageDimensions()
{
	var oImages = document.getElementsByTagName('IMG');
	for (oImage in oImages)
	{
		// Not a resized image? Skip it.
		if (oImages[oImage].className == undefined || oImages[oImage].className.indexOf('bbc_img resized') == -1)
			continue;

		oImages[oImage].style.cursor = 'pointer';
		oImages[oImage].onclick = function() {
			this.style.width = this.style.height = this.style.width == 'auto' ? null : 'auto';
		};
	}
}

// Add a load event for the function above.
addLoadEvent(smc_toggleImageDimensions);

// Adds a button to a certain button strip.
function smf_addButton(sButtonStripId, bUseImage, oOptions)
{
	var oButtonStrip = document.getElementById(sButtonStripId);
	var aItems = oButtonStrip.getElementsByTagName('span');

	// Remove the 'last' class from the last item.
	if (aItems.length > 0)
	{
		var oLastSpan = aItems[aItems.length - 1];
		oLastSpan.className = oLastSpan.className.replace(/\s*last/, 'position_holder');
	}

	// Add the button.
	var oButtonStripList = oButtonStrip.getElementsByTagName('ul')[0];
	var oNewButton = document.createElement('li');
	setInnerHTML(oNewButton, '<a href="' + oOptions.sUrl + '" ' + ('sCustom' in oOptions ? oOptions.sCustom : '') + '><span class="last"' + ('sId' in oOptions ? ' id="' + oOptions.sId + '"': '') + '>' + oOptions.sText + '</span></a>');

	oButtonStripList.appendChild(oNewButton);
}

// Adds hover events to list items. Used for a versions of IE that don't support this by default.
var smf_addListItemHoverEvents = function()
{
	var cssRule, newSelector;

	// Add a rule for the list item hover event to every stylesheet.
	for (var iStyleSheet = 0; iStyleSheet < document.styleSheets.length; iStyleSheet ++)
		for (var iRule = 0; iRule < document.styleSheets[iStyleSheet].rules.length; iRule ++)
		{
			oCssRule = document.styleSheets[iStyleSheet].rules[iRule];
			if (oCssRule.selectorText.indexOf('LI:hover') != -1)
			{
				sNewSelector = oCssRule.selectorText.replace(/LI:hover/gi, 'LI.iehover');
				document.styleSheets[iStyleSheet].addRule(sNewSelector, oCssRule.style.cssText);
			}
		}

	// Now add handling for these hover events.
	var oListItems = document.getElementsByTagName('LI');
	for (oListItem in oListItems)
	{
		oListItems[oListItem].onmouseover = function() {
			this.className += ' iehover';
		};

		oListItems[oListItem].onmouseout = function() {
			this.className = this.className.replace(new RegExp(' iehover\\b'), '');
		};
	}
}

// Add hover events to list items if the browser requires it.
if (is_ie7down && 'attachEvent' in window)
	window.attachEvent('onload', smf_addListItemHoverEvents);

/*
 * timeago: a jQuery plugin, version: 0.9.3 (2011-01-21)
 * @requires jQuery v1.2.3 or later
 *
 * Timeago is a jQuery plugin that makes it easy to support automatically
 * updating fuzzy timestamps (e.g. "4 minutes ago" or "about 1 day ago").
 *
 * For usage and examples, visit:
 * http://timeago.yarp.com/
 *
 * Licensed under the MIT:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright (c) 2008-2011, Ryan McGeary (ryanonjavascript -[at]- mcgeary [*dot*] org)
 */
(function($) {
  $.timeago = function(timestamp) {
    if (timestamp instanceof Date) {
      return inWords(timestamp);
    } else if (typeof timestamp === "string") {
      return inWords($.timeago.parse(timestamp));
    } else {
      return inWords($.timeago.datetime(timestamp));
    }
  };
  var $t = $.timeago;

  $.extend($.timeago, {
    settings: {
      refreshMillis: 60000,
      allowFuture: false,
    },
    inWords: function(distanceMillis) {
      var $l = this.settings.strings;
      var prefix = $l.prefixAgo;
      var suffix = $l.suffixAgo;
      if (this.settings.allowFuture) {
        if (distanceMillis < 0) {
          prefix = $l.prefixFromNow;
          suffix = $l.suffixFromNow;
        }
        distanceMillis = Math.abs(distanceMillis);
      }

      var seconds = distanceMillis / 1000;
      var minutes = seconds / 60;
      var hours = minutes / 60;
      var days = hours / 24;
      var years = days / 365;

      function substitute(stringOrFunction, number) {
        var string = $.isFunction(stringOrFunction) ? stringOrFunction(number, distanceMillis) : stringOrFunction;
        var value = ($l.numbers && $l.numbers[number]) || number;
        return string.replace(/%d/i, value);
      }

      var words = seconds < 45 && substitute($l.seconds, Math.round(seconds)) ||
        seconds < 90 && substitute($l.minute, 1) ||
        minutes < 45 && substitute($l.minutes, Math.round(minutes)) ||
        minutes < 90 && substitute($l.hour, 1) ||
        hours < 24 && substitute($l.hours, Math.round(hours)) ||
        hours < 48 && substitute($l.day, 1) ||
        days < 30 && substitute($l.days, Math.floor(days)) ||
        days < 60 && substitute($l.month, 1) ||
        days < 365 && substitute($l.months, Math.floor(days / 30)) ||
        years < 2 && substitute($l.year, 1) ||
        substitute($l.years, Math.floor(years));

      return $.trim([prefix, words, suffix].join(" "));
    },
    parse: function(iso8601) {
      var s = $.trim(iso8601);
      s = s.replace(/\.\d\d\d+/,""); // remove milliseconds
      s = s.replace(/-/,"/").replace(/-/,"/");
      s = s.replace(/T/," ").replace(/Z/," UTC");
      s = s.replace(/([\+\-]\d\d)\:?(\d\d)/," $1$2"); // -04:00 -> -0400
      return new Date(s);
    },
    datetime: function(elem) {
      // jQuery's `is()` doesn't play well with HTML5 in IE
      var isTime = $(elem).get(0).tagName.toLowerCase() === "time"; // $(elem).is("time");
      var iso8601 = isTime ? $(elem).attr("datetime") : $(elem).attr("title");
      return $t.parse(iso8601);
    }
  });

  $.fn.timeago = function() {
    var self = this;
    self.each(refresh);

    var $s = $t.settings;
    if ($s.refreshMillis > 0) {
      setInterval(function() { self.each(refresh); }, $s.refreshMillis);
    }
    return self;
  };

  function refresh() {
    var data = prepareData(this);
    if (!isNaN(data.datetime)) {
      $(this).text(inWords(data.datetime));
    }
    return this;
  }

  function prepareData(element) {
    element = $(element);
    if (!element.data("timeago")) {
      element.data("timeago", { datetime: $t.datetime(element) });
      var text = $.trim(element.text());
      if (text.length > 0) {
        element.attr("title", text);
      }
    }
    return element.data("timeago");
  }

  function inWords(date) {
    var dist = distance(date);
    var oneday = 86400 * 1000;
    var days = dist / oneday;
    if(days > 2 && days < 7)
    	return($t.settings.strings.weekdays[date.getUTCDay()] + ', ' + date.getUTCHours() + ':'+ date.getUTCMinutes());
	if(days > 1 && days < 2)
    	return($t.settings.strings.yesterday + ', ' + date.getUTCHours() + ':'+ date.getUTCMinutes());
    	
    return($t.inWords(dist));
  }

  function distance(date) {
  	  var d = new Date();
      return (d.getTime() - (date.getTime() + (d.getTimezoneOffset() * 60 * 1000) ));
  }
}(jQuery));
	