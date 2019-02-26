<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:hananils="https://hananils.de/kirby-xslt">

<xsl:template match="data" mode="data">
    <html>
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
            <title>
                <xsl:text>Data: </xsl:text>
                <xsl:value-of select="page/title" />
            </title>
            <link rel="stylesheet" type="text/css" href="{$media}/plugins/hananils/xslt/styles/debug.css" />
        </head>
        <body id="app" lang="en">
            <xsl:copy-of select="hananils:kirby-icons/*" />
            <main class="l-main">
                <header class="l-header">
                    <h1 class="m-site-title">
                        <span aria-hidden="true" class="m-icon">
                            <svg viewBox="0 0 16 16">
                                <use xlink:href="#icon-page" />
                            </svg>
                        </span>

                        <xsl:value-of select="$site" />
                    </h1>
                    <form class="m-search">
                        <input type="hidden" name="data" />
                        <input type="text" class="m-search-field" name="xpath" placeholder="{@hananils:find-by-xpath}" value="{kirby/request/query/xpath}" />
                        <button class="m-search-button">
                            <span aria-hidden="true" class="m-icon">
                                <svg viewBox="0 0 16 16">
                                    <use xlink:href="#icon-search" />
                                </svg>
                            </span>
                            <span class="is-hidden">
                                <xsl:value-of select="@hananils:search" />
                            </span>
                        </button>
                    </form>
                </header>
                <div class="l-secondary">
                    <header class="m-header">
                        <h1 class="m-header-title">
                            <a href="{$page}/?data">
                                <xsl:value-of select="@hananils:data" />
                                <xsl:text>: </xsl:text>
                                <xsl:value-of select="$title" />
                            </a>
                        </h1>
                        <nav class="m-header-nav">
                            <a class="m-header-nav-item has-icon" href="{page/@url}" target="_blank">
                                <i aria-hidden="true" class="m-icon">
                                    <svg viewBox="0 0 16 16">
                                        <use xlink:href="{$media}/plugins/hananils/xslt/images/icons.svg#icon-open" />
                                    </svg>
                                </i>
                                <span class="m-icon-label">
                                    <xsl:value-of select="@hananils:open" />
                                </span>
                            </a>
                            <a class="m-header-nav-item has-icon" href="{kirby/urls/panel}" target="_blank">
                                <i aria-hidden="true" class="m-icon">
                                    <svg viewBox="0 0 16 16">
                                        <use xlink:href="{$media}/plugins/hananils/xslt/images/icons.svg#icon-settings" />
                                    </svg>
                                </i>
                                <span class="m-icon-label">Panel</span>
                            </a>
                        </nav>
                    </header>
                    <section id="index" class="m-index">
                        <h2 class="m-index-title">
                            <xsl:value-of select="@hananils:overview" />
                        </h2>
                        <xsl:apply-templates select="." mode="index" />
                        <p>
                            <xsl:value-of select="@hananils:execution" />
                            <xsl:text>: </xsl:text>
                            <xsl:value-of select="sum(//@hananils:execution-time)" />
                            <xsl:text>ms</xsl:text>
                        </p>
                    </section>
                </div>
                <div class="l-primary">
                    <pre id="xml"><code><xsl:apply-templates select="." mode="xml" /></code></pre>
                    <xsl:if test="//error[@type = 'invalid']">
                        <ol class="m-errors">
                            <xsl:apply-templates select="//error[@type = 'invalid']" mode="error" />
                        </ol>
                    </xsl:if>
                </div>
            </main>
            <script type="text/javascript" src="{$media}/plugins/hananils/xslt/scripts/app.register.js"></script>
            <script type="text/javascript" src="{$media}/plugins/hananils/xslt/scripts/app.components.js"></script>
            <script type="text/javascript" src="{$media}/plugins/hananils/xslt/scripts/app.switch.js"></script>
            <script type="text/javascript" src="{$media}/plugins/hananils/xslt/scripts/app.js"></script>
        </body>
    </html>
</xsl:template>

<!--
    Index
-->

<xsl:template match="data" mode="index">
    <ul class="m-index-nodes">
        <xsl:apply-templates select="child::*" mode="index-node" />
    </ul>
</xsl:template>

<xsl:template match="hananils:*" mode="index-node">
    <!-- Ignore hananils namespace -->
</xsl:template>

<xsl:template match="*" mode="index-node">
    <li class="m-index-node">
        <xsl:apply-templates select="." mode="index-link" />
        <xsl:apply-templates select="." mode="secondary-index" />
    </li>
</xsl:template>

<xsl:template match="kirby" mode="secondary-index">
    <ul class="m-index-subnodes">
        <xsl:apply-templates select="urls | request | user" mode="secondary-index-node" />
    </ul>
</xsl:template>

<xsl:template match="*" mode="secondary-index">
    <ul class="m-index-subnodes">
        <xsl:apply-templates select="child::path | child::content | child::children | child::files" mode="secondary-index-node" />
    </ul>
</xsl:template>

<xsl:template match="*" mode="secondary-index-node">
    <li class="m-index-subnode">
        <xsl:apply-templates select="." mode="index-link">
            <xsl:with-param name="icon" select="true()" />
        </xsl:apply-templates>
    </li>
</xsl:template>

<xsl:template match="*" mode="index-link">
    <xsl:param name="icon" select="false()" />

    <xsl:variable name="anchor">
        <xsl:apply-templates select="ancestor-or-self::*" mode="path" />
    </xsl:variable>

    <div class="m-index-actions">
        <xsl:if test="$icon = true()">
            <span class="m-icon">
                <svg viewBox="0 0 16 16">
                    <use xlink:href="#icon-code" />
                </svg>
            </span>
        </xsl:if>

        <a class="m-index-link" href="#{$anchor}">
            <xsl:value-of select="name()" />
            <xsl:apply-templates select="@hananils:execution-time" />
        </a>
        <button class="m-index-switch">
            <span class="m-icon">
                <svg viewBox="0 0 16 16">
                    <use xlink:href="#icon-circle" />
                </svg>
            </span>
        </button>
    </div>
</xsl:template>

<xsl:template match="@hananils:execution-time">
    <em class="m-execution-time">
        <xsl:value-of select="." />
        <xsl:text>ms</xsl:text>
    </em>
</xsl:template>

<!--
    Errors
-->

<xsl:template match="error[@type = 'invalid']" mode="error">
    <xsl:variable name="path">
        <xsl:apply-templates select="ancestor-or-self::*" mode="path" />
    </xsl:variable>

    <li class="m-errors-entry">
        <a href="#{$path}">
            <xsl:text>Invalid markup in </xsl:text>
<!--             <xsl:choose>
                <xsl:when test="count(error) != 1">
                    <xsl:value-of select="concat(count(error), ' errors in ')" />
                </xsl:when>
                <xsl:otherwise>1 error in </xsl:otherwise>
            </xsl:choose> -->
            <code>
                <xsl:for-each select="ancestor::*">
                    <xsl:text>/</xsl:text>
                    <xsl:value-of select="name()" />
                </xsl:for-each>
            </code>
            <xsl:text>.</xsl:text>
        </a>
    </li>
</xsl:template>

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
