<?xml version="1.0"?>
<!DOCTYPE smf:helpfile [
	<!ENTITY copyright "&#169;">
	<!ENTITY space "&#160;">
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="index.xslt" />
	<xsl:import href="registering.xslt" />
	<xsl:import href="loginout.xslt" />
	<xsl:import href="profile.xslt" />
	<xsl:import href="posting.xslt" />
	<xsl:import href="pm.xslt" />
	<xsl:import href="searching.xslt" />

	<xsl:variable name="default_smiley_url" select="'../../../Smileys'" />

	<xsl:output method="xml" encoding="iso-8859-1" indent="yes" omit-xml-declaration="yes" media-type="text/html"
		doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN"
		doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<!-- Here follows the main document template. -->

	<xsl:template match="smf:helpfile" xmlns:smf="http://www.simplemachines.org/">
		<html>
			<xsl:comment> Version: 1.0; help/<xsl:value-of select="./@subject" /> </xsl:comment>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
				<script language="JavaScript" type="text/javascript" src="../script.js"></script>
				<script language="JavaScript" type="text/javascript"><![CDATA[
					var smf_theme_url = "..";
					var smf_images_url = "../images";
				]]></script>
				<title><xsl:value-of select="title" /></title>
				<link type="text/css" rel="stylesheet" href="../style.css" />
				<link type="text/css" rel="stylesheet" href="help.css" />
			</head>
			<body>
				<xsl:call-template name="header" />
				<xsl:apply-templates />
			</body>
		</html>
	</xsl:template>

<!-- Some empty templates to stop stuff already used elsewhere from appearing twice! -->

	<xsl:template match="language | title | help | secheading | subheading | visit-simple-machines" />

<!-- The hyperlinked SMF images, main heading and menu. -->

	<xsl:template name="header">
		<table summary="two simple cells for layout purposes" width="100%">
			<tr>
				<td width="1%">
					<a href="http://www.simplemachines.org/">
						<img src="../images/helplogo.jpg" alt="{//visit-simple-machines}" title="{//visit-simple-machines}" border="0" /></a>
				</td>
				<td valign="top">
					<a href="http://www.simplemachines.org/">
						<img src="../images/smflogo.gif" align="right" alt="{//visit-simple-machines}" title="{//visit-simple-machines}" border="0" /></a>
					<h1 style="margin-top: 12px;"><xsl:value-of select="//title" /></h1>
					<br clear="right" />
					<hr />
					<p>
						<xsl:for-each select="//menu/help">
							<xsl:choose>
								<xsl:when test="self::node()[@page = 'here']">
									<strong>
										<xsl:value-of select="." />
									</strong>
								</xsl:when>
								<xsl:otherwise>
									<a href="{@page}.{//language}.html">
										<xsl:value-of select="." />
									</a>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:if test="position() != last()"> &#8226; </xsl:if>
						</xsl:for-each>
					</p>
					<hr />
				</td>
			</tr>
		</table>
	</xsl:template>

<!-- Here we follow the introduction with a linked contents list generated from the page sections and subsections. -->

	<xsl:template match="intro">
		<xsl:apply-templates />
		<xsl:call-template name="contents" />
	</xsl:template>

	<xsl:template name="contents">
		<ol>
			<xsl:for-each select="//section">
				<li>
					<a href="#{@id}">
						<xsl:value-of select="secheading" />
					</a>
					<xsl:if test=".//subsection">
						<ol class="la">
							<xsl:for-each select=".//subsection">
								<li>
									<a href="#{@id}">
										<xsl:value-of select="subheading" />
									</a>
								</li>
							</xsl:for-each>
						</ol>
					</xsl:if>
				</li>
			</xsl:for-each>
		</ol>
	</xsl:template>

<!-- The copyright footer. -->

	<xsl:template match="copyright">
		<div class="footer">
			<hr noshade="noshade" size="1" />
			<p><xsl:apply-templates /></p>
		</div>
	</xsl:template>

<!-- The main generic page elements. -->

	<xsl:template match="section">
		<h2 id="{@id}">
			<xsl:value-of select="secheading" />
		</h2>
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="subsection">
		<h3 id="{@id}">
			<xsl:value-of select="subheading" />
		</h3>
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="paragraph">
		<p><xsl:apply-templates /></p>
	</xsl:template>

	<xsl:template match="emphasis">
		<em><xsl:apply-templates /></em>
	</xsl:template>

	<xsl:template match="strong">
		<strong><xsl:apply-templates /></strong>
	</xsl:template>

	<xsl:template match="list">
		<ul><xsl:apply-templates /></ul>
	</xsl:template>

	<xsl:template match="item">
		<li><xsl:apply-templates /></li>
	</xsl:template>

	<xsl:template match="link">
		<xsl:for-each select=".">
			<xsl:choose>
				<xsl:when test="self::node()[@site]">
					<a href="{@site}">
						<xsl:value-of select="." />
					</a>
				</xsl:when>
				<xsl:when test="self::node()[@page] and self::node()[@ref]">
					<a href="{@page}.{//language}.html#{@ref}">
						<xsl:value-of select="." />
					</a>
				</xsl:when>
				<xsl:when test="self::node()[@page]">
					<a href="{@page}.{//language}.html">
						<xsl:value-of select="." />
					</a>
				</xsl:when>
				<xsl:when test="self::node()[@ref]">
					<a href="#{@ref}">
						<xsl:value-of select="." />
					</a>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="." />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>

<!-- These things are all currently highlighted the same way, but don't have to be. -->

	<xsl:template match="action | check | dialog | field | icon | option | screen | sort-by | term">
		<strong><xsl:apply-templates /></strong>
	</xsl:template>

<!-- Some SMF presentational HTML intentionally preserved in the source tree. -->

	<xsl:template match="html-b">
		<b><xsl:apply-templates /></b>
	</xsl:template>

	<xsl:template match="html-i">
		<i><xsl:apply-templates /></i>
	</xsl:template>

</xsl:stylesheet>