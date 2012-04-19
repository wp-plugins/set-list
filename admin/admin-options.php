<?php
// PVW PLUGIN ADMIN FRAMEWORK -------------------------------------------------------------------------------------//
// ADMIN OPTIONS //

if (!function_exists('pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_options_template_init')) {

/* Set up options template */
function pvw_slp_options_template_init(){
	
	$shortname = SLP_PVW_PLUGIN_SHORTCODE;
	
	// Set the Options Array
	$options = array();
	
	
	// ---------------------------------------------------------------------------------------------------------------------------------------------//					
		
	$options[] = array( "name" => __('Shortcode',SLP_PVW_PLUGIN_LINK),
						"type" => "heading");
						
	$options[] = array( "name" => "",
						"message" => __('If you have issues finding a gig using the plugin, please check the gig at ',SLP_PVW_PLUGIN_LINK).'<a href="http://www.setlist.fm/">setlist.fm</a>',
						"type" => "intro");
						

	$table_shots = array(	
							array(__('Parameter',SLP_PVW_PLUGIN_LINK),__('Default',SLP_PVW_PLUGIN_LINK),__('Options',SLP_PVW_PLUGIN_LINK),__('Description',SLP_PVW_PLUGIN_LINK)),
							array('artist','','',__('Name of artist',SLP_PVW_PLUGIN_LINK)),
							array('date','','',__('Date of gig. Use date format DD-MM-YYYY',SLP_PVW_PLUGIN_LINK)),
							array('venue','','',__('Venue name of gig. eg. O2 Academy Bournemouth',SLP_PVW_PLUGIN_LINK))
						
						);
	
	$options[] = array( "name" => "",
						"message" => 	sprintf(__('The shortcode of %1$s can be used in posts and pages. It has the following options',SLP_PVW_PLUGIN_LINK), '<b>slp_get_setlist</b>').': <br/>'.
										slp_pvw_plugin_framework::pvw_table_generator($table_shots).
										'<b>'.__('Example',SLP_PVW_PLUGIN_LINK).': </b><br/>[slp_get_setlist artist=\'Drake\' date=\'01-03-2012\' venue=\'Sprint Center\']',
						"type" => "note");
						
	
											
	// ---------------------------------------------------------------------------------------------------------------------------------------------//					

	$options[] = array( "name" => __('General Settings',SLP_PVW_PLUGIN_LINK),
						"type" => "heading");
						
	$options[] = array( "name" => "",
						"message" => __('These are general settings for the plugin',SLP_PVW_PLUGIN_LINK),
						"type" => "intro");
						
	
	
	$options[] = array( "name" => __('Enable Caching for Widgets and Shortcodes',SLP_PVW_PLUGIN_LINK),
					"desc" => __('Check this to enable caching of the setlist.fm API data.',SLP_PVW_PLUGIN_LINK),
					"id" => $shortname."_cache_config",
					"std" => "true",
					"type" => "checkbox");
	
	$options[] = array( "name" => __('Cache Expiry',SLP_PVW_PLUGIN_LINK),
					"desc" => __('Enter the expiry of the cache, in seconds. Default is a week. Only works if caching is enabled',SLP_PVW_PLUGIN_LINK),
					"id" => $shortname."_cache_exp",
					"std" => "604800",
			 		"type" => "text");
					
	$options[] = array( "name" => __('Clear Cache',SLP_PVW_PLUGIN_LINK),
					"desc" => __('Clear the cache manually for widgets and shortcodes',SLP_PVW_PLUGIN_LINK),
					"id" => $shortname."_cache_clear",
					"text" => "Clear",
					"method" => "clear_cache",
					"message" => "Cache cleared!",
					"type" => "button");
					
		$options[] = array( "name" => sprintf(__('Create link to the %1$s plugin page',SLP_PVW_PLUGIN_LINK),SLP_PVW_PLUGIN_NAME),
					"desc" => __('Check this to enable a credit link to the plugin page at the end of the widget or shortcode output',SLP_PVW_PLUGIN_LINK),
					"id" => $shortname."_sl_credit_link",
					"std" => "false",
					"type" => "checkbox");
					
	// ---------------------------------------------------------------------------------------------------------------------------------------------//					
							
	$options[] = array( "name" => __('Custom Styling',SLP_PVW_PLUGIN_LINK),
						"type" => "heading");
						
	$options[] = array( "name" => "",
						"message" => __('Configure the look of the output created by this plugin. Style the elements created by the widget and shortcode',SLP_PVW_PLUGIN_LINK),
						"type" => "intro");
						
	$table_css = array(	
							array(__('CSS Class',SLP_PVW_PLUGIN_LINK),__('Parent Class',SLP_PVW_PLUGIN_LINK),__('Child Tags',SLP_PVW_PLUGIN_LINK),__('Used By',SLP_PVW_PLUGIN_LINK),__('Description',SLP_PVW_PLUGIN_LINK)),
							array('.setlist','',array('ul','p','span.encore'),'slp_get_setlist',__('Overall wrapper div for the set list. Child tags include span for encore text.',SLP_PVW_PLUGIN_LINK)),
							array('.setlist ul','','li','slp_get_setlist',__('Unordered list used for songs.',SLP_PVW_PLUGIN_LINK)),
							array('.setlist p','',array('span.artist','span.venue','span.date'),'slp_get_setlist',__('You can style the artist, venue and date text which are wrapped in a span tag.',SLP_PVW_PLUGIN_LINK)),
							array('.plugin_credit','.setlist','','slp_get_setlist',__('Plugin credit link at the end of set list.',SLP_PVW_PLUGIN_LINK)),
							
							
						);
	
	$options[] = array( "name" => "",
						"message" => 	__('The plugin uses a variety of CSS classes that you can apply custom styling to',SLP_PVW_PLUGIN_LINK).': <br/>'.
										slp_pvw_plugin_framework::pvw_table_generator($table_css),
						"type" => "note");
	
	
	$css_options = array("default" => "Plugin's Default CSS","custom" => "Custom CSS", "none" => "No Extra CSS, Use Theme's"); 
	
	$options[] = array( "name" => __('CSS Settings',SLP_PVW_PLUGIN_LINK),
					"desc" => __('Select the set list is displayed',SLP_PVW_PLUGIN_LINK),
					"id" => $shortname."_css_options",
					"std" => "default",
					"options" => $css_options,
					"type" => "radio");
	
	$options[] = array( "name" => __('Custom CSS',SLP_PVW_PLUGIN_LINK),
					"desc" => __('Insert your custom CSS ',SLP_PVW_PLUGIN_LINK),
					"id" => $shortname."_custom_css",
					"std" => "",
					"options" => array("rows" => "20"),
					"type" => "textarea");
						
	
	// ---------------------------------------------------------------------------------------------------------------------------------------------//					
	
	// PLUGIN DEBUG AND SUPPORT SECTION
	
	// ---------------------------------------------------------------------------------------------------------------------------------------------//					
							
	$options[] = array( "name" => __('Plugin Support',SLP_PVW_PLUGIN_LINK),
						"type" => "heading");
						
	$options[] = array( "message" => __('If you have any issues with the please visit the <a href="http://www.polevaultweb.com/support/forum/'.SLP_PVW_PLUGIN_LINK.'-plugin/">Support Forum</a>.',SLP_PVW_PLUGIN_LINK),
						"type" => "intro");
						
	$options[] = array( "name" => __('Donations',SLP_PVW_PLUGIN_LINK),
						"desc" => __('If you like the plugin or receive support then please consider donating so we can keep on developing and supporting.',SLP_PVW_PLUGIN_LINK),
						"type" => "donation");
						
	$options[] = array( "name" => "",
					"desc" => __('If you raise a topic or reply on the Support Forum about an issue you are having, please send the following debug data so we can troubleshoot your issue',SLP_PVW_PLUGIN_LINK),
					"id" => $shortname."_send_debug",
					"text" => __('Send Debug Data', SLP_PVW_PLUGIN_LINK),
					"method" => "pvw_send_debug_data",
					"message" => __('Debug data sent! Thank you', SLP_PVW_PLUGIN_LINK),
					"std" => "debug",
					"type" => "button");
						
	$options[] = array( "name" => "",
						"type" => "debug");

						
	
			
	
	// ---------------------------------------------------------------------------------------------------------------------------------------------//					
	// Save options
	
	update_option('pvw_'.$shortname.'_template',$options); 
	
    
}
}


function slp_pvw_validate_setting($plugin_options) {
	  
		$shortname = SLP_PVW_PLUGIN_SHORTCODE;
		
		$template = get_option('pvw_'.$shortname.'_template');
		
		//var_dump($plugin_options);
		
		foreach($template as $option) {
		
			if($option['type'] == 'checkbox') {
							
				$id = $option['id'];
							
				$key = array_search($id, $plugin_options);	
			
				//array_push($plugin_options, $option['id']=>false);
				if ( !$plugin_options[$id]  ) {
						
					$plugin_options[$id] = "false";
					
				}
				
			}
			
			
			
		}
		
		return $plugin_options;
}


?>