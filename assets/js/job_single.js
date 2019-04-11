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

        window.onscroll = function() {myFunction()};

        var detail_menu = $(".job-detail-page-heading");
        var sticky = detail_menu.offset().top;

        function myFunction() {
            if (window.pageYOffset >= sticky) {
                detail_menu.addClass("sticky");
            } else {
                detail_menu.removeClass("sticky");
            }
        }
    });

})(jQuery);