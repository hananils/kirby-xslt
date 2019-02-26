<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:hananils="https://hananils.de/kirby-xslt">

<xsl:template match="data" mode="error">
    <html>
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
            <title>Processing Error</title>
            <link rel="stylesheet" type="text/css" href="{@hananils:media}/plugins/hananils/xslt/styles/debug.css" />
        </head>
        <body id="app" class="is-error" lang="en">
            <main class="l-main">
                <header class="l-header">
                    <h1 class="m-site-title">
                        <xsl:value-of select="count(errors/error)" />
                        <xsl:text> Processing Error</xsl:text>
                        <xsl:if test="count(errors/error) != 1">s</xsl:if>
                    </h1>
                </header>
                <div class="l-primary">
                    <pre id="xml" class="is-invalid"><code><xsl:apply-templates select="file/line" /></code></pre>
                    <ol class="m-errors">
                        <xsl:apply-templates select="errors/error" mode="error" />
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
    Lines
-->

<xsl:template match="line">
    <xsl:variable name="line" select="position()" />
    <div id="line{$line}">
        <xsl:attribute name="class">
            <xsl:text>node</xsl:text>
            <xsl:if test="//error[@line = $line]">
                <xsl:text> has-errored</xsl:text>
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
    Errors
-->

<xsl:template match="error" mode="error">
    <li class="m-errors-entry">
        <a href="#line{@line}">
            <xsl:value-of select="." />
        </a>
    </li>
</xsl:template>


</xsl:stylesheet>
