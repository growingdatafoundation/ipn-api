<?php
/**
 * Dispatcher
 * https://www.flickr.com/services/api/explore/flickr.photos.search
 */

@include_once ('Connector.php');
@include_once ('Helpers.php');

$error = true;

//controller vars vars
$tag = '';
$maxresults = 5;

// botanical name: required
if (!isset($_GET["bname"])) {
	die('Invalid parameters: `bname` required.');
}
$tag = htmlspecialchars($_GET["bname"]);

// template: required
if (!isset($_GET["template"])) {
	die('Invalid parameters: `template` required.');
}
$template = htmlspecialchars($_GET["template"]);

//maxresults: optional 
$maxresults = (!empty($_GET["maxresults"]) && (int) $_GET["maxresults"] > 0) ? $_GET["maxresults"] : $maxresults;

/**
 * Set CORS headers, prepare for future xAPI implementation
 */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS');
header('Access-Control-Allow-Headers: Origin,Content-Type,Authorization,Accept,X-Experience-API-Version,If-Match,If-None-Match');
header('Content-Type: text/html; charset=utf-8');

/**
 * Call templates
 */
$templatesSplit = explode(',', $template);
foreach($templatesSplit as $templateSingle){
	
	$templateFile = 'template.'.$templateSingle.'.php';
	if(!is_file($templateFile)){
		die('Error: template not found.');
	}
	require($templateFile);
}//foreach
