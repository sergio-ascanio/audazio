<?php
/**
 * main class Audazio
 *
 * @version $Id: Audazio.php 1 2010-02-24 10:32:56Z sjsa $
 * @package Audazio
 */

// Config file with constants vars
include('cfg.php');

/**
 * Auzadio
 *
 * A little box of music.
 *
 * @package		Audazio
 * @author		Sergio Ascanio
 * @copyright		Copyright (c) 2009 - 2010, Sergio Ascanio (sergiojulio.com).
 * @license		Dual licensed under the MIT (MIT-LICENSE.txt) and GPL (GPL-LICENSE.txt) licenses.
 * @link		http://audazio.com
 * @since		Version 0.1
 * @filesource
 */

Class Audazio {

	public $lastfmApi;
	public $youtubeApi;
	public $limitTracks;
	public $topTracksRatio;
	public $cachePath;
	public $cacheTime;
	
	public $arrBands;
	public $countBand;
	public $countTrack;
	public $countAttempts;

	public $query;
	public $autoPlay;
	public $queryType;
	public $language;


	/**
	 * Constructor: set local public vars.
	 * Load the language file.
	 *
	 * @param	void
	 * @return 	avoid
	 */
	public function __construct() {

		$this->lastfmApi	= LASTFMAPI;
		$this->youtubeApi	= YOUTUBEAPI;
		$this->limitTracks 	= LIMITTRACKS;
		$this->topTracksRatio	= TOPTRACKSRATIO;
		$this->cachePath	= CACHEPATH;
		$this->cacheTime	= (60 * 60 * 24 * is_numeric(CACHETIME)? CACHETIME : 7); //one week
		
		$this->arrBands		= (isset($_SESSION['arrBands']))? $_SESSION['arrBands'] : 0;;
		$this->countBand	= (isset($_SESSION['countBand']))? $_SESSION['countBand'] : 0;
		$this->countTrack	= (isset($_SESSION['countTrack']))? $_SESSION['countTrack'] : 0;
		$this->countAttempts	= (isset($_SESSION['countAttempts']))? $_SESSION['countAttempts'] : 0;

		$this->query		= (isset($_COOKIE['au_query']))? $_COOKIE['au_query'] : "";
		$this->autoPlay		= (isset($_COOKIE['au_auto_play']))? $_COOKIE['au_auto_play'] : "";
		$this->queryType	= (isset($_COOKIE['au_query_type']))? $_COOKIE['au_query_type'] : 1;
		$this->language		= (isset($_COOKIE['au_language']))? $_COOKIE['au_language'] : "en";

		// 
		$this->themes		= array('black' => 'Black', 'silver' => 'Silver');
		$this->languages	= array('en' => 'English', 'es' => 'Español', 'it' => 'Italiano');

		// include language
		include('lang/'. $this->language .'.php');
		
	}


	/**
	 * player
	 *
	 * Generate player html string
	 *
	 * @access	public
	 * @param	void
	 * @return	string
	 */
	public function player()
	{

		if ($this->queryType == 1) {
			$opt_similar = "checked";
			$opt_tag = "";
		} else {
			$opt_similar = "";
			$opt_tag = "checked";
		}

		$cheked = ($this->autoPlay == 1)? "checked" : "";

		$html = '
		<div id="audazio">		
			<div id="audazio-header">
				<div id="audazio-header-search">
					<div class="audazio-left"><input type="text" name="au_q" id="au_q" value="'. $this->query .'"  class="audazio-find-input"  autocomplete="off" maxlength="30" >&nbsp;&nbsp;</div>
					<div class="ui-state-default ui-corner-all audazio-boton audazio-left"><a href="javascript:void(0)" class="ui-icon ui-icon-search au_search"></a></div>
					<div class="audazio-header-options audazio-left">
						<label title="'. SIMILAR_ARTIST .'" ><input name="au_type" id="au_type" type="radio" value="1" class="audazio-left" '. $opt_similar .'><a href="javascript:void(0)" class="audazio-state-default ui-icon-transferthick-e-w audazio-left"></a></label>
						<label title="'. MUSICAL_GENRE .'" ><input name="au_type" id="au_type" type="radio" value="2" class="audazio-left"'. $opt_tag .'><a href="javascript:void(0)" class="audazio-state-default ui-icon-tag audazio-left"></a></label>
					</div>
				</div>
				<div id="audazio-header-result"  style="display:none" >
				</div>
				<div id="audazio-header-buffer"  style="display:none">
					<span class="audazio-loading"> '. BUFFERING .'</span><br /><br /><br />
				</div>
				<div id="audazio-header-searching"  style="display:none">
					<span class="audazio-loading"> '. SEARCHING .'</span>
				</div>
			</div>
			<div id="audazio-body">
				<div id="audazio-body-separator"></div>
				<div id="audazio-body-panel">
					<div id="audazio-body-panel-image">
						<div id="audazio-img-album">
							<div class="audazio-cover"></div>
						</div>
					</div>
					<div id="audazio-body-panel-controls">
						<div id="audazio-body-panel-controls-info">
							<div id="audazio-body-panel-controls-curve">
								<div id="audazio-scroller-container"><div id="audazio-scroller"></div></div><div class="audazio-track-time audazio-left" id="audazio-videotime"></div>
							</div>
						</div>
						<div id="audazio-body-panel-controls-buttons">
							<div class="ui-state-default ui-corner-all audazio-boton audazio-left"><a href="javascript:void(0)"  class="ui-icon ui-icon-play au_play-pause" id="audazio-play-pause"></a></div>
							<div class="ui-state-default ui-corner-all audazio-boton audazio-left"><a href="javascript:void(0)" rel="1" class="ui-icon ui-icon-stop au_stop"></a></div>
							<div class="ui-state-default ui-corner-all audazio-boton audazio-left"><a href="javascript:void(0)" class="ui-icon ui-icon-seek-end au_next"></a></div>
							<div id="audazio-volume-slider" class="audazio-left"></div>
							<div class="ui-state-default ui-corner-all audazio-boton audazio-left"><a href="javascript:void(0)" rel="audazio-body-config" class="ui-icon ui-icon-wrench au_swiche"></a></div>
						</div>
					</div>
					<br class="audazio-clear"/>
				</div>
				<div id="audazio-body-video"></div>
				<div id="audazio-body-config" style="display:none">
					<span  class="au_setconf" >
						<label for="au_auto" ><input type="checkbox" name="au_auto" id="au_auto" value="1" class="check"  '. $cheked .' >'. REMEMBER_STATION .'</label>
					</span><br />
					&nbsp;&nbsp;'. THEME .': ';

		foreach ( $this->themes as $themes => $value ) {

			$html .= '		<label><input name="au_theme" id="au_theme_'. $themes .'" type="radio" value="'. $themes .'" class="au_settheme" >'. $value .'</label>';

		}


		$html .= '		<br />&nbsp;&nbsp;'. LANGUAGE .': ';

		foreach ( $this->languages as $languages => $value ) {

			$html .= '		<label><input name="au_language" id="au_theme_'. $languages .'" type="radio" value="'. $languages .'" class="au_changelang" >'. $value .'</label>';

		}

		$html .= '		<span id="au_opt_video" style="display:none"><br />&nbsp;&nbsp;'. SHOW_VIDEO .': <label><input name="au_video" id="au_video" type="radio" value="1" class="au_showvideo">'. YES .'</label> <label><input name="au_video" id="au_video" type="radio" value="0" class="au_showvideo" checked >'. NO .'</label></span>';

		$html .= '		</div>
			</div>
			<div id="audazio-footer" ></div>
		</div>';

		return $html;

	}


	/**
	 * response
	 *
	 * return the player status messages
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function response($code,$txt)
	{
		// search
		if ($code == 1) { // deprecated
			$html = '';
		} elseif ($code == 2) {// no match at lastf

			$html = '<div class="audazio-info audazio-left au_close_result" title="'. CLOSE .'" ><div class="audazio-state-default ui-icon-info audazio-left"></div>'. sprintf(NO_RESULTS,$txt) .'</div>';
		
		} elseif ($code == 3) {// radios ends

			$html = '<div class="audazio-info audazio-left au_close_result"  title="'. CLOSE .'"><div class="audazio-state-default ui-icon-info audazio-left"></div>'. RADIO_FINISHED .'</div><br /><br /><br />';
		
		} elseif ($code == 4) {//show radio station

			$html = '<div class="audazio-radio-tune">'. sprintf(RADIO_TUNED,$txt) .'</div>';

		} else {

			$html = '';

		}

		return $html;
	}


	/**
	 * getBandsList
	 *
	 * get from the web service the list of bands and return an array
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function getBandsList($str_query,$type_query)
	{ 

		//avoid 90&#39;s
		if( strlen( $str_query > 30) ) return "";

		$str_query = trim( strip_tags( strtolower( $str_query ) ) );
		$str_query = str_replace("&#39;", "'", $str_query);

		$type_query = ( is_numeric( $type_query ) && $type_query <= 2 )?  $type_query : 1;
		
		if ($type_query == "1") {

			$str = $str_query.$type_query;

			$xml = $this->findCache($str);

			if ($xml == "") { // similar bands

				$vars = "method=artist.getsimilar&artist=". $str_query ."&api_key=". $this->lastfmApi;

				$xml = $this->request('lastfm',$vars);

				$this->saveCache($str_query.$type_query,$xml);

				$xml = simplexml_load_string($xml);

			}

			if (!isset($xml->similarartists->artist) || $xml == "") {

				return "";
			}

			foreach ($xml->similarartists->artist as $xml) { 
		
				$array[] = $xml->name; 
			} 



		} elseif ($type_query == "2") { // tag

			$str = $str_query.$type_query;

			$xml = $this->findCache($str);

			if ($xml == "") {

				$vars = "method=tag.gettopartists&tag=". $str_query ."&api_key=". $this->lastfmApi;

				$xml = $this->request('lastfm',$vars);

				$this->saveCache($str_query.$type_query,$xml);

				$xml = simplexml_load_string($xml);

			}

			if(!isset($xml->topartists->artist) || $xml == ""){
				return "";
			}
	
			foreach ($xml->topartists->artist as $xml) { 
		
				$array[] = $xml->name; 

			} 

		}else{


		}

		// random shuffle( $array );

		return $array;
	}


	/**
	 * getTopTracksBand
	 *
	 * get from last.fm web service the list of most popular songs of the band
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	function getTopTracksBand($band)
	{

		$str = $band.'3';
		
		$xml = $this->findCache($str);

		if ($xml == "") {

			$vars = "method=artist.gettoptracks&artist=". $band ."&api_key=". $this->lastfmApi;

			$xml = $this->request('lastfm',$vars);

			$this->saveCache($band.'3',$xml);

			$xml = simplexml_load_string($xml);


		}

		if ( !isset($xml->toptracks) ) { 
			return ""; 
		}

		foreach ( $xml->toptracks->track as $xml) { 

			$data['track'][] = (string)$xml->name;
			$data['image'][] = (string)$xml->image[1];

		} 

		// count y rand temas aleatorios!
		$c = count($data['track']) - 1;
		$r = rand(0, $this->topTracksRatio); 


		// isset ?
		//if ( isset ($data['track'][$r]) ){

			// $array['track'] = $data['track'][$r]; 
			// $array['image'] = $data['image'][$r]; 

		//} else {

			$array['track'] = $data['track'][0]; 
			$array['image'] = $data['image'][0]; 

		//}

		return $array;
	}


	/**
	 * getVideos
	 *
	 * get from youtube web service the query search result
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function getVideos($band,$track)
	{
		$str = $band.$track.'4';
		
		$xml = $this->findCache($str);

		if ( !is_object($xml) ) {

			// formating youtube web service search: "band"+song name
			$params = "\"".urlencode($band)."\"+".urlencode($track);

			$xml = $this->request('youtube',$params);

			$this->saveCache($str,$xml);

			$xml = simplexml_load_string($xml);

		}


		if ( !isset($xml->entry) ) { 
			return ""; 
		}
		

		foreach ($xml->entry as $entry) { // 3 videos

			$media = $entry->children('http://search.yahoo.com/mrss/');
			
			$attrs = $media->group->player->attributes();

			$watch = $attrs['url']; 

			$array['title'][] = (string)$entry->title; 

			$array['code'][] = substr($watch, 31,-22);//;'wIGxKY5y6Mg'
		}
		

		return $array;
		
	} // 


	/**
	 * request
	 *
	 * make the web service request by cUrl
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	public function request($type,$params)
	{

		$curl = curl_init();

		if ($type == "lastfm") {
			$url = "http://ws.audioscrobbler.com/2.0/?";
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			$useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20091204 Firefox/3.5.0.1"; 
			curl_setopt($curl, CURLOPT_USERAGENT, $useragent);		
			$xml = curl_exec($curl);
		} else {
			// you can get IP and apply restriction=255.255.255.255
			$url = "http://gdata.youtube.com/feeds/api/videos?q=". $params ."&max-results=3&v=2&key=".$this->youtubeApi;
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			$xml = curl_exec($curl);
		}		

		logRequest( $url );

		curl_close($curl);

		return $xml;

	}


	/**
	 * findCache
	 *
	 * find a similar query into cache path before make a web service request
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function findCache($filename)
	{

		$filename = trim( strip_tags( strtolower( $filename ) ) );

		$file = $this->cachePath. md5( $filename );

		$time = time();

		//if ( file_exists($file) && ( ( filemtime($file) + $this->cacheTime ) < $time ) ) { //  

		if ( file_exists($file) && ( $time  < ( filemtime($file) + $this->cacheTime ) ) ) { //  
			
			$xml = simplexml_load_file($file);
		
		} else {

			$xml =  "";
		}

		return $xml;
	}


	/**
	 * saveCache
	 *
	 * save the web service query into text file
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	public function saveCache($filename,$xml)
	{
		$filename = trim( strip_tags( strtolower( $filename ) ) );

		$filename = $this->cachePath . md5( $filename );

		if ( file_put_contents( $filename, $xml ) ) {

			return true;

		} else {

			return false;

		} 
	}

}