<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="board-index-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
				<table summary="SMF layout" width="100%" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="bottom">
							<span class="nav">
								<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
								<b><a href="#board" class="nav"><xsl:value-of select="forum-name" /></a></b>
							</span>
						</td>
					</tr>
				</table>
				<!-- Collapsing categories and marking as read are PHP actions in the actual forums. -->
				<script language="JavaScript1.2" type="text/javascript"><![CDATA[
					var collapseExpand = false;
					function collapseExpandCategory()
					{
						document.getElementById("collapseArrow").src = smf_images_url + "/" + (collapseExpand ? "collapse.gif" : "expand.gif");
						document.getElementById("collapseArrow").alt = collapseExpand ? "-" : "+";

						document.getElementById("collapseCategory").style.display = collapseExpand ? "" : "none";

						collapseExpand = !collapseExpand;
					}

					function markBoardRead()
					{
						document.getElementById("board-new-or-not").src = smf_images_url + "/" + "off.gif";
						document.getElementById("board-new-or-not").alt = "]]><xsl:value-of select="no-new-posts" /><![CDATA[";
					}
				]]></script>
				<div class="tborder">
					<table summary="SMF layout" border="0" width="100%" cellspacing="1" cellpadding="5">
						<tr>
							<td colspan="4" class="catbg" height="18">
								<a href="javascript:collapseExpandCategory();"><img src="../images/collapse.gif" alt="-" border="0" id="collapseArrow" /></a>&#160;
								<a href="javascript:collapseExpandCategory();" class="board">
									<xsl:value-of select="category-name" />
								</a>
							</td>
						</tr>
						<tr id="collapseCategory" class="windowbg2">
							<td class="windowbg" width="6%" align="center" valign="top">
								<img src="../images/on.gif" id="board-new-or-not">
									<xsl:attribute name="alt">
										<xsl:value-of select="new-posts" />
									</xsl:attribute>
								</img>
							</td>
							<td align="left" style="background-color: #F0F0F0;">
								<b>
									<a href="#message" class="board">
										<xsl:value-of select="board-name" />
									</a>
								</b>
								<br />
								<xsl:value-of select="board-description" />
							</td>
							<td class="windowbg" valign="middle" align="center" style="width: 12ex;">
								<span class="smalltext">
									<xsl:value-of select="post-info" />
								</span>
							</td>
							<td class="smalltext" valign="middle" width="22%" style="background-color: #F0F0F0;">
								<xsl:value-of select="last-post" />
							</td>
						</tr>
					</table>
				</div>
				<br />
				<div class="tborder" style="padding: 3px;">
					<table summary="SMF layout" border="0" width="100%" cellspacing="0" cellpadding="5">
						<tr class="titlebg">
							<td align="left" class="smalltext">
								<img src="../images/new_some.gif" alt="" align="middle" />&#160;
								<xsl:value-of select="new-posts" />
								<img src="../images/new_none.gif" alt="" align="middle" style="margin-left: 4ex;" />&#160;
								<xsl:value-of select="no-new-posts" />
							</td>
							<td align="right" class="smalltext">
								<!-- Don't move the closing anchor tag to a new line. -->
								<a href="javascript:markBoardRead();">
									<img src="../images/{//language}/markread.gif" border="0" alt="{alt-mark-as-read}" /></a>
							</td>
						</tr>
					</table>
				</div>
				<br />
			</div>
		</div>
		<br />
	</xsl:template>

	<xsl:template match="message-index-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
				<!-- Marking as read and sorting messages are PHP actions in the actual forums. -->
				<script language="JavaScript1.2" type="text/javascript"><![CDATA[
					var currentSort = false;
					function sortLastPost()
					{
						document.getElementById("sort-arrow").src = smf_images_url + "/" + (currentSort ? "sort_down.gif" : "sort_up.gif");
						document.getElementById("sort-arrow").alt = "";

						currentSort = !currentSort;
					}

					function markMessageRead()
					{
						document.getElementById("message-new-or-not").style.display = "none";
					}
				]]></script>
				<table summary="SMF layout" width="100%" cellpadding="3" cellspacing="0">
					<tr>
						<td>
							<span class="nav">
								<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
								<b><a href="#board" class="nav"><xsl:value-of select="//forum-name" /></a></b>
								<br />
								<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
								<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
								<b><a href="#board" class="nav"><xsl:value-of select="//category-name" /></a></b>
								<br />
								<img src="../images/icons/linktree_main.gif" alt="| " border="0" />
								<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
								<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
								<b><a href="#message" class="nav"><xsl:value-of select="//board-name" /></a></b>
							</span>
						</td>
					</tr>
				</table>
				<table summary="SMF layout" width="100%" cellpadding="3" cellspacing="0" border="0" class="tborder" style="margin-bottom: 1ex;">
					<tr>
						<td align="left" class="catbg" width="100%" height="30">
							<table summary="SMF layout" cellpadding="3" cellspacing="0" width="100%">
								<tr>
									<td><b><xsl:value-of select="pages" />: </b>[<b>1</b>]</td>
									<td align="right" nowrap="nowrap" style="font-size: smaller;">
										<!-- Don't move the closing anchor tags to new lines. -->
										<a href="javascript:markMessageRead();">
											<img src="../images/{//language}/markread.gif" border="0" alt="{alt-mark-as-read}" /></a>
										<a href="#message" onclick="return confirm('{notification-confirm}');">
											<img src="../images/{//language}/notify.gif" border="0" alt="{alt-notify}" /></a>
										<a href="posting.{//language}.html#newtopic">
											<img src="../images/{//language}/new_topic.gif" border="0" alt="{alt-new-topic}" /></a>
										<a href="posting.{//language}.html#newpoll">
											<img src="../images/{//language}/new_poll.gif" border="0" alt="{alt-new-poll}" /></a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<table summary="SMF layout" border="0" width="100%" cellspacing="1" cellpadding="4" class="bordercolor">
					<tr class="titlebg">
						<td width="9%" colspan="2">
						</td>
						<td>
							<a href="#message">
								<xsl:value-of select="subject" />
							</a>
						</td>
						<td width="14%">
							<a href="#message">
								<xsl:value-of select="started-by" />
							</a>
						</td>
						<td width="4%" align="center">
							<a href="#message">
								<xsl:value-of select="replies" />
							</a>
						</td>
						<td width="4%" align="center">
							<a href="#message">
								<xsl:value-of select="views" />
							</a>
						</td>
						<td width="22%">
							<!-- Don't move the closing anchor tag to a new line. -->
							<a href="javascript:sortLastPost();">
								<xsl:value-of select="last-post" />
								&#160;
								<img id="sort-arrow" src="../images/sort_down.gif" alt="" border="0" /></a>
						</td>
					</tr>
					<tr>
						<td class="windowbg2" valign="middle" align="center" width="5%">
							<img src="../images/topic/my_normal_poll.gif" alt="" />
						</td>
						<td class="windowbg2" valign="middle" align="center" width="4%">
							<img src="../images/post/xx.gif" alt="" align="middle" />
						</td>
						<td class="windowbg" valign="middle">
							<a href="#topic" class="board"><xsl:value-of select="topic-subject" /></a>
							<!-- Don't move the closing anchor tag to a new line. -->
							<a href="#topic">
								<img id="message-new-or-not" src="../images/{//language}/new.gif" border="0" alt="{alt-new}" /></a>
						</td>
						<td class="windowbg2" valign="middle" width="14%">
							<a href="profile.{//language}.html" class="board">
								<xsl:value-of select="topic-starter" />
							</a>
						</td>
						<td class="windowbg" valign="middle" width="4%" align="center">0</td>
						<td class="windowbg" valign="middle" width="4%" align="center">0</td>
						<td class="windowbg2" valign="middle" width="22%">
							<span class="smalltext">
								<xsl:value-of select="last-poster" />
							</span>
						</td>
					</tr>
				</table>
				<table summary="SMF layout" width="100%" cellpadding="3" cellspacing="0" border="0" class="tborder" style="margin-top: 1ex;">
					<tr>
						<td align="left" class="catbg" width="100%" height="30">
							<table summary="SMF layout" cellpadding="3" cellspacing="0" width="100%">
								<tr>
									<td><b><xsl:value-of select="pages" />:</b> [<b>1</b>]</td>
									<td align="right" nowrap="nowrap" style="font-size: smaller;">
										<!-- Don't move the closing anchor tags to new lines. -->
										<a href="javascript:markMessageRead();">
											<img src="../images/{//language}/markread.gif" border="0" alt="{alt-mark-as-read}" /></a>
										<a href="#message" onclick="return confirm('{notification-confirm}');">
											<img src="../images/{//language}/notify.gif" border="0" alt="{alt-notify}" /></a>
										<a href="posting.{//language}.html#newtopic">
											<img src="../images/{//language}/new_topic.gif" border="0" alt="{alt-new-topic}" /></a>
										<a href="posting.{//language}.html#newpoll">
											<img src="../images/{//language}/new_poll.gif" border="0" alt="{alt-new-poll}" /></a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<table summary="SMF layout" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="smalltext" align="left" style="padding-top: 1ex;">
							<img src="../images/topic/my_normal_post.gif" alt="" align="middle" />&#160;
							<xsl:value-of select="my-normal-post" />
							<br />
							<img src="../images/topic/normal_post.gif" alt="" align="middle" />&#160;
							<xsl:value-of select="normal-post" />
							<br />
							<img src="../images/topic/hot_post.gif" alt="" align="middle" />&#160;
							<xsl:value-of select="hot-post" />
							<br />
							<img src="../images/topic/veryhot_post.gif" alt="" align="middle" />&#160;
							<xsl:value-of select="very-hot-post" />
						</td>
						<td class="smalltext" align="left" valign="top" style="padding-top: 1ex;">
							<img src="../images/topic/normal_post_locked.gif" alt="" align="middle" />&#160;
							<xsl:value-of select="normal-post-locked" />
							<br />
							<img src="../images/topic/normal_post_sticky.gif" alt="" align="middle" />&#160;
							<xsl:value-of select="normal-post-sticky" />
							<br />
							<img src="../images/topic/normal_poll.gif" alt="" align="middle" />&#160;
							<xsl:value-of select="normal-poll" />
						</td>
						<td class="smalltext" align="right" valign="middle">
							<form action="index.{//language}.html" method="get" name="jumptoForm">
								<label for="jumpto"><xsl:value-of select="jump-to" /></label>:
								<select name="jumpto" id="jumpto" onchange="if (this.options[this.selectedIndex].value) window.location.href='index.{//language}.html' + this.options[this.selectedIndex].value;">
									<option value=""><xsl:value-of select="destination" />:</option>
									<option value="">-----------------------------</option>
									<option value="#board"><xsl:value-of select="//category-name" /></option>
									<option value="">-----------------------------</option>
									<option value="#message"> => <xsl:value-of select="//board-name" /></option>
									<option value="#message"> => <xsl:value-of select="board-name-2" /></option>
								</select>&#160;
								<input type="button" onclick="if (document.jumptoForm.jumpto.options[document.jumptoForm.jumpto.selectedIndex].value) window.location.href = 'index.{//language}.html' + document.jumptoForm.jumpto.options[document.jumptoForm.jumpto.selectedIndex].value;">
									<xsl:attribute name="value">
										<xsl:value-of select="go" />
									</xsl:attribute>
								</input>
							</form>
						</td>
					</tr>
				</table>
			<br />
			</div>
		</div>
		<br />
	</xsl:template>

	<xsl:template match="topic-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
				<table summary="SMF layout" width="100%" cellpadding="3" cellspacing="0">
					<tr>
						<td valign="bottom">
							<span class="nav">
								<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
								<b><a href="#board" class="nav"><xsl:value-of select="//forum-name" /></a></b><br />
								<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
								<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
								<b><a href="#board" class="nav"><xsl:value-of select="//category-name" /></a></b><br />
								<img src="../images/icons/linktree_main.gif" alt="| " border="0" />
								<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
								<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
								<b><a href="#message" class="nav"><xsl:value-of select="//board-name" /></a></b><br />
								<img src="../images/icons/linktree_main.gif" alt="| " border="0" />
								<img src="../images/icons/linktree_main.gif" alt="| " border="0" />
								<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
								<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
								<b><a href="#topic" class="nav"><xsl:value-of select="//topic-subject" /></a></b>
							</span>
						</td>
					</tr>
				</table>
				<table summary="SMF layout" width="100%" cellpadding="3" cellspacing="0" border="0" class="tborder" style="margin-bottom: 1ex;">
					<tr>
						<td align="left" class="catbg" width="100%" height="35">
							<table summary="SMF layout" cellpadding="3" cellspacing="0" width="100%">
								<tr>
									<td>
										<b><xsl:value-of select="//pages" />:</b> [<b>1</b>]
									</td>
									<td align="right" style="font-size: smaller;">
										<!-- Don't move the closing anchor tags to new lines. -->
										<a href="posting.{//language}.html#reply">
											<img src="../images/{//language}/reply.gif" border="0" alt="{alt-reply}" /></a>
										<a href="#topic" onclick="return confirm('{notification-confirm}');">
											<img src="../images/{//language}/notify.gif" border="0" alt="{//alt-notify}" /></a>
										<a href="#topic">
											<img src="../images/{//language}/markunread.gif" border="0" alt="{alt-mark-unread}" /></a>
										<a href="#topic">
											<img src="../images/{//language}/sendtopic.gif" border="0" alt="{alt-send-topic}" /></a>
										<a href="#topic">
											<img src="../images/{//language}/print.gif" border="0" alt="{alt-print}" /></a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<table summary="SMF layout" width="100%" cellpadding="3" cellspacing="0" border="0" class="tborder" style="border-bottom: 0;">
					<tr class="titlebg">
						<td valign="middle" align="left" width="15%" style="padding-left: 6px;">
							<img src="../images/topic/normal_post.gif" alt="" align="middle" />&#160;<xsl:value-of select="author" />
						</td>
						<td valign="middle" align="left" width="85%" style="padding-left: 6px;">
							<xsl:value-of select="topic" />: <xsl:value-of select="//topic-subject" /> &#160;(<xsl:value-of select="read" />)
						</td>
					</tr>
				</table>
				<table summary="SMF layout" cellpadding="0" cellspacing="0" border="0" width="100%" class="bordercolor">
					<tr>
						<td style="padding: 1px;">
							<table summary="SMF layout" cellpadding="3" cellspacing="0" border="0" width="100%">
								<tr>
									<td class="windowbg">
										<table summary="SMF layout" width="100%" cellpadding="5" cellspacing="0" style="table-layout: fixed;">
											<tr>
												<td valign="top" width="15%" rowspan="2" style="overflow: hidden;">
													<b>
														<a href="profile.{//language}.html" class="board">
															<xsl:attribute name="title">
																<xsl:value-of select="title-author" />
															</xsl:attribute>
															<xsl:value-of select="author" />
														</a>
													</b>
													<br />
													<span class="smalltext">
														<xsl:value-of select="member-group" />
														<br />
														<xsl:value-of select="post-rank-group" />
														<br />
														<img src="../images/star.gif" alt="*" border="0" />
														<br />
														<xsl:value-of select="post-count" />
														<br />
														<br />
														<br />
														<br />
														<!-- Don't move the closing anchor tags to new lines. -->
														<a href="profile.{//language}.html">
															<xsl:attribute name="title">
																<xsl:value-of select="alt-view-profile" />
															</xsl:attribute>
															<img src="../images/icons/profile_sm.gif" border="0" alt="{alt-view-profile}" /></a>
														<a href="mailto:author@some.address">
															<xsl:attribute name="title">
																<xsl:value-of select="alt-email" />
															</xsl:attribute>
															<img src="../images/email_sm.gif" border="0" alt="{alt-email}" /></a>
														<a href="pm.{//language}.html">
															<xsl:attribute name="title">
																<xsl:value-of select="alt-pm" />
															</xsl:attribute>
															<img src="../images/im_off.gif" border="0" alt="{alt-pm}" /></a>
													</span>
												</td>
												<td valign="top" width="85%" height="100%">
													<table summary="SMF layout" width="100%" border="0">
														<tr>
															<td width="20" align="left" valign="middle"><a href="index.php?topic=2.msg2#msg2"><img src="../images/post/xx.gif" alt="" border="0" /></a>
															</td>
															<td align="left" valign="middle">
																<b><a href="#topic" class="board"><xsl:value-of select="//topic-subject" /></a></b>
																<div class="smalltext">&#171; <xsl:value-of select="post-date-time" /> &#187;</div>
															</td>
															<td align="right" valign="bottom" height="20" nowrap="nowrap" style="font-size: smaller;">
																<!-- Don't move the closing anchor tag to a new line. -->
																<a href="posting.{//language}.html#quote">
																	<img src="../images/{//language}/quote.gif" border="0" alt="{alt-reply-with-quote}" /></a>
															</td>
														</tr>
													</table>
													<hr width="100%" size="1" class="hrcolor" />
													<div style="overflow: auto; width: 100%;">
														<xsl:value-of select="topic-text" />&#160;<img src="{$default_smiley_url}/default/smiley.gif" border="0" alt="{alt-smiley}" />
													</div>
												</td>
											</tr>
											<tr>
												<td valign="bottom" class="smalltext">
													<table summary="SMF layout" width="100%" border="0" style="table-layout: fixed;">
														<tr>
															<td align="right" valign="bottom" class="smalltext">
																<a href="#topic" class="board" style="font-size: x-small;">
																	<xsl:value-of select="report-to-mod" />
																</a>&#160;&#160;
																<img src="../images/ip.gif" alt="" border="0" />&#160;
																<xsl:value-of select="logged" />
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<a name="lastPost"></a>
					<table summary="SMF layout" width="100%" cellpadding="3" cellspacing="0" border="0" class="tborder" style="margin-top: 1ex;">
						<tr>
							<td align="left" class="catbg" width="100%" height="30">
								<table summary="SMF layout" cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td>
											<b><xsl:value-of select="//pages" />:</b> [<b>1</b>]
										</td>
										<td align="right" style="font-size: smaller;">
										<!-- Don't move the closing anchor tags to new lines. -->
										<a href="posting.{//language}.html#reply">
											<img src="../images/{//language}/reply.gif" border="0" alt="{alt-reply}" /></a>
										<a href="#topic" onclick="return confirm('{notification-confirm}');">
											<img src="../images/{//language}/notify.gif" border="0" alt="{//alt-notify}" /></a>
										<a href="#topic">
											<img src="../images/{//language}/markunread.gif" border="0" alt="{alt-mark-unread}" /></a>
										<a href="#topic">
											<img src="../images/{//language}/sendtopic.gif" border="0" alt="{alt-send-topic}" /></a>
										<a href="#topic">
											<img src="../images/{//language}/print.gif" border="0" alt="{alt-print}" /></a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<div style="padding-top: 4px; padding-bottom: 4px;"></div>
				<div align="right" style="float: right; margin-bottom: 1ex;">
					<form action="index.{//language}.html" method="get" name="jump2Form">
						<label for="jump2"><xsl:value-of select="//jump-to" /></label>:
						<select name="jump2" id="jump2" onchange="if (this.options[this.selectedIndex].value) window.location.href='index.{//language}.html' + this.options[this.selectedIndex].value;">
							<option value=""><xsl:value-of select="//destination" />:</option>
							<option value="">-----------------------------</option>
							<option value="#board"><xsl:value-of select="//category-name" /></option>
							<option value="">-----------------------------</option>
							<option value="#message"> => <xsl:value-of select="//board-name" /></option>
							<option value="#message"> => <xsl:value-of select="//board-name-2" /></option>
						</select>&#160;
						<input type="button" onclick="if (document.jump2Form.jump2.options[document.jump2Form.jump2.selectedIndex].value) window.location.href = 'index.{//language}.html' + document.jump2Form.jump2.options[document.jump2Form.jump2.selectedIndex].value;">
							<xsl:attribute name="value">
								<xsl:value-of select="//go" />
							</xsl:attribute>
						</input>
					</form>
				</div>
				<br />
				<br clear="all" />
			</div>
		</div>
		<br />
	</xsl:template>

</xsl:stylesheet>