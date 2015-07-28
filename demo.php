<?php
$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$url .= $_SERVER['SERVER_NAME'];
$url .= $_SERVER['REQUEST_URI'];

define ('BaseUrl', dirname($url));

function themeLink($href){
?>
    <td><pre><?php print $href; ?></pre></td>
    <td><a href="<?php print BaseUrl.$href; ?>">json</a></td>
    <td><a href="<?php print BaseUrl.$href.'&dump=1'; ?>">dumper</a></td>
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
        </style>
    </head>

    <body>
        <h1>Examples</h1>
        <table class="pure-table pure-table-horizontal">
            <tbody>
                <tr>
                    <th>Occurences<br><small>location</small></th>
                    <td>Occurences of <em>Acacia</em> in Adelaide (short)</td>
                    <?php themeLink('/ala.occurences.php?bname=Acacia&lat=-34.928726&lon=138.59994&radius=5'); ?>
                </tr>
                <tr>
                    <th>Occurences + species<br><small>location</small></th>
                    <td>Occurences of <em>Acacia</em> in Adelaide <strong>plus</strong> species short</td>
                    <?php themeLink('/ala.occurences.php?include=ala.species&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5'); ?>
                </tr>

                <tr>
                    <th>Occurences + species, groups<br><small>location</small></th>
                    <td>Occurences of <em>Acacia</em> in Adelaide <strong>plus</strong> species short and species groups</td>
                    <?php themeLink('i/ala.occurences.php?include=ala.species,ala.explore.groups&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5'); ?>
                </tr>
                <tr>
                    <th>Species</th>
                    <td>Species short info for <em>Acacia penninervis</em></td>
                    <?php themeLink('/ala.species.php?taxon_name=Acacia+penninervis'); ?>
                </tr>
                <tr>
                    <th>Species details</th>
                    <td>Species detailed info for <em>Acacia penninervis</em></td>
                <?php themeLink('/ala.species.details.php?guid=urn:lsid:biodiversity.org.au:apni.taxon:298661'); ?>
                </tr>
                <tr>
                    <th>Count Species groups<br><small>location</small></th>
                    <td>The number species per group for a location (e.g. Plants, Birds, etc)</td>
                <?php themeLink('/ala.explore.groups.php?include=ala.details&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5'); ?>
                </tr>
            </tbody>
        </table>
    </body>
</html>
