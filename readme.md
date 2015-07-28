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
            // curl response status
            "_status": 200,
            // total records found
            "count": 107,
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
            // curl response status
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
/ala.species.php?taxon_name=Acacia+penninervis
```

Params:

 * `taxon_name`: taxonomy name
 * `dump` (optional, debug!): pretty-dumps json for debugging

### Example response:

```javascript
{
    "ala": {
        "species": {
            // curl response status
            "_status": 200,
            "species": {
                "Acacia penninervis": {
                    // taxonomy concept id: see ala.species.details
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

## ala.species.details

```javascript
// Acacia penninervis
/ala.species.details.php?guid=urn:lsid:biodiversity.org.au:apni.taxon:298661&dump=1
```
* rturns details about a species: description, images, conservation status, links classifications etc

Params:

 * `guid`: The guid for the taxon concept, returned by **ala.species** lookup
 * `dump` (optional, debug!): pretty-dumps json for debugging

### Example response:

```javascript
{
    "ala": {
        "species": {
            "details": {,
                // curl response status
                "_status": 200,
                "name": "Acacia penninervis",
                "isAustralian": true,
                // properties are string or null
                "classification": {
                    "kingdom": "Plantae",
                    "phylum": "Charophyta",
                    "class_": "Equisetopsida",
                    "order": "Fabales",
                    "family": "Fabaceae",
                    "genus": "Acacia",
                    "species": "Acacia penninervis"
                ...
                },
                // array of common_names
                "commonNames": [
                    "Hickory",
                    "Hickory Wattle",
                    "Mountain Hickory",
                    "Native Hickory",
                    "Mountain Hickory"
                ...
                ],
                // conservation statuses by region empty array if no data, seea  populated example below
                "conservationStatuses": [
                ],
                "descriptions": {
                    "Description": "Erect or spreading shrub or tree mostly 2\u20138 m high; bark finely or deeply fissured, dark grey; branchlets \u00b1 terete, glabrous, sometimes pruinose.",
                    "Distribution": "Widespread, especially in inland divisions.",
                    "Flowering Season": "Flowers throughout year"
                ...
                },
                // empty array if no data
                "references": [
                    {
                        "source": "Wikipedia",
                        "title": "Acacia penninervis",
                        "url": "http:\/\/en.wikipedia.org\/wiki\/Acacia_penninervis"
                    }
                ...
                ],
                // empty array if no data
                "images": [
                    {
                        "source": "Encyclopedia of Life",
                        "contentType": "image\/jpeg",
                        "thumbnail": "http:\/\/bie.ala.org.au\/repo\/1051\/187\/1874162\/thumbnail.jpg",
                        "title": "Acacia penninervis"
                    },
                ...
                ]
            }
        }
    }
}
```

### conservation data status example:

example of pouplated array for an endangered species: Macrotis lagotis (Bilby): `/ala.species.details.php?guid=urn:lsid:biodiversity.org.au:afd.taxon:3814d122-c95f-467f-a3a2-2b269931b74f&dump=1`

```javascript
{
    "ala": {
        "species": {
            "details": {
                // ... other properties ...

                "conservationStatuses": {
                    "New South Wales": {
                        "system": "Threatened Species Conservation Act 1995",
                        "status": "Presumed Extinct",
                        "rawCode": "E4"
                    },
                    "Australia": {
                        "system": "The Environment Protection and Biodiversity Conservation Act 1999",
                        "status": "Vulnerable",
                        "rawCode": null
                    },
                    "Queensland": {
                        "system": "Nature Conservation Act 1992",
                        "status": "Endangered",
                        "rawCode": "E"
                    },
                    "South Australia": {
                        "system": "National Parks and Wildlife Act 1972",
                        "status": "Vulnerable",
                        "rawCode": "V"
                    },
                    "Northern Territory": {
                        "system": "Territory Parks and Wildlife Conservation Act 2000",
                        "status": "Vulnerable",
                        "rawCode": null
                    },
                    "Western Australia": {
                        "system": "Wildlife Conservation Act 1950",
                        "status": "Vulnerable",
                        "rawCode": "VU"
                    }
                },

                // ... other properties ...
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

### Example response:

```javascript
{
    "ala": {
        "explore": {
            // curl response status
            "_status": 200,
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
            }
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
