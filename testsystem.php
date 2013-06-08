<?php
/**
 * Test system requirements
 */

// php 5
$error = false;

echo "My PHP Version is OK?: ";

$error = (version_compare(PHP_VERSION, '5.2') < 0) ? true : false;

echo ($error == false)? "Yes you have ".PHP_VERSION : "No your version is". PHP_VERSION ."and must be above to 5.2";

echo "<br/><br/>";

// CURL
echo "Is enabled CURL extension in my php server?: ";

$error = (!function_exists('curl_init')) ? true : false;

echo ($error == false)? "Yes, you have it enabled" : "No, you have not, make sure to enable it";

echo "<br/><br/>";

//  SimpleLoadXml
echo "Is enabled Mcrypt extension in my php server?: ";

$error = (!function_exists('mcrypt_get_iv_size')) ? true : false;

echo ($error == false)? "Yes, you have it enabled" : "No, you have not, make sure to enable it";

echo "<br/><br/>";

// SimpleLoadXml
echo "Is enabled SimpleLoadXml extension in my php server?: ";

$error = (!function_exists('simplexml_load_string')) ? true : false;

echo ($error == false)? "Yes, you have it enabled" : "No, you have not, make sure to enable it";

echo "<br/><br/>";

// file permision

include('app/cfg.php');

echo "Is writable ". CACHEPATH ."? ";

$error = (!is_writable( CACHEPATH )) ? true : false;

echo ($error == false)? "Yes, is writable" : "Is not writable";

echo "<br/><br/>";

// valid apis

