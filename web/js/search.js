var italica = {
    general: {
        clickHandler: function(e){
            var $clicked = $(e.target);
            if($clicked.hasClass('done-sorting')){
                var url = $clicked.attr('data-url');
                var sort = $('select option:selected').val();
                if (sort == 'none') {
                    italica.search.sort = null;
                    italica.search.direction = null;
                }
                if (sort == 'asc') {
                    italica.search.sort = 'price';
                    italica.search.direction = 'asc';
                }
                if (sort == 'desc') {
                    italica.search.sort = 'price';
                    italica.search.direction = 'desc';
                }
                if (sort == 'a-z') {
                    italica.search.sort = 'name';
                    italica.search.direction = 'asc';
                }
                if (sort == 'z-a') {
                    italica.search.sort = 'name';
                    italica.search.direction = 'desc';
                }
                italica.redirect(url);
                return false;
            }
            if($clicked.hasClass('clear-filters')){
                var url = $clicked.attr('data-url');
                var key = $clicked.attr('data-key');
                $('input[name=' + key + ']').each(function() {
                    italica.search.removeFacet(key, $(this).val());
                });
                if (key == 'categories') {
                    $('input[name=category1]').each(function() {
                        italica.search.removeFacet(key, $(this).val());
                    });
                    $('input[name=category2]').each(function() {
                        italica.search.removeFacet(key, $(this).val());
                    });
                }
                italica.redirect(url);
                return false;
            }
        }
    },
    search: {
        facets : [],
        sort: null ,
        direction: null,
        page: 1,
        term: null,
        price: '0;10000',
        priceTo: 0,
        priceFrom: 10000,
        addFacet: function(facet, field) {
            facet = {
                facet: facet,
                field:field
            };
            var push = true;
            for (i=0;i<italica.search.facets.length;i++)
            {
                if (facet.field == italica.search.facets[i].field && facet.facet == italica.search.facets[i].facet) {
                    push = false;
                }
            }
            if (push) {
                italica.search.facets.push(facet);
            }
        },
        removeFacet: function(field, facet) {
            facet = facet.replace("amp;","");
            var facets = [];
            for (i=0;i<italica.search.facets.length;i++)
            {
                var brand = italica.search.facets[i].facet.replace("&amp;","&");
                if (field != italica.search.facets[i].field || facet != brand) {
                    italica.search.facets[i].facet = brand;
                    facets.push(italica.search.facets[i]);
                }
            }
            italica.search.facets = facets;
        }
    },
    redirect: function(url) {
        console.log(url);
        console.log(italica.search);
        var param = [];
        if (italica.search.term){
            param.push('term='+italica.search.term);
        }
        if (italica.search.page){
            param.push('page='+italica.search.page);
        }
        if (italica.search.sort){
            param.push('sort='+italica.search.sort);
            if (italica.search.direction){
                param.push('direction='+italica.search.direction);

            }
        }
        if (italica.search.price){
            param.push('price='+italica.search.price);
        }
        var color = [];
        var size = [];
        var promotion = [];
        var gender = [];
        var delivery = [];
        var brand = [];
        var material = [];
        var category1 = [];
        var category2 = [];
        for (i=0;i<italica.search.facets.length;i++) {
            if (italica.search.facets[i].field == 'color') {
                if (color.hasOwnProperty(italica.search.facets[i].facet)) {
                    continue;
                 }
                color.push(italica.search.facets[i].facet);
            }
            if (italica.search.facets[i].field == 'size') {
                if (size.hasOwnProperty(italica.search.facets[i].facet)) {
                    continue;
                 }
                size.push(italica.search.facets[i].facet);
            }
            if (italica.search.facets[i].field == 'promotion') {
                if (promotion.hasOwnProperty(italica.search.facets[i].facet)) {
                    continue;
                 }
                promotion.push(italica.search.facets[i].facet);
            }
            if (italica.search.facets[i].field == 'gender') {
                if (gender.hasOwnProperty(italica.search.facets[i].facet)) {
                    continue;
                 }
                gender.push(italica.search.facets[i].facet);
            }
            if (italica.search.facets[i].field == 'delivery') {
                if (delivery.hasOwnProperty(italica.search.facets[i].facet)) {
                    continue;
                 }
                delivery.push(italica.search.facets[i].facet);
            }
            if (italica.search.facets[i].field == 'brand') {
                if (brand.hasOwnProperty(italica.search.facets[i].facet)) {
                    continue;
                 }
                brand.push(italica.search.facets[i].facet);
            }
            if (italica.search.facets[i].field == 'material') {
                if (material.hasOwnProperty(italica.search.facets[i].facet)) {
                    continue;
                 }
                material.push(italica.search.facets[i].facet);
            }
            if (italica.search.facets[i].field == 'category1') {
                if (category1.hasOwnProperty(italica.search.facets[i].facet)) {
                    continue;
                 }
                category1.push(italica.search.facets[i].facet);
            }
            if (italica.search.facets[i].field == 'category2') {
                if (category2.hasOwnProperty(italica.search.facets[i].facet)) {
                    continue;
                 }
                category2.push(italica.search.facets[i].facet);
            }
            
        }
        if (param.length > 0) {
            url = url+'?'+param.join('&');
        }
        if (color.length > 0) {
            url = url+'&color='+color.join(',');
        }
        if (size.length > 0) {
            url = url+'&size='+size.join(',');
        }
        if (promotion.length > 0) {
            url = url+'&promotion='+promotion.join(',');
        }
        if (gender.length > 0) {
            url = url+'&gender='+gender.join(',');
        }
        if (delivery.length > 0) {
            url = url+'&delivery='+delivery.join(',');
        }
        if (brand.length > 0) {
            url = url+'&brand='+brand.join(',');
        }
        if (material.length > 0) {
            url = url+'&material='+material.join(',');
        }
        if (category1.length > 0) {
            url = url+'&category1='+category1.join(',');
        }
        if (category2.length > 0) {
            url = url+'&category2='+category2.join(',');
        }
        window.location = url;
    }
};

$(document).ready(function(){
$('.filter input[type=checkbox]').click(function(e) {
   var url = $(this).attr('data-url');
   var name = $(this).attr('name');
   var value = $(this).attr('value');
   if($(this).is(":checked")) {
     italica.search.addFacet(value, name);
     if (name == 'category2') {
        var parent = $(this).closest('ul').siblings('input:checkbox');
        italica.search.addFacet(parent.attr('value'), parent.attr('name'));
     }
     italica.redirect(url);
   }else{
     italica.search.removeFacet(name, value);
     italica.redirect(url);
   }

});
$('#fromPrice').change(function() {
    var url = $(this).attr('data-url');
    italica.search.price = $(this).val();
    italica.redirect(url);
    return false;
 });
 
$('.filter').on('click',italica.general.clickHandler);
$('.sort-by').on('change',italica.general.clickHandler);

});
