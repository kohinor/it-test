(function ($) {
    'use strict';

    var methods = {
        init: function(options) {
            var settings = $.extend({
              'prototypePrefix': false,
              'prototypeElementPrefix': '<hr />',
              'containerSelector': false
            }, options);

            return this.each(function() {
                show($(this), false);
                $(this).change(function() {
                    show($(this), true);
                });

                function show(element, replace) {
                    var id = element.attr('id');
                    var selectedValue = element.val();
                    var prototypePrefix = id;
                    if (false != settings.prototypePrefix) {
                        prototypePrefix = settings.prototypePrefix;
                    }

                    var prototypeElement = $('#' + prototypePrefix + '_' + selectedValue);
                    var container;

                    if (settings.containerSelector) {
                        container = $(settings.containerSelector);
                    } else {
                        container = $(prototypeElement.data('container'));
                    }

                    if (!container.length) {
                        return;
                    }

                    if (!prototypeElement.length) {
                        container.empty();
                        return;
                    }

                    if (replace || !container.html().trim()) {
                        container.html(settings.prototypeElementPrefix + prototypeElement.data('prototype'));
                    }
                }
            });
        }
    };

    $.fn.handlePrototypes = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.handlePrototypes' );
        }
    };
})(jQuery);

$(document).ready(function() {
    'use strict';

    $('a[data-collection-button="add"]').on('click', function (e) {
        var collectionContainer = $('#' + $(this).data('collection'));
        var lastElementNumber = (collectionContainer.children().length) - 1;
        $('#sylius_product_properties_' + lastElementNumber + ' .property-chooser').handlePrototypes({
            prototypePrefix: 'property-prototype',
            prototypeElementPrefix: '',
            containerSelector: '#sylius_product_properties_' + lastElementNumber + ' .control-group:last .controls'
        });
        $('#sylius_product_properties_' + lastElementNumber + ' .property-chooser').change(function() {
            $('#sylius_product_properties_' + lastElementNumber + ' .control-group:last .controls input, #sylius_product_properties_' + lastElementNumber + ' .control-group:last .controls select').each(function() {
                this.name = this.name.replace(/__name__/g, lastElementNumber);
                this.id = this.id.replace(/__name__/g, lastElementNumber);
            });
        });
    });
});

$(function() {
    $('ul.a2lix_translationsLocales').on('click', 'a', function(evt) {
        evt.preventDefault();
        $(this).tab('show');
    });

    $('div.a2lix_translationsLocalesSelector').on('change', 'input', function(evt) {
        var $tabs = $('ul.a2lix_translationsLocales');

        $('div.a2lix_translationsLocalesSelector').find('input').each(function() {
            $tabs.find('li:has(a[data-target=".a2lix_translationsFields-' + this.value + '"])').toggle(this.checked);
        });

        $('ul.a2lix_translationsLocales li:visible:first').find('a').tab('show');
    }).trigger('change');

    // Manage focus on right bootstrap tab when invalid event (A2lixTranslation tab or not, and inner tabs include)
    $(':input', 'div.tab-content').on('invalid', function(e) {
        var $tabPanes = $(this).parents('div.tab-pane');

        $tabPanes.each(function() {
            var $tabPane = $(this);

            if (!$tabPane.hasClass('active')) {
                var $tabNavs = $tabPane.parent('.tab-content')
                                       .siblings('ul.nav.nav-tabs');

                // Tab target by id
                if (this.id) {
                    $tabNavs.find('a[href="#'+ this.id +'"], a[data-target="#'+ this.id +'"]')
                            .trigger('click');

                    return true;
                }

                // Tab target by class for a2lixTranslation
                var a2lixTranslClass = /a2lix_translationsFields-[\S]+/.exec(this.className);
                if (a2lixTranslClass.length) {
                    $tabNavs.find('a[data-target=".'+ a2lixTranslClass[0] +'"]')
                            .trigger('click');

                    return true;
                }
            }
        });

        return true;
    });
});
