<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="register-warning-demo">
		<table summary="SMF layout" width="400" cellspacing="0" cellpadding="3" class="tborder" align="center">
			<tr class="titlebg">
				<td>
					<xsl:value-of select="warning" />
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="padding-top: 2ex; padding-bottom: 2ex;">
					<xsl:apply-templates select="warning-text-1" />
					<br />
					<xsl:apply-templates select="warning-text-2" />
					<a href="#screen" class="board"><xsl:apply-templates select="warning-text-3" /></a>
					<xsl:apply-templates select="warning-text-4" />
				</td>
			</tr>
		</table>
		<br />
	</xsl:template>

	<xsl:template match="registration-screen-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
				<form action="registering.{//language}.html" method="post">
					<table summary="SMF layout" border="0" width="100%" cellpadding="3" cellspacing="0" class="tborder">
						<tr class="titlebg">
							<td><xsl:value-of select="required" /></td>
						</tr>
						<tr class="windowbg">
							<td width="100%" style="background-color: #e1e1e1;">
								<table summary="SMF layout" cellpadding="3" cellspacing="0" border="0" width="100%">
									<tr>
										<td width="40%">
											<b><xsl:value-of select="choose-username" />:</b>
											<div class="smalltext"><xsl:value-of select="caption-username" /></div>
										</td>
										<td>
											<input type="text" size="20" maxlength="18" />
										</td>
									</tr>
									<tr>
										<td width="40%">
											<b><xsl:value-of select="email" />:</b>
											<div class="smalltext"><xsl:value-of select="caption-email" /></div>
										</td>
										<td>
											<input type="text" size="30" />
											<input type="checkbox" class="check" /> <label><xsl:value-of select="hide-email" /></label>
										</td>
									</tr>
									<tr>
										<td width="40%">
											<b><xsl:value-of select="choose-password" />:</b>
										</td>
										<td>
											<input type="password" size="30" />
										</td>
									</tr>
									<tr>
										<td width="40%">
											<b><xsl:value-of select="verify-password" />:</b>
										</td>
										<td>
											<input type="password" size="30" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<table summary="SMF layout" width="100%" align="center" border="0" cellspacing="0" cellpadding="5" class="tborder" style="border-top: 0;">
						<tr>
							<td class="windowbg2" style="padding-top: 8px; padding-bottom: 8px;">
								<xsl:value-of select="agreement" />
							</td>
						</tr>
						<tr>
							<td align="center" class="windowbg2">
								<label><input type="checkbox" class="check" /> <b><xsl:value-of select="i-agree" /></b></label>
							</td>
						</tr>
					</table>
					<br />
					<div align="center">
						<input type="button">
							<xsl:attribute name="value">
								<xsl:value-of select="register" />
							</xsl:attribute>
						</input>
					</div>
				</form>
			</div>
		</div>
		<br />
	</xsl:template>

</xsl:stylesheet>