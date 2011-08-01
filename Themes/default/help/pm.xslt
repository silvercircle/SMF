<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="pm-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
				<!-- Sorting PMs is a PHP action in the actual forums. -->
				<script language="JavaScript1.2" type="text/javascript"><![CDATA[
					var currentSort = false;
					function sortLastPM()
					{
						document.getElementById("sort-arrow").src = smf_images_url + "/" + (currentSort ? "sort_up.gif" : "sort_down.gif");
						document.getElementById("sort-arrow").alt = "";

						currentSort = !currentSort;
					}
				]]></script>

				<form action="pm.{//language}.html" method="post">
					<table summary="SMF layout" border="0" width="100%" cellspacing="0" cellpadding="3">
						<tr>
							<td valign="bottom">
								<span class="nav">
									<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
									<b><a href="index.{//language}.html#board" class="nav"><xsl:value-of select="forum-name" /></a></b>
									<br />
									<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
									<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
									<b><a href="#interface" class="nav"><xsl:value-of select="personal-messages" /></a></b>
									<br />
									<img src="../images/icons/linktree_main.gif" alt="| " border="0" />
									<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
									<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
									<b><a href="#interface" class="nav"><xsl:value-of select="inbox" /></a></b>
								</span>
							</td>
						</tr>
					</table>
					<table summary="SMF layout" cellpadding="0" cellspacing="0" border="0" width="100%" class="bordercolor" align="center">
						<tr>
							<td>
								<table summary="SMF layout" border="0" width="100%" cellspacing="1" class="bordercolor">
									<tr>
										<td align="right" valign="bottom" class="catbg" colspan="4" style="font-size: smaller;">
											<!-- Don't move the closing anchor tags to new lines. -->
											<a href="#interface">
												<img src="../images/{//language}/im_delete.gif" border="0" alt="{alt-delete}" /></a>
											<a href="#interface">
												<img src="../images/{//language}/im_outbox.gif" border="0" alt="{alt-outbox}" /></a>
											<a href="#interface">
												<img src="../images/{//language}/im_new.gif" border="0" alt="{alt-new-message}" /></a>
											<a href="#interface">
												<img src="../images/{//language}/im_reload.gif" border="0" alt="{alt-reload}" /></a>
										</td>
									</tr>
									<tr class="titlebg">
										<td style="width: 32ex;">
											<!-- Don't move the closing anchor tag to a new line. -->
											<a href="javascript:sortLastPM();">
												<xsl:value-of select="date" />&#160;
												<img id="sort-arrow" src="../images/sort_up.gif" alt="" border="0" /></a>
										</td>
										<td width="46%">
											<a href="#interface">
												<xsl:value-of select="subject" />
											</a>
										</td>
										<td>
											<a href="#interface">
												<xsl:value-of select="from" />
											</a>
										</td>
										<td align="center" width="24">
											<input type="checkbox" onclick="invertAll(this, this.form);" class="check" />
										</td>
									</tr>
									<tr class="windowbg">
										<td>
											<xsl:value-of select="date-time" />
										</td>
										<td>
											<a href="#interface" class="board">
												<xsl:value-of select="about" />
											</a>
										</td>
										<td>
											<xsl:value-of select="member" />
										</td>
										<td align="center">
											<input type="checkbox" class="check" />
										</td>
									</tr>
									<tr>
										<td class="windowbg" style="padding: 2px;" align="right" colspan="5">
											<input type="button">
												<xsl:attribute name="value">
													<xsl:value-of select="delete" />
												</xsl:attribute></input>
										</td>
									</tr>
									<tr>
										<td colspan="5" class="catbg" height="25">
											<table summary="SMF layout" width="100%" cellpadding="2" cellspacing="0" border="0">
												<tr>
													<td><b><xsl:value-of select="pages" />:</b> [<b>1</b>]</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<br />
					<br />
				</form>
			</div>
		</div>
		<br />
	</xsl:template>

</xsl:stylesheet>