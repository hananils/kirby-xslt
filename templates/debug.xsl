<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xlink="http://www.w3.org/1999/xlink">

<xsl:import href="data.xsl" />
<xsl:import href="error.xsl" />

<xsl:variable name="index" select="/data/kirby/urls/index" />
<xsl:variable name="media" select="/data/kirby/urls/media" />

<xsl:output method="xml"
    doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
    doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
    omit-xml-declaration="yes"
    encoding="UTF-8"
    indent="no" />

<xsl:template match="data">
    <xsl:choose>
        <xsl:when test="errors/file/messages">
            <xsl:apply-templates select="." mode="error" />
        </xsl:when>
        <xsl:otherwise>
            <xsl:apply-templates select="." mode="data" />
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>


</xsl:stylesheet>
