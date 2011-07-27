// smfFadeIndex: the current item in smfFadeContent.
var smfFadeIndex = -1;
// smfFadePercent: percent of fade. (-64 to 510.)
var smfFadePercent = 510
// smfFadeSwitch: direction. (in or out)
var smfFadeSwitch = false;
// smfFadeScroller: the actual div to mess with.
var smfFadeScroller = document.getElementById('smfFadeScroller');
// The ranges to fade from for R, G, and B. (how far apart they are.)
var smfFadeRange = {
	'r': smfFadeFrom.r - smfFadeTo.r,
	'g': smfFadeFrom.g - smfFadeTo.g,
	'b': smfFadeFrom.b - smfFadeTo.b
};

// Divide by 10 because we are doing it 10 times per 1ms.
smfFadeDelay /= 10;

// Start the fader!
window.setTimeout('smfFader()', 10);

// Main	fading function... called 10 times an ms.
function smfFader()
{
	if (smfFadeContent.length <= 1)
		return;

	// Starting out?  Set up the first item.
	if (smfFadeIndex == -1)
	{
		setInnerHTML(smfFadeScroller, smfFadeBefore + smfFadeContent[0] + smfFadeAfter);
		smfFadeIndex = 1;
	}

	// Are we already done fading in?  If so, fade out.
	if (smfFadePercent >= 510)
		smfFadeSwitch = !smfFadeSwitch;
	// All the way faded out?
	else if (smfFadePercent <= -64)
	{
		smfFadeSwitch = !smfFadeSwitch;

		// Go to the next item, or first if we're out of items.
		setInnerHTML(smfFadeScroller, smfFadeBefore + smfFadeContent[smfFadeIndex++] + smfFadeAfter);
		if (smfFadeIndex >= smfFadeContent.length)
			smfFadeIndex = 0;
	}

	// Increment or decrement the fade percentage.
	if (smfFadeSwitch)
		smfFadePercent -= 255 / smfFadeDelay;
	else
		smfFadePercent += 255 / smfFadeDelay;

	// If it's not outside 0 and 256... (otherwise it's just delay time.)
	if (smfFadePercent < 256 && smfFadePercent > 0)
	{
		// Easier... also faster...
		var tempPercent = smfFadePercent / 255;

		// Get the new R, G, and B. (it should be bottom + (range of color * percent)...)
		var r = Math.ceil(smfFadeTo.r + smfFadeRange.r * tempPercent);
		var g = Math.ceil(smfFadeTo.g + smfFadeRange.g * tempPercent);
		var b = Math.ceil(smfFadeTo.b + smfFadeRange.b * tempPercent);

		// Set the color in the style, thereby fading it.
		smfFadeScroller.style.color = 'rgb(' + r + ', ' + g + ', ' + b + ')';
	}

	// Keep going...
	window.setTimeout('smfFader()', 10);
}