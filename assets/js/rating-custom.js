'use strict';
jQuery(document).ready(function () {
	jQuery(".iwj_num_rate").rating();

	var total_view = jQuery('.iwj-review-voting').data('total_views');
	if (typeof total_view !== 'undefined' && total_view !== 0) {
		var to_Array = [];
		jQuery('input[type="hidden"].iwj_num_rate').on('change', function () {
			var sum_arr = 0;
			for (var i = 0; i < total_view; i++) {
				var val_cri = jQuery('input[name="iwj_rate_num_' + i + '"]').val();
				val_cri = val_cri ? val_cri : 0;
				to_Array[i] = val_cri;
			}

			for (var j = 0; j < to_Array.length; j++) {
				sum_arr += parseInt(to_Array[j]);
			}

			var total_vote = sum_arr / total_view;
			if (typeof total_vote !== 'undefined' && total_vote !== 0) {
				for (var k = 1; k <= 5; k++) {
					if (total_vote % 1 === 0) {
						if (k <= total_vote) {
							jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star');
						} else {
							jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star-outline');
						}
					} else {
						if (k < Math.ceil(total_vote)) {
							jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star');
						} else if (k === Math.ceil(total_vote)) {
							jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star-half');
						} else {
							jQuery('.iwj-votes-icon > i:nth-child(' + k + ')').attr('class', 'ion-android-star-outline');
						}
					}
				}
			}

			/*if (to_Array.iwj_allHaveValues() === true) {
				setTimeout(function () {
					jQuery('.iwj-review-voting').removeClass('iwj-show-popup-rate');
				}, 2000);
			}*/

		});
	}

	jQuery('.rating-stars').mouseleave(function () {
		var val_in = jQuery(this).find('.iwj_num_rate').val();
		if (typeof val_in !== 'undefined' || val_in !== '' || val_in !== 0) {
			jQuery(this).find('.filled-stars').css('width', val_in * 20 + '%');
		}
	});

});

Array.prototype.iwj_allHaveValues = function () {
	for (var i = 1; i < this.length; i++) {
		if (this[i] === 0)
			return false;
	}

	return true;
};