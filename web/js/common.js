$(document).ready(function(){ 
    var tabs = $('#nav-main ul > li.navigation-level3');
    for(numberoftabs=1; numberoftabs<=tabs.length; numberoftabs++){
        $('#nav-main ul > li.navigation-level3:nth-child('+numberoftabs+')').each(function(){
            var menutabnumber = numberoftabs;
            showsubmenu(this, menutabnumber);
        });
    }
}); 

function showsubmenu(element, menutabnumber){
    $(element).mouseover(function(){ 
        timedelay = setTimeout(function(){visible(menutabnumber)}, 5);
    }); 
    $(element).mouseleave(function(){ 
        clearTimeout(timedelay);
        $('#nav-main ul > li.navigation-level3 > ul').each(function(){
            $(this).hide();
            $(this).prev('a').removeClass('selected');
        });
    }); 
}

function visible(menutabnumber){
    $('#nav-main ul > li.navigation-level3:nth-child('+menutabnumber+') > ul').each(function(){
        $(this).show();
        $(this).prev('a').addClass('selected');
    });
}

$(document).ready(function(){ 

	$(".container").imagezoomsl({ 
		
		descarea: ".big-caption", 				
		zoomrange: [1.68, 10],
		zoomstart: 5,
		cursorshadeborder: "5px solid black",
		magnifiereffectanimate: "fadeIn",	
	});
  

	$(".tmb-caption img").click(function(){

	    var that = this;
		$(".container").fadeOut(600, function(){
		
			$(this).attr("src", 	   $(that).attr("data-src"))
                               .attr("data-large", $(that).attr("data-tmb-large"))
                               .fadeIn(1000);				
		});

	    return false;
	});  
        
        $(".close").click(function(){
            $(".modal").hide();
            $(".alert").hide();
            return false;
          });
        $(".modal-show").click(function(){
            $(".modal").show();
          });
        $(document).mouseup(function (e)
        {
            var container = $(".modal");

            if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
            {
                container.hide();
            }
        });

});


