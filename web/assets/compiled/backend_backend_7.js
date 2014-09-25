/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        $('.variant-table-toggle i.glyphicon').on('click', function(e) {
            $(this).toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
            $(this).parent().parent().find('table tbody').toggle();
        });
        $('.datepicker').datepicker({});
    });
})( jQuery );

(function($) {

                $(document).ready(function() {
                    $('.kit-cms-modal-open > a').kitCmsModal({closable:false});
                    $('a.kit-cms-modal-open').kitCmsModal({closable:false});
                });
                $(document).ready(function() {
                $('.kit-cms-publish-all').click(function(e) {
                    var response = confirm("Do you confirm you want to publish all pages and the navigation ?");
                    if (!response) {
                        e.preventDefault();
                        $('.kit-cms-modal-open > a').kitCmsModal("close");
                        $('a.kit-cms-modal-open').kitCmsModal("close");
                    }
                });
                });

            })( jQuery );
            
  
