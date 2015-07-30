wkt from map:

http://arthur-e.github.io/Wicket/sandbox-gmaps3.html

kml 2 wkt
http://geospatialconversions.azurewebsites.net/

http://biocache.ala.org.au/ws/occurrences/search?q=qid:1438131283873
http://biocache.ala.org.au/ws/occurrences/search?q=1438131283873
http://biocache.ala.org.au/ws/occurrences/search?qid=1438131283873

works:
http://biocache.ala.org.au/ws/occurrences/search?q=genus:Macropus&wkt=POLYGON((138.4332275390625%20-34.68022889363676,138.900146484375%20-34.68022889363676,138.900146484375%20-35.04981365914965,138.4332275390625%20-35.04981365914965,138.4332275390625%20-34.68022889363676))


example:
http://bl.ocks.org/djtfmartin/8fe594f0ead33e1472d4

http://biocache.ala.org.au/ws/occurrences/search?q={q}&fq={fq}
geodata: see "latLong"

```
{
      "raw_catalogNumber": "4190514.00",
      "taxonConceptID": "urn:lsid:biodiversity.org.au:afd.taxon:43639327-3bc9-43ae-861b-e0f982b1a8b2",
      "eventDate": 1033344000000,
      "occurrenceYear": 1009843200000,
      "vernacularName": "Eastern Grey Kangaroo",
      "taxonRank": "species",
      "taxonRankID": 7000,
      "classs": "MAMMALIA",
      "genusGuid": "urn:lsid:biodiversity.org.au:afd.taxon:9e6a0bba-de5b-4465-8544-aa8fe3943fab",
      "speciesGuid": "urn:lsid:biodiversity.org.au:afd.taxon:43639327-3bc9-43ae-861b-e0f982b1a8b2",
      "stateProvince": "Victoria",
      "coordinateUncertaintyInMeters": 100.0,
      "basisOfRecord": "HumanObservation",
      "lga": "Indigo - Pt A",
      "dataResourceName": "Victorian Biodiversity Atlas",
      "hasUserAssertions": "false",
      "speciesGroups": [
        "Animals",
        "Mammals",
        "Animals",
        "Mammals"
      ],
      "geospatialKosher": "true",
      "taxonomicKosher": "true",
      "collector": "Martin O'Brien",
      "raw_scientificName": "Macropus giganteus",
      "raw_basisOfRecord": "Human observation",
      "raw_vernacularName": "Eastern Grey Kangaroo",
      "latLong": "-36.12,146.61",
      "point1": "-36,147",
      "point01": "-36.1,146.6",
      "point001": "-36.12,146.61",
      "point0001": "-36.12,146.61",
      "point00001": "-36.12,146.61",
      "namesLsid": "Macropus giganteus|urn:lsid:biodiversity.org.au:afd.taxon:43639327-3bc9-43ae-861b-e0f982b1a8b2|Eastern Grey Kangaroo|ANIMALIA|MACROPODIDAE",
      "multimedia": [
        "None"
      ],
      "collectors": [
        "O'Brien, M. Martin"
      ],
      "order": "DIPROTODONTIA",
      "rowKey": "dr1097|4190514.00",
      "assertions": [
        "assumedPresentOccurrenceStatus",
        "decimalLatLongConverted",
        "countryInferredByCoordinates"
      ],
      "species": "Macropus giganteus",
      "kingdom": "ANIMALIA",
      "decimalLongitude": 146.61,
      "decimalLatitude": -36.12,
      "uuid": "97d9bdc8-b1c9-4218-8b5f-164626507b9c",
      "scientificName": "Macropus giganteus",
      "dataResourceUid": "dr1097",
      "genus": "Macropus",
      "phylum": "CHORDATA",
      "left": 402783,
      "right": 402784,
      "family": "MACROPODIDAE",
      "country": "Australia",
      "month": "09",
      "year": "2002"
    },
    ```
