
$(document).ready(function(){

	$(".tags").jCarouselLite({
		btnPrev: ".up-tag",
		btnNext: ".down-tag",
		vertical: true,
		easing: "",//easeOutBounce
		speed: 1000,
		visible: 5,
		circular: true,
		scroll: 4, //TODO FIX BUG
		mouseWheel: true
	});

	$(".similar").jCarouselLite({
		btnPrev: ".up-similar",
		btnNext: ".down-similar",
		vertical: true,
		easing: "",//easeOutBounce
		speed: 1000,
		visible: 5,
		circular: true,
		scroll: 4, //TODO FIX BUG
		mouseWheel: true
	});


	$(".tab_content").hide(); 
	$(".tab li:first").addClass("tab_active").show(); 
	$(".tab_content:first").show(); 
	$(".tab li").click(function() {
		$(".tab li").removeClass("tab_active"); 
		$(".tab li").addClass("tab_inactive");
		$(this).removeClass("tab_inactive");
		$(this).addClass("tab_active"); 
		$(".tab_content").hide(); 
		var activeTab = $(this).find("a").attr("rel"); 
		$('#' + activeTab).fadeIn(); 
		return false;
	});
	
	
	$('.left').bind('mouseenter',function(){
		$('.btn-tag').fadeIn('fast');
	}).bind('mouseleave',function(){
		$('.btn-tag').fadeOut('fast');
	});
	
	$('.right').bind('mouseenter',function(){
		$('.btn-similar').fadeIn('fast');
	}).bind('mouseleave',function(){
		$('.btn-similar').fadeOut('fast');
	});	
	
	
	$('.download').bind('mouseenter',function(){
		$('.download-info').fadeIn('fast');
	}).bind('mouseleave',function(){
		$('.download-info').fadeOut('fast');
	});
	
});
