<?php
/**
 * misc functions
 *
 * @version $Id: functions.php 1 2010-02-24 10:32:56Z sjsa $
 * @package Audazio
 */

function get_date(){
	$day = date("d");
	$month = date("m");
	$year = date("Y");
	$hour = date("H");
	$minutes = date("i");
	$seconds = date("s");
	$date = $day."/".$month."/".$year." - ".$hour.":".$minutes.":".$seconds;
	return $date;
}

set_error_handler('logError');
function logError($errno, $errstr, $errfile, $errline){
	$ddf = fopen( CACHEPATH .'error.log','a'); // you should change the file name
	switch ($errno) {
	    case 1:$errno = "ERROR";
	    break;
	    case 2:$errno = "WARNING";
	    break;
	    case 4:$errno = "PARSE ERROR";
	    break;
	    case 8:$errno = "NOTICE";
	    break;
	    case 16:$errno = "CORE ERROR";
	    break;
	    case 32:$errno = "CORE WARNING";
	    break;
	}
	fwrite($ddf,"[".get_date()."][$errno: $errstr ][Line: $errline ~ File: $errfile]\r\n");
	fclose($ddf);
}
function logRequest( $event ){
	$ddf = fopen( CACHEPATH .'request.log','a'); // you should change the file name
	fwrite($ddf,"[".get_date()."][Event: ". $event ." ]\r\n");
	fclose($ddf);
}


// Misc Functions

function arrayToJson($array){
	if ($array != "") {
		$array = array_reverse($array);
	} else {
		return "{}";
	}
	$c = count($array);
	$str = "";
	for ($i = 0; $i <= $c - 1; $i++) {
		$str = $array[$i].",".$str;
	}
	$str = substr($str, 0,-1);
	$str = "{".$str."}";
	return $str;
}

function jsonToArray($str){
	$str = substr($str, 1, -1);
	if ($str == "") return "";
	$vector = explode(",",$str);
	return $vector;
}

function encode($value){
	$key = MCRYPTKEY;
	if(!$value){return false;}
	$key = $sys_key;
	$text = $value;
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
	return trim(base64_encode($crypttext)); //encode
}

function decode($value){
	$key = MCRYPTKEY;
	if(!$value){return false;}
	$key = $sys_key;
	$crypttext = base64_decode($value); //decode
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
	return trim($decrypttext);
}