/**
 *
 */
jQuery(document).ready(function() {   

    jQuery( "#sortable" ).sortable({
        revert: true,
        helper:'clone',
        start: function(event, ui) {
            jQuery(ui.item).parent().css("border", "3px dashed #787c82");
        },
        stop: function(event, ui) {
            jQuery(ui.item).parent().css("border", "none");
        }
    });

    jQuery('.color-picker').wpColorPicker({
            defaultColor: false,
            change: function(event, ui){},
            clear: function() {},
            hide: true,
            palettes: true
        });

    jQuery("#rawp-add-numeric-criteria-btn, #rawp-add-lookup-criteria-btn, #rawp-add-star-rating-criteria-btn").on("click", function(e) {

        var type = e.target.id.includes("lookup") ? 'lookup' : e.target.id.includes("numeric") ? 'numeric' : 'star-rating';

        var data = {
            action : "rawp_add_criteria",
            type : type,
            nonce : rawp_admin_data.ajax_nonce,
        };

        jQuery.get(rawp_admin_data.ajax_url, data, function(response) {
            
            var jsonResponse = jQuery.parseJSON(response);

            if (jsonResponse.success == true) {
                
                jQuery("#sortable").append(jsonResponse.html);
                jQuery("#sortable").sortable('refresh');
                
                // show
                jQuery(".rawp-sortable-item .rawp-sortable-item-container").last().show();
                jQuery(".rawp-sortable-item h2").last().parent().addClass("is-active");
                
                // actions event handlers
                jQuery(".rawp-sortable-item .rawp-sortable-item-actions").last()
                    .find(".dashicons-arrow-down").removeClass("dashicons-arrow-down")
                    .addClass("dashicons-arrow-up").on("click", toggleSortableItemDashicon );
                jQuery(".rawp-sortable-item .rawp-sortable-item-actions .rawp-delete-btn").on("click", deleteCriteriaClick );
                jQuery(".rawp-sortable-item h2").last().on("click", toggleSortableItemH2 );
                jQuery(".rawp-sortable-item .rawp-sortable-item-actions").last()
                    .find(".dashicons-arrow-up-alt2, .dashicons-arrow-down-alt2")
                    .on("click", moveUpOrDownSortableItem );

                // lookup option event handers
                if (type === 'lookup') {
                    jQuery(".rawp-sortable-item").last().find(".add-lookup-option").on("click", addLookupOptionClick );
                    jQuery(".rawp-sortable-item").last().find(".delete-lookup-option").on("click", function(e) {
                        jQuery(this).closest("tr").empty();
                    });
                }
            }
        });
    });

    function toggleSortableItemDashicon() {
        let isUp = jQuery(this).hasClass("dashicons-arrow-up") ? true : false;

        if (isUp) {
            jQuery(this).removeClass("dashicons-arrow-up").addClass("dashicons-arrow-down");
            jQuery(this).parent().parent().next().hide(); 
            jQuery(this).parent().parent().removeClass("is-active");
        } else {
            jQuery(this).removeClass("dashicons-arrow-down").addClass("dashicons-arrow-up");
            jQuery(this).parent().parent().next().show(); 
            jQuery(this).parent().parent().addClass("is-active");
        }
    }

    jQuery(".rawp-sortable-item-actions .dashicons-arrow-down, .rawp-sortable-item-actions .dashicons-arrow-up").on("click", toggleSortableItemDashicon );

    function toggleSortableItemH2(e) {
        
        if (jQuery(this).parent().next().is(":visible")) {
            jQuery(this).next().find(".dashicons-arrow-up").removeClass("dashicons-arrow-up").addClass("dashicons-arrow-down");
            jQuery(this).parent().next().hide(); 
            jQuery(this).parent().removeClass("is-active");
        } else {
            jQuery(this).next().find(".dashicons-arrow-down").removeClass("dashicons-arrow-down").addClass("dashicons-arrow-up");
            jQuery(this).parent().next().show();
            jQuery(this).parent().addClass("is-active");
        }
    }

    /**
     *
     */
    function deleteCriteriaClick(e) {
        e.preventDefault();

        jQuery(this).closest(".rawp-sortable-item").fadeOut( 500, function() {
            jQuery(this).closest(".rawp-sortable-item").remove();
            jQuery("#sortable").sortable('refresh');
        } );

    }

    jQuery(".rawp-sortable-item .rawp-sortable-item-actions .rawp-delete-btn").on("click", deleteCriteriaClick );

    jQuery(".rawp-sortable-item h2").on("click", toggleSortableItemH2 );

    jQuery(".rawp-sortable-item-actions .dashicons-arrow-up-alt2, .rawp-sortable-item-actions .dashicons-arrow-down-alt2").on("click", moveUpOrDownSortableItem );
    
    /**
     *
     */
    function moveUpOrDownSortableItem(e) { 
        
        var currentSortableItem = jQuery(this).closest(".rawp-sortable-item");
        var isUp = jQuery(this).hasClass("dashicons-arrow-up-alt2") ? true : false;
        var index = currentSortableItem.prevAll().length;

        if (isUp) {
            currentSortableItem.insertBefore(currentSortableItem.prev());
        } else {
            currentSortableItem.insertAfter(currentSortableItem.next());
        }

        jQuery("#sortable").sortable('refresh');
    }

    jQuery(".delete-lookup-option").on("click", function(e) {
        jQuery(this).closest("tr").remove();
    });

    function addLookupOptionClick(e) {
        var clazz = 'alternate';
        if (jQuery(this).parent().find("tr").length % 2 == 0) {
             clazz = '';
        }

        var criteriaId = jQuery(this).closest(".rawp-sortable-item-container").find("input[name=criteria-id]").val();

        var html = "<tr class=\"" + clazz + "\">"
            + "<td><input name=\"percentage-value\" min=\"0\" max=\"1\" step=\"0.01\" type=\"number\" class=\"small-text\" value=\"1.00\" /></td>"
            + "<td><input name=\"option-text\" maxlength=50 required type=\"text\" class=\"regular-text\" value=\"" + rawp_admin_data.strings.sample_option_text + "\" /></td>"
            + "<td><input type=\"radio\" name=\"is-default-" + criteriaId + "\" value=\"true\" /><label>Yes</label></td>"
            + "<td><a class=\"delete-lookup-option\" href=\"#\">Delete</a><input type=\"hidden\" name=\"lookup-option-id\" value=\"\" /></td>"
            + "</tr>";

        jQuery(this).prev().find("tr:last").after(html);

        // add delete option click
        jQuery(this).closest("table").find("tr:last a.delete-lookup-option").on("click", function(e) {
            jQuery(this).closest("tr").empty();
        });

    }

    jQuery(".add-lookup-option").on("click", addLookupOptionClick );

    jQuery("#rawp-save-form-btn:submit").on("click", function(e) {
            
        jQuery('#rawp-edit-form :invalid').each(function (index, value) {
                
            var h2 = jQuery(this).closest(".rawp-sortable-item").find("h2");

            if (! h2.parent().next().is(":visible") ) {
                h2.next().find(".dashicons-arrow-down").removeClass("dashicons-arrow-down").addClass("dashicons-arrow-up");
                h2.parent().next().show();
                h2.parent().addClass("is-active");
            }
        });


        if ( ! jQuery('#rawp-edit-form')[0].checkValidity() ) {
            e.preventDefault(); 
            return;
        }

        e.preventDefault();

        var formId = jQuery("#form-id").val();
        var name = jQuery("#name").val();
        var criteriaItems = [];

        jQuery(".rawp-sortable-item").each(function (index, value) {

            // check hidden field for type
            var type = jQuery(this).find("input[name=type]").val();
            var criteriaId = jQuery(this).find("input[name=criteria-id]").val();
            var label = jQuery(this).find("input[name=label]").val();
            var value = jQuery(this).find("input[name=value]").val();

            if (type === 'numeric') { // numeric

                criteriaItems.push({ 
                    id : criteriaId,
                    type : type,
                    label : label,
                    value : value,
                    isAscending : jQuery(this).find("input[name=is-ascending]").is(":checked") ? true : false,
                    min : jQuery(this).find("input[name=min]").val(),
                    max : jQuery(this).find("input[name=max]").val(),
                    default : jQuery(this).find("input[name=default]").val(),
                    display : jQuery(this).find("select[name=display]").val()
                });

            } else if (type === 'lookup') {

                var isDefaultId = ( criteriaId !== ""  ) ? criteriaId : jQuery(this).find("input[name=dummy-criteria-id]").val();
                
                var lookupOptions = [];
                jQuery(this).find(".rawp-lookup-options tbody tr").each(function (index, value) {
                    lookupOptions.push({
                        id : jQuery(this).find("input[name=lookup-option-id").val(),
                        percentageValue : jQuery(this).find("input[name=percentage-value").val(),
                        optionText : jQuery(this).find("input[name=option-text").val(),
                        isDefault : jQuery(this).find("input[name=is-default-" + isDefaultId + "]").is(':checked')
                    });
                });

                criteriaItems.push({ 
                    id : criteriaId,
                    type : type,
                    label : label,
                    value : value,
                    lookupOptions : lookupOptions,
                    display : jQuery(this).find("select[name=display]").val()
                });

            } else { // star rating

                criteriaItems.push({ 
                    id : criteriaId,
                    type : type,
                    label : label,
                    value : value,
                    outOf : jQuery(this).find("input[name=out-of]").val(),
                });

            }

        });

        var data = {
                action : "rawp_save_form",
                nonce : rawp_admin_data.ajax_nonce,
                criteriaItems : criteriaItems,
                name : name,
                formId : formId
        };

        jQuery.post(rawp_admin_data.ajax_url, data, function(response) {
            var jsonResponse = jQuery.parseJSON(response);

            if (jsonResponse.success) {// redirect to view forms page on save
                window.location.href = rawp_admin_data.admin_url + '?page=rawp_forms';
            }
        });
    });

    /**
     * Switch rating form
     */
    jQuery("#rawp-switch-form").on("click", function(e) {
        var formId = jQuery("#rawp-switch-form-select").val();
        window.location.href = replaceQueryStringParam(window.location.href, "id", formId);
    });

    /**
     * Replace query string parameter
     *
     * @param url
     * @param paramName
     * @param paramValue
     *
     * @returns url
     *
     */
    function replaceQueryStringParam(url, paramName, paramValue) {
        var regex = new RegExp("([?&])" + paramName + "=.*?(&|$)", "i");
        var separator = url.indexOf('?') !== -1 ? "&" : "?";

        if (url.match(regex)) {
            return url.replace(regex, '$1' + paramName + "=" + paramValue + '$2');
        } else {
            return url + separator + paramName + "=" + paramValue;
        }
    }

});