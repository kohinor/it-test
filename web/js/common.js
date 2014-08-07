$(document).ready(function(){ 
    var tabs = $('#nav-main ul > li.navigation-level3');
    for(numberoftabs=1; numberoftabs<=tabs.length; numberoftabs++){
        $('#nav-main ul > li.navigation-level3:nth-child('+numberoftabs+')').each(function(){
            var menutabnumber = numberoftabs;
            showsubmenu(this, menutabnumber);
        });
    }
    $('a.back').click(function(){
        parent.history.back();
        return false;
    });
    
    
});
$(document).ready(function(){
        $('.brand-slider').slick({
  slidesToShow: 5,
  slidesToScroll: 1,
  autoplay: true,
  autoplaySpeed: 2000,
  arrows:true
});
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

$(document).ready(function(){
   if (readCookie('acceptCookies') != '1') {
       $("#cookie-div").show();
   }
   
   $(".acceptCookie").click(function(){
        $("#cookie-div").hide();
        var date = new Date();
        date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
        document.cookie = escape("acceptCookies") + "=" + escape("1") + expires + "; path=/";
    });

    function readCookie(name) {
        var nameEQ = escape(name) + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
        }
        return null;
    }
});

$(document).ready(function(){
    $("li.slide-header").click(function(e){
            $("li.slide-body").hide();
            $(this).next().slideDown();
    }); 

        $('.sylius-different-billing-address-trigger').click(function() {
            if ($(this).is(':checked')) {
                $('#sylius-billing-address-container').show();
            }else {
                $('#sylius-billing-address-container').hide();
            }
        });
        
    $('#sylius_cart_item_variant select').on('change', function(event){
        var value = $('select option:selected').text();
        var slug = $('#product-slug').val();
        var locale = $('#locale').val();
        $.ajax({
            type: 'post',
            url: '/'+locale+'/product/'+slug+'/stock/',
            data: {
                  option: value,
                  json: 'true'
                 },
            dataType: 'json',
            success: function(data) {
                if (data.code=='200' ){
                    $('#product-stock').html(data.html);
                }
            }
        });
        return false;
    });
});

$(document).ready(function() {
        $('select[name$="[country]"]').on('change', function(event) {
            var $select = $(event.currentTarget);
            var $provinceContainer = $select.closest('div.form-group').next('div.province-container');
            var provinceName = $select.attr('name').replace('country', 'province');

            if (null === $select.val()) {
                return;
            }

            $.get($provinceContainer.attr('data-url'), {countryId: $(this).val()}, function (response) {
                if (!response.content) {
                    $provinceContainer.fadeOut('slow', function () {
                        $provinceContainer.html('');
                    });
                } else {
                    $provinceContainer.fadeOut('slow', function () {
                        $provinceContainer.html(response.content.replace(
                            'name="sylius_address_province"',
                            'name="' + provinceName + '"'
                        ));

                        $provinceContainer.fadeIn();
                    });
                }
            });
        });

        if('' === $.trim($('div.province-container').text())) {
            $('select.country-select').trigger('change');
        }

        var $billingAddressCheckbox  = $('input[type="checkbox"][name$="[differentBillingAddress]"]');
        var $billingAddressContainer = $('#sylius-billing-address-container');
        var toggleBillingAddress = function() {
            $billingAddressContainer.toggle($billingAddressCheckbox.prop('checked'));
        };
        toggleBillingAddress();
        $billingAddressCheckbox.on('change', toggleBillingAddress);
    });