/**
 *
 */
jQuery(document).ready(function() {  

	/**
	 * Save rating form entry
	 */
	function saveRatingFormEntry(ratingForm) {

		var formId = ratingForm.find("input[name=form-id]").val();
		var subjectType = ratingForm.find("input[name=subject-type]").val();
		var subjectSubType = ratingForm.find("input[name=subject-sub-type]").val();
		var subjectId = ratingForm.find("input[name=subject-id]").val();

		var criteriaItems = [];
		ratingForm.find(".rawp-criteria-wrapper .rawp-stars, .rawp-criteria-wrapper .rawp-radio, .rawp-criteria-wrapper .rawp-select, .rawp-criteria-wrapper .rawp-number, .rawp-criteria-wrapper .rawp-range").each(function( index ) {
			
			var criteriaId = null;
			var value = null;
			
			if (jQuery(this).hasClass("rawp-select") || jQuery(this).hasClass("rawp-number")
				|| jQuery(this).hasClass("rawp-range")) {
				criteriaId = jQuery(this).find("input, select").attr('id');
				value = jQuery(this).find("input, select").val();
			} else if (jQuery(this).hasClass("rawp-radio")) {
				criteriaId = jQuery(this).find("input").first().attr('id');
				value = jQuery(this).find("input:checked").val();
			} else if (jQuery(this).hasClass("rawp-stars")) {
				criteriaId = this.id;
				value = jQuery(this).find(".dashicons-star-filled").length;
			}
			
			if (criteriaId && value) {
			
				criteriaItems.push({ 
					'id': criteriaId, 
					'value': value
				});
			
			}
		});
		
		jQuery.ajax( {
		    url: rawp_frontend_data.rest_url + 'ratingwp/v1/forms/' + formId + '/entry',
		    type: 'POST',
		    beforeSend: function ( xhr ) {
		        xhr.setRequestHeader( 'X-WP-Nonce', rawp_frontend_data.rest_nonce );
		    },
		    data:{
		    	'formId': formId,
		        'subjectType': subjectType,
		        'subjectSubType': subjectSubType,
		        'subjectId': subjectId,
		        'criteriaItems' : criteriaItems
		    }
		} ).done( function ( response ) {
		    if (response.success) {
		    	ratingForm.replaceWith('<p>' + response.data.message + '</p>');
		    } else {
		    	ratingForm.prepend('<p>' + response.data.message + '</p>');
		    }
		} );
	}

	jQuery("form.rawp-rating-form .rawp-star-rating-submit .dashicons").on("click", function(e) {
		var ratingForm = jQuery(this).closest(".rawp-rating-form");
		saveRatingFormEntry(ratingForm);
	});

	jQuery("form.rawp-rating-form input[type=submit]").on("click", function(e) {
		e.preventDefault();
		var ratingForm = jQuery(this).closest(".rawp-rating-form"); 
		saveRatingFormEntry(ratingForm);
	});

	jQuery(".rawp-range input").change(function() {
		jQuery(this).next().text( jQuery(this).val() );
	});


	/**
	 * Star rating hover/click logic
	 */
	jQuery(".rawp-rating-form .rawp-stars .dashicons").on("mouseover", function () {

		if ( ! jQuery(this).parent().hasClass("rawp-selected") ) {
			jQuery(this).removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
			jQuery(this).prevAll().removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
			jQuery(this).nextAll().removeClass("dashicons-star-filled").addClass("dashicons-star-empty");	
		}

	});

	jQuery(".rawp-rating-form .rawp-stars .dashicons").on("mouseleave", function () {

		if ( ! jQuery(this).parent().hasClass("rawp-selected") ) {
			jQuery(this).removeClass("dashicons-star-filled").addClass("dashicons-star-empty");
			jQuery(this).prevAll().removeClass("dashicons-star-filled").addClass("dashicons-star-empty");
			jQuery(this).nextAll().removeClass("dashicons-star-filled").addClass("dashicons-star-empty");	
		}

	});

	jQuery(".rawp-rating-form .rawp-stars .dashicons").on("click", function () {
		
		jQuery(this).removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
		jQuery(this).prevAll().removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
		jQuery(this).nextAll().removeClass("dashicons-star-filled").addClass("dashicons-star-empty");

		jQuery(this).parent().addClass("rawp-selected");
	});

});