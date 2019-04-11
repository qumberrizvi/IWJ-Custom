/* 
 * @package Inwave Job
 * @version 1.0.0
 * @created Jun 2, 2016
 * @author Inwavethemes
 * @email inwavethemes@gmail.com
 * @website http://inwavethemes.com
 * @support Ticket https://inwave.ticksy.com/
 * @copyright Copyright (c) 2015 Inwavethemes. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

/**
 * Description of injob-script
 *
 * @developer Hien Tran
 */

(function ($) {
	'use strict';
	$(document).ready(function ($) {

        // Menu Single Page
        $('.candidate-detail-menu .menu ul').singlePageNav({
            currentClass: 'current',
            filter: ':not(.external-link)',
            updateHash: false,
            offset: 100,
            threshold: 0
        });

        window.onscroll = function() {myFunction()};

        var detail_menu = $("#iwj-detail-menu");
        var sticky = detail_menu.offset().top;

        function myFunction() {
            if (window.pageYOffset >= sticky) {
                detail_menu.addClass("sticky");
            } else {
                detail_menu.removeClass("sticky");
            }
        }
    });

    $(window).load(function () {
        // Video candidate
        if ($('.iwj-candidate-video').length) {
            var video = document.querySelectorAll( ".iwj-candidate-video" );
            for (var i = 0; i < video.length; i++) {

                video[i].addEventListener( "click", function() {

                    var iframe = document.createElement( "iframe" );

                    iframe.setAttribute( "frameborder", "0" );
                    iframe.setAttribute( "allowfullscreen", "" );
                    iframe.setAttribute( "width", "770" );
                    iframe.setAttribute( "height", "435" );
                    iframe.setAttribute( "src", this.dataset.embed +"?rel=0&showinfo=0&autoplay=1" );

                    this.innerHTML = "";
                    this.appendChild( iframe );
                } );
            }
        }
    });

})(jQuery);