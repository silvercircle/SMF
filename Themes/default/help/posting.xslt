<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="post-screen-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
				<form action="posting.{//language}.html" method="post" style="margin: 0;">
					<table summary="SMF layout" width="100%" align="center" cellpadding="0" cellspacing="3">
						<tr>
							<td valign="bottom" colspan="2">
								<span class="nav">
									<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
									<b><a href="index.{//language}.html#board" class="nav"><xsl:value-of select="forum-name" /></a></b>
									<br />
									<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
									<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
									<b><a href="index.{//language}.html#board" class="nav"><xsl:value-of select="category-name" /></a></b>
									<br />
									<img src="../images/icons/linktree_main.gif" alt="| " border="0" />
									<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
									<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
									<b><a href="index.{//language}.html#message" class="nav"><xsl:value-of select="board-name" /></a></b>
									<br />
									<img src="../images/icons/linktree_main.gif" alt="| " border="0" />
									<img src="../images/icons/linktree_main.gif" alt="| " border="0" />
									<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
									<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
									<b><i><xsl:value-of select="start-new-topic" /></i></b>
								</span>
							</td>
						</tr>
					</table>
					<table summary="SMF layout" border="0" width="100%" align="center" cellspacing="1" cellpadding="3" class="bordercolor">
						<tr class="titlebg">
							<td><xsl:value-of select="start-new-topic" /></td>
						</tr>
						<tr>
							<td class="windowbg">
								<table summary="SMF layout" border="0" cellpadding="3" width="100%">
									<tr class="windowbg">
										<td colspan="2" align="center"><a href="#standard"><xsl:value-of select="standard-options" />&#160;<xsl:value-of select="omitted-for-clarity" /></a></td>
									</tr>
									<tr>
										<td align="right">
											<b><xsl:value-of select="subject" />:</b>
										</td>
										<td>
											<input type="text" name="subject" size="80" maxlength="80" tabindex="1" />
										</td>
									</tr>
									<tr>
										<td valign="top" align="right"></td>
										<td>
											<textarea class="editor" name="message" rows="12" cols="60" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);" tabindex="2"></textarea>
										</td>
									</tr>
									<tr class="windowbg">
										<td colspan="2" align="center"><a href="#additional"><xsl:value-of select="additional-options" />&#160;<xsl:value-of select="omitted-for-clarity" /></a></td>
									</tr>
									<tr>
										<td align="center" colspan="2">
											<span class="smalltext">
												<br />
												<xsl:value-of select="shortcuts" />
											</span>
											<br />
											<input type="button" accesskey="s" tabindex="3">
												<xsl:attribute name="value">
													<xsl:value-of select="post" />
												</xsl:attribute>
											</input>
											<input type="button" accesskey="p" tabindex="4">
												<xsl:attribute name="value">
													<xsl:value-of select="preview" />
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</form>
				<br />
			</div>
		</div>
		<br />
	</xsl:template>

	<xsl:template match="standard-options-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
				<br />
				<script language="JavaScript1.2" type="text/javascript"><![CDATA[
					function showimage()
					{
						document.images.icons.src="../images/post/" + document.postmodify.icon.options[document.postmodify.icon.selectedIndex].value + ".gif";
					}

					function updateAttachmentCache()
					{
						if (typeof(document.postmodify.attachmentPreview) == "undefined")
							return;

						document.postmodify.attachmentPreview.value = "";

						if (typeof(document.postmodify["attachment[]"].length) != "undefined")
						{
							var tempArray = [];

							for (var i = 0; i < document.postmodify["attachment[]"].length; i++)
								tempArray[i] = document.postmodify["attachment[]"][i].value;

							document.postmodify.attachmentPreview.value = tempArray.join(", ");
						}
						else
							document.postmodify.attachmentPreview.value = document.postmodify["attachment[]"].value;
					}

					var currentSwap = false;
					function swapOptions()
					{
						document.getElementById("postMoreExpand").src = smf_images_url + "/" + (currentSwap ? "collapse.gif" : "expand.gif");
						document.getElementById("postMoreExpand").alt = currentSwap ? "-" : "+";

						document.getElementById("postMoreOptions").style.display = currentSwap ? "" : "none";

						if (document.getElementById("postAttachment"))
							document.getElementById("postAttachment").style.display = currentSwap ? "" : "none";
						if (document.getElementById("postAttachment2"))
							document.getElementById("postAttachment2").style.display = currentSwap ? "" : "none";

						currentSwap = !currentSwap;
					}
				]]></script>
				<form action="posting.{//language}.html"  method="post" name="postmodify" style="margin: 0;">
					<table summary="SMF layout" border="0" width="100%" align="center" cellspacing="1" cellpadding="3" class="bordercolor">
						<tr>
							<td class="windowbg">
								<table summary="SMF layout" border="0" cellpadding="3" width="100%">
									<tr>
										<td align="right">
											<b><xsl:value-of select="message-icon" />:</b>
										</td>
										<td>
											<select name="icon" id="icon" onchange="showimage()">
												<option value="xx" selected="selected"><xsl:value-of select="message-selected" /></option>
												<option value="thumbup"><xsl:value-of select="message-thumbup" /></option>
												<option value="thumbdown"><xsl:value-of select="message-thumbdown" /></option>
												<option value="exclamation"><xsl:value-of select="message-exclamation" /></option>
												<option value="question"><xsl:value-of select="message-question" /></option>
												<option value="lamp"><xsl:value-of select="message-lamp" /></option>
												<option value="smiley"><xsl:value-of select="message-smiley" /></option>
												<option value="angry"><xsl:value-of select="message-angry" /></option>
												<option value="cheesy"><xsl:value-of select="message-cheesy" /></option>
												<option value="grin"><xsl:value-of select="message-grin" /></option>
												<option value="sad"><xsl:value-of select="message-sad" /></option>
												<option value="wink"><xsl:value-of select="message-wink" /></option>
											</select>
											<img src="../images/post/xx.gif" name="icons" border="0" hspace="15" alt="" />
										</td>
									</tr>
									<tr>
										<td align="right"></td>
										<td valign="middle">
											<script language="JavaScript" type="text/javascript"><![CDATA[
												function bbc_highlight(something, mode)
												{
													something.style.backgroundImage = "url(" + smf_images_url + (mode ? "/bbc/bbc_hoverbg.gif)" : "/bbc/bbc_bg.gif)");
												}
											]]></script>

											<xsl:for-each select="bbc[@row = '1']/*">
												<xsl:if test="name() = 'divider'"><img src="../images/bbc/divider.gif" alt="|" style="margin: 0 3px 0 3px;" /></xsl:if>
												<xsl:if test="name() != 'divider'"><a href="javascript:void(0);" onclick="surroundText({@example}, document.postmodify.message);"><img onmouseover="bbc_highlight(this, true);" onmouseout="bbc_highlight(this, false);" src="../images/bbc/{name()}.gif" align="bottom" width="23" height="22" border="0" style="background-image: url(../images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;" alt="{.}" /></a></xsl:if>
											</xsl:for-each>

											<select onchange="surroundText('[color='+this.options[this.selectedIndex].value+']', '[/color]', document.postmodify.message); this.selectedIndex = 0;" style="margin-bottom: 1ex; margin-left: 2ex;">
												<option value="" selected="selected"><xsl:value-of select="color/color-selected" /></option>
												<option value="Black"><xsl:value-of select="color/color-black" /></option>
												<option value="Red"><xsl:value-of select="color/color-red" /></option>
												<option value="Yellow"><xsl:value-of select="color/color-yellow" /></option>
												<option value="Pink"><xsl:value-of select="color/color-pink" /></option>
												<option value="Green"><xsl:value-of select="color/color-green" /></option>
												<option value="Orange"><xsl:value-of select="color/color-orange" /></option>
												<option value="Purple"><xsl:value-of select="color/color-purple" /></option>
												<option value="Blue"><xsl:value-of select="color/color-blue" /></option>
												<option value="Beige"><xsl:value-of select="color/color-beige" /></option>
												<option value="Brown"><xsl:value-of select="color/color-brown" /></option>
												<option value="Teal"><xsl:value-of select="color/color-teal" /></option>
												<option value="Navy"><xsl:value-of select="color/color-navy" /></option>
												<option value="Maroon"><xsl:value-of select="color/color-maroon" /></option>
												<option value="LimeGreen"><xsl:value-of select="color/color-limegreen" /></option>
											</select>
											<br />

											<xsl:for-each select="bbc[@row = '2']/*">
												<xsl:if test="name() = 'divider'"><img src="../images/bbc/divider.gif" alt="|" style="margin: 0 3px 0 3px;" /></xsl:if>
												<xsl:if test="name() != 'divider'"><a href="javascript:void(0);" onclick="surroundText({@example}, document.postmodify.message);"><img onmouseover="bbc_highlight(this, true);" onmouseout="bbc_highlight(this, false);" src="../images/bbc/{name()}.gif" align="bottom" width="23" height="22" border="0" style="background-image: url(../images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;" alt="{.}" /></a></xsl:if>
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td align="right"></td>
										<td valign="middle">
											<!-- Don't move the closing anchor tags to new lines. -->
											<xsl:for-each select="smileys/*">
												<a href="javascript:void(0);" onclick="replaceText(' {@code}', document.postmodify.message);">
												<img src="{$default_smiley_url}/default/{name()}.gif" align="bottom" alt="{.}" border="0" /></a>
											</xsl:for-each>
											<br />
										</td>
									</tr>
									<tr>
										<td valign="top" align="right"></td>
										<td>
											<textarea class="editor" name="message" rows="12" cols="60" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);" tabindex="2"></textarea>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</form>
				<br />
			</div>
		</div>
		<br />
	</xsl:template>

	<xsl:template match="additional-options-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
			<br />
			<script language="JavaScript1.2" type="text/javascript"><![CDATA[
				var currentSwap = false;
				function swapOptions()
				{
					document.getElementById("postMoreExpand").src = smf_images_url + "/" + (currentSwap ? "collapse.gif" : "expand.gif");
					document.getElementById("postMoreExpand").alt = currentSwap ? "-" : "+";

					document.getElementById("postMoreOptions").style.display = currentSwap ? "" : "none";

					if (document.getElementById("postAttachment"))
						document.getElementById("postAttachment").style.display = currentSwap ? "" : "none";
					if (document.getElementById("postAttachment2"))
						document.getElementById("postAttachment2").style.display = currentSwap ? "" : "none";

					currentSwap = !currentSwap;
				}
			]]></script>
			<form action="posting.{//language}.html" method="post" style="margin: 0;">
				<table summary="SMF layout" border="0" width="100%" align="center" cellspacing="1" cellpadding="3" class="bordercolor">
					<tr>
						<td class="windowbg">
							<table summary="SMF layout" border="0" cellpadding="3" width="100%">
								<tr>
									<td colspan="2" style="padding-left: 5ex;">
										<a href="javascript:swapOptions();"><img src="../images/expand.gif" alt="+" border="0" id="postMoreExpand" /></a> <a href="javascript:swapOptions();" class="board"><b><xsl:value-of select="//post-screen-demo/additional-options" />...</b></a>
									</td>
								</tr>
								<tr>
									<td></td>
									<td>
										<div id="postMoreOptions">
											<table summary="SMF layout" width="80%" cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td class="smalltext">
														<input type="checkbox" class="check" />&#160;<xsl:value-of select="notify" />
													</td>
												</tr>
												<tr>
													<td class="smalltext">
														<input type="checkbox" class="check" />&#160;<xsl:value-of select="return" />
													</td>
												</tr>
												<tr>
													<td class="smalltext">
														<input type="checkbox" class="check" />&#160;<xsl:value-of select="no-smileys" />
													</td>
												</tr>
											</table>
										</div>
									</td>
								</tr>
								<tr id="postAttachment2">
									<td align="right" valign="top">
										<b><xsl:value-of select="attach" />:</b>
									</td>
									<td class="smalltext">
										<input type="file" size="48" name="attachment[]" onchange="updateAttachmentCache();" />
										<br />
										<input type="file" size="48" name="attachment[]" onchange="updateAttachmentCache();" />
										<br />
										<xsl:value-of select="allowed-types" />
										<br />
										<xsl:value-of select="max-size" />
										</td>
									</tr>
									<tr>
										<td align="center" colspan="2">
											<script language="JavaScript" type="text/javascript"><![CDATA[
												swapOptions();
											]]></script>
											<span class="smalltext">
												<br />
												<xsl:value-of select="//post-screen-demo/shortcuts" />
											</span>
											<br />
											<input type="button" accesskey="s" tabindex="3">
												<xsl:attribute name="value">
													<xsl:value-of select="//post-screen-demo/post" />
												</xsl:attribute>
											</input>
											<input type="button" accesskey="p" tabindex="4">
												<xsl:attribute name="value">
													<xsl:value-of select="//post-screen-demo/preview" />
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</form>
				<br />
			</div>
		</div>
		<br />
	</xsl:template>

	<xsl:template match="bbc-reference">
		<table id="reference1" summary="five columns to show the BBC tag names, buttons, the code they produce, how they render and any further comments" cellspacing="4" cellpadding="2">
			<xsl:apply-templates />
		</table>
		<br />
	</xsl:template>

	<xsl:template match="bbc-heading">
		<tr>
			<th><xsl:value-of select="header-name" /></th>
			<th><xsl:value-of select="header-button" /></th>
			<th><xsl:value-of select="header-code" /></th>
			<th><xsl:value-of select="header-output" /></th>
			<th><xsl:value-of select="header-comments" /></th>
		</tr>
	</xsl:template>

	<xsl:template match="bbc-help">
		<!-- bbc_highlight JavaScript located in standard-options-demo template. -->
		<tr>
			<td><xsl:value-of select="bbc-name" /></td>
			<xsl:choose>
				<xsl:when test="bbc-menu">
					<td>
						<select>
							<option value="" selected="selected"><xsl:value-of select="//standard-options-demo/color/color-selected" /></option>
							<option value="Black"><xsl:value-of select="//standard-options-demo/color/color-black" /></option>
							<option value="Red"><xsl:value-of select="//standard-options-demo/color/color-red" /></option>
							<option value="Yellow"><xsl:value-of select="//standard-options-demo/color/color-yellow" /></option>
							<option value="Pink"><xsl:value-of select="//standard-options-demo/color/color-pink" /></option>
							<option value="Green"><xsl:value-of select="//standard-options-demo/color/color-green" /></option>
							<option value="Orange"><xsl:value-of select="//standard-options-demo/color/color-orange" /></option>
							<option value="Purple"><xsl:value-of select="//standard-options-demo/color/color-purple" /></option>
							<option value="Blue"><xsl:value-of select="//standard-options-demo/color/color-blue" /></option>
							<option value="Beige"><xsl:value-of select="//standard-options-demo/color/color-beige" /></option>
							<option value="Brown"><xsl:value-of select="//standard-options-demo/color/color-brown" /></option>
							<option value="Teal"><xsl:value-of select="//standard-options-demo/color/color-teal" /></option>
							<option value="Navy"><xsl:value-of select="//standard-options-demo/color/color-navy" /></option>
							<option value="Maroon"><xsl:value-of select="//standard-options-demo/color/color-maroon" /></option>
							<option value="LimeGreen"><xsl:value-of select="//standard-options-demo/color/color-limegreen" /></option>
						</select>
					</td>
				</xsl:when>
				<xsl:when test="bbc-button='none'">
					<td>*</td>
				</xsl:when>
				<xsl:otherwise>
					<td>
						<img onmouseover="bbc_highlight(this, true);" onmouseout="bbc_highlight(this, false);" src="../images/bbc/{bbc-button}.gif" alt="{bbc-name}" style="background-image: url(../images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;" />
					</td>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="bbc-code[@disable = 'no']">
					<td>
						<xsl:value-of select="bbc-code" disable-output-escaping="no" />
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td>
						<xsl:value-of select="bbc-code" disable-output-escaping="yes" />
					</td>
				</xsl:otherwise>
			</xsl:choose>
			<td>
				<xsl:value-of select="html-open" disable-output-escaping="yes" />
				<xsl:value-of select="html-output" />
				<xsl:value-of select="html-close" disable-output-escaping="yes" />
			</td>
			<td><xsl:value-of select="bbc-comments" /></td>
		</tr>
	</xsl:template>

	<xsl:template match="bbc-help-top">
		<tr>
			<td rowspan="2"><xsl:value-of select="bbc-name" /></td>
			<td rowspan="2"><img onmouseover="bbc_highlight(this, true);" onmouseout="bbc_highlight(this, false);" src="../images/bbc/{bbc-button}.gif" alt="{bbc-name}" style="background-image: url(../images/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;" /></td>
			<td><xsl:value-of select="bbc-code" disable-output-escaping="yes" /></td>
			<td>
				<xsl:value-of select="html-open" disable-output-escaping="yes" />
				<xsl:value-of select="html-output" />
				<xsl:value-of select="html-close" disable-output-escaping="yes" />
			</td>
			<td rowspan="2"><xsl:value-of select="bbc-comments" /></td>
		</tr>
	</xsl:template>

	<xsl:template match="bbc-help-bottom">
		<tr>
			<td><xsl:value-of select="bbc-code" disable-output-escaping="yes" /></td>
			<td>
				<xsl:value-of select="html-open" disable-output-escaping="yes" />
				<xsl:value-of select="html-output" />
				<xsl:value-of select="html-close" disable-output-escaping="yes" />
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="smileys-reference">
		<table id="reference2" summary="three columns to show the standard SMF smiley names, images and codes" cellspacing="4" cellpadding="2">
			<xsl:apply-templates />
		</table>
		<br />
	</xsl:template>

	<xsl:template match="smiley-heading">
		<tr>
			<th><xsl:value-of select="header-name" /></th>
			<th><xsl:value-of select="header-image" /></th>
			<th><xsl:value-of select="header-code" /></th>
		</tr>
	</xsl:template>

	<xsl:template match="smiley-help">
		<tr>
			<td><xsl:value-of select="smiley-name" /></td>
			<td><img src="{$default_smiley_url}/default/{smiley-image}.gif" alt="" /></td>
			<td><xsl:value-of select="smiley-code" /></td>
		</tr>
	</xsl:template>

</xsl:stylesheet>