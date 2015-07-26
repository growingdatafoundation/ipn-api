* Base API: http://api.ala.org.au/
* old repository: https://github.com/NatureNinjas/whatgrowshere

# Install
=========

1. Install dependencies

```
composer install
```

2. Rename `example.ApiConfig.php` to `ApiConfig.php` and fill in your Api Keys.

Recomended: The `ApiConfig.php` should be moved out of the `htdocs`. Update the `require_once` directive for this file the index.php accordingly.


## ALA Occurences aggregator API

```
/ala.occurences.php?include=ala.species&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5&dump=1
```
params:

 * `include`: modules to include (see below), multiple dataset can becombined as a comm-separated list
 * `bname`: binomial name search (starts with), currently interpreted as [genus](https://en.wikipedia.org/wiki/Genus)
 * `lat`: latitude
 * `lon`: longitude
 * `rad`: radius
 * `dump` (optional, debug!): pretty-dumps json for debugging

 `include` modules:
 * ala.species: fetches details for each returned species of occurence search

## ala.occurences

 * returns lists of occurrence counts of queried specimen for location


Example json with `include=ala.species`

```javascript
{
    "ala": {
        // ala.occurences core module
        "occurences": {
            // total records found
            "count": 107,
            // curl response status 
            "_status": 200, 
            // occurences count by species: common name
            // note, that many species have no common name
            "common_name": {
                "Acacia Hedge": 4,
                "Australian Golden Wattle": 13,
                    "Black Wattle": 16,
                    ...
                },
                // occurences count by species: taxonomy name
                "taxon_name": {
                    "Acacia": 1,
                    "Acacia acinacea": 12,
                    ....
            }
        },
        // ala.species aggregator
        "species": { 
            //curl response status
            "_status": 200,
            // species module - species are indexed by taxonomy name
            "species": {
                "Acacia": {
                    // ala guid for further lookups
                    "guid": "urn:lsid:biodiversity.org.au:apni.taxon:295861",
                    "common_name": "Wattle",
                    // native species
                    "isAustralian": "recorded",
                    "image": "http:\/\/bie.ala.org.au\/repo\/1009\/24\/250623\/raw.jpg",
                    "thumbnail": "http:\/\/bie.ala.org.au\/repo\/1009\/24\/250623\/thumbnail.jpg"
                    // density map module
                    "densityMap": {
                        // Australia - png image
                        "australia": "http:\/\/biocache.ala.org.au\/ws\/density\/map?q=Acacia"
                    }
                },
                "Acacia acinacea": {
                    "guid": "urn:lsid:biodiversity.org.au:apni.taxon:295874",
                    "common_name": "Gold-dust Acacia",
                    "isAustralian": "recorded",
                    "image": "http:\/\/bie.ala.org.au\/repo\/1124\/193\/1933412\/raw.jpg",
                    "thumbnail": "http:\/\/bie.ala.org.au\/repo\/1124\/193\/1933412\/thumbnail.jpg"
                    "densityMap": {
                        "australia": "http:\/\/biocache.ala.org.au\/ws\/density\/map?q=Acacia+acinacea"
                    }
                },
                ....
        }
    }
}
```
