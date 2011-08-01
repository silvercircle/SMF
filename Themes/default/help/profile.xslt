<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="profile-all-demo">
			<div style="background-color: #D0D0D0;">
				<div style="padding: 2px 30px;">
					<table summary="SMF layout" width="100%" border="0" cellpadding="0" cellspacing="0" style="padding-top: 1ex;">
						<tr>
							<td width="100%" valign="top">
								<table summary="SMF layout" border="0" cellpadding="4" cellspacing="1" align="center" class="bordercolor">
									<tr class="titlebg">
										<td align="left" width="420" height="26">
											<img src="../images/icons/profile_sm.gif" alt="" border="0" align="top" />&#160;
											<xsl:value-of select="username" />:&#160;<xsl:value-of select="login-name" />
										</td>
										<td align="center" width="150"><xsl:value-of select="picture-text" /></td>
									</tr>
									<tr>
										<td class="windowbg" width="420" align="left">
											<table summary="SMF layout" border="0" cellspacing="0" cellpadding="2" width="100%">
												<tr>
													<td><b><xsl:value-of select="name" />: </b></td>
													<td><xsl:value-of select="screen-name" /></td>
												</tr>
												<tr>
													<td><b><xsl:value-of select="posts" />: </b></td>
													<td><xsl:value-of select="post-count" /></td>
												</tr>
												<tr>
													<td><b><xsl:value-of select="position" />: </b></td>
													<td><xsl:value-of select="position-user" /></td>
												</tr>
												<tr>
													<td><b><xsl:value-of select="date-reg" />: </b></td>
													<td><xsl:value-of select="date-time-reg" /></td>
												</tr>
												<tr>
													<td><b><xsl:value-of select="last-active" />: </b></td>
													<td><xsl:value-of select="date-time-active" /></td>
												</tr>
												<tr>
													<td colspan="2"><hr size="1" width="100%" class="hrcolor" /></td>
												</tr>
												<tr>
													<td><b>ICQ:</b></td>
													<td></td>
												</tr>
												<tr>
													<td><b>AIM: </b></td>
													<td></td>
												</tr>
												<tr>
													<td><b>MSN: </b></td>
													<td></td>
												</tr>
												<tr>
													<td><b>YIM: </b></td>
													<td></td>
												</tr>
												<tr>
													<td><b><xsl:value-of select="email" />: </b></td>
													<td>
														<a href="mailto:{email-user}" class="board">
															<xsl:value-of select="email-user" />
														</a>
													</td>
												</tr>
												<tr>
													<td><b><xsl:value-of select="website" />: </b></td>
													<td><a href="http://www.simplemachines.org/" target="_blank"></a></td>
												</tr>
												<tr>
													<td><b><xsl:value-of select="current-status" />: </b></td>
												<td>
													<i>
														<!-- Don't move the closing anchor tag to a new line. -->
														<a href="pm.{//language}.html">
															<xsl:attribute name="title">
																<xsl:value-of select="personal-message" /> (<xsl:value-of select="online" />)
															</xsl:attribute>
															<img src="../images/useron.gif" border="0" align="middle">
																<xsl:attribute name="alt">
																	<xsl:value-of select="online" />
																</xsl:attribute>
															</img></a>
														<span class="smalltext"> <xsl:value-of select="online" /></span>
													</i>
												</td>
											</tr>
											<tr>
												<td colspan="2"><hr size="1" width="100%" class="hrcolor" /></td>
											</tr>
											<tr>
												<td><b><xsl:value-of select="gender" />: </b></td>
												<td></td>
											</tr>
											<tr>
												<td><b><xsl:value-of select="age" />:</b></td>
												<td><xsl:value-of select="n-a" /></td>
											</tr>
											<tr>
												<td><b><xsl:value-of select="location" />:</b></td>
												<td></td>
											</tr>
											<tr>
												<td><b><xsl:value-of select="local-time" />:</b></td>
												<td><xsl:value-of select="local-time-user" /></td>
											</tr>
											<tr>
												<td>
													<b><xsl:value-of select="language" />:</b>
												</td>
												<td>
												</td>
											</tr>
											<tr>
												<td colspan="2"><hr size="1" width="100%" class="hrcolor" /></td>
											</tr>
											<tr>
												<td colspan="2" height="25">
													<table summary="SMF layout" border="0">
														<tr>
															<td><b><xsl:value-of select="signature" />:</b></td>
														</tr>
														<tr>
															<td colspan="2"></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
									<td class="windowbg" valign="middle" align="center" width="150">
										<br />
										<br />
									</td>
								</tr>
								<tr class="titlebg">
									<td colspan="2" align="left"><xsl:value-of select="additional-info" />:</td>
								</tr>
								<tr>
									<td class="windowbg2" colspan="2" align="left">
										<a href="#all" class="board">
											<xsl:value-of select="send-pm" />
										</a>
										<br />
										<br />
										<a href="#all" class="board">
											<xsl:value-of select="show-posts" />
										</a>
										<br />
										<a href="#all" class="board">
											<xsl:value-of select="show-stats" />
										</a>
										<br />
										<br />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<br />
			</div>
		</div>
		<br />
	</xsl:template>

	<xsl:template match="look-and-layout-demo">
		<div style="background-color: #D0D0D0;">
			<div style="padding: 2px 30px;">
				<table summary="SMF layout" width="100%" border="0" cellpadding="0" cellspacing="0" style="padding-top: 1ex;">
					<tr>
						<td width="180" valign="top">
							<table summary="SMF layout" border="0" cellpadding="4" cellspacing="1" class="bordercolor" width="170">
								<tr>
									<td class="catbg"><xsl:value-of select="profile-info" /></td>
								</tr>
								<tr class="windowbg2">
									<td class="smalltext" style="background-color: #f0f0f0;">
										<a href="#owners" style="font-size: x-small;" class="board"><xsl:value-of select="summary" /></a>
										<br />
										<a href="#owners" style="font-size: x-small;" class="board"><xsl:value-of select="show-stats" /></a>
										<br />
										<a href="#owners" style="font-size: x-small;" class="board"><xsl:value-of select="show-posts" /></a>
										<br />
										<br />
									</td>
								</tr>
								<tr>
									<td class="catbg"><xsl:value-of select="modify-profile" /></td>
								</tr>
								<tr class="windowbg2">
									<td class="smalltext" style="background-color: #f0f0f0;">
										<a href="#owners" style="font-size: x-small;" class="board"><xsl:value-of select="account-settings" /></a>
										<br />
										<a href="#owners" style="font-size: x-small;" class="board"><xsl:value-of select="forum-profile" /></a>
										<br />
										<b><a href="#owners" style="font-size: x-small;" class="board"><xsl:value-of select="look-and-layout" /></a></b>
										<br />
										<a href="#owners" style="font-size: x-small;" class="board"><xsl:value-of select="notification" /></a>
										<br />
										<a href="#owners" style="font-size: x-small;" class="board"><xsl:value-of select="pm-options" /></a>
										<br />
										<br />
									</td>
								</tr>
								<tr>
									<td class="catbg"><xsl:value-of select="actions" /></td>
								</tr>
								<tr class="windowbg2">
									<td class="smalltext" style="background-color: #f0f0f0;">
										<a href="#owners" style="font-size: x-small;" class="board"><xsl:value-of select="delete-account" /></a>
										<br />
										<br />
									</td>
								</tr>
							</table>
						</td>
						<td width="100%" valign="top">
							<form action="profile.{//language}.html" method="post">
								<table summary="SMF layout" border="0" width="85%" cellspacing="1" cellpadding="4" align="center" class="bordercolor">
									<tr class="titlebg">
										<td height="26" align="left">
											&#160;<img src="../images/icons/profile_sm.gif" alt="" border="0" align="top" />&#160;
											<xsl:value-of select="edit-profile" />
										</td>
									</tr>
									<tr>
										<td class="windowbg" height="25" align="left">
											<span class="smalltext"><br /><xsl:value-of select="caption-screen" />
												<br />
												<br />
											</span>
										</td>
									</tr>
									<tr>
										<td class="windowbg2" align="left">
											<table summary="SMF layout" border="0" width="100%" cellpadding="3">
												<tr>
													<td colspan="2" width="40%">
														<b><xsl:value-of select="current-theme" />:</b>&#160;<xsl:value-of select="board-default" />&#160;<a href="#owners" class="board">(<xsl:value-of select="change" />)</a>
													</td>
												</tr>
												<tr>
													<td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
												</tr>
												<tr>
													<td width="40%">
														<b><xsl:value-of select="time-format" />:</b><br />
														<!-- Don't move the closing anchor tag to a new line. -->
														<a href="../../../index.php?action=helpadmin;help=time_format" onclick="return reqWin(this.href);" class="help">
															<img src="../images/helptopics.gif" alt="{help}" border="0" align="left" style="padding-right: 1ex;" /></a>
														<span class="smalltext"><xsl:value-of select="caption-date" /></span>
													</td>
													<td>
														<select style="margin-bottom: 4px;">
															<option selected="selected">(<xsl:value-of select="date-option-selected" />)</option>
															<option><xsl:value-of select="date-option-1" /></option>
															<option><xsl:value-of select="date-option-2" /></option>
															<option><xsl:value-of select="date-option-3" /></option>
															<option><xsl:value-of select="date-option-4" /></option>
															<option><xsl:value-of select="date-option-5" /></option>
														</select><br />
														<input type="text" value="" size="30" />
													</td>
												</tr>
												<tr>
													<td width="40%"><b><xsl:value-of select="time-offset" />:</b><div class="smalltext"><xsl:value-of select="number-hours" /></div></td>
													<td class="smalltext"><input type="text" size="5" maxlength="5" value="0" /><br /><em>(<xsl:value-of select="current-forum" />)</em></td>
												</tr>
												<tr>
													<td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
												</tr>
												<tr>
													<td colspan="2">
														<br />
														<table summary="SMF layout" width="100%" cellspacing="0" cellpadding="3">
															<tr>
																<td width="28">
																	<input type="checkbox" class="check" />
																</td>
																<td><xsl:value-of select="show-descriptions" /></td>
															</tr>
															<tr>
																<td width="28">
																	<input type="checkbox" class="check" />
																</td>
																<td><xsl:value-of select="show-child" /></td>
															</tr>
															<tr>
																<td width="28">
																	<input type="checkbox" class="check" />
																</td>
																<td><xsl:value-of select="no-avatars" /></td>
															</tr>
															<tr>
																<td width="28">
																	<input type="checkbox" class="check" />
																</td>
																<td><xsl:value-of select="no-signatures" /></td>
															</tr>
															<tr>
																<td width="28">
																	<input type="checkbox" class="check" />
																</td>
																<td><xsl:value-of select="return-to-topics" /></td>
															</tr>
															<tr>
																<td width="28">
																	<input type="checkbox" class="check" />
																</td>
																<td><xsl:value-of select="recent-posts" /></td>
															</tr>
															<tr>
																<td width="28">
																	<input type="checkbox" class="check" />
																</td>
																<td><xsl:value-of select="recent-pms" /></td>
															</tr>
															<tr>
																<td colspan="2"><xsl:value-of select="first-day-of-week" />:
																	<select>
																		<option selected="selected"><xsl:value-of select="sunday" /></option>
																		<option><xsl:value-of select="monday" /></option>
																	</select>
																</td>
															</tr>
															<tr>
																<td colspan="2"><xsl:value-of select="quick-reply" />:
																	<select>
																		<option selected="selected"><xsl:value-of select="not-at-all" /></option>
																		<option><xsl:value-of select="off-default" /></option>
																		<option><xsl:value-of select="on-default" /></option>
																	</select>
																</td>
															</tr>
															<tr>
																<td colspan="2"><xsl:value-of select="quick-mod" />&#160;
																	<select>
																		<option selected="selected"><xsl:value-of select="no-quick-mod" />.</option>
																		<option><xsl:value-of select="check-quick-mod" />.</option>
																		<option><xsl:value-of select="icon-quick-mod" />.</option>
																	</select>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
												</tr>
												<tr>
													<td align="right" colspan="2">
														<input type="button">
															<xsl:attribute name="value">
																<xsl:value-of select="change-profile" />
															</xsl:attribute>
														</input>
													</td>
												</tr>
											</table>
											<br />
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
				</table>
				<br />
			</div>
		</div>
		<br />
	</xsl:template>

	<xsl:template match="profile-admin-demo">
		<div style="width: 180px; float: left; border: none;">
			<table summary="SMF layout" border="0" cellpadding="4" cellspacing="1" class="bordercolor" width="170">
				<tr>
					<td class="catbg"><xsl:value-of select="//look-and-layout-demo/profile-info" /></td>
				</tr>
				<tr class="windowbg2">
					<td class="smalltext" style="background-color: #f0f0f0;">
						<b><a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="//look-and-layout-demo/summary" /></a></b>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="//look-and-layout-demo/show-stats" /></a>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="//look-and-layout-demo/show-posts" /></a>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="track-user" /></a>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="track-ip" /></a>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="show-permissions" /></a>
						<br />
						<br />
					</td>
				</tr>
				<tr>
					<td class="catbg"><xsl:value-of select="//look-and-layout-demo/modify-profile" /></td>
				</tr>
				<tr class="windowbg2">
					<td class="smalltext" style="background-color: #f0f0f0;">
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="//look-and-layout-demo/account-settings" /></a>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="//look-and-layout-demo/forum-profile" /></a>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="//look-and-layout-demo/look-and-layout" /></a>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="//look-and-layout-demo/notification" /></a>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="//look-and-layout-demo/pm-options" /></a>
						<br />
						<br />
					</td>
				</tr>
				<tr>
					<td class="catbg"><xsl:value-of select="//look-and-layout-demo/actions" /></td>
				</tr>
				<tr class="windowbg2">
					<td class="smalltext" style="background-color: #f0f0f0;">
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="ban-user" /></a>
						<br />
						<a href="#admins" style="font-size: x-small;" class="board"><xsl:value-of select="//look-and-layout-demo/delete-account" /></a>
						<br />
						<br />
					</td>
				</tr>
			</table>
		</div>
		<br />
	</xsl:template>

	<xsl:template match="profile-xslt-div-1">
		<div><xsl:apply-templates /></div>
		<br clear="all" />
	</xsl:template>

	<xsl:template match="profile-xslt-div-2">
		<div style="margin: -1.8em 20px 0 200px;">
			<xsl:apply-templates />
		</div>
	</xsl:template>

</xsl:stylesheet>