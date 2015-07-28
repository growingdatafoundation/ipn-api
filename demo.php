<?php
$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$url .= $_SERVER['SERVER_NAME'];
$url .= $_SERVER['REQUEST_URI'];

define ('BaseUrl', dirname($url));

function themeLink($href){
?>
    <a href="<?php print BaseUrl.$href; ?>"><?php print $href; ?></a>
<?php
}
?>
<html>
    <head>
    </head>

    <body>
        <p>
            Search for occurences of <em>Acacia</em> in Adelaide (short)
        </p>
        <p>
            <?php themeLink('/ala.occurences.php?bname=Acacia&lat=-34.928726&lon=138.59994&radius=5&dump=1'); ?>
        </p>

        <p>
            Search for occurences of <em>Acacia</em> in Adelaide <strong>and</strong> species short info with <em>include </em> param
        </p>
        <p>
            <?php themeLink('/ala.occurences.php?include=ala.species&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5&dump=1'); ?>
        </p>

        <p>
            Species short info for <em>Acacia penninervis</em>
        </p>
        <p>
            <?php themeLink('/ala.species.php?taxon_name=Acacia+penninervis&dump=1'); ?>
        </p>

        <p>
            Species detailed info for <em>Acacia penninervis</em>
        </p>
        <p>
            <?php themeLink('/ala.species.details.php?guid=urn:lsid:biodiversity.org.au:apni.taxon:298661&dump=1'); ?>
        </p>
    </body>
</html>
