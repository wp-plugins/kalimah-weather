<?php
/*
 * Plugin Name: Kalimah Apps - Widgets
 * Plugin URI: http://www.kalimah-apps.com
 * Description: Core Kalimah Apps plugin. It is neccessary for other polugins to work
 * Version: 1.0
 * Author: Kalimah Apps
 * Text Domain: KALIMAHAPPSWIDGETS
 * Author URI: http://www.kalimah-apps.com
 */

 
$file_dir = plugin_dir_path(__FILE__);


add_action('admin_init', 'add_scripts_style');
function add_scripts_style()
{
    $file_dir = plugin_dir_url(__FILE__);
    $options  = get_option('kalimah_options');
    
    // Call colorpicker
    wp_enqueue_script('color-picker', $file_dir . "resources/js/colpick.js", false, true);
    wp_enqueue_style('color-picker', $file_dir . "resources/css/colpick.css", false, true);
    
	 wp_enqueue_style("switch", $file_dir . "resources/css/style.css", false, "1.0", "all");
	 
	if(is_rtl())
		wp_enqueue_style("switch-rtl", $file_dir . "resources/css/rtl.css", false, "1.0", "all");

	
    wp_enqueue_script("jquery_calls", $file_dir . "resources/js/jquery-calls.js");
	
	wp_enqueue_media();
    
}
/*
add_action( 'admin_menu', 'register_my_custom_menu_page' );

function register_my_custom_menu_page(){
	add_menu_page( 'Kalimah Widgets', 'Kalimah Widgets', 'manage_options', 'kalimahwidgets', 'kalimahwidgets_page', plugins_url( 'myplugin/images/icon.png' ), 6 ); 
}

function kalimahwidgets_page(){
			
	  $exampleListTable = new Example_List_Table();
        $exampleListTable->prepare_items();
        ?>
            <div class="wrap">
                <div id="icon-users" class="icon32"></div>
					<div>
					<div><h2>Kalimah Widgets List</h2></div>
					<div><form action="" method="post" enctype="multipart/form-data">
							Upload Widget: <input type="file" name="widget_upload" class="custom-file-input" />
							<input type="submit" name="submit" value="Upload" />
					</form></div>
				</div>
				<?php
				// Check if image file is a actual image or fake image
				if(isset($_POST["submit"])) {

					$destination = wp_upload_dir();
					$destination_path = $destination['path'];

					 WP_Filesystem();

					$destination = wp_upload_dir();
				
				
					$destination_path = plugin_dir_path( __FILE__ )."widgets/";
					

					$folder_name = pathinfo($_FILES["widget_upload"]["name"], PATHINFO_FILENAME);
					if (file_exists($destination_path."/".$folder_name)) {
						   echo "<div class='error'>
							   <p>The widget <b>$folder_name</b> already exists</p>
							</div>";    
					} else {
						$unzipfile = unzip_file($_FILES["widget_upload"]["tmp_name"], $destination_path);
						   if ( !is_wp_error($unzipfile)) {
						    echo '<div class="updated">
							   <p>Successfully uploaded the widget!</p>
							</div>';      
					   } else {
						  echo $unzipfile->get_error_message();       
					   }
					}

					
					
				
				}
				?> 

                <?php $exampleListTable->display(); ?>
            </div>
        <?php
		
		
	
	//	$weather = "kalimah_weather_widget";
		
	//	echo get_option("widget_$weather");
}

*/
function kalimah_process_widget_options($array, $wp_object, $instance)
{
    $file_dir = plugin_dir_url(__FILE__);
    
    //print_r($instance);
    # Loop through each section to get form type and display it accordingly
    foreach ($array as $item) {
        # Set defaults
        $rows_count = (isset($item['rows'])) ? $item['rows'] : '10';
        $cols_count = (isset($item['cols'])) ? $item['cols'] : '30';
        
        # Check if slang exit for item
        $item_name = isset($item['slang']) ? $item['slang'] : $item['id'];
        
        $option_value = (!empty($instance[$item['id']])) ? $instance[$item['id']] : $item['std'];
        
        // echo "<br>----".print_r($instance);
        # Check if the divs should be hidden on first load and hide them (For divs that related to radio buttons)
        if (is_array($array_radio_options) && array_key_exists($item_name, $array_radio_options))
            $radio_hide_div = ($selected_radio_option != $item_name) ? "style='display: none;'" : "";
        
        
        switch ($item['type']) {
            # Text input
            case "text":
                $form .= "<div id='{$item['id']}' class='kalimah-widget-input clearfix {$item['id']} {$item['class']}'  {$radio_hide_div}>";
                $form .= "<span class='form-title'>{$item['name']}</span>";
                $form .= "<span class='form-bit'>";
                $form .= "<input id='" . $wp_object->get_field_id($item['id']) . "' name='" . $wp_object->get_field_name($item['id']) . "' type='{$item['type']}' size='{$item['size']}' value='{$option_value}'/>";
                $form .= "<span class='desc'>{$item['desc']}</span>";
                $form .= '</span>';
                
                $form .= '</div>';
                unset($radio_hide_div);
                break;
            
            
            # Media Uploader
            case "media":
                
                if ($option_value == '')
                    $option_value = $file_dir . '/resources/images/media_none.png';
                
                $form .= "<div id='{$item['id']}' class='kalimah-widget-media clearfix {$item['id']}'  {$radio_hide_div}>";
                $form .= "<span class='form-title'>{$item['name']}</span>";
                $form .= "<span class='form-bit'>";
                $form .= "<input id='" . $wp_object->get_field_id($item['id']) . "' name='" . $wp_object->get_field_name($item['id']) . "' type='hidden' size='{$item['size']}' value='{$option_value}'/>";
                $form .= "<img name='{$item['id']}' src='{$option_value}'/>";
                $form .= '<input type="button" name="upload-btn" id="' . $item["id"] . '" class="upload-btn button-secondary" value="' . __("Upload Image") . '">';
                $form .= "<span class='desc'>{$item['desc']}</span>";
                $form .= '</span></div>';
                unset($radio_hide_div);
                break;
            
            
            # Images selector
            case 'images':
                $file_dir = plugins_url();

                $form .= "<div id='{$item['id']}' class='kalimah-widget-images clearfix {$item['id']}'  {$radio_hide_div}>";
                
                if (isset($item['name']))
                    $form .= "<span class='form-title'>{$item['name']}</span>";
                
                $form .= "<span class='form-bit'>";
                foreach ($item['options'] as $key => $value) {
                    // Clear checked value
                    $radio_checked = '';
                    $radio_class   = '';
                    
                    # Check if the item is selected
                    if ($item['std'] == $key) {
                        // if item is checked create the variable and its value
                        // break the loop as we don't need it anymore for this field
                        $radio_checked = "checked = 'checked'";
                        $radio_class   = "class = 'image-selected'";
                    }
                    $form .= "<label class='images' id='{$key}'><input type='radio' id='" . $wp_object->get_field_id($item['id']) . "' name='" . $wp_object->get_field_name($item['id']) . "' value='{$key}' {$radio_checked}>";
                    $form .= "<img src='{$file_dir}/{$value}' option='{$key}' id='{$key}' {$radio_class}></label>";
                }
                $form .= "<span class='desc'>{$item['desc']}</span>";
                $form .= '</span></div>';
                unset($radio_hide_div);
                break;
            
            
            # Radio buttons
            case 'radio':
                # Does this radio group show/hide other elements?
                # Add class so we can use it through jQuery
                
                $class = (isset($item['enable-hide'])) ? "radio-enable-hide" : "";
                if (isset($item['enable-hide'])) {
                    $array_radio_options   = $item['options'];
                    $selected_radio_option = $option_value;
                }
                
                # Display group
                $form .= "<div id='{$item['id']}' class='kalimah-widget-radio clearfix {$item['id']} {$class}'  {$radio_hide_div}>";
                $form .= "<span class='form-title'>{$item['name']}</span>";
                $form .= "<span class='form-bit'>";
                foreach ($item['options'] as $key => $value) {
                    
                    $radio_checked = ($item['std'] == $key) ? "checked = 'checked'" : "";
                    $form .= "<label id='{$key}'><input type='radio' name='" . $wp_object->get_field_name($item['id']) . "' value='{$key}' {$radio_checked}> ";
                    $form .= "<span>{$value}</span></label>";
                }
                $form .= "<span class='desc'>{$item['desc']}</span>";
                $form .= '</span></div>';
                unset($radio_hide_div);
                break;
            
            # Multiple selection
            case "select":
                unset($multiple);
                unset($array);
                if ($item['multiple'] == 'yes') {
                    $multiple = 'multiple="multiple"';
                    $array    = '[]';
                }
                
                $option_value = unserialize($option_value);
                
                $form .= "<div id='{$item_name}' class='kalimah-widget-select clearfix {$item['id']}'  {$radio_hide_div}>";
                $form .= "<span class='form-title'>{$item['name']}</span>";
                $form .= "<span class='form-bit'>";
                $form .= "<select id='{$item['id']}' name='" . $wp_object->get_field_name($item['id']) . $array . "' {$multiple}>";
                
                foreach ($item['options'] as $key => $value) {
                    // Clear selected value
                    $selected = '';
                    $selected = ($item['std'] == $key) ? "selected" : "";
                    
                    $form .= "<option value='{$key}' {$selected}>{$value}</option>";
                }
                $form .= '</select>';
                $form .= "<span class='desc'>{$item['desc']}</span>";
                $form .= '</span></div>';
                unset($radio_hide_div);
                break;
            
            # Textarea type
            case "textarea":
                $form .= "<div id='{$item['id']}' class='kalimah-widget-select clearfix {$item['id']}'  {$radio_hide_div}>";
                
                if (isset($item['name']))
                    $form .= "<span class='form-title'>{$item['name']}</span>";
                
                $form .= "<span class='form-bit'>";
                $form .= "<textarea id='" . $wp_object->get_field_id($item['id']) . "' name='" . $wp_object->get_field_name($item['id']) . "' rows='{$rows_count}' cols='{$cols_count}' >{$option_value}";
                $form .= '</textarea>';
                $form .= "<span class='desc'>{$item['desc']}</span>";
                $form .= "</span></div>";
                
                unset($radio_hide_div);
                break;
            
            # Checkbox type
            case "checkbox":
                
                $form .= "<div id='{$item['id']}' class='kalimah-widget-checkbox clearfix {$item['id']}'  {$radio_hide_div}>";
                $form .= "<span class='form-title'>{$item['name']}</span>";
                $form .= "<span class='form-bit'>";
                $option_value = unserialize($option_value);
                
                foreach ($item['options'] as $key => $value) {
                    // Clear checked value
                    $checked = '';
                    
                    # Check that we don't have empty values for this option 
                    if (is_array($option_value)) {
                        # Check if the item is selected
                        foreach ($option_value as $checked_key) {
                            if ($key == $checked_key) {
                                // if item is checked create the variable and its value
                                // break the loop as we don't need it anymore for this field
                                $checked = "checked";
                                break;
                            }
                        }
                    }
                    // Outpot the form
                    $form .= "<label><input type='checkbox' id='{$key}' name='" . $wp_object->get_field_name($item['id']) . "[]' value='{$key}' {$checked}>";
                    $form .= "{$value}</label><br>";
                }
                $form .= "<span class='desc'>{$item['desc']}</span>";
                $form .= '</span>';
                $form .= '</div>';
                unset($radio_hide_div);
                break;
            
            # Range type
            case "range":
                if ($item['snap'] == 'true')
                    $snap = "data-slider-snap='true'";
                
                if ($item['step'] != '')
                    $step = "step='{$item['step']}'";
                
                $range = explode(',', $item['range']);
                $min   = $range[0];
                $max   = $range[1];
                
                $form .= "<div id='{$item['id']}' class='kalimah-widget-range clearfix {$item['id']}'  {$radio_hide_div}>";
                $form .= "<span class='form-title'>{$item['name']}</span>";
                $form .= "<span class='form-bit'>";
                $form .= "<input id='" . $wp_object->get_field_id($item['id']) . "' name='" . $wp_object->get_field_name($item['id']) . "' type='range' min='{$min}' max='{$max}' value='{$option_value}' {$snap} {$step}>";
                $form .= "<span class='data-slider-value'>{$option_value}</span>";
                $form .= "<span class='data-slider-units'> {$item['units']}</span>";
                $form .= "<span class='desc'>{$item['desc']}</span>";
                $form .= '</span></div>';
                unset($radio_hide_div);
                break;
            
            
            # On/off type
            case "switch":
                # Add the hide array and the option value to a variable
                # in order to be used in the next circle of the loop
                $hide_array   = $item['hide'];
                $switch_value = $option_value;
                
                # Prepare hide/show spcified divs on switch click
                $hide    = ($item['hide']) ? "data-hide='" . implode(',', $item['hide']) . "'" : "";
                $checked = ($item['std'] == "checked") ? "checked='checked'" : " ";
                
                
                $form .= "<div id='{$item['id']}' class='kalimah-widget-checkbox switch switch-info clearfix {$item['id']}' {$hide}>";
                $form .= "<span class='form-title'>{$item['name']}</span>";
                $form .= "<span class='form-bit'>";
                
                # We add hidden form to submit value if the box is unchecked (it has to have same name as the checbox)
                $form .= "<input type='hidden' name='" . $wp_object->get_field_name($item['id']) . "' value='n' />";
                
                $form .= "<label><input id='switch-{$item['id']}' type='checkbox' name='" . $wp_object->get_field_name($item['id']) . "' value='checked' {$checked} />";
                $form .= "<span></span></label>";
                
                $form .= "<span class='desc'>{$item['desc']}</span>";
                $form .= '</span></div>';
                unset($radio_hide_div);
                unset($hide_div);
                break;
            
            
            # Section Start
            case "hidden":
                $form .= "<input type='hidden' id='hidden-{$item['id']}' value='{$item['std']}' name='" . $wp_object->get_field_name($item['id']) . "' />";
                break;
            
            # Section Start
            case "section_start":
                # check if section is a group of divs 
                if (isset($item['group']) AND $item['group'] == 'yes') {
                    $form .= "<div id='{$item['id']}' class='clearfix {$item['id']}'  {$radio_hide_div}>";
                } else {
                    $form .= "<div id='{$item['id']}' class='kalimah-widget-section-start clearfix {$item['id']}'  {$radio_hide_div}>";
                    $form .= "<h2>{$item['name']}</h2><hr>";
                }
                
                # We don't need the variable here so unset
                unset($radio_hide_div);
                break;
            
            # Section End
            case "section_end":
                $form .= "</div>";
                break;
            
            
            # Soft-section Start
            # We mainly need this for hide/unhiding divs
            case "softsection_start":
                $form .= "<div id='{$item['id']}' class='kalimah-widget-softsection-start clearfix {$item['id']}'  {$radio_hide_div}>";
                
                # We don't need the variable here so unset
                unset($radio_hide_div);
                break;
            
            # sub-section End
            case "softsection_end":
                $form .= "</div>";
                break;
        }
    }
    return $form;
}


?>