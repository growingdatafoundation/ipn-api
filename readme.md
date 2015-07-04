* Base API: http://api.ala.org.au/
* old repository: https://github.com/NatureNinjas/whatgrowshere

```
// simple request - fast

/api?bname=Acacia&template=images.binomial

// combine multipe data in one request - slow

/api?bname=Acacia&template=images.binomial,ala.birds,wikipedia

```

# Install
=========

1. Install dependencies

```
composer install
```

2. Rename `example.ApiConfig.php` to `ApiConfig.php` and fill in your Api Keys.

Recomended: The `ApiConfig.php` should be moved out of the `htdocs`. Update the `require_once` directive for this file the index.php accordingly.
