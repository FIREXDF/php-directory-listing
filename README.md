# php-directory-listing
Generate a directory listing HTML file for the current directory.

Works without dependencies, contained in a single file. Indented as a polyfill for static sites.

## Usage

```bash
wget https://raw.githubusercontent.com/caendesilva/php-directory-listing/master/directory-listing.php -O directory-listing.php
php directory-listing.php
```

An index.html file will be created in the current directory. Warning: this will overwrite any existing index.html file.

## Attributions

Highly inspired by the default Apache directory listings, but recreated from scratch as a PHP exercise.

Icons are in the public domain and provided by
[www.ideocentric.com](http://web.archive.org/web/20170708074904/http://www.ideocentric.com/technology/articles/title/apache-icons)