<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="search-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
				<form action="searching.{//language}.html" method="post" name="searchform" id="searchform">
					<table summary="SMF layout" width="80%" border="0" cellspacing="0" cellpadding="3" align="center">
						<tr>
							<td>
								<span class="nav">
									<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
									<b><a href="index.{//language}.html#board" class="nav"><xsl:value-of select="forum-name" /></a></b>
									<br />
									<img src="../images/icons/linktree_side.gif" alt="|-" border="0" />
									<img src="../images/icons/folder_open.gif" alt="+" border="0" />&#160;
									<b><a href="#advanced" class="nav"><xsl:value-of select="search" /></a></b>
								</span>
							</td>
						</tr>
					</table>
					<table summary="SMF layout" width="80%" border="0" cellspacing="0" cellpadding="4" align="center" class="tborder">
						<tr class="titlebg">
							<td>
								<xsl:value-of select="parameters" />
							</td>
						</tr>
						<tr>
							<td class="windowbg">
								<table summary="SMF layout">
									<tr>
										<td><b><xsl:value-of select="search-for" />:</b></td>
										<td>&#160;</td>
										<td><b><xsl:value-of select="by-user" />:</b></td>
									</tr>
									<tr>
										<td><input type="text" size="40" /></td>
										<td>
											<select>
												<option selected="selected">
													<xsl:value-of select="match-all" />
												</option>
												<option>
													<xsl:value-of select="match-any" />
												</option>
											</select>&#160;&#160;&#160;
										</td>
										<td><input type="text" value="*" size="40" />&#160;</td>
									</tr>
									<tr>
										<td colspan="3">&#160;</td>
									</tr>
									<tr>
										<td colspan="2"><b><xsl:value-of select="options" />:</b></td>
										<td><b><xsl:value-of select="age" />:</b></td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="checkbox" class="check" /> <label><xsl:value-of select="messages" /></label><br />
											<input type="checkbox" class="check" /> <label><xsl:value-of select="subjects-only" /></label><br />
										</td>
										<td>
											<xsl:value-of select="between" />
											<input type="text" value="0" size="5" maxlength="5" />
											<xsl:value-of select="and" />
											<input type="text" value="9999" size="5" maxlength="5" />
											<xsl:value-of select="days" />.
										</td>
									</tr>
									<tr>
										<td colspan="3" style="padding-top: 2ex;"><b><xsl:value-of select="search-order" />:</b></td>
									</tr>
									<tr>
										<td colspan="3">
											<select>
												<option selected="selected="><xsl:value-of select="relevant-first" /></option>
												<option><xsl:value-of select="big-first" /></option>
												<option><xsl:value-of select="small-first" /></option>
												<option><xsl:value-of select="recent-first" /></option>
												<option><xsl:value-of select="oldest-first" /></option>
											</select>
										</td>
									</tr>
								</table>
								<br />
								<b><xsl:value-of select="choose" />:</b>
								<br />
								<br />
								<table summary="SMF layout" width="80%" border="0" cellpadding="1" cellspacing="0">
									<tr>
										<td width="50%">
											<span style="text-decoration: underline;">
												<xsl:value-of select="category" />
											</span>
										</td>
										<td width="50%">
											<input type="checkbox" id="brd2" name="brd[2]" value="2" checked="checked" class="check" />
											<label for="brd2"><xsl:value-of select="another-board" /></label>
										</td>
									</tr>
									<tr>
										<td width="50%">
											<input type="checkbox" id="brd1" name="brd[1]" value="1" checked="checked" class="check" />
											<label for="brd1"><xsl:value-of select="board-name" /></label>
										</td>
									</tr>
								</table>
								<br />
								<input type="checkbox" name="all" id="check_all" value="" checked="checked" onclick="invertAll(this, this.form, 'brd');" class="check" /><i> <label for="check_all"><xsl:value-of select="check-all" /></label></i>
								<br />
								<br />
								<table summary="SMF layout" border="0" cellpadding="2" cellspacing="0" align="left">
									<tr>
										<td valign="bottom">
											<input type="button">
												<xsl:attribute name="value">
													<xsl:value-of select="search" />
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<br />
				</form>
			</div>
		</div>
		<br />
	</xsl:template>

</xsl:stylesheet>