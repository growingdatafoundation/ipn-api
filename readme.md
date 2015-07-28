* Base API: http://api.ala.org.au/
* old repository: https://github.com/NatureNinjas/whatgrowshere

# Install
=========

* Install dependencies

```
composer install
```

* Rename `example.ApiConfig.php` to `ApiConfig.php` and fill in your Api Keys.

Recomended: The `ApiConfig.php` should be moved out of the `htdocs`. Update the `require_once` directive for this file the index.php accordingly.

* if you want to use the Mongo cache you currently need to install MongoDB on your server and install the PHP Mongo drivers

# ALA Occurences aggregator API

```
/ala.occurences.php?include=ala.species&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5
```

Params:

 * `include`: modules to include (see below), multiple dataset can becombined as a comm-separated list
 * `bname`: binomial name search (starts with), currently interpreted as [genus](https://en.wikipedia.org/wiki/Genus)
 * `lat`: latitude
 * `lon`: longitude
 * `rad`: radius
 * `dump` (optional, debug!): pretty-dumps json for debugging

 `include` modules:
 * ala.species: fetches details for each returned species of occurence search

## ala.occurences

 * returns lists of occurrence counts of queried specimen for a given location


Example response with `include=ala.species`

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

## ala.species

 * returns information for a species

```
/ala.species.php?bname=Acacia+penninervis
```

Params:

 * `taxon_name`: taxonomy name
 * `dump` (optional, debug!): pretty-dumps json for debugging

Example response:

```javascript
{
    "ala": {
        "species": {
            "_status": 200,
            "species": {
                "Acacia penninervis": {
                    "guid": "urn:lsid:biodiversity.org.au:apni.taxon:298661",
                    "common_name": "Hickory",
                    "isAustralian": "recorded",
                    "image": "http:\/\/bie.ala.org.au\/repo\/1051\/187\/1874162\/raw.jpg",
                    "thumbnail": "http:\/\/bie.ala.org.au\/repo\/1051\/187\/1874162\/thumbnail.jpg",
                    "densityMap": {
                        "australia": "http:\/\/biocache.ala.org.au\/ws\/density\/map?q=Acacia+penninervis"
                    }
                }
            }
        }
    }
}
```

## ala.explore.groups

```
/ala.explore.groups.php?lat=-34.928726&lon=138.59994&radius=5&dump=1
```
* returns counts of all species groups for a given location

Params:

 * `lat`: latitude
 * `lon`: longitude
 * `rad`: radius
 * `dump` (optional, debug!): pretty-dumps json for debugging

Example response:

```
{
    "ala": {
        "explore": {
            //all species
            "count": 105858,
            //sorted by groups
            "groups": {
                " Animals": 99633,
                " Mammals": 341,
                " Birds": 93738,
                " Reptiles": 135,
                " Amphibians": 498,
                " Fish": 173,
                " Molluscs": 148,
                " Arthropods": 4558,
                " Crustaceans": 13,
                " Insects": 3759,
                " Plants": 5405,
                " Bryophytes": 29,
                " Gymnosperms": 15,
                " Ferns And Allies": 21,
                " Angiosperms": 5304,
                " Monocots": 1674,
                " Dicots": 3630,
                " Fungi": 116,
                " Chromista": 18,
                " Protozoa": 8,
                " Bacteria": 0,
                " Algae": 13
            },
            "_status": 200
        }
    }
}
```

# Mongo Cache

 A simple Mongo cache api is in development and is located in `\Api\Cache.php`. Currently this Api requires a local Mongo server which you need to set up.

 TODO:

  * expiry
  * remote Mongo
  * hook up to `ala.species`
