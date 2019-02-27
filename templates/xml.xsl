<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:hananils="https://hananils.de/kirby-xslt">

<!--
    XML
-->

<!-- Prolog -->

<xsl:template match="data" mode="xml">
    <div class="line prolog">&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;</div>
    <xsl:apply-templates select="." mode="node" />
</xsl:template>

<!-- Nodes -->

<xsl:template match="hananils:*" mode="node">
    <!-- Ignore hananils namespace -->
</xsl:template>

<xsl:template match="*" mode="node">
    <a>
        <xsl:attribute name="id">
            <xsl:apply-templates select="ancestor-or-self::*" mode="path" />
        </xsl:attribute>
    </a>
    <div data-depth="{count(ancestor::*)}">
        <xsl:attribute name="class">
            <xsl:text>node</xsl:text>
            <xsl:if test="name(.) = 'error'"> has-errored</xsl:if>
            <xsl:if test="@hananils:xpath-matched"> is-matching</xsl:if>
        </xsl:attribute>

        <xsl:apply-templates select="." mode="lines" />
    </div>
</xsl:template>

<!-- Lines -->

<xsl:template match="*[* or text()[normalize-space()]]" mode="lines">
    <div class="line" data-name="{name()}">
        <xsl:apply-templates select="." mode="tag-open" />
    </div>

    <xsl:apply-templates select="text()[normalize-space()] | *" mode="node"/>

    <div class="line">
        <xsl:apply-templates select="." mode="tag-close" />
    </div>
</xsl:template>

<xsl:template match="*[not(*) and text()] | *[* and text()[normalize-space()]]" mode="lines">
    <div class="line" data-name="{name()}">
        <xsl:apply-templates select="." mode="tag-open" />
        <xsl:apply-templates select="* | text()" mode="inline-node" />
        <xsl:apply-templates select="." mode="tag-close" />
    </div>
</xsl:template>

<xsl:template match="*[not(*) and not(text())]" mode="lines">
    <div class="line" data-name="{name()}">
        <xsl:apply-templates select="." mode="tag-open">
            <xsl:with-param name="selfclose" select="true()" />
        </xsl:apply-templates>
    </div>
</xsl:template>

<xsl:template match="*" mode="inline-node">
    <xsl:apply-templates select="." mode="tag-open" />
    <xsl:apply-templates select="* | text()[normalize-space()]" mode="inline-node" />
    <xsl:apply-templates select="." mode="tag-close" />
</xsl:template>

<!-- Error -->

<xsl:template match="line" mode="error">
    <xsl:variable name="line" select="position()" />
    <div id="line{$line}">
        <xsl:attribute name="class">
            <xsl:text>node</xsl:text>
            <xsl:if test="//error[@line = $line]">
                <xsl:text> has-errored-sequentially</xsl:text>
            </xsl:if>
            <xsl:if test="//error[1][@referenced-line = $line]">
                <xsl:text> is-source</xsl:text>
            </xsl:if>
        </xsl:attribute>

        <div class="line">
            <xsl:value-of select="." />
        </div>
    </div>
</xsl:template>

<!--
    Tags
-->

<!-- Opening -->

<xsl:template match="*" mode="tag-open">
    <xsl:param name="selfclose" select="false()" />

    <span class="punctuation">&lt;</span>
    <a class="tag">
        <xsl:attribute name="href">
            <xsl:text>#</xsl:text>
            <xsl:apply-templates select="ancestor-or-self::*" mode="path" />
        </xsl:attribute>

        <xsl:value-of select="name()" />
    </a>

    <xsl:apply-templates select="@*" mode="attribute" />

    <xsl:choose>
        <xsl:when test="$selfclose = true()">
            <xsl:if test="@*"> </xsl:if>
            <span class="punctuation">/&gt;</span>
        </xsl:when>
        <xsl:otherwise>
            <span class="punctuation">&gt;</span>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Closing -->

<xsl:template match="*" mode="tag-close">
    <span class="tag">
        <span class="punctuation">&lt;/</span>
        <xsl:value-of select="name()" />
    </span>
    <span class="punctuation">&gt;</span>
</xsl:template>

<!--
    Attributes
-->

<xsl:template match="@hananils:*" mode="attribute">
    <!-- Remove helper attribute -->
</xsl:template>

<xsl:template match="@*" mode="attribute">
    <xsl:text> </xsl:text>
    <span class="attr">
        <span class="attr-name">
            <xsl:value-of select="name(.)" />
        </span>
        <span class="attr-value">
            <span class="punctuation">=</span>
            <span class="punctuation">"</span>
            <xsl:value-of select="." />
            <span class="punctuation">"</span>
        </span>
    </span>
</xsl:template>

<!--
    Path
-->

<xsl:template match="*" mode="path">
    <xsl:if test="name() != 'data'">
        <xsl:if test="name(..) != 'data'">/</xsl:if>
        <xsl:value-of select="name()" />

        <xsl:variable name="name" select="name()" />
        <xsl:if test="preceding-sibling::*[name() = $name] or following-sibling::*[name() = $name]">
            <xsl:text>[</xsl:text>
            <xsl:value-of select="count(preceding-sibling::*[name() = $name]) + 1" />
            <xsl:text>]</xsl:text>
        </xsl:if>
    </xsl:if>
</xsl:template>


</xsl:stylesheet>
