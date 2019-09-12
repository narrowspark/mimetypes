<h2 align="center">MIME Types</h2>
<h3 align="center">PHP MIME Types Guesser and extension mapper.</h3>
<p align="center">
    <a href="https://github.com/narrowspark/mimetypes/releases"><img src="https://img.shields.io/packagist/v/narrowspark/mimetypes.svg?style=flat-square"></a>
    <a href="https://php.net/"><img src="https://img.shields.io/badge/php-%5E7.2.0-8892BF.svg?style=flat-square"></a>
    <a href="https://travis-ci.org/narrowspark/mimetypes"><img src="https://img.shields.io/travis/rust-lang/rust/master.svg?style=flat-square"></a>
    <a href="https://codecov.io/gh/narrowspark/mimetypes"><img src="https://img.shields.io/codecov/c/github/narrowspark/mimetypes/master.svg?style=flat-square"></a>
    <a href="http://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square"></a>
</p>

built with [jshttp/mime-db][1].

Mime types mapping, the right way.
------------
This library uses [jshttp/mime-db][1] as its default mapping which aggregates data from multiple sources and creates a single ```db.json``` making it the most complete mapping.
- [IANA](http://www.iana.org/assignments/media-types/media-types.xhtml)
- [Apache](http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)
- [Nginx](http://hg.nginx.org/nginx/file/tip/conf/mime.types)

Installation
------------

```bash
composer require narrowspark/mimetypes
```

Use
------------

This mime type guesser has support for all OS supported guesser.

```php
<?php
    use Narrowspark\MimeType\MimeType;
    
    // You can register custom guessers by calling the register() method
    MimeType::register('CustomGuesser');

    return MimeType::guess('image.gif'); // returns image/gif
```

You looking for the full mime type array? Just use:

```php
<?php
    use Narrowspark\MimeType\MimeTypesList;

    return MimeTypesList::MIMES; // returns array
```

If you like to use the build in php mime type guesser, just use:

```php
<?php
    use Narrowspark\MimeType\MimeTypeFileInfoGuesser;
    use Narrowspark\MimeType\MimeTypeFileBinaryGuesser;
    use Narrowspark\MimeType\MimeTypeExtensionGuesser;
    use Narrowspark\MimeType\MimeTypeFileExtensionGuesser;

    
    // Inspecting the file using finfo and relies on magic db files.
    return MimeTypeFileInfoGuesser::guess('image.gif'); // returns image/gif
    // Inspecting the file using file -b --mime
    return MimeTypeFileBinaryGuesser::guess('image.gif'); // returns image/gif
    // Inspecting the extension using mime type list
    return MimeTypeExtensionGuesser::guess('gif'); // returns image/gif
    // Inspecting the file using mime type list
    return MimeTypeFileExtensionGuesser::guess('image.gif'); // returns image/gif
```

Contributing
------------

If you would like to help take a look at the [list of issues](http://github.com/narrowspark/mimetypes/issues) and check our [Contributing](CONTRIBUTING.md) guild.

> **Note:** Please note that this project is released with a Contributor Code of Conduct. By participating in this project you agree to abide by its terms.

License
---------------

The Narrowspark mimetypes is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

[1]: http://github.com/jshttp/mime-db
