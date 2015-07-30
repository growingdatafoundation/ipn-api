<?php
$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$url .= $_SERVER['SERVER_NAME'];
$url .= $_SERVER['REQUEST_URI'];

define ('BaseUrl', dirname($url));

$wktBioRegion = file_get_contents('./data/example.wkt');
$wktInnesNP = file_get_contents('./data/innes-national-park.wkt');
function themeLink($href){
?>
    <td><a href="<?php print BaseUrl.$href; ?>">json</a></td>
    <td><a href="<?php print BaseUrl.$href.'&dump=1'; ?>">dump</a></td>
    <td><pre><?php print $href; ?></pre></td>
<?php
}
function wktform($href, $wkt){
?>
    <td>
        <form id="<?php print uniqid('wktForm-1-');?>" method="post" action="<?php print BaseUrl.$href; ?>">
        <input name="wkt" type="hidden" value="<?php print $wkt; ?>">
        <input type="submit" class="button-xsmall pure-button" value="json">
        </form>
    </td>
    <td>
        <form id="<?php print uniqid('wktForm-2-');?>" method="post" action="<?php print BaseUrl.$href.'&dump=1'; ?>">
        <input name="wkt" type="hidden" value="<?php print $wkt; ?>">
        <input type="submit" class="button-xsmall pure-button" value="dump">
        </form>
    </td>
    <td><pre><?php print $href; ?></pre></td>
<?php
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WGH API demo</title>
        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
        <style>
            tr{
                font-size: 12px;
            }
            td a{
                font-weight: bold;
            }
            th{
                text-align: left;
            }
            th small{
                color: #666666;
                font-weight: normal;
            }
            td pre{
                max-width: 500px;
                overflow: auto;
            }
        </style>
    </head>

    <body>
        <h1>Examples</h1>
        <table class="pure-table pure-table-horizontal">
            <tbody>
                <tr>
                    <th>Occurences<br><small>location(lat,lon,radius)</small></th>
                    <th><small>GET</small></th>
                    <td>Occurences of <em>Acacia</em> in Adelaide (short)</td>
                    <?php themeLink('/ala.occurences.php?bname=Acacia&lat=-34.928726&lon=138.59994&radius=5'); ?>
                </tr>
                <tr>
                    <th>Occurences + species<br><small>region(wkt polygon)</small></th>
                    <th><small>POST</small></th>
                    <td>Occurences of <em>Acacia</em> around Frazer Island <strong>plus</strong> species short</td>
                    <?php wktForm('/ala.occurences.php?include=ala.species&bname=anceps', $wktBioRegion); ?>
                </tr>
                <!--
                <tr>
                    <th>Occurences + species<br><small>region(wkt polygon)</small></th>
                    <th><small>POST</small></th>
                    <td>
                        Same as above, but including <em>ala.explore.groups</em><br>
                        The module "ala" log respond "_status": 400 and no data, as groups are not supporting wkt polygons.
                        <small><pre>"ala":{ ... "explore":{"groups":{"_status":400,"_errors":["No polygon requests supported"],"count":false,"groups":[]}}}}</pre></small>
                    </td>
                    <?php wktForm('/ala.occurences.php?include=ala.species,ala.explore.groups&bname=Acacia', $wktBioRegion); ?>
                </tr>
                -->
                <tr>
                    <th>Occurences + species<br><small>location(lat,lon,radius)</small></th>
                    <th><small>GET</small></th>
                    <td>Occurences of <em>Acacia</em> in Adelaide <strong>plus</strong> species short</td>
                    <?php themeLink('/ala.occurences.php?include=ala.species&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5'); ?>
                </tr>
                <tr>
                    <th>Occurences + species, groups<br><small>location(lat,lon,radius)</small></th>
                    <th><small>GET</small></th>
                    <td>Occurences of <em>Acacia</em> in Adelaide <strong>plus</strong> species short and species groups</td>
                    <?php themeLink('/ala.occurences.php?include=ala.species,ala.explore.groups&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5'); ?>
                </tr>
                <tr>
                    <th>Species</th>
                    <th><small>GET</small></th>
                    <td>Species short info for <em>Acacia penninervis</em></td>
                    <?php themeLink('/ala.species.php?taxon_name=Acacia+penninervis'); ?>
                </tr>
                <tr>
                    <th>Species details</th>
                    <th><small>GET</small></th>
                    <td>Species detailed info for <em>Acacia penninervis</em></td>
                    <?php themeLink('/ala.species.details.php?guid=urn:lsid:biodiversity.org.au:apni.taxon:298661'); ?>
                </tr>
                <tr>
                    <th>Count Species groups<br><small>location(lat,lon,radius)</small></th>
                    <th><small>GET</small></th>
                    <td>The number species per group for a location (e.g. Plants, Birds, etc)</td>
                    <?php themeLink('/ala.explore.groups.php?include=ala.details&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5'); ?>
                </tr>
                <tr>
                    <th>Total and distinct species count for a specific group<br><small>location(lat,lon,radius)</small></th>
                    <th><small>GET</small></th>
                    <td>
                        Count total and distinct species of a specific group for a location (e.g. Plants, Birds, etc). Allowed values:
                        <small><pre>"Animals", "Mammals", "Birds", "Reptiles", "Amphibians", "Fish", "Molluscs", "Arthropods", "Crustaceans", "Insects", "Plants", "Bryophytes", "Gymnosperms", "FernsAndAllies", "Monocots", "Dicots", "Fungi", "Chromista", "Protozoa", "Bacteria", "Algae"</pre></small>
                    </td>
                    <?php themeLink('/ala.explore.group.php?group_name=Birds&lat=-34.928726&lon=138.59994&radius=5'); ?>
                </tr>
            </tbody>
        </table>
    </body>
</html>
