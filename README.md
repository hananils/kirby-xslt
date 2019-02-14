# XSLT for Kirby 3

This plugin enables XSLT templating in your Kirby install by generating XML for all front-end pages. Based on the page template (blueprint), you can setup custom nodes and specify included element.

# Data

By default, the plugin provides XML nodes for the `$kirby`, `$site`, `$pages` and `$page` objects. As soon as you are logged in, you can view the data of any page by appending `?data` to the URL. The base output without additional setting using the Plainkit looks similar to this:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<data>
    <kirby content-extension="txt" version="3.0.1">
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
            <query/>
        </request>
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
        <page id="error" slug="error" status="unlisted" template="default" uid="error" url="http://localhost:8888/error">
            <title>Error</title>
            <path url="error">
                <param template="default" title="Error">error</param>
            </path>
            <content>
                <text/>
            </content>
        </page>
        <page id="home" slug="home" status="unlisted" template="default" uid="home" url="http://localhost:8888">
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

Any object defined in a controller, will be added to the XML output as well, e. g.:

```php
<?php

return function ($kirby) {
    return [
        'projects' => $kirby->collection('projects')
    ];
};
```

Known types are:

-   `Kirby\Cms\App`, see [Kirby docs](https://getkirby.com/docs/reference/objects/kirby)
-   `Kirby\Cms\Site`, see [Kirby docs](https://getkirby.com/docs/reference/objects/site)
-   `Kirby\Cms\Pages`, see [Kirby docs](https://getkirby.com/docs/reference/objects/pages)
-   `Kirby\Cms\Page`, see [Kirby docs](https://getkirby.com/docs/reference/objects/page)
-   `Kirby\Cms\Files`, see [Kirby docs](https://getkirby.com/docs/reference/objects/files)
-   `Kirby\Cms\File`, see [Kirby docs](https://getkirby.com/docs/reference/objects/file)
-   `Kirby\Cms\Users`, see [Kirby docs](https://getkirby.com/docs/reference/objects/users)
-   `Kirby\Cms\User`, see [Kirby docs](https://getkirby.com/docs/reference/objects/user)
-   `DomDocument`, see [PHP docs](https://secure.php.net/manual/en/class.domdocument.php)
-   `DomElement`, see [PHP docs](https://secure.php.net/manual/en/class.domelement.php)

## Included Elements

In order to customize the XML output, you have to create a new folder `definitions` inside your `site` folder. Similar to your blueprints, you can create definitions files for each template. The `default.yml` might look like this for example:

```yml
kirby: true
site: true
page: true
pages: false
```

The key equals the object name, let this be the default `$kirby`, `$site`, `$pages` or `$page` objects or a custom controller returning a `project` object. Setting an object to `true` will show the full content, setting an object to `false` will exclude its content.

The different type have different subsettings:

### Kirby

The `kirby` object can only be switched on and off by either setting it to `true` or `false`.

### Site and Page

The `page` objects know the following subsettings:

```yml
page:
    title: true
    path: true
    content: true
    files: true
    children: false
```

-   The `title` object can be switched on and off by setting `true` or `false`.
-   The `path` object is helpful to apply different templates base on the URL, it can be switched on and off by setting `true` or `false`.
-   The `content` subsetting takes and array of fields you'd like to include, e. g. `content: title, description, tags`.
-   The `files` object can be switched on and off by setting `true` or `false`. It also takes additional settings, see below.
-   The `files` object can be switched on and off by setting `true` or `false`. It also takes additional settings from the `pages` object, see below.

There is one field with additional settings: the textarea field accepts format settings `unformatted`, `markdown` or `kirbytext`. The default is `kirbytext`. If you'd like to change the default, the `content` object needs to be changed from:

```yml
content: title, description, tags
```

to:

```yml
content:
    title: true
    description: unformatted
    tags: true
```

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
      crop: fields.focus
    - width: 1800
```

-   The `filename` can be switched on and off by setting `true` or `false`.
-   The `meta` object equals the `content` object of a page, see above.
-   The `thumbs` object lets you setup image thumbnails. It takes a list of thumbs with optional settings for `width`, `height` and `crop` position. If your crop position is stored in a field, you can reference it using the syntax `fields.fieldname`.

### Extending Included Elements

You can create subfolders for files and pages, `/site/definitions/files` and `/site/definitions/pages` to create subsets for settings you'd like to reuse across defintions files. This works like in blueprints.

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

# Templates
