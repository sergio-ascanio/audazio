<?php
/**
 * this default this file referenced in js/audazio.js Line: 26 
 * you can change it, for example: var auUrlbase = 'http://localhost/mysite/audazio/app/ajax.php';
 *
 * @version $Id: ajax.php 1 2010-02-24 10:32:56Z sjsa $
 * @package Audazio
 */

if ( isset( $_GET['jsoncallback'] ) ) {

	include('app/ajax.php');
	
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Audazio - Tune it</title>
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="img/favicon.png" /> 
<script src="http://www.google.com/jsapi?autoload=%7B%22modules%22%3A%5B%7B%22name%22%3A%22jquery%22%2C%22version%22%3A%221.4.4%22%7D%2C%7B%22name%22%3A%22jqueryui%22%2C%22version%22%3A%221.8.9%22%7D%2C%7B%22name%22%3A%22swfobject%22%2C%22version%22%3A%222.2%22%7D%5D%7D" type="text/javascript" language="javascript"></script>
<script src="js/audazio.js" type="text/javascript" language="javascript"></script>
</head>

<body onLoad="$(document).audazio({position:'absolute'})">

<h1>Audazio</h1>

</body>
</html>
