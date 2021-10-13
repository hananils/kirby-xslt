# XSLT for Kirby 3

We work as designers, and over the years we have fallen in love with XSLT for templating. XSLT allows you to transform XML to any other structure by applying templates and thus you can use it to generate HTML from structured data. The syntax looks like HTML and works like CSS conceptually.

This plugin enables XSLT templating in your Kirby install by generating XML for all front-end pages. You can setup custom nodes and specify included elements for each page.

### Overview

- [Installation](#installation)
- [Settings up XML output](#data)
- [Creating templates](#templates)
- [Shortcomings](#shortcomings)

# Installation

This plugin requires PHP's [LibXML module](https://secure.php.net/manual/en/book.libxml.php) with the [XSLT extension](https://secure.php.net/manual/en/book.xsl.php) enabled (`--with-xsl`) in order to work.

## Via Download

Download the latest release from https://github.com/hananils/kirby-xslt/releases, unzip all files and copy them to the plugin folder at `/site/plugins/xslt`.

## Via Git Submodule

Add this repository as submodule to `/site/plugins/xslt`:

```git
git submodule add https://github.com/hananils/kirby-xslt.git site/plugins/xslt
```

## Via Composer

Require this plugin using composer:

```
composer require hananils/kirby-xslt
```

# Data

By default, the plugin provides XML nodes for the `$kirby`, `$site`, `$pages` and `$page` objects. As soon as you are logged in, you can view the data of any page by appending `?data` to the URL. The base output without additional setting using the Plainkit looks similar to this:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<data>
  <kirby content-extension="txt" version="3.1.0">
    <urls>
      <index host="localhost" path="" port="8888" scheme="http">http://localhost:8888</index>
      <base host="localhost" path="" port="8888" scheme="http">http://localhost:8888</base>
      <current host="localhost" path="" port="8888" scheme="http">http://localhost:8888</current>
      <assets host="localhost" path="/assets" port="8888" scheme="http">http://localhost:8888/assets</assets>
      <api host="localhost" path="/api" port="8888" scheme="http">http://localhost:8888/api</api>
      <media host="localhost" path="/media" port="8888" scheme="http">http://localhost:8888/media</media>
      <panel host="localhost" path="/panel" port="8888" scheme="http">http://localhost:8888/panel</panel>
    </urls>
    <request>
      <path/>
      <params/>
      <get/>
      <post/>
    </request>
    <session>
      <csrf>912ec803b2ce49e4a541068d495ab570912ec803b2ce49e4a541068d495ab570</csrf>
      <user-id>dac9630a</user-id>
    </session>
    <user id="dac9630a" language="de" role="admin">
      <username>Nils Hörrmann</username>
      <email alias="buero" domain="hananils.de" hash="9f8e362eca81e9723d9e699a45caf841">buero@hananils.de</email>
      <avatar/>
      <content/>
    </user>
  </kirby>
  <page id="home" slug="home" status="unlisted" template="default" uid="home" url="http://localhost:8888">
    <title>Home</title>
  </page>
  <pages>
    <page id="error" url="http://localhost:8888/error">
      <title>Error</title>
      <path url="error">
        <param template="default" title="Error">error</param>
      </path>
      <content>
        <text/>
      </content>
    </page>
    <page id="home" url="http://localhost:8888">
      <title>Home</title>
      <path url="home">
        <param template="default" title="Home">home</param>
      </path>
      <content>
        <text/>
      </content>
    </page>
  </pages>
  <site url="http://localhost:8888">
    <title>hana+nils · Büro für Gestaltung</title>
  </site>
</data>
```

Any object defined in a controller will be added to the XML output as well, e. g.:

```php
<?php

return function ($kirby) {
  return [
    'projects' => $kirby->collection('projects')
  ];
};
```

Known types are:

- `Kirby\Cms\App`, see [Kirby docs](https://getkirby.com/docs/reference/objects/kirby)
- `Kirby\Cms\Site`, see [Kirby docs](https://getkirby.com/docs/reference/objects/site)
- `Kirby\Cms\Pages`, see [Kirby docs](https://getkirby.com/docs/reference/objects/pages)
- `Kirby\Cms\Page`, see [Kirby docs](https://getkirby.com/docs/reference/objects/page)
- `Kirby\Cms\Files`, see [Kirby docs](https://getkirby.com/docs/reference/objects/files)
- `Kirby\Cms\File`, see [Kirby docs](https://getkirby.com/docs/reference/objects/file)
- `Kirby\Cms\Users`, see [Kirby docs](https://getkirby.com/docs/reference/objects/users)
- `Kirby\Cms\User`, see [Kirby docs](https://getkirby.com/docs/reference/objects/user)
- `DomDocument`, see [PHP docs](https://secure.php.net/manual/en/class.domdocument.php)
- `DomElement`, see [PHP docs](https://secure.php.net/manual/en/class.domelement.php)

## Included Elements

In order to customize the XML output, you have to create a new folder `definitions` inside your `site` folder. Similar to your blueprints, you can create definitions files for each template. The `default.yml` might look like this for example:

```yml
kirby: true
site: true
page: true
pages: false
```

The key equals the object name, let this be the default `$kirby`, `$site`, `$pages` or `$page` objects or a custom controller returning a `project` object. Setting an object to `true` will show the full content, setting an object to `false` will exclude its content.

The different type have different options:

### Kirby

The `kirby` object has the following options that either switch a node on or off:

```yml
kirby:
  attributes:
    - content-extension
    - language
    - multilang
    - version'
  urls: true
  request: true
  languages: false
  session: true
  user: true
```

The `attributes` object allows to creation of node attributes from kirby methods. The name of the attribute has to match the method’s name, see [the Kirby docs](https://getkirby.com/docs/reference/objects/kirby).

If you are working on a multilingual install, language settings are available:

```xml
<languages>
  <language code="de" direction="ltr" locale="de_DE.uft-8" url="http://localhost:8888/de" default="true" current="true">Deutsch</language>
  <language code="en" direction="ltr" locale="en_US.uft-8" url="http://localhost:8888/en">Englisch</language>
</languages>
```

### Site and Page

The `page` objects have the following options:

```yml
page:
  attributes:
    - id
    - url
  title: true
  path: true
  languages: true
  content: true
  files: true
  children: false
```

- The `attributes` object allows to creation of node attributes from page methods. The name of the attribute has to match the method’s name, see [the Kirby docs](https://getkirby.com/docs/reference/objects/page).
- The `title` object can be switched on and off by setting `true` or `false`.
- The `path` object is helpful to apply different templates base on the URL, it can be switched on and off by setting `true` or `false`.
- The `languages` object is available in multilingual installs and offers link for all languages, it can be switched on and off by setting `true` or `false`.
- The `content` subsetting takes and array of fields you'd like to include, e. g. `content: title, description, tags`.
- The `files` object can be switched on and off by setting `true` or `false`. It also takes additional settings, see below.
- The `children` object can be switched on and off by setting `true` or `false`. It also takes additional settings from the `pages` object, see below.

If you'd like to pass options to a specific fields, you can still include all other field by using a wildcard:

```yml
content:
    *: true
    description: markdown
```

#### Textarea field

The textarea field offers additional settings to define formatters to be applied to the field content. By default, Kirbytext is applied to all textareas:

```yml
content: title, description, tags
```

To get unformatted source text, the field has to be set to `unformatted` in the page definitions:

```yml
content:
  title: true
  description: unformatted
  tags: true
```

To get formatted output, the field has to be set to either `markdown` or `kirbytext`:

```yml
content:
  title: true
  description: markdown
  tags: true
```

To apply multiple formatters – like Kirbytext and SmartyPants –, the field also accepts a list of method:

```yml
content:
  title: true
  description:
    - kirbytext
    - smartypants
  tags: true
```

Methods are applied from top to bottom. [Any existing field method](https://getkirby.com/docs/reference/templates/field-methods) can be used so it's also possible to apply the `lower` method for example:

```yml
content:
  title: true
  description:
    - kirbytext
    - lower
  tags: true
```

#### Telephone field

When using a Composer setup, you can install the optional libphonenumber library. This will add additional phone number formats to the XML output (E164, national, international and RFC3966).

### Pages

The `pages` objects returns a collection of child pages. It takes the same settings as the `page` object which are applied to all children.

### Files

The `files` object returns a collection of files grouped by file template:

```yml
filename: true
meta: description, credits, focus
thumbs:
  - width: 600
    height: 400
    crop: left
  - width: 1200
    crop: file.focus
  - width: 1800
```

- The `filename` can be switched on and off by setting `true` or `false`.
- The `meta` object equals the `content` object of a page, see above.
- The `thumbs` object lets you setup image thumbnails. It takes a list of thumbs with optional settings for `width`, `height` and `crop` position. If your crop position is stored in a field, you can reference it using the syntax `file.fieldname`.

### Extending Included Elements

You can create subfolders for files and pages, `/site/definitions/files` and `/site/definitions/pages` to create subsets for settings you'd like to reuse across definitions files. This works like in blueprints.

#### Extending the default definitions:

```yml
extends: default
```

#### Extending file definitions

`/site/defintions/files/image.yml`

```yml
filename: true
meta: description, credits, focus
thumbs:
  - width: 600
    height: 400
    crop: left
  - width: 1200
    crop: fields.focus
  - width: 1800
```

`/site/definitions/project.yml`

```yml
page:
  title: true
  content: title, description, url, date, tags
  files:
  image:
    extends: files/image
```

#### Extending page definitions

`/site/definitions/pages/default.yml`

```yml
title: true
path: true
```

`/site/definitions/project.yml`

```yml
page:
  extends: default
```

### Helper Objects

The plugin bundles two helper objects to be used inside your controllers:

```php
<?php

return function ($kirby) {
  return [
    'datetime' => $kirby->collection('datetime'),
    'assets' => $kirby->collection('assets')
  ];
};
```

#### Date and Time

This object returns current date and time information as well as localized month and weekday names:

```xml
<datetime>
  <today day="14" iso="2019-02-14T20:24:38+00:00" month="2" offset="+0000" time="20:24" timestamp="1550175878" weekday="4" year="2019">2019-02-14</today>
  <languages>
    <language id="en" locale="en_ca">
      <months>
        <month abbr="Jan" id="1">January</month>
        <month abbr="Feb" id="2">February</month>
        <month abbr="Mar" id="3">March</month>
        <month abbr="Apr" id="4">April</month>
        <month abbr="May" id="5">May</month>
        <month abbr="Jun" id="6">June</month>
        <month abbr="Jul" id="7">July</month>
        <month abbr="Aug" id="8">August</month>
        <month abbr="Sep" id="9">September</month>
        <month abbr="Oct" id="10">October</month>
        <month abbr="Nov" id="11">November</month>
        <month abbr="Dec" id="12">December</month>
      </months>
      <weekdays>
        <weekday abbr="Sun" id="1">Sunday</weekday>
        <weekday abbr="Mon" id="2">Monday</weekday>
        <weekday abbr="Tue" id="3">Tuesday</weekday>
        <weekday abbr="Wed" id="4">Wednesday</weekday>
        <weekday abbr="Thu" id="5">Thursday</weekday>
        <weekday abbr="Fri" id="6">Friday</weekday>
        <weekday abbr="Sat" id="7">Saturday</weekday>
      </weekdays>
    </language>
  </languages>
</datetime>
```

The used locale can be set in the config:

```php
<?php

return [
  'locale' => 'en_CA.utf-8'
];
```

If you are working on a multilingual site, all languages you've set up will be available.

It’s possible to set the included node in the definition files:

```yml
datetime:
  today: true
  languages: false
```

#### Assets

This object return information about all files and folders inside the `/asset` folder:

```xml
<assets>
  <images>
    <file extension="png" mime="image/png" modified="1544107969">apple-touch-icon.png</file>
  </images>
  <scripts>
    <file extension="js" mime="text/plain" modified="1549887572">app.js</file>
  </scripts>
  <styles>
    <file extension="css" mime="text/plain" modified="1544531895">app.globals.css</file>
    <file extension="css" mime="text/plain" modified="1549887572">app.layouts.css</file>
  </styles>
</assets>
```

This information can be used to automatically generate links for scripts and styles:

```xslt
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="data">
  <html>
    <head>
      <xsl:apply-templates select="assets/styles/file[@extension = 'css']" />
    </head>
    <body>
      <h1>Hello!></h1>
    </body>
  </html>
</xsl:template>

<xsl:template match="assets/styles/file[@extension = 'css']">
  <link rel="stylesheet" type="text/css" href="{$assets}/{@modified}/{name(..)}/{.}" />
</xsl:template>

</xsl:stylesheet>
```

It's also possible to use the `modified` attribute to create timestamped links:

```ApacheConf
# in your .htaccess
RewriteRule ^assets/([0-9]+)/(.*)$ ./assets/$2 [L,NC]
```

# Templates

Templates are defined in the default `templates` and `snippets` folders. If you are using the Kirby Starterkit or Plainkit, please remove the default PHP templates and add a new `default.xsl` file. This works well as a starting point:

```xslt
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html"
  omit-xml-declaration="yes"
  media-type="text/html"
  encoding="utf-8"
  doctype-system="about:legacy-compat" />

<xsl:template match="data">
  <html>
    <head>
      <title>
        <xsl:value-of select="page/title" />
      </title>
    </head>
    <body>
      <h1>
        <xsl:value-of select="page/title" />
      </h1>
    </body>
  </html>
</xsl:template>

</xsl:stylesheet>
```

Template naming conventions follow the default Kirby scheme, see https://getkirby.com/docs/guide/templates/basics#naming-your-templates.

## Doctype

If you use `doctype-system="about:legacy-compat"` as in the example above, the plugin will automatically shorten the default doctype output `<!DOCTYPE html SYSTEM "about:legacy-compat">` to `<!DOCTYPE html>`.

# Shortcomings

The plugin is work in progress. We are extending it based on our own needs:

- Field support is limited to the core fields and a few additional fields we use ourselves.

Contributions are always welcome.
