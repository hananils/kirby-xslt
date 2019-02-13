<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xlink="http://www.w3.org/1999/xlink">

<xsl:template match="data" mode="error">
    <html>
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
            <title>
                <xsl:text>Processing Error</xsl:text>
            </title>
            <link rel="stylesheet" type="text/css" href="{$media}/plugins/hananils/xslt/styles/debug.css" />
        </head>
        <body id="app" lang="en">
            <main class="l-main">
                <div class="l-secondary">
                    <header class="m-header">
                        <h1 class="m-header-title">Processing Error</h1>
                    </header>
                    <nav id="index" class="m-index">
                        <xsl:apply-templates select="errors" mode="index" />
                    </nav>
                </div>
                <div class="l-primary">
                    <pre id="xml" class="is-invalid"><code><xsl:apply-templates select="errors/file[1]/content/line" /></code></pre>
                    <ol class="m-errors">
                        <xsl:apply-templates select="errors/file[1]/messages/item" mode="error" />
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

<xsl:template match="errors" mode="index">
    <ul class="m-index-list">
        <xsl:apply-templates select="file" mode="index" />
    </ul>
</xsl:template>

<xsl:template match="file" mode="index">
    <li class="m-index-item">
        <a class="m-index-link" href="">
            <xsl:text>site/</xsl:text>
            <xsl:value-of select="substring-after(@path, 'site/')" />
        </a>
    </li>
</xsl:template>

<!--
    Lines
-->

<xsl:template match="line">
    <div class="line">
        <xsl:attribute name="class">
            <xsl:text>line</xsl:text>
            <xsl:if test="/data/errors/file/messages/item/@ref = position()"> is-ref</xsl:if>
            <xsl:if test="/data/errors/file/messages/item/@line = position()"> has-errored</xsl:if>
        </xsl:attribute>

        <xsl:value-of select="." />
    </div>
</xsl:template>

<!--
    Errors
-->

<xsl:template match="messages/item" mode="error">
    <li class="m-errors-entry">
        <a href="">
            <xsl:value-of select="." />
        </a>
    </li>
</xsl:template>


</xsl:stylesheet>
