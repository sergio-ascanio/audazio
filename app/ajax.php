<?php
/**
 * controler file for ajax requests
 *
 * @version $Id: ajax.php 1 2010-02-24 10:32:56Z sjsa $
 * @package Audazio
 */

// start session
session_start();

// Some functions
require('functions.php');

// Main Class
include('Audazio.php');


$Audazio = New Audazio;

// Ajax Requests

if ( isset($_GET['action']) && $_GET['action'] == "search") { //

	$query = $_GET['q'];
	$type = $_GET['type'];

	$bands = $Audazio->getBandsList($query,$type);

	if ( is_array($bands) ) { 

		$_SESSION['arrBands']		 = arrayToJson($bands);
		$_SESSION['countBand']		 = 0;
		$_SESSION['countAttempts']	 = 1;
		$_SESSION['countTrack']		 = 1;

		$infoTrack = $Audazio->getTopTracksBand($bands[0]); 

		$array = $Audazio->getVideos($bands[0],$infoTrack['track']); // otro error puede ocurrir not found

		if ( is_array($array) ) {

			$_SESSION['youtubeCodes']  = arrayToJson($array['code']);

			$vector['error_cod']	 = "0";
			$vector['error_msg']	 = ""; 
			$vector['title']	 = $array['title'][0];
			$vector['code']		 = $array['code'][0];
			$vector['image']	 = $infoTrack['image'];

			if ( $Audazio->autoPlay == 1 ) {

				setcookie("query",encode(trim( strip_tags( strtolower( $query ) ) ) ),time() + 60 * 60 * 24 * 7,"/");

			}

		} else { // is empty no result in lastfm

			$vector['error_cod'] = "1";
			$vector['error_msg'] = ""; 

		}

		echo $_GET['jsoncallback'] . '(' . json_encode($vector) . ')'; 
		exit;

	} else {

		$vector['error_cod'] = "2";
		$vector['error_msg'] = $Audazio->response(2,$query); 

		echo $_GET['jsoncallback'] . '(' . json_encode($vector) . ')';
		exit;

	}

} elseif ( isset($_GET['action']) && $_GET['action'] == "next" ) { // next track

	if ( $Audazio->countTrack >= $Audazio->limitTracks ) {

		$vector['error_cod'] = "3";
		$vector['error_msg'] = $Audazio->response(3,''); // radio is over;

		echo $_GET['jsoncallback'] . '(' . json_encode($vector) . ')'; // first attempt
		exit;
	}

	$bands 				= jsonToArray($_SESSION['arrBands']);
	$_SESSION['countBand'] 		= $_SESSION['countBand']  + 1;
	$_SESSION['countTrack'] 	= $_SESSION['countTrack'] + 1;
	$_SESSION['countAttempts'] 	= 0;

	$infoTrack = $Audazio->getTopTracksBand($bands[$_SESSION['countBand']]); // 

	$array = $Audazio->getVideos($bands[$_SESSION['countBand']],$infoTrack['track']); // 

	if ( is_array($array) ) {

		$_SESSION['youtubeCodes'] = arrayToJson($array['code']);	
	
		$vector['error_cod'] = "0";
		$vector['error_msg'] = ""; 
		$vector['title'] = $array['title'][$_SESSION['countAttempts']];
		$vector['code'] = $array['code'][$_SESSION['countAttempts']];
		$vector['image'] = $infoTrack['image'];

	} else  {

		$vector['error_cod'] = "1";
		$vector['error_msg'] = "";  
		// player will jumps netx track 
	}

	echo $_GET['jsoncallback'] . '(' . json_encode($vector) . ')';
	exit;

} elseif ( isset($_GET['action']) && $_GET['action'] == "error" ) { 
	
	$_SESSION['countAttempts'] = $_SESSION['countAttempts']  + 1;

	$codes = jsonToArray($_SESSION['youtubeCodes']);	

	if ( $_SESSION['countAttempts'] == 3 ) {// si supera 3 next banda salto de banda por next via player
						// || isset($codigos[$_SESSION['sys_track_intentos']] algo asi
						// aqui hay peos hasta solo 2

		$vector['error_cod'] = "4"; //  next band
		$vector['error_msg'] = ""; // 

	} else {

		if ( isset($codigos[$_SESSION['countAttempts']]) && $codigos[$_SESSION['countAttempts']] != "" ) {

			$vector['error_cod'] = "0";
			$vector['error_msg'] = ""; 
			$vector['codige'] = $codigos[$_SESSION['countAttempts']];

		} else {

			$vector['error_cod'] = "4"; // next band
			$vector['error_msg'] = ""; 

		}

	}

	echo $_GET['jsoncallback'] . '(' . json_encode($vector) . ')'; 
	exit;

} elseif ( isset($_GET['action']) && $_GET['action'] == "response" ) {

	$txt = $_GET['txt'];
	$code = $_GET['code'];

	$vector['txt'] =  $Audazio->response($code,$txt);

	echo $_GET['jsoncallback'] . '(' . json_encode($vector) . ')';
	exit;

} elseif ( isset($_GET['action']) && $_GET['action'] == "init" ) { 

	$vector['html'] =  $Audazio->player();
	echo $_GET['jsoncallback'] . '(' . json_encode($vector) . ')';
	exit;

} else {

}