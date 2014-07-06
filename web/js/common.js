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


