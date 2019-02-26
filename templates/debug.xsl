<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:hananils="https://hananils.de/kirby-xslt">

<xsl:import href="data.xsl" />
<xsl:import href="error.xsl" />

<xsl:variable name="index" select="/data/@hananils:index" />
<xsl:variable name="media" select="/data/@hananils:media" />
<xsl:variable name="page" select="/data/@hananils:page" />
<xsl:variable name="site" select="/data/@hananils:site" />
<xsl:variable name="title" select="/data/@hananils:title" />

<xsl:output method="html"
    omit-xml-declaration="yes"
    media-type="text/html"
    encoding="utf-8"
    indent="no"
    doctype-system="about:legacy-compat" />

<xsl:template match="data">
    <xsl:choose>
        <xsl:when test="errors">
            <xsl:apply-templates select="." mode="error" />
        </xsl:when>
        <xsl:otherwise>
            <xsl:apply-templates select="." mode="data" />
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>


</xsl:stylesheet>
