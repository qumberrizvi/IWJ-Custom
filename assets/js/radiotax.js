    jQuery(document).ready(function($) {  

		$('.iwj-radiotax').each(function () {

			var taxonomy = $(this).data('taxonomy');

			$('#' + taxonomy + 'checklist li :radio, #' + taxonomy + 'checklist-pop :radio').on( 'click', function(){
				var t = $(this), c = t.is(':checked'), id = t.val();
				$('#' + taxonomy + 'checklist li :radio, #' + taxonomy + 'checklist-pop :radio').prop('checked',false);
				$('#in-' + taxonomy + '-' + id + ', #in-popular-' + taxonomy + '-' + id).prop( 'checked', c );
            });

			$('#taxonomy-'+taxonomy+' .radio-tax-toggle-add').click( function(e){
				e.preventDefault();
				$(this).next('.wp-hidden-child').toggle();
				$('#' + taxonomy+'-add #new-'+taxonomy).focus();
			});

			$('#' + taxonomy +'-add .radio-tax-add').on( 'click', function(){
				term = $('#' + taxonomy+'-add #new-'+taxonomy).val();
				nonce =$('#' + taxonomy+'-add #_wpnonce_radio-add-tag').val();
				if(term){
					$.post(ajaxurl, {
						action: 'iwj_radio_tax_add_taxterm',
						term: term,
						'_wpnonce_radio-add-tag':nonce,
						taxonomy: taxonomy
					}, function(r){
						$('#' + taxonomy+'-add #new-'+taxonomy).val('');
						$('#' + taxonomy + 'checklist').append(r.html).find('li#'+taxonomy+'-'+r.term+' :radio').attr('checked', true);
					},'json');
				}
			});
		});
    });
