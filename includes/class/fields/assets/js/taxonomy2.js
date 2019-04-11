jQuery( function ( $ ) {
	'use strict';

	/**
	 * Turn select field into beautiful dropdown with select2 library
	 * This function is called when document ready and when clone button is clicked (to update the new cloned field)
	 *
	 * @return void
	 */
	function update() {
        function htmlDecode(value) {
            return $("<textarea/>").html(value).text();
        }

		var $this = $( this );
        var options = $this.data( 'options' );
        if(!options){
            options = {}
        }
        if(!options.selectAllText){
            options.selectAllText = i18niwjmbTaxonomy2.selectAllText;
        }
        if(!options.filterPlaceholder){
            options.filterPlaceholder = i18niwjmbTaxonomy2.filterPlaceholder;
        }
        if(!options.filterPlaceholder){
            options.nonSelectedText = i18niwjmbTaxonomy2.nonSelectedText;
        }

        options.allSelectedText = i18niwjmbTaxonomy2.allSelectedText;
        options.onChange = function(option, checked) {
            var $select = $(this.$select.get(0));
            var max_items = options.maxSelectItems ? options.maxSelectItems : '';
            if(max_items){
                // Get selected options.
                var selectedOptions = $select.find('option:selected');
                if (selectedOptions.length >= max_items) {
                    // Disable all other checkboxes.
                    var nonSelectedOptions = $select.find('option').filter(function() {
                        return !$(this).is(':selected');
                    });

                    nonSelectedOptions.each(function() {
                        var input = $('input[value="' + $(this).val() + '"]');
                        input.prop('disabled', true);
                        input.parent('li').addClass('disabled');
                    });
                }
                else {
                    // Enable all checkboxes.
                    $select.find('option').each(function() {
                        var input = $('input[value="' + $(this).val() + '"]');
                        input.prop('disabled', false);
                        input.parent('li').addClass('disabled');
                    });
                }
            }
        };
        options.buttonText = function(options, select) {
            if (options.length == 0) {
                return this.nonSelectedText;
            }
            else {
                var selected = [];
                var i = 0;
                var numberDisplayed = this.numberDisplayed;
                options.each(function() {
                    var label = ($(this).attr('label') !== undefined) ? $(this).attr('label') : $(this).html();
                    if(i < numberDisplayed){
                        selected.push(label);
                    }else{
                        selected.push('...('+options.length+')');
                        return false;
                    }

                    i++;
                });
                return htmlDecode(selected.join(', '));
            }
        };
        options.onDropdownShow = function(options, select) {
            if(!this.$ul.mark_checkbox){
                $(this.$ul).find('input[type="checkbox"]').after('<span><i></i></span>');
                this.$ul.mark_checkbox = true;
            }
        };
        options.optionClass = function(element) {
            var level = $(element).data('level');
            if (level) {
                return 'level-'+level;
            }
            else {
                return '';
            }
        };

        $this.multiselect(options);
	}

	$( ':input.iwjmb-taxonomy2' ).each( update );
	$( '.iwjmb-input' ).on( 'clone', ':input.iwjmb-taxonomy2', update );
} );
