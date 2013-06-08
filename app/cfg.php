<?php
/**
 * config file
 *
 * @version $Id: cfg.php 1 2010-02-24 10:32:56Z sjsa $
 * @package Audazio
 */
date_default_timezone_set('America/La_Paz');

/*
|
| You need a last fm api key, you can get it here: http://www.last.fm/api
|
*/
define('LASTFMAPI', '');

/*
|
| You need also a youtube api key: http://code.google.com/apis/youtube/overview.html
|
*/
define('YOUTUBEAPI', '');

/*
|
| how many songs do you want the player play?
|
*/
define('LIMITTRACKS', 20);

/*
|
| all bands has many songs, TOPTRACKSRATIO sets the limit of songs that will be selected
|
*/
define('TOPTRACKSRATIO', 10);

/*
|
| this var set the full server path where the cache files will be saved, make sure the folder permissions has 0777
|
*/
define('CACHEPATH', './app/cache/');

/*
|
| in how many days would you like the lifetime of cache files?
|
*/
define('CACHETIME', 30); // days

/*
|
| you need a some alpha numeric word for encrypt the cookies strings
|
*/
define('MCRYPTKEY',  'your_key');
