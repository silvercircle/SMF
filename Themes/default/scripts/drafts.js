// Kick everything off
function draftAutosave(oOptions)
{
	this.opt = oOptions;
	addLoadEvent(this.opt.sSelf + '.init();');
}

// Create a timer
draftAutosave.prototype.init = function init()
{
	if (this.opt.iFreq > 0)
		setInterval(this.opt.sSelf + '.draftSend();', this.opt.iFreq);
}

// Check if there's actually content, if so, save the draft ^_^
draftAutosave.prototype.draftSend = function ()
{
	if (isEmptyText(document.forms.postmodify['message']))
		return false;

	var x = [
		'topic=' + parseInt(document.forms.postmodify.elements['topic'].value),
		'draft_id=' + parseInt(document.forms.postmodify.elements['draft_id'].value),
		'subject=' + escape(document.forms.postmodify['subject'].value.replace(/&#/g, "&#38;#").php_to8bit()).replace(/\+/g, "%2B"),
		'message=' + escape(document.forms.postmodify['message'].value.replace(/&#/g, "&#38;#").php_to8bit()).replace(/\+/g, "%2B"),
		'icon=' + escape(document.forms.postmodify['icon'].value.replace(/&#/g, "&#38;#").php_to8bit()).replace(/\+/g, "%2B"),
		this.opt.sSessionVar + '=' + this.opt.sSessionId
	];

	// Are we on the full page? WYSIWYG even?
	if (this.opt.sType == 'full')
		x[x.length] = 'message_mode=' + parseInt(document.forms.postmodify.elements['message_mode'].value);

	// What about lock/sticky status? These may or may not be listed
	if (document.getElementById('check_lock'))
		x[x.length] = 'lock=' + parseInt(document.getElementById('check_lock').value);
	if (document.getElementById('check_sticky'))
		x[x.length] = 'sticky=' + parseInt(document.getElementById('check_sticky').value);

	var sUrl = smf_prepareScriptUrl(this.opt.sScriptUrl) + "action=post2;draft;xml";
	if (this.opt.iBoard)
		sUrl += ';board=' + this.opt.iBoard;

	// Send in the XMLhttp request and let's hope for the best.
	setBusy(true);
	sendXMLDocument.call(this, sUrl, x.join("&"), this.draftReply);

	return false;
}

// OK, so we saved our draft, let's update the interface
draftAutosave.prototype.draftReply = function (XMLDoc)
{
	setBusy(false); // Tell the user we're done.

	// Update the last saved counter.
	var oDiv = document.getElementById(this.opt.sLastNote);
	var oNote = XMLDoc.getElementsByTagName('lastsave')[0];
	document.postmodify.draft_id.value = oNote.getAttribute('id');
	setInnerHTML(oDiv, oNote.childNodes[0].nodeValue);
}