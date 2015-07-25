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


# ala Aggregators

```
/json.php?return=ala.occurences,ala.details&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5
```
params:

 * `return`: datasets to return (see folder \Api\Ala), multiple dataset can becombined as a comm-separated list
 * `bname`: binomial name search (starts with), currently interpreted as [genus](https://en.wikipedia.org/wiki/Genus)
 * `lat`: latitude
 * `lon`: longitude
 * `rad`: radius
 * `dump` (optional, debug!): pretty-dumps json for debugging

## ala.occurences

 * returns lists of occurrence counts of queried specimen for location

```
{
    "ala": { //api
        "occurences": { //api module (e.g. ala.occurences
            "totalRecords": 107,
            "status": 200, //curl response status ala
            "results": {
                "common_name": {
                    "Acacia Hedge": 4,
                    "Australian Golden Wattle": 13,
                    "Black Wattle": 16,
                    ...
                },
                "taxon_name": {
                    "Acacia": 1,
                    "Acacia acinacea": 12,
                    "Acacia baileyana": 2,
                    ...
                }
            }
        }
    }
}
```
