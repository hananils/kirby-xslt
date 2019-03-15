<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:hananils="https://hananils.de/kirby-xslt">

<xsl:import href="xml.xsl" />

<xsl:output method="html"
    omit-xml-declaration="yes"
    media-type="text/html"
    encoding="utf-8"
    indent="no"
    doctype-system="about:legacy-compat" />

<xsl:decimal-format name="en" decimal-separator="." grouping-separator=" "/>
<xsl:decimal-format name="de" decimal-separator="," grouping-separator="."/>

<xsl:variable name="index" select="/data/hananils:kirby-xslt/kirby/urls/index" />
<xsl:variable name="media" select="/data/hananils:kirby-xslt/kirby/urls/media" />
<xsl:variable name="plugin" select="/data/hananils:kirby-xslt" />
<xsl:variable name="errors" select="/data/hananils:kirby-xslt-errors" />
<xsl:variable name="language">
    <xsl:choose>
        <xsl:when test="$plugin/kirby/@language != ''">
            <xsl:value-of select="$plugin/kirby/@language" />
        </xsl:when>
        <xsl:when test="$plugin/kirby/user/@language != ''">
            <xsl:value-of select="$plugin/kirby/user/@language" />
        </xsl:when>
        <xsl:otherwise>en</xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<xsl:template match="data">
    <html>
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
            <title>
                <xsl:value-of select="$plugin/dictionary/data" />
                <xsl:text>: </xsl:text>
                <xsl:value-of select="$plugin/page/title" />
            </title>
            <link rel="stylesheet" type="text/css" href="{$media}/plugins/hananils/xslt/styles/debug.css" />
        </head>
        <body id="app" lang="{$plugin/kirby/@language}">
            <xsl:copy-of select="hananils:kirby-xslt/icons/*" />
            <main class="l-main">
                <header class="l-header">
                    <h1 class="m-site-title">
                        <span aria-hidden="true" class="m-icon">
                            <svg viewBox="0 0 16 16">
                                <use xlink:href="#icon-page" />
                            </svg>
                        </span>

                        <xsl:value-of select="$plugin/site/title" />
                    </h1>
                    <xsl:choose>
                        <xsl:when test="$errors != ''">
                            <p class="m-error-title">
                                <xsl:value-of select="count($errors/error)" />
                                <xsl:text> </xsl:text>
                                <xsl:choose>
                                    <xsl:when test="count($errors/error) = 1">
                                        <xsl:value-of select="$plugin/dictionary/error" />
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of select="$plugin/dictionary/errors" />
                                    </xsl:otherwise>
                                </xsl:choose>
                            </p>
                        </xsl:when>
                        <xsl:otherwise>
                            <form class="m-search">
                                <input type="hidden" name="data" />
                                <input type="text" class="m-search-field" name="xpath" placeholder="{$plugin/dictionary/xpath}" value="{$plugin/kirby/request/query/xpath}" />
                                <button class="m-search-button">
                                    <span aria-hidden="true" class="m-icon">
                                        <svg viewBox="0 0 16 16">
                                            <use xlink:href="#icon-search" />
                                        </svg>
                                    </span>
                                    <span class="is-hidden">
                                        <xsl:value-of select="$plugin/dictionary/search" />
                                    </span>
                                </button>
                            </form>
                        </xsl:otherwise>
                    </xsl:choose>
                </header>
                <div class="l-secondary">
                    <header class="m-header">
                        <h1 class="m-header-title">
                            <a href="{$plugin/page/@url}/?data">
                                <xsl:value-of select="$plugin/dictionary/data" />
                                <xsl:text>: </xsl:text>
                                <xsl:value-of select="$plugin/page/title" />
                            </a>
                        </h1>
                        <nav class="m-header-nav">
                            <a class="m-header-nav-item has-icon" href="{$plugin/page/@url}" target="_blank">
                                <i aria-hidden="true" class="m-icon">
                                    <svg viewBox="0 0 16 16">
                                        <use xlink:href="#icon-open" />
                                    </svg>
                                </i>
                                <span class="m-icon-label">
                                    <xsl:value-of select="$plugin/dictionary/open" />
                                </span>
                            </a>
                            <a class="m-header-nav-item has-icon" href="{kirby/urls/panel}" target="_blank">
                                <i aria-hidden="true" class="m-icon">
                                    <svg viewBox="0 0 16 16">
                                        <use xlink:href="#icon-settings" />
                                    </svg>
                                </i>
                                <span class="m-icon-label">
                                    <xsl:value-of select="$plugin/dictionary/panel" />
                                </span>
                            </a>
                        </nav>
                    </header>
                    <section id="index" class="m-index">
                        <h2 class="m-index-title">
                            <xsl:value-of select="$plugin/dictionary/overview" />
                        </h2>
                        <xsl:apply-templates select="." mode="index" />
                        <xsl:if test="//@hananils:execution-time">
                            <p>
                                <xsl:value-of select="$plugin/dictionary/execution" />
                                <xsl:text>: </xsl:text>
                                <xsl:value-of select="format-number(sum(//@hananils:execution-time), '#,##0.00', $language)" />
                                <xsl:text>ms</xsl:text>
                            </p>
                        </xsl:if>
                    </section>
                </div>
                <div class="l-primary">
                    <xsl:choose>
                        <xsl:when test="$errors != ''">
                            <pre id="xml"><code><xsl:apply-templates select="$errors/file/line" mode="error" /></code></pre>
                        </xsl:when>
                        <xsl:otherwise>
                            <pre id="xml"><code><xsl:apply-templates select="." mode="xml" /></code></pre>
                        </xsl:otherwise>
                    </xsl:choose>

                    <ol class="m-errors">
                        <xsl:apply-templates select="//*[@type = 'invalid']" mode="error" />
                        <xsl:apply-templates select="//hananils:kirby-xslt-errors/error" mode="error" />
                    </ol>
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

        <a class="m-index-link">
            <xsl:attribute name="href">
                <xsl:value-of select="$plugin/page/@url" />
                <xsl:text>?data</xsl:text>
                <xsl:if test="$plugin/kirby/request/query/xpath != ''">
                    <xsl:value-of select="concat('&amp;xpath=', $plugin/kirby/request/query/xpath)" />
                </xsl:if>
                <xsl:text>#</xsl:text>
                <xsl:value-of select="$anchor" />
            </xsl:attribute>

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
        <xsl:attribute name="class">
            <xsl:text>m-execution-time</xsl:text>
            <xsl:if test=". &gt; 100"> is-high</xsl:if>
        </xsl:attribute>

        <xsl:value-of select="format-number(., '#,##0.00', $language)" />
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
        <a>
            <xsl:attribute name="href">
                <xsl:value-of select="$plugin/page/@url" />
                <xsl:text>?data</xsl:text>
                <xsl:if test="$plugin/kirby/request/query/xpath != ''">
                    <xsl:value-of select="concat('&amp;xpath=', $plugin/kirby/request/query/xpath)" />
                </xsl:if>
                <xsl:text>#</xsl:text>
                <xsl:value-of select="$path" />
            </xsl:attribute>

            <xsl:text>Invalid markup in </xsl:text>
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

<xsl:template match="errors/*[@type = 'invalid']" mode="error">
    <xsl:variable name="path">
        <xsl:apply-templates select="ancestor-or-self::*" mode="path" />
    </xsl:variable>

    <li class="m-errors-entry">
        <a >
            <xsl:attribute name="href">
                <xsl:value-of select="$plugin/page/@url" />
                <xsl:text>?data</xsl:text>
                <xsl:if test="$plugin/kirby/request/query/xpath != ''">
                    <xsl:value-of select="concat('&amp;xpath=', $plugin/kirby/request/query/xpath)" />
                </xsl:if>
                <xsl:text>#</xsl:text>
                <xsl:value-of select="$path" />
            </xsl:attribute>

            <xsl:value-of select="ancestor::page/title" />
            <xsl:text> – </xsl:text>
            <xsl:value-of select="@label" />
            <xsl:text>: </xsl:text>
            <xsl:value-of select="." />
        </a>
    </li>
</xsl:template>

<xsl:template match="error" mode="error">
    <li class="m-errors-entry">
        <a href="{$plugin/page/@url}#line{@line}">
            <xsl:value-of select="." />
        </a>
    </li>
</xsl:template>


</xsl:stylesheet>
