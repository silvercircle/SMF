<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="login-screen-demo">
		<form name="frmLogin" action="loginout.{//language}.html" method="post" style="margin-top: 4ex;">
			<table summary="SMF layout" width="400" cellspacing="0" cellpadding="4" class="tborder" align="center">
				<tr class="titlebg">
					<td colspan="2">
						<img src="../images/icons/login_sm.gif" alt="" align="top" />&#160;
						<xsl:value-of select="login" />
					</td>
				</tr>
				<tr class="windowbg">
					<td width="50%" align="right" style="background-color: #e1e1e1;">
						<b><xsl:value-of select="username" />:</b>
					</td>
					<td style="background-color: #e1e1e1;"><input type="text" size="20" value="" /></td>
				</tr>
				<tr class="windowbg">
					<td align="right" style="background-color: #e1e1e1;">
						<b><xsl:value-of select="password" />:</b>
					</td>
					<td style="background-color: #e1e1e1;">
						<input type="password" value="" size="20" />
					</td>
				</tr>
				<tr class="windowbg">
					<td align="right" style="background-color: #e1e1e1;">
						<b><xsl:value-of select="minutes" />:</b>
					</td>
					<td style="background-color: #e1e1e1;">
						<input name="cookielength" type="text" size="4" maxlength="4" value="60" />
					</td>
				</tr>
				<tr class="windowbg">
					<td align="right" style="background-color: #e1e1e1;">
						<b><xsl:value-of select="always" />:</b>
					</td>
					<td style="background-color: #e1e1e1;">
						<input type="checkbox" class="check" onclick="document.frmLogin.cookielength.disabled = this.checked;" />
					</td>
				</tr>
				<tr class="windowbg">
					<td align="center" colspan="2" style="background-color: #e1e1e1;">
						<input type="button" style="margin-top: 2ex;">
							<xsl:attribute name="value">
								<xsl:value-of select="login" />
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr class="windowbg">
					<td align="center" colspan="2" class="smalltext" style="background-color: #e1e1e1;">
						<a href="#reminder" style="font-size: x-small;" class="board">
							<xsl:value-of select="forgot" />?
						</a>
						<br />
						<br />
					</td>
				</tr>
			</table>
		</form>
		<br />
	</xsl:template>

	<xsl:template match="quick-login-demo">
		<table summary="SMF layout" cellspacing="0" cellpadding="0" border="0" align="center" width="400" class="tborder">
			<tr>
				<td style="background-color: #efefef;">
					<table summary="SMF layout" width="99%" cellpadding="0" cellspacing="5" border="0">
						<tr>
							<td width="100%" valign="top" class="smalltext" style="font-family: verdana, arial, sans-serif;">
								<form action="loginout.{//language}.html" method="post" style="margin: 3px 1ex 1px 0; text-align:right;">
									<input type="text" size="10" />
									<input type="password" size="10" />
										<select>
											<option><xsl:value-of select="hour" /></option>
											<option><xsl:value-of select="day" /></option>
											<option><xsl:value-of select="week" /></option>
											<option><xsl:value-of select="month" /></option>
											<option selected="selected"><xsl:value-of select="forever" /></option>
										</select>
									<input type="button">
										<xsl:attribute name="value">
											<xsl:value-of select="//login" />
										</xsl:attribute>
									</input>
									<br />
									<xsl:value-of select="caption" />
								</form>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br />
	</xsl:template>

	<xsl:template match="password-reminder-demo">
		<form action="loginout.{//language}.html" method="post">
			<table summary="SMF layout" border="0" width="400" cellspacing="0" cellpadding="3" align="center" class="tborder">
				<tr class="titlebg">
					<td colspan="2">
						<xsl:value-of select="password-reminder" />
					</td>
				</tr>
				<tr class="windowbg">
					<td colspan="2" style="background-color: #e1e1e1;">
						<xsl:value-of select="username-email" />:&#160;
						<input type="text" size="30" />
						<input type="button">
							<xsl:attribute name="value">
								<xsl:value-of select="send" />
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr class="windowbg">
					<td align="center" style="background-color: #e1e1e1;">
						<input type="radio" name="searchtype" checked="checked" class="check" />
						<xsl:value-of select="user" />
					</td>
					<td align="center" style="background-color: #e1e1e1;">
					<input type="radio" name="searchtype" class="check" />
						<xsl:value-of select="email" />
					</td>
				</tr>
				<tr class="windowbg" style="background-color: #e1e1e1;">
					<td colspan="2" align="center" style="background-color: #e1e1e1;">
						<input type="checkbox" class="check" />
						<xsl:value-of select="question" />
					</td>
				</tr>
				<tr class="windowbg" style="background-color: #e1e1e1;">
					<td colspan="2" align="center" class="smalltext" style="background-color: #e1e1e1;">
						<xsl:value-of select="help-text" />
					</td>
				</tr>
			</table>
		</form>
	</xsl:template>

</xsl:stylesheet>