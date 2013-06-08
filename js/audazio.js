/* ------------------------------------------------------------------------
	Class: audazio
	Use: a little box of music
	Author: Sergio Ascanio (http://www.sergiojulio.com)
	Version: 0.0.1
------------------------------------------------------------------------- */

$(document).ready(function(){

	// Themes
	auLoadfile( auUrlbase + 'css/black.css', 'css', 'black' );
	auLoadfile( auUrlbase + 'css/silver.css', 'css', 'silver');

	// comprobar version

	// Create the doom object
	$('body').prepend('<div id="audazio-content" ></div><div id="audazio-video" style="height:0;  display:block; position:relative; z-index:10"><div id="au_ytapiplayer"></div></div>');

	var params = { allowScriptAccess: "always", bgcolor: "#cccccc" };
	var atts = { id: "audazio_player" };
	swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1&playerapiid=ytplayer", "au_ytapiplayer", "0", "0", "8", null, null, params, atts);

});

//
var auUrlbase = 'http://localhost/audazio/';

// cookie path

(function($) {
	$.fn.audazio = function(settings) {
		// global Variables
		var ytplayer = document.getElementById("audazio_player");
	
		// Global elements

		//vars
		settings = jQuery.extend({
			theme: 'black', /* black / silver */
			position: 'fixed', /* absolute / fixed */
			language: 'en',
			callback: function(){}
		}, settings);
		
		// construct
		$(this).each(function(){
			init();
			return false;
		});
	
		function init(){


			if( $('#audazio').is(":visible") ){ 
				return;
			}

			$.getJSON(auUrlbase + "?action=init&jsoncallback=?",
			function( data ){
				$('#audazio-content').html(data.html);
				$(function() {
					$('.ui-state-default').hover(
						function(){ $(this).addClass('ui-state-hover'); }, 
						function(){ $(this).removeClass('ui-state-hover'); }
					);
					$("#audazio-volume-slider").slider({
						value:100,
						min: 0,
						max: 100,
						step: 10,
						slide: function(event, ui) {
							vol = ui.value;
							setVolume(vol);
						}
					});
				});

				$("#audazio").draggable();

				$jScroller.add("#audazio-scroller-container","#audazio-scroller","left",3);
				$jScroller.start();

				// checked option
				var cookie_theme = auReadCookie('au_theme');
				cookie_theme = (cookie_theme == '' )? settings.theme : cookie_theme;
				$('#au_theme_' + cookie_theme).attr("checked", "true");


				// checked option
				var cookie_lg = auReadCookie('au_language');


				cookie_lg = (cookie_lg == '' )? settings.language : cookie_lg;
				$('#au_theme_' + cookie_lg).attr("checked", "true");


				//
				$('#audazio').center(settings.position); 
				$('#audazio').fadeIn('slow');

	
				//setVolume(100);

				// events click
				$('.au_search').bind('click',function(){ search(); return false; });

				$('.au_play-pause').bind('click',function(){ playPause(); return false; });
				$('.au_stop').bind('click',function(){
					var n = $(this).attr('rel');
					stop(1); 
					return false; 
				});
				$('.au_next').bind('click',function(){ nextTrack(); return false; });
				$('.au_swiche').bind('click',function(){
					var obj = $(this).attr('rel');
					swiche(obj); 
					return false; 
				});
				$('.au_setconf label').bind('click',function(){ setConf(); return true; });
				$('.au_settheme').bind('change',function(){
					var value = $(this).attr('value');
					auSetCSS(value); 
					return true; 
				});
				$('.au_changelang').bind('click',function(){
					var value = $(this).attr('value');
					changeLang(value); 
					return true; 
				});
				$('.au_showvideo').bind('change',function(){
					showVideo();
					return true; 
				});
				$('.au_tag').bind('click',function(){
					var q = $(this).attr('title');
					stop(1); 
					$('#au_q').attr('value',q);
					$('input:radio[name=au_type]')[1].checked = true;
					search()
					return false; 
				});
				$('.au_similar').bind('click',function(){
					var q = $(this).attr('title');
					stop(1); 
					$('#au_q').attr('value',q);
					$('input:radio[name=au_type]')[0].checked = true;
					search()
					return false; 
				});

				$('#au_q').focus();

				if(!$.browser.msie){
					// move swf to inside player
					// fix top move -20px ?
					$("#audazio-video").prependTo("#audazio-body-video");


//id="audazio-video" style="height:0; top: 20px; display:block; position:relative; z-index:10"

					$('#au_opt_video').show(); getCurrentTime
				}

				var auto =  $("input[name='au_auto']:checked").val();
				if (auto == 1) {

					//setTimeout('this.search()', 3000);
					// delay 1 second
					setTimeout(function(){search();}, 1000);
				}


			});
		};
	
		function search(){

			// init ytplayer
			onYouTubePlayerReady();

			var q = $('#au_q').val();

			if (q == "") {
				return ;
			}

			$('#audazio-header-search').hide();
			$('#audazio-header-searching').fadeIn('slow');

			var type =  $("input[name='au_type']:checked").val();

			var auto =  $("input[name='au_auto']:checked").val();
			if (auto == 1) {
				setConf();
			}

			$.getJSON(auUrlbase + '?action=search&q='+ q + '&type=' + type + '&jsoncallback=?',
			function( msg ){

				track(msg);

			});

		};
		
		//

		function nextTrack(){
			var state = getPlayerState();
			if ( state == 1 || state == 0 || state == 3 ) {
				return next();
			} else {
				return false;
			}
		}

		function next(){

			$('#audazio-header-searching').fadeIn('slow');
			stop(0);

			$.getJSON(auUrlbase + '?action=next&jsoncallback=?',
			function( msg ){

				track(msg);

			});
		};

		function response(code,txt){

			$('#audazio-header-search').hide();
			$('#audazio-header-result').fadeIn('slow');

			$.getJSON(auUrlbase + '?action=response&code=' + code + '&txt=' + txt + '&jsoncallback=?',
			function( msg ){

				$('#audazio-header-searching').hide();
				$('#audazio-header-result').html(msg.txt);


			});

		};

		function closeResult(){
			$('#audazio-header-result').hide();
			$('#audazio-header-searching').hide();
			$('#audazio-header-result').html('');
			$('#audazio-header-search').fadeIn('slow');
		};

		function track(msg){

			var error_cod = msg.error_cod;
			var error_msg = msg.error_msg;

			if ( error_cod != "0" ) {

				if( error_cod == 1 ){

					next();

				} else if( error_cod == 2 ) {

					$('#audazio-header-searching').hide();
					$('#audazio-header-result').html(error_msg);
					$('#audazio-header-result').fadeIn('fast');

				} else if ( error_cod == 3 ) {

					// ojo
					ytplayer.stopVideo();

					$('#audazio-header-searching').hide();
					$('#audazio-header-result').html(error_msg);
					$('#audazio-header-result').fadeIn('fast');
					$('#audazio-scroller').html('');

					$('#audazio-img-album').css({'background-image':'url()'}); 
					$('#audazio-play-pause').removeClass('ui-icon-pause');
					$('#audazio-play-pause').addClass('ui-icon-play');

					//closeResult();
					//$('#audazio-header-result').hide();

					$('#audazio-body-panel-controls-curve').css({ backgroundPosition:"-232px 0px"});
					$('#audazio-videotime').html('');



				} else if ( error_cod == 4 ) {
					next();
				}

			}else{
				var title = msg.title;
				var code = msg.code;
				var image = msg.image;

				// effects 
				$('#audazio-scroller').html(title);

				var q = $('#au_q').val();

				// title radio
				response(4,q);
 
				// cd cover
				$('#audazio-img-album').css({'background-image':'url(' + image + ')'}); 

				loadNewVideo(code, 0); 
			}

			$('.au_close_result').bind('click',function(){ closeResult(); return false; });

		};

		function error(){

			alert('Error Youtube');

			$.getJSON(auUrlbase + '?action=error&jsoncallback=?',
			function( msg ){

				var code = msg.code;
				var error_cod = msg.error_cod;

				if ( error_cod == 4 ) {
					nextTrack();
				} else if ( error_cod == 0 ) {
					loadNewVideo(code, 0); 
				} else {
					// stop ?
				}

			});
		};

		function playPause(){

			var state = getPlayerState();
			if ( state == 1 ) {
				pause();
				$('#audazio-play-pause').removeClass('ui-icon-pause');
				$('#audazio-play-pause').addClass('ui-icon-play');
			} else if ( state == 2 ) {
				play();
				$('#audazio-play-pause').removeClass('ui-icon-play');
				$('#audazio-play-pause').addClass('ui-icon-pause');
			} else {

			}
		};

		function changeLang(lang){

			stop(1);
			$('#audazio').hide();

			if(!$.browser.msie){
				$("#audazio-video").prependTo("body");
				var playerObj = document.getElementById("audazio_player");
				playerObj.height = 0;
				playerObj.width = 0;
			}

			var date = new Date();
			date.setTime(date.getTime()+(7*24*60*60*1000)); //7 days
			var expires = "; expires="+date.toGMTString();
			//Set Cookie
			document.cookie = "au_language="  + lang + expires + "; path=/";
			// Reload
			init();
		};

		function setConf(){

			var type =  $("input[name='au_type']:checked").val();
			var auto =  $("input[name='au_auto']:checked").val();

			var q = $('#au_q').val(); // ''

			auto = (auto == 1)? 1: 0;
			q = (auto == 1)? q : "";
 
			var date = new Date();
			date.setTime(date.getTime()+(7*24*60*60*1000)); //7 days
			var expires = "; expires="+date.toGMTString();
			
			//Set Cookie
			document.cookie = "au_auto_play="  + auto + expires + "; path=/";
			document.cookie = "au_query=" + q + expires + "; path=/";
			document.cookie = "au_query_type="  + type + expires + "; path=/";

		};

		function showVideo(){

			var opt = $("#au_video:radio:checked").val();

			var playerObj = document.getElementById("audazio_player");

			if( opt == 1){
				$("#audazio-body-video").css("padding","5px 20px 0 20px");
				$("#audazio-body-video").height(195);
				playerObj.height = 195;
				playerObj.width = 318;

			}else{
				$("#audazio-body-video").css("padding","0");
				$("#audazio-body-video").height(0);
				playerObj.height = 0;
				playerObj.width = 0;
			}
			swiche('audazio-body-config'); 
		}

		// FUNCTIONS YOUTUBE PLAYER

		function onYouTubePlayerReady() {

			var ytplayer = document.getElementById("audazio_player");

			setInterval(updateytplayerInfo, 250);

			updateytplayerInfo();

			ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
			ytplayer.addEventListener("onError", "onPlayerError");

		};

		onytplayerStateChange = function (newState) {

			if ( newState == 0 ) {
				return nextTrack();
			}
			//alert(newState)
			// unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5). 

			if(newState == 3){
				$('#audazio-header-searching').hide();
				$('#audazio-header-result').hide();
				$('#audazio-header-buffer').fadeIn('slow');
			}else if(newState == 1 || newState == 2  ){
				$('#audazio-header-buffer').hide();
				$('#audazio-header-result').fadeIn('slow');
			}else{
				$('#audazio-header-buffer').hide();
				$('#audazio-header-result').hide();
			}


		};

		onPlayerError = function(errorCode) {
			error();
		};

		function updateytplayerInfo() {

			$('#audazio-videotime').html(getCurrentTime());
		};

		function loadNewVideo(id, startSeconds) {

			if ( ytplayer ) {

				$('#audazio-play-pause').removeClass('ui-icon-play');
				$('#audazio-play-pause').addClass('ui-icon-pause');

				ytplayer.loadVideoById(id, parseInt(startSeconds));
			}
		};

		function cueNewVideo(id, startSeconds) {
			if (ytplayer) {
				ytplayer.cueVideoById(id, startSeconds);
			}
		};

		function play() {
			if ( ytplayer ) {
				ytplayer.playVideo();
			}
		};

		function pause() {
			if ( ytplayer ) {
				ytplayer.pauseVideo();
			}
		};

		function stop(n) {

			var state = getPlayerState();

			if ( state == -1 || state == 0 ) {
				return;
			}

			if ( ytplayer ) {

				if(n == 1){
					closeResult();
					$('#audazio-header-result').hide();
				}

				// reset player scroller time image
				$('#audazio-scroller').html('');
				$('#audazio-body-panel-controls-curve').css({ backgroundPosition:"-232px 0px"});
				$('#audazio-videotime').html('');
				//  cd cover
				$('#audazio-img-album').css({'background-image':'url()'}); 
				$('#audazio-play-pause').removeClass('ui-icon-pause');
				$('#audazio-play-pause').addClass('ui-icon-play');

				ytplayer.stopVideo();
			}

		};

		function getPlayerState() {
/*
			if ( ytplayer ) {
				return ytplayer.getPlayerState(); // ojo cuando arranca da error poner try
			}*/


				if ( ytplayer ) {
					try{
						return ytplayer.getPlayerState();
					}
					catch(err){
						return -1;
					}
				}

		};

		function getCurrentTime() {

			if (ytplayer) {

				var max = getDuration();
				var weight = 232;


				try{
					var time = ytplayer.getCurrentTime();
				}
				catch(err){
					return secondsToTime( 0 );
				}


				var pos = weight-parseInt(( weight * parseInt( time ))/max);
				$('#audazio-body-panel-controls-curve').css({ backgroundPosition:"-"+pos+"px 0px"});
			
				return secondsToTime( ytplayer.getCurrentTime() );
			}
		};

		function getDuration () {

			if (ytplayer) {
				try{
					return ytplayer.getDuration();
				}
				catch(err){
					return -0.000025;
				}
			}

		};

		function getStartBytes() {
			if (ytplayer) {
				return ytplayer.getVideoStartBytes();
			}
		};

		function setVolume(newVolume) {
			if (ytplayer) {
				ytplayer.setVolume(newVolume);
			}
		};

		function getVolume() {
			if (ytplayer) {
				return ytplayer.getVolume();
			}
		};

	};
	

	function secondsToTime(secs){

		if (secs == -0.000025) return '';

		var hours = Math.floor(secs / (60 * 60));
		
		var divisor_for_minutes = secs % (60 * 60);
		var minutes = Math.floor(divisor_for_minutes / 60);

		var divisor_for_seconds = divisor_for_minutes % 60;
		var seconds = Math.ceil(divisor_for_seconds);
		
		if (minutes < 10) {
			minutes = '0' + minutes;
		}

		if (seconds < 10) {
			seconds = '0' + seconds;
		}

		if (seconds == 60) {
			seconds = '00';
			minutes = 1 + parseFloat(minutes);
			minutes = '0' + minutes;
		}

		var track = minutes + ':' + seconds;

		if (track == '00:00' ) return ''; // no

		return track;
	};

	jQuery.fn.center = function (position) {
		this.css("position",position); //
		this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
		this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
		return this;
	};

	function swiche(obejeto){
		$('#'+obejeto).slideToggle("slow");
	};


})(jQuery);

function auLoadfile(filename, filetype, title){

	if (filetype=="js"){ //if filename is a external JavaScript file

		var fileref=document.createElement('script');
		fileref.setAttribute("type","text/javascript");
		fileref.setAttribute("src", filename);

	}else if (filetype=="css"){ //if filename is an external CSS file

		var cookie_theme = auReadCookie('au_theme');


		var fileref=document.createElement("link");
		if(cookie_theme == title){
			fileref.setAttribute("rel", "stylesheet");


		}else if(cookie_theme == ''){

			fileref.setAttribute("rel", "stylesheet");
			auSetCSS(title);


		}else{
			fileref.setAttribute("rel", "alternate stylesheet");
		}
		fileref.setAttribute("title", title);
		fileref.setAttribute("type", "text/css");
		fileref.setAttribute("href", filename);
	}

	if (typeof fileref!="undefined")

	document.getElementsByTagName("head")[0].appendChild(fileref);

}

function auSetCSS(title) {
	
	var i, a, main;
	for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
		if(a.getAttribute("rel").indexOf("style") != -1 &&
			a.getAttribute("title")) {
			a.disabled = true;
		if(a.getAttribute("title") == title)
				a.disabled = false;
		}
	}
	
	
	var date = new Date();
	date.setTime(date.getTime()+(7*24*60*60*1000)); //7 dias
	var expires = "; expires="+date.toGMTString();
	
	//Set Cookie
	document.cookie = "au_theme=" + title + expires + "; path=/";
}

function auReadCookie(cookieName) {
		var theCookie=""+document.cookie;
		var ind=theCookie.indexOf(cookieName);
		if (ind==-1 || cookieName=="") return ""; 
		var ind1=theCookie.indexOf(';',ind);
		if (ind1==-1) ind1=theCookie.length; 
		return unescape(theCookie.substring(ind+cookieName.length+1,ind1));
}

/*
 * jScroller 0.4 - Autoscroller PlugIn for jQuery
 *
 * Copyright (c) 2007 Markus Bordihn (http://markusbordihn.de)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * $Date: 2009-06-18 20:00:00 +0100 (Sat, 18 Jul 2009) $
 * $Rev: 0.4 $
 */
$jScroller={info:{Name:"ByRei jScroller Plugin for jQuery",Version:0.4,Author:"Markus Bordihn (http://markusbordihn.de)",Description:"Next Generation Autoscroller"},config:{obj:[],refresh:120,regExp:{px:/([0-9,.\-]+)px/}},cache:{timer:0,init:0},add:function(parent,child,direction,speed,mouse){if($(parent).length&&$(child).length&&direction&&speed>=1){$(parent).css({overflow:'hidden'});$(child).css({position:'absolute',left:0,top:0});if(mouse){$(child).hover(function(){$jScroller.pause($(child),true)},function(){$jScroller.pause($(child),false)})}$jScroller.config.obj.push({parent:$(parent),child:$(child),direction:direction,speed:speed,pause:false})}},pause:function(obj,status){if(obj&&typeof status!=='undefined'){for(var i in $jScroller.config.obj){if($jScroller.config.obj[i].child.attr("id")===obj.attr("id")){$jScroller.config.obj[i].pause=status}}}},start:function(){if($jScroller.cache.timer===0&&$jScroller.config.refresh>0){$jScroller.cache.timer=window.setInterval($jScroller.scroll,$jScroller.config.refresh)}if(!$jScroller.cache.init){$(window).blur($jScroller.stop);$(window).focus($jScroller.start);$(window).resize($jScroller.start);$(window).scroll($jScroller.start);$(document).mousemove($jScroller.start);if($.browser.msie){window.focus()}$jScroller.cache.init=1}},stop:function(){if($jScroller.cache.timer){window.clearInterval($jScroller.cache.timer);$jScroller.cache.timer=0}},get:{px:function(value){var result='';if(value){if(value.match($jScroller.config.regExp.px)){if(typeof value.match($jScroller.config.regExp.px)[1]!=='undefined'){result=value.match($jScroller.config.regExp.px)[1]}}}return result}},scroll:function(){for(var i in $jScroller.config.obj){if($jScroller.config.obj.hasOwnProperty(i)){var obj=$jScroller.config.obj[i],left=Number(($jScroller.get.px(obj.child.css('left'))||0)),top=Number(($jScroller.get.px(obj.child.css('top'))||0)),min_height=obj.parent.height(),min_width=obj.parent.width(),height=obj.child.height(),width=obj.child.width();if(!obj.pause){switch(obj.direction){case'up':if(top<=-1*height){top=min_height}obj.child.css('top',top-obj.speed+'px');break;case'right':if(left>=min_width){left=-1*width}obj.child.css('left',left+obj.speed+'px');break;case'left':if(left<=-1*width){left=min_width}obj.child.css('left',left-obj.speed+'px');break;case'down':if(top>=min_height){top=-1*height}obj.child.css('top',top+obj.speed+'px');break}}}}}};
