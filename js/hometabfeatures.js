$(document).ready(function() {
	
	
	/*var preload = $(".close_arrow");
	preload.css({ position: "absolute", visibility: "hidden", display: "block" });
	var cla = preload.outerWidth();
	preload.css({ position: "", visibility: "", display: "" });*/
	//console.log($(".close_arrow").outerWidth()+' '+$('#hometabfeatures .content_bloc .close_arrow').outerWidth());
	$('#hometabfeatures .content_bloc .close_arrow').css('left', $('#hometabfeatures .content_bloc').width()/2-$(".close_arrow").outerWidth()/2);
	
	if($(window).width() > 640)
	//$('#hometabfeatures .description_content').css('overflow', 'scroll');
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
 // some code..
	}
	else{
		//$( '#hometabfeatures .description_bloc .description_content' ).niceScroll().hide();
	}
	
	
	
	
	
	$('#hometabfeatures .description_image img').load(function(){
		
		if($(window).width() > 801)
	$('#hometabfeatures .description_content, #hometabfeatures .description_content table, #hometabfeatures .description_content_right, #hometabfeatures .description_content_right table').height($('#hometabfeatures .description_image img').height()-30);
	else
	$('#hometabfeatures .description_content, #hometabfeatures .description_content table, #hometabfeatures .description_content_right, #hometabfeatures .description_content_right table').css('height', 'auto');
		
	});
	
	$('#hometabfeatures .content_bloc').removeClass('activ');
	$('#hometabfeatures .content_bloc').removeClass('back_opacity');
	$('#hometabfeatures .content_bloc:first-child').addClass('activ');
	$('#hometabfeatures .content_bloc:first-child').addClass('back_opacity');
	
	$('#hometabfeatures .content_bloc').click(function(){
		if($('#hometabfeatures #hometab_edit').is(':hidden'))
		{
			var ke = $(this).attr('id').replace(new RegExp("[^(0-9)]", "g"), '');
			$('#hometabfeatures #hometab_edit').slideDown().css({'left': '-'+$('.description_bloc').width()*Math.abs(ke)+'px'});
			$(this).addClass('activ');
			$(this).addClass('back_opacity');
			$('#hometabfeatures .description_content, #hometabfeatures .description_content table').height($('#hometabfeatures .description_image img').height()-30);

		}
		else
		{
			slidin(this);
		}
			
		var clo = $('#hometabfeatures .content_bloc .close_arrow').outerWidth()/2;
		
		$('#hometabfeatures .content_bloc .close_arrow').css('left', $('#hometabfeatures .content_bloc').width()/2-clo);
		
	});
	
	$('#hometabfeatures .close_arrow').on('click', function(){
		$('#hometabfeatures .content_bloc').removeClass('activ');
		$('#hometabfeatures .content_bloc').removeClass('back_opacity');
		$('#hometabfeatures #hometab_edit').slideUp();
		return false;
	});
	
	$('#hometabfeatures .content_bloc .close_arrow').load(function(){
		var cla = $('#hometabfeatures .content_bloc .close_arrow').outerWidth()/2;
		$('#hometabfeatures .content_bloc .close_arrow').css('left', $('#hometabfeatures .content_bloc').width()/2-cla);
	});

	
	if($(window).width() <= 640)
	{
		
		$('#hometabfeatures .content_bloc').removeClass('activ');
		$('#hometabfeatures .content_bloc').removeClass('back_opacity');
		$('#hometabfeatures #hometab_edit').slideUp()
		$('#hometabfeatures .content_bloc').click(function(){
			var id_last = $('#hometabfeatures .content_bloc').last().attr('id').replace(new RegExp("[^(0-9)]", "g"), '');
			var this_id = $(this).attr('id').replace(new RegExp("[^(0-9)]", "g"), '');
			var this_ofset = $(this).position().top;
			var last_ofset = $('#hometabfeatures .content_bloc').height()*Math.abs(id_last);
			var sw = last_ofset-this_ofset;
			
			$(this).animate({"top" : "+=" + sw +"px"});
			$('#hometabfeatures .content_bloc').each(function(index, element) {
				if(index+1 != this_ofset)
				{
					if($(this).position().top > this_ofset)
						$(this).animate({"top" : "-=" + $('#hometabfeatures .content_bloc').height()});
				}
					
            });
			
/*			$('#hometabfeatures #first_tab_edit').css('height', $('#first_tab_edit #description_bloc'+ this_id).height() +'px');
*/			var body_ofset = $('#first_tab_edit').position().top + $('#home_mob_pic').height() -59;
			 $('body#index').animate({scrollTop: body_ofset},2000);
			
			
		});
	}
	
	
	
});

$(window).resize(function() {
	
	
	if($('#hometabfeatures .content_bloc').hasClass('activ'))
	{
		
		var ke = $('#hometabfeatures .content_bloc.activ').attr('id').replace(new RegExp("[^(0-9)]", "g"), '');
		$('#hometabfeatures #hometab_edit').slideDown().css({'left': '-'+$('.description_bloc').width()*Math.abs(ke)+'px'});
	}
	
	if($(window).width() > 801)
		$('#hometabfeatures .description_content, #hometabfeatures .description_content table, #hometabfeatures .description_content_right, #hometabfeatures .description_content_right table').height($('#hometabfeatures .description_image img').height()-30);
	else
		$('#hometabfeatures .description_content, #hometabfeatures .description_content table, #hometabfeatures .description_content_right, #hometabfeatures .description_content_right table').css('height', 'auto');
	
	var clo = $('#hometabfeatures .content_bloc .close_arrow').outerWidth(true)/2;
	$('#hometabfeatures .content_bloc .close_arrow').css('left', $('#hometabfeatures .content_bloc').width()/2-clo);

});


function slidin(this_act){
	
	var current = $('#hometabfeatures .content_bloc.activ').attr('id').replace(new RegExp("[^(0-9)]", "g"), '');
	$('#hometabfeatures .content_bloc').removeClass('activ');
	$('#hometabfeatures .content_bloc').removeClass('back_opacity');
	$(this_act).addClass('activ');
	$(this_act).addClass('back_opacity');
	var key = $(this_act).attr('id').replace(new RegExp("[^(0-9)]", "g"), '');
	
	var pos = $('.description_bloc').width()*Math.abs(key-current);
	
	if(current<key)
	{
		$('#hometab_edit').animate({"left" : "-=" + pos});
	}
	else if(key<current)
	{
		$('#hometab_edit').animate({"left" : "+=" + pos});;
	}
		$('#hometabfeatures .description_content, #hometabfeatures .description_content table').height($('#hometabfeatures .description_image img').height()-30);
		var clo = $('#hometabfeatures .content_bloc .close_arrow').width()/2;
	$('#hometabfeatures .content_bloc .close_arrow').css('left', $('#hometabfeatures .content_bloc').width()/2-clo);

}


/*var $table = $("#parent").children("table");
$table.css({ position: "absolute", visibility: "hidden", display: "block" });
var tableWidth = $table.outerWidth();
$table.css({ position: "", visibility: "", display: "" });*/