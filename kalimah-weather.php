<?php
/*
 * Plugin Name: Kalimah Widgets - Weather (lite)
 * Plugin URI: http://www.kalimah-apps.com
 * Description: This widget allows you to easily add super clean weather widget to your site. Create a weather, select your city, choose the options and customize the look in a few clicks. The weather data is provided for free by http://openweathermap.org
 * Version: 1.0.2
 * Author: Kalimah Apps
 * Text Domain: KALIMAHAPPSWIDGETS
 * Author URI: http://www.kalimah-apps.com
 */

class kalimah_weather_light extends WP_Widget 
{
	var $domain = 'kalimahwidgets';
	
	function kalimah_weather_light() {
		$widget_ops = array( 'classname' => 'kalimah_weather_light', 'description' => 'Display Weather Information' );
		$control_ops = array( 'width' => 330, 'height' => 350, 'id_base' => 'kalimah_weather_light' );
		$this->WP_Widget( 'kalimah_weather_light', 'Kalimah: Weather (lite)', $widget_ops, $control_ops );
		
		add_action('init', array($this, 'loadTextDomain'));
		add_action('admin_init', array($this, 'check_kalimah_widgets_plugin'));
		
	}

	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$file_dir = plugin_dir_url( __FILE__ );

		wp_enqueue_style( 'kalimah-weather-light', $file_dir."kalimah-weather.css", array(), '', 'all');
		

		$title 			= apply_filters('widget_title', $title );
		$forcast_days = $instance['forcast_days'] +1;
		$dispaly_country = $city;

		
		if($instance['city_type'] == 'ip_address')
		{
			$ip = $_SERVER['REMOTE_ADDR'];
			$details = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));

			$city = $details->city.",".$details->countryCode; // 
			$dispaly_country = $details->city.", ".$details->country; // 
		}
		
		if($dispaly_country == '')
			$dispaly_country = "No city selected";

		echo $before_widget;

		
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			
			$format = ($units == 'C') ? "metric" : "imperial";
			$data = $this->get_external_json("http://api.openweathermap.org/data/2.5/forecast/daily?q=".urlencode($city)."&mode=json&cnt={$forcast_days}&units={$format}", $enable_cache, $cache_period, $this->id);
			
			
				$css = '<style> 
						#'.$this->id.' .weather_widget
						{';

						$css .= 'background-color: '.$background_color;
						$css .='; 
						color: '.$text_color.'
						}
				
						#'.$this->id.' .weather_footer
						{
							border-top: 5px groove '.$text_color.';
						}
						
						.data_not_found
						{
							color: '.$text_color.'
						}
						
						.weather_footer .forcast_temp > div
						{
							width: calc(100% / '.($forcast_days-1).');
						}';
											
						$css .= '</style>';
				echo $css;

				$wind_label = array ( 
							__('North', $this->domain),
							__('NNE', $this->domain),
							__('North-East', $this->domain),
							__('ENE', $this->domain),
							__('East', $this->domain),
							__('ESE', $this->domain),
							__('South-East', $this->domain),
							__('SSE', $this->domain),
							__('South', $this->domain),
							__('SSW', $this->domain),
							__('South-West', $this->domain),
							__('WSW', $this->domain),
							__('West', $this->domain),
							__('WNW', $this->domain),
							__('North-West', $this->domain),
							__('NNW', $this->domain)
						);
						
				$wind_direction = $wind_label[ fmod((($data->list[0]->deg + 11) / 22.5), 16) ];
				
					if($data->list[0]->weather[0]->main == 'Rain')
					{
						$class = 'rain';
						$icon = 'l';
					}
					elseif($data->list[0]->weather[0]->main == 'Clear')
					{
						$class = 'clearsun';
						$icon = 'c';
					}
					elseif($data->list[0]->weather[0]->main == 'Clouds')
					{
						$class = 'cloudsweather';
						$icon = 'n';
					}	
						
				
			
	
			?>
			
				<div class='weather_widget'>
					<?php 
				
					if($data->cod == "200") :
					?>
							
						<div class='weather_header'>
								<span class='weather_city'><?php echo $dispaly_country; ?></span>
								<?php if($date == 'checked'): ?>
									<span class='weather_date'><?php echo date("d.m.Y") ?></span>
								<?php endif; ?>
							</div>
						<div class='weather_body clearfix'>
							<div class="temp-icon clearfix">
								<span class='weather_icon' title="<?php echo $data->list[0]->weather[0]->main; ?>"><?php echo $icon; ?></span>
								<span class='temp' title="<?php echo $data->list[0]->weather[0]->main; ?>"><?php echo round($data->list[0]->temp->day); ?>°</span>
							</div>
							
							<div class="weather-details clearfix">
							<?php if($humidity == 'checked'): ?>
								<span title="<?php echo __("Humidity Level", $this->domain);?>" class='humidity'><?php echo round($data->list[0]->humidity); ?>%</span>
							<?php endif; ?>
							<?php if($wind == 'checked'): ?>
								<span title="<?php echo __("Wind Speed", $this->domain);?>" class='wind'><?php echo round($data->list[0]->speed); echo "km "; ?></span>
							<?php endif; ?>
							<?php if($highlow == 'checked'): ?>
								<span title="<?php echo __("Highest/Lowest Temperature", $this->domain);?>" class='highlow'>L<?php echo round($data->list[0]->temp->min);?>° H<?php echo round($data->list[0]->temp->max); ?>°</span>
							<?php endif; ?>
							<?php if($direction == 'checked'): ?>
								<span title="<?php echo __("Wind Direction", $this->domain);?>" class='direction'><?php echo $wind_direction; ?></span>
							<?php endif; ?>
						</div>
						</div>
						<?php if($forecast == 'checked') :?>
						<div class='weather_footer condensed'>
							<span class='forcast_temp clearfix'>
							<?php 
							if(gettype ($data) == 'object')
							{
								foreach($data as $key => $value)
								{
									if(is_array($value))
										foreach($value as $weather_key => $weather_details)
										{
											$title = date('D', $weather_details->dt)." ".$weather_details->weather[0]->main." ".round($weather_details->temp->min)."°/".round($weather_details->temp->max)."°";
										
											if($weather_details->weather[0]->main == 'Rain')
													$icon = 'l';
												elseif($weather_details->weather[0]->main == 'Clear')
													$icon = 'c';
												elseif($weather_details->weather[0]->main == 'Clouds')
														$icon = 'n';
														
														
												if($weather_key != 0)
												{
													echo "<div class='clearfix'>";
											
														echo "<span class='day' title='$title'>".date('D', $weather_details->dt)."</span>";
														echo "<span class='weather_icon' title='$title'>$icon</span>";
														echo "<span class='small_temp' title='$title'>".round($weather_details->temp->min)."°/".round($weather_details->temp->max)."°</span>";
													
													echo "</div>";
												}
										}
								}
							}
							?></span>
						</div>
						<?php endif;
						
						else :
							echo "<div class='data_not_found'>No Data Found</div>";
						endif; 
						?>	
				</div>
				
			<?php
		echo 	$after_widget;
	
	}
	
	function get_external_json($url, $enabel_cache, $minutes, $id)
	{
		$current_time = time();
		$cache_stored_time = get_option("widget_kalimah_weather_sortedtime_".$id, time());

		
		if(($enabel_cache == 'checked') && (strtotime("+{$minutes} minute", $cache_stored_time) > $current_time))
		{
			$data = get_option('widget_kalimah_weather_data_'.$id);
			$stored_time = $cache_stored_time;
		}
		else
		{
			$stored_time = time();
			$data = wp_remote_get($url);
			if( is_wp_error( $data ) )
				return $data->get_error_message();
		
			$data = wp_remote_retrieve_body($data);
			$data = json_decode($data);
			
			
		}
		
		update_option("widget_kalimah_weather_data_".$id, $data);
		update_option("widget_kalimah_weather_sortedtime_".$id, $stored_time);
		
		
		return $data;
	}
	


	function print_my_inline_script() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
	   // This event is after widget is been updated
		jQuery(document).on('widget-updated widget-added', function(e, widget) {
			 geocomplete();
		});
	
		geocomplete();
		// Geo complete for weather widget
		function geocomplete(){
		jQuery("#city_list input").geocomplete().bind("geocode:result", function(event, result) {
				console.log(result.formatted_address);
				
				if (result.address_components[2] != undefined)
				{
					country_name = result.address_components[2].short_name;
					country_long_name = result.address_components[2].long_name;
				}
				else
				{
					country_name = result.address_components[1].short_name;
					country_long_name = result.address_components[1].long_name;
				}
				
				jQuery(this).closest("#city_list").siblings("#hidden-city").val(result.formatted_address);//result.address_components[0].short_name + "," + country_name+ "," + country_long_name);
			})
			.bind("geocode:error", function(event, status) {
				console.log("ERROR: " + status);
			})
			.bind("geocode:multiple", function(event, results) {
				console.log("Multiple: " + results.length + " results found");
			});
		}
		});
	
	</script>
	<?php
	  
	}

	
	function form( $instance ) {
		wp_enqueue_script( 'google-places', "//maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places", array(), '3', true);
			
		$file_dir = plugin_dir_url( __FILE__ );
		
		$this->print_my_inline_script();
		wp_enqueue_script( 'kalimah-weather-geocomplete', $file_dir."geocomplete.js", false, true );
		
		$defaults = array(
			'title' => '',
			'city' => 'London,uk',
			'city_type' => 'ip_address',
			'city_list' => '',
			'units' => 'C',
			'forecast' => 'checked',
			'direction' => 'checked',
			'humidity' => 'checked',
			'wind' => 'checked',
			'highlow' => 'checked',
			'forcast_days' => '5',
			'background_color' => '#345799',
			'text_color' => '#ffffff',
			'background_type' => 'color',
			'forcast_display' => 'condensed',
			'enable_cache' => 'checked'
		);
		
	
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		
	
		
	
$kalimah_options = array(
    array(
        "name" => __('Options', $this->domain),
        "type" => "section_start"
    ),
	array(
        "name" => __('Title', $this->domain),
        "desc" => __('Optional', $this->domain),
        "id" => "title",
        "type" => "text",
        "std" => $instance['title']
    ),
    array(
        "name" => __('City location', $this->domain),
        "desc" => "",
        "id" => "city_type",
        "type" => "radio",
        "std" => $instance['city_type'],
		"options" => array('ip_address' => __("IP address", $this->domain), "city_list"=> __("Manual", $this->domain)),
		"enable-hide" => "yes"
    ),array(
        "name" => __('City', $this->domain),
        "desc" => "",
        "id" => "city_list",
        "type" => "text",
        "std" => $instance['city_list']
    ), array(
       
        "id" => "city",
        "type" => "hidden",
        "std" => $instance['city']
    ),
	array(
        "name" => __('Show Date', $this->domain),
        "desc" => "",
        "id" => "date",
        "type" => "switch",
        "std" => $instance['date'],
    ),
    array(
        "name" => __('Units', $this->domain),
        "desc" => "",
        "id" => "units",
        "type" => "radio",
        "std" => $instance['units'],
		"options" => array('F' => __("F", $this->domain), "C"=> __("C", $this->domain))
    ), 
	array(
        "name" => __('Show Wind', $this->domain),
        "desc" => "",
        "id" => "wind",
        "type" => "switch",
        "std" => $instance['wind']
    ),array(
        "name" => __('Show Humidity', $this->domain),
        "desc" => "",
        "id" => "humidity",
        "type" => "switch",
        "std" => $instance['humidity'],
    ),array(
        "name" => __('Show Direction', $this->domain),
        "desc" => "",
        "id" => "direction",
        "type" => "switch",
        "std" => $instance['direction'],
    ),array(
        "name" => __('Show Highest/Lowest Temperature', $this->domain),
        "desc" => "",
        "id" => "highlow",
        "type" => "switch",
        "std" => $instance['highlow'],
    ),
	
	array(
        "name" => __('Text Color', $this->domain),
        "desc" => "",
        "id" => "text_color",
        "type" => "text",
        "std" => $instance['text_color'],
		"class" => "colorPicker"
    ),
	
   	array(
        "name" => __('Background Color', $this->domain),
        "desc" => "",
        "id" => "background_color",
        "type" => "text",
        "std" => $instance['background_color'],
		"class" => "colorPicker"
    ),
   
	 array(
        "name" => __('Forecast Options', $this->domain),
        "type" => "section_start"
    ),
	array(
        "name" => __('Show Forecast', $this->domain),
        "desc" => "",
        "id" => "forecast",
        "type" => "switch",
        "std" => $instance['forecast'],
		"hide" => array("forcast_days", "forcast_display", "forcast_options")
    ),
	array(
        "name" => __('Forecast Days', $this->domain),
        "desc" => "",
        "id" => "forcast_days",
        "type" => "range",
		"range" => "1,5",
		"step" => "1",
		"units" => __("Days", $this->domain),
		"std" => $instance['forcast_days']
    ),

	 array(
        "type" => "section_end"
    ),
	
	 array(
		"name" => __("Cache", $this->domain),
        "type" => "section_start"
    ),
	array(
        "name" => __("Enable Cache", $this->domain),
        "desc" => "",
        "id" => "enable_cache",
        "type" => "switch",
        "std" => $instance["enable_cache"],
		"hide" => array("cache_period")
    ),
	array(
        "name" => __("Update Cache Every", $this->domain),
        "desc" => "",
        "id" => "cache_period",
        "type" => "range",
		"range" => "1,360",
		"step" => "1",
		"units" => __("Mins", $this->domain),
        "std" => $instance["cache_period"]
		),
	array(
        "type" => "section_end"
    ),
    array(
        "type" => "section_end"
    ),array(
        "id" => "widget_id",
        "type" => "hidden",
        "std" => $instance["widget_id"]
		)
);


echo kalimah_process_widget_options($kalimah_options, $this, $instance);
	}
	
		
function loadTextDomain() {
    $locale = get_locale();
    $languageDir = dirname(__FILE__) . '/languages';

    $domain = 'kalimahwidgets';
    $mofile = $languageDir . '/' . $locale . '.mo';

	if(!file_exists($mofile))
		return false;
	
    global $l10n;
    $mo = new MO();
    if (!$mo->import_from_file($mofile)) {
        return false;
    }

    if (isset($l10n[$domain]) && !empty($l10n[$domain]->entries)) {
        $l10n[$domain]->merge_with($mo);
    } else {
        $l10n[$domain] = $mo;
    }
}	

function check_kalimah_widgets_plugin() {
  /**
 * Detect plugin. For use in Admin area only.
 */
if ( !is_plugin_active( 'KalimahWidgets/kalimahwidgets.php' ) ) {
  add_action( 'admin_notices', array($this, 'my_admin_error_notice' ));
}else
{
	 add_action( 'admin_notices', array($this, 'buy_full_version' ));
}
}

function my_admin_error_notice() {
	$class = "error";
	$message = "Kalimah Apps - Widget is not installed or activated. Please download it from <a href='https://wordpress.org/plugins/kalimah-apps-widgets/'>Kalimah Apps Widgets</a> and activate";
        echo"<div class=\"$class\"> <p>$message</p></div>"; 
}

function buy_full_version() {
	$class = "update-nag";
	$message = "Buy Kalimah Weather (full version) from <a href='http://kalimah-apps.diy-cms.com/?product=kalimah-weather'>Kalimah Apps</a>";
        echo"<div class=\"$class\"> <p>$message</p></div>"; 
}

}

function kalimah_weather_light() {
	register_widget('kalimah_weather_light');
}

add_action('widgets_init', 'kalimah_weather_light');




?>