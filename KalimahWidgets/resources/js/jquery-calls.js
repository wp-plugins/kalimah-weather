jQuery(document).ready(function() {
    /* Images selector */
    // Add selection frame to images
    jQuery('.kalimah-widget-images img').click(function() {
        jQuery('.kalimah-widget-images img').removeClass("image-selected");
        jQuery(this).addClass("image-selected");
    });
	
    // Color inputs for colorpiocker
    var arr = jQuery('.colorPicker input[type="text"]').each(function(i, e) {
            jQuery(e).css('background-color', jQuery(e).val());
            if (jQuery(e).val() == "#000000") jQuery(e).css('color', '#ffffff');
        })
		
        // Display value for range
    jQuery(document).on("change mousemove", 'input[type="range"]', function() {
            jQuery(this).siblings(".data-slider-value").text(jQuery(this).val());
        })
		
        // This event is after widget is been updated
    jQuery(document).on('widget-updated widget-added', function(e, widget) {
        sortable();
        update_checkbox();
        checkbox_switch();
        radio_slide();
        colorpicker();
		 upload_media();
		 
		/* Images selector */
		// Add selection frame to images
		jQuery('.kalimah-widget-images img').click(function() {
			jQuery('.kalimah-widget-images img').removeClass("image-selected");
			jQuery(this).addClass("image-selected");
		});
		
        jQuery('.colorPicker input[type="text"]').each(function(i, e) {
            jQuery(e).css('background-color', jQuery(e).val());
        });
		
    });
    // Sortable for counter widget
    function sortable() {
        // Widget Sortable
        jQuery("#sortable ul").sortable({
            placeholder: "highlight", // Placeholder class
            containment: false, // Where the childern move in     
            tolerance: 'pointer',
            cursor: 'move', // Cursor type when boxes are moved
            forcePlaceholderSize: true,
            opacity: 0.2
        }).disableSelection();
    }
    sortable();
    update_checkbox();
    checkbox_switch();
    radio_slide();
    colorpicker();
  upload_media();
	

	
	
    // Colorpicker
    function colorpicker() {
        jQuery('.colorPicker input[type="text"]').colpick({
            layout: 'hex',
          
            onSubmit: function(hsb, hex, rgb, el) {
                if (jQuery(el).attr("name") == "primaryThemeColor") {
                    jQuery.ajax({
                        type: "POST",
                        cache: false,
                        url: ajaxurl,
                        dataType: 'json',
                        data: {
                            "action": "get_colors",
                            "color": '#' + hex
                        },
                        success: function(data) {
                            jQuery('#secondaryThemeColor input[type="text"]').css('background-color', '#' + data.secondary).val('#' + data.secondary);
                            jQuery('#tertiaryThemeColor input[type="text"]').css('background-color', '#' + data.tertiary).val('#' + data.tertiary);
                            jQuery('#fourthThemeColor input[type="text"]').css('background-color', '#' + data.fourth).val('#' + data.fourth);
                            jQuery('#fifthThemeColor input[type="text"]').css('background-color', '#' + data.fifth).val('#' + data.fifth);
                            jQuery('#hoverThemeColor input[type="text"]').css('background-color', '#' + data.hover).val('#' + data.hover);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {}
                    })
                }
              
                jQuery(el).css('background-color', '#' + hex);
                jQuery(el).val('#' + hex);
                jQuery(el).colpickHide();
            }
        });
    }

    function checkbox_switch() {
            // Hide or Show div related to switch
            var arr = jQuery('.switch').each(function(i, e) {
				var parent = jQuery(this).closest(".widget-content");
                var arr = jQuery(e).data("hide");
                var checkbox = jQuery(e).find(':checkbox');
                // Check first to see if there is data to hide
                if (arr) {
                    // Convert to string and split it
                    var final_arr = arr.toString().split(",");
                    // if the button is checked
                    if (checkbox.is(":checked") == true) {
                        // Toggle slide all elements in the array
                        jQuery.each(final_arr, function(index, value) {
                           parent.find('.' + value).show();
                        });
                    } else {
                        // Toggle slide all elements in the array
                        jQuery.each(final_arr, function(index, value) {
                            parent.find('.' + value).hide();
							
                        });
                    }
                }
            })
        }
        // Update checkboxes on load

    function update_checkbox() {
            jQuery('.switch input[type="checkbox"]').click(function(e) {
				var parent = jQuery(this).closest(".widget-content");
                var element = jQuery(this).find("input[type='checkbox']");
                var arr = jQuery(this).closest('.switch').data("hide");
                // Check first to see if there is data to hide
                if (arr) {
                    // Convert to string and split it
                    var final_arr = arr.toString().split(",");
                    // if the button is checked
                    if (jQuery(element.is(":checked"))) {
                        // Toggle slide all elements in the array
                        jQuery.each(final_arr, function(index, value) {
                             parent.find('.' + value).stop().stop().slideToggle('slow');
                        });
                    }
                }
            });
        }
		
	function upload_media()
	{
        // Media uploader
    jQuery('.upload-btn').click(function(e) {
        img = jQuery(this).siblings('img');
        input = jQuery(this).siblings('input');
        e.preventDefault();
        var image = wp.media({
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open().on('select', function(e) {
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            input.val(image_url);
            img.attr("src", image_url);
        });
    });
	}
    /* Toggle slide elements when radio form is clicked*/
    function radio_slide() {
        jQuery('.radio-enable-hide label').click(function() {
            var eleme = jQuery(this).attr('id');
            console.log(eleme);
            // Slide up (hide) all sibling elements
            jQuery(this).siblings('label').each(function(index, value) {
                var sibling = 'div#' + jQuery(this).attr('id');
                jQuery(sibling).stop().stop().slideUp('slow');
            });
            // Show the needed element
            jQuery('div#' + eleme).stop().stop().slideDown('slow');
        });
    }
});