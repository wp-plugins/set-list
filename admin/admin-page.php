<?php
// PVW PLUGIN ADMIN FRAMEWORK -------------------------------------------------------------------------------------//
// ADMIN PAGE INTERFACE //

// PVW PLUGIN ADMIN FRAMEWORK -------------------------------------------------------------------------------------//
// FUNCTIONS //


if (!class_exists('slp_pvw_plugin_framework')) {

class slp_pvw_plugin_framework {

	function pvw_get_image_src($string){
		
		preg_match( '/src="(.+?)"/',$string,$matches);
		return $matches[1]; 
	} 

	function pvw_get_custom_string($customtext, $pos_text, $find_text, $default_pos = "start") {

		$find_text = '%%'.$find_text.'%%';
		
		if ($customtext == "") {
		
			$newtext = $pos_text;
		}
		else {
		
			if (strpos($customtext,$find_text) === false) {
			
				$newtext = $pos_text.' '.$customtext;
			}
			else
			{
				$newtext = str_replace($find_text, $pos_text, $customtext);
			}
		
		}
		
		return $newtext;

	}

	function pvw_get_debug_info($type) {

		global $current_user, $wpt_version;

		get_currentuserinfo();
		
		$request = '';

		$version = $wpt_version;
		// send fields for all plugins
		$wp_version = get_bloginfo('version');
		$home_url = home_url();
		$wp_url = get_bloginfo('wpurl');
		$language = get_bloginfo('language');
		$charset = get_bloginfo('charset');
		// server
		$php_version = phpversion();
		
		$curl_init = ( function_exists('curl_init') )?'yes':'no';
		$curl_exec = ( function_exists('curl_exec') )?'yes':'no';
		
		// theme data
		$theme_path = get_stylesheet_directory().'/style.css';
		$theme = get_theme_data($theme_path);
			$theme_name = $theme['Name'];
			$theme_uri = $theme['URI'];
			$theme_parent = $theme['Template'];
			$theme_version = $theme['Version'];
		// plugin data
		$plugins = get_plugins();
		$plugins_string = '';
			foreach( array_keys($plugins) as $key ) {
				if ( is_plugin_active( $key ) ) {
					$plugin =& $plugins[$key];
					$plugin_name = $plugin['Name'];
					$plugin_uri = $plugin['PluginURI'];
					$plugin_version = $plugin['Version'];
					
					if ($type == 'html') {
						$plugins_string .= "<b>$plugin_name:</b> $plugin_version; $plugin_uri<br/>";
					}else {
						$plugins_string .= "$plugin_name: $plugin_version; $plugin_uri\n";
					}
					
				}
			}

		if ($type == 'html') {
		
		$data = '';	
		$data .= '<b>================ '.__('Debug Data',SLP_PVW_PLUGIN_LINK).' - '.SLP_PVW_PLUGIN_NAME.' ====================</b><br/><br/>';
		$data .= '<b>==WordPress==</b><br/><br/>';
		$data .= '<b>'.__('Version',SLP_PVW_PLUGIN_LINK).': </b>'.$wp_version.'<br/>';
		$data .= '<b>'.__('URL',SLP_PVW_PLUGIN_LINK).': </b>'.$home_url.'<br/>';
		$data .= '<b>'.__('Install',SLP_PVW_PLUGIN_LINK).': </b>'.$wp_url.'<br/>';
		$data .= '<b>'.__('Language',SLP_PVW_PLUGIN_LINK).': </b>'.$language.'<br/>';
		$data .= '<b>'.__('Charset',SLP_PVW_PLUGIN_LINK).': </b>'.$charset.'<br/>';
		$data .= '<br/><br/>';
		$data .= '<b>=='.__('Extra Info',SLP_PVW_PLUGIN_LINK).'==</b><br/><br/>';
		$data .= '<b>'.__('PHP Version',SLP_PVW_PLUGIN_LINK).': </b>'. $php_version.'<br/>';
		$data .= '<b>'.__('Server Software',SLP_PVW_PLUGIN_LINK).': </b>'. $_SERVER['SERVER_SOFTWARE'].'<br/>';
		$data .= '<b>'.__('User Agent',SLP_PVW_PLUGIN_LINK).': </b> '.$_SERVER['HTTP_USER_AGENT'].'<br/>';
		$data .= '<b>'.__('cURL Init',SLP_PVW_PLUGIN_LINK).': </b>'. $curl_init.'<br/>';
		$data .= '<b>'.__('cURL Exec',SLP_PVW_PLUGIN_LINK).': </b>'. $curl_exec.'<br/>';
		$data .= '<br/><br/>';			
		$data .= '<b>=='.__('Theme',SLP_PVW_PLUGIN_LINK).'==</b><br/><br/>';
		$data .= '<b>'.__('Name',SLP_PVW_PLUGIN_LINK).': </b>'.$theme_name.'<br/>';
		$data .= '<b>'.__('URI',SLP_PVW_PLUGIN_LINK).': </b>'.$theme_uri.'<br/>';
		$data .= '<b>'.__('Parent',SLP_PVW_PLUGIN_LINK).': </b>'.$theme_parent.'<br/>';
		$data .= '<b>'.__('Version',SLP_PVW_PLUGIN_LINK).': </b>'.$theme_version.'<br/>';
		$data .= '<br/><br/>';			
		$data .= '<b>=='.__('Active Plugins',SLP_PVW_PLUGIN_LINK).'==</b><br/><br/>';
		$data .= $plugins_string;
		
		}
		else {

		$data = "
================ Debug Data - ".SLP_PVW_PLUGIN_NAME." ====================

==WordPress==
Version: $wp_version
URL: $home_url
Install: $wp_url
Language: $language
Charset: $charset

==Extra info==
PHP Version: $php_version
Server Software: ".$_SERVER['SERVER_SOFTWARE']."
User Agent: ".$_SERVER['HTTP_USER_AGENT']."
cURL Init: $curl_init
cURL Exec: $curl_exec

==Theme==
Name: $theme_name
URI: $theme_uri
Parent: $theme_parent
Version: $theme_version

==Active Plugins==
$plugins_string
	";
	
}
					
		return $data;
					
	}

	function pvw_send_debug_data() {

		global $current_user;
		get_currentuserinfo();
		
		$subject = SLP_PVW_PLUGIN_NAME.' Support - Debug Data';
		$message = self::pvw_get_debug_info('plain');
		
		$headers =  "From: \"$current_user->display_name\" <$current_user->user_email>\r\n";
		
		//print $message;
		wp_mail( 'info@polevaultweb.com',$subject,$message,$headers);
	}

	function pvw_attach_image($url) {

		$tmp = download_url( $url );
		$file_array = array(
			'name' => basename( $url ),
			'tmp_name' => $tmp
		);
		
		// Check for download errors
		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array[ 'tmp_name' ] );
			return $tmp;
		}

		$id = media_handle_sideload( $file_array, 0 );
		// Check for handle sideload errors.
	 
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return $id;
		} else {
			
			return $id;
			
		}
	}

	/* Remove the querystring from a URL */
	function pvw_strip_querysting($url) {

		if (strpos($url,'?') !== false) {
			$url = substr($url,0,strpos($url, '?'));
		}
		return $url;
		
	}

	/* Creates an HTML table from an array of data */
	function pvw_table_generator($data) {
	
		$html = '<table class="pvw_admin_table">';
	
		$head = 0;
		
		foreach ($data as $innerArray) {
			
			
			if (is_array($innerArray)){
				//  Scan through inner loop
				
				$html .= '<tr>';
				
				foreach ($innerArray as $value) {
					
					if ($head == 0) {
					
						$html .= '<th>'.$value.'</th>';
						
					
					} else {
					
						if (is_array($value)){
						
							$html .= '<td><ul>';
							
							foreach ($value as $li) {
							
								$html .= '<li>'.$li.'</li>';
				
							}
							
							$html .= '</ul></td>';
						
						}
						else {
					
							$html .= '<td>'.$value.'</td>';
						
						}
					}
					
				}
				$head ++;
				$html .= '</tr>';
				
			}
		
		}
		
		$html .= '</table>';
		
		return $html;
		
	}

	/* Output Custom CSS from theme options */
	function pvw_head_css() {

			$shortname =  SLP_PVW_PLUGIN_SHORTCODE;	
			$saved_options = get_option('pvw_'.$shortname.'_options');
			
			$output = '';
			$css_option = $saved_options[$shortname.'_css_options'];
			$custom_css = $saved_options[$shortname.'_custom_css'];
			
			if ($css_option == 'custom') {
			
				if ($custom_css <> '') {
				$output .= $custom_css . "\n";
				}
				
				// Output styles
				if ($output <> '') {
					$output = "<!-- ".SLP_PVW_PLUGIN_NAME." Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
					echo $output;
				}
			
			} 

	}

	function pvw_plugin_get_saved_option($option) {

		
		$shortname =  SLP_PVW_PLUGIN_SHORTCODE;	

		$saved_options = get_option('pvw_'.$shortname.'_options');

		$return_value = "";
		$return_value =  $saved_options[$option];
		
		return $return_value;

	}

	/* Set up options from template */
	function pvw_options_setup(){

		$shortname = SLP_PVW_PLUGIN_SHORTCODE;	
		
		//Update EMPTY options
		$pvw_array = array();
		add_option('pvw_'.$shortname.'_options',$pvw_array);

		$template = get_option('pvw_'.$shortname.'_template');
		//$saved_options = get_option('pvw_'.$shortname.'_options');
		
		foreach($template as $option) {
			if($option['type'] != 'heading' && $option['type'] != 'button' && $option['type'] != 'note' &&  $option['type'] != 'intro' &&  $option['type'] != 'donation' &&  $option['type'] != 'debug'){
				$id = $option['id'];
				$std = $option['std'];
								
				$db_option = get_option($id);
				if(empty($db_option)){
					if(is_array($option['type'])) {
						foreach($option['type'] as $child){
							$c_id = $child['id'];
							$c_std = $child['std'];
							//update_option($c_id,$c_std);
							$pvw_array[$c_id] = $c_std; 
						}
					} else {
						//update_option($id,$std);
						$pvw_array[$id] = $std;
					}
				}
				else { //So just store the old values over again.
					$pvw_array[$id] = $db_option;
				}
			}
		}	
		update_option('pvw_'.$shortname.'_options',$pvw_array);
	}

	/* Reset saved options to template */
	function pvw_options_reset() {
		
		$delete = delete_option('pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_options');
		self::pvw_options_setup();
		
	}

	function pvw_plugin_options_generator($options) {
			
		$shortname =  SLP_PVW_PLUGIN_SHORTCODE;	
		
		$prename = 'pvw_'.$shortname.'_options';
		
		$counter = 0;
		$button_count = 0;
		$menu = '';
		$output = null;
		$buttons = '';
		
		foreach ($options as $value) {
		
						
			if (isset($value['id'])) {
			
				$name = $prename.'['.$value['id'].']';
			
			}
				   
			$counter++;
			$val = '';
							
			//Start Heading
			 if ( $value['type'] != "heading"  )
			 {
				$class = ''; if(isset( $value['class'] )) { $class = $value['class']; }
				
				$h3 = '';
				if (isset($value['name'])) {
					$h3 = $value['name'];
				}
				
				$output .= '<div class="section section-'.$value['type'].' '. $class .'">'."\n";
				$output .= '<h3 class="heading">'. $h3 .'</h3>'."\n";
				$output .= '<div class="option">'."\n" . '<div class="controls">'."\n";

			 } 
			 
			//End Heading
			
			$select_value = ''; 

			switch ( $value['type'] ) {
				
			case 'text':
				$val = $value['std'];
				$std = self::pvw_plugin_get_saved_option($value['id']);
				if ( $std != "") { $val = $std; }
				$output .= '<input class="pvw-input" name="'. $name .'" id="'. $value['id'] .'" type="'. $value['type'] .'" value="'. stripslashes($val) .'" />';
			break;
			
			case 'select':

				$output .= '<select class="pvw-input" name="'. $name .'" id="'. $value['id'] .'">';
			
				$select_value = self::pvw_plugin_get_saved_option($value['id']);
				 
				foreach ($value['options'] as $key=>$option) {
					
					$selected = '';
					
					 if($select_value != '') {
						 if ( $select_value == $key) { $selected = ' selected="selected"';} 
					 } else {
						 if ( isset($value['std']) )
							 if ($value['std'] == $option) { $selected = ' selected="selected"'; }
					 }
					  
					 $output .= '<option'. $selected .' value="'.$key.'" >';
					 $output .= $option;
					 $output .= '</option>';
				 
				 } 
				 $output .= '</select>';

				
			break;
			
			// Get schedules
			case 'select-schedule':
			
				$output .= '<select class="pvw-input" name="'. $name .'" id="'. $value['id'] .'">';
			
				$select_value = self::pvw_plugin_get_saved_option($value['id']);
				
				$schedules = wp_get_schedules();
				
				
				//order array by earliest second interval
				foreach ($schedules as $key => $row) {
					$orderByInterval[$key]  = $row['interval'];
				}

				array_multisort($orderByInterval, SORT_ASC, $schedules);

				foreach ($schedules as $key=>$option) {
					
					$selected = '';
					
					 if($select_value != '') {
						 if ( $select_value == $key) { $selected = ' selected="selected"';} 
					 } else {
						 if ( isset($value['std']) )
							 if ($value['std'] == $option) { $selected = ' selected="selected"'; }
					 }
					  
					 $output .= '<option'. $selected .' value="'.$key.'" >';
					 $output .= $option['display'];
					 $output .= '</option>';
				 
				 } 
				 $output .= '</select>';
				 
			break;
			
			// Get users
			case 'select-user':
			
				$val = $value['std'];
				$std = self::pvw_plugin_get_saved_option($value['id']);
				if ( $std != "") { $val = $std; }
				
				
				$args = array( 	'selected'                => $val,
								'include_selected'        => true,
								'name'                    => $name,
								'class'              	  => 'pvw-input',
								'echo'               => 0
								
								); 
			
				 
				$output .=  wp_dropdown_users($args);
				
			break;
			
			// Get categories
			case 'select-cat':
				
				$val = $value['std'];
				$std = self::pvw_plugin_get_saved_option($value['id']);
				if ( $std != "") { $val = $std; }
				
				$args = array(
					
					'hide_empty'         => 0, 
					'echo'               => 0,
					'selected'           => $val,
					'hierarchical'       => 0, 
					'name'               => $name,
					'class'              => 'pvw-input',
					'depth'              => 0,
					'tab_index'          => 0,
					'taxonomy'           => 'category',
					'hide_if_empty'      => false ,
					'id'				 => $value['id'] 
				);
		
				 $output .= wp_dropdown_categories( $args );
				
				
			break;
		
			
			// Get post formats
			
			case 'select-format':

				$output .= '<select class="pvw-input" name="'. $name .'" id="'. $value['id'] .'">';
			
				if ( current_theme_supports( 'post-formats' ) ) {
					
					$post_formats = get_theme_support( 'post-formats' );
					if ( is_array( $post_formats[0] ) ) {
				
						$output .= '<option value="0">Standard</option>';
						$select_value = self::pvw_plugin_get_saved_option($value['id']);
						 
						foreach ($post_formats[0] as $option) {
							
							$selected = '';
							
							 if($select_value != '') {
								 if ( $select_value == $option) { $selected = ' selected="selected"';} 
							 } else {
								 if ( isset($value['std']) )
									 if ($value['std'] == $option) { $selected = ' selected="selected"'; }
							 }
							  
							 $output .= '<option'. $selected .'>';
							 $output .= $option;
							 $output .= '</option>';
						 
						 } 
						 
					
					}
					
					else
					{
					
						$output .= '<option>';
						$output .= 'Standard';
						$output .= '</option>';
				
					}
				 
				}
				else
				{
					
					$output .= '<option>';
					$output .= 'Standard';
					$output .= '</option>';
				
				}
				
				$output .= '</select>';

				
			break;
		
					
			// Get pages
			case 'select-page':
				
				$val = $value['std'];
				$std = self::pvw_plugin_get_saved_option($value['id']);
				if ( $std != "") { $val = $std; }
				
				$args = array(
					'selected'         => $val,
					'echo'             => 0,
					'name'             => $name
				);
		
				$output .= wp_dropdown_pages( $args );

				
			break;
			
			// Get taxonomies
			case 'select-tax':
				
				$val = $value['std'];
				$std = self::pvw_plugin_get_saved_option($value['id']);
				if ( $std != "") { $val = $std; }
				
				$args = array(
					'show_option_all'    => __('All '.$value['taxonomy'], 'pvw_framework'),
					'show_option_none'   => __('No '.$value['taxonomy'], 'pvw_framework'),
					'hide_empty'         => 0, 
					'echo'               => 0,
					'selected'           => $val,
					'hierarchical'       => 0, 
					'name'               => $name,
					'class'              => 'postform',
					'depth'              => 0,
					'tab_index'          => 0,
					'taxonomy'           => $value['taxonomy'],
					'hide_if_empty'      => false 	
				);
		
				$output .= wp_dropdown_categories( $args );

				
			break;
			//-------
			
			case 'select2':

				$output .= '<select class="pvw-input" name="'. $name .'" id="'. $value['id'] .'">';
			
				$select_value = self::pvw_plugin_get_saved_option($value['id']);
				 
				foreach ($value['options'] as $option => $name) {
					
					$selected = '';
					
					 if($select_value != '') {
						 if ( $select_value == $option) { $selected = ' selected="selected"';} 
					 } else {
						 if ( isset($value['std']) )
							 if ($value['std'] == $option) { $selected = ' selected="selected"'; }
					 }
					  
					 $output .= '<option'. $selected .' value="'.$option.'">';
					 $output .= $name;
					 $output .= '</option>';
				 
				 } 
				 $output .= '</select>';

				
			break;
			case 'textarea':
				
				$cols = '8';
				$rows = '8';
				$ta_value = '';
				
				if(isset($value['std'])) {
					
					$ta_value = $value['std']; 
					
					if(isset($value['options'])){
						$ta_options = $value['options'];
						if(isset($ta_options['cols'])){
						$cols = $ta_options['cols'];
						}
						if(isset($ta_options['rows'])){
						$rows = $ta_options['rows'];
						}
					}
					
				}
					$std = self::pvw_plugin_get_saved_option($value['id']);
					if( $std != "") { $ta_value = stripslashes( $std ); }
					$output .= '<textarea class="pvw-input" name="'. $name .'" id="'. $value['id'] .'" cols="'. $cols .'" rows="'.$rows.'">'.$ta_value.'</textarea>';
				
				
			break;
			case "radio":
				
				 $select_value = self::pvw_plugin_get_saved_option( $value['id']);
					   
				 foreach ($value['options'] as $key => $option) 
				 { 

					 $checked = '';
					   if($select_value != '') {
							if ( $select_value == $key) { $checked = ' checked'; } 
					   } else {
						if ($value['std'] == $key) { $checked = ' checked'; }
					   }
					$output .= '<input class="pvw-input pvw-radio" type="radio" name="'. $name .'" value="'. $key .'" '. $checked .' />' . $option .'<br />';
				
				}
				 
			break;
			case "checkbox": 
			
				$std = $value['std'];
				$saved_std = self::pvw_plugin_get_saved_option($value['id']);
				$checked = '';
			
				
				
				if(isset($saved_std)) {
					
					
					if($saved_std == "true") {
					$checked = 'checked="checked"';
					}
					else{
					   $checked = '';
					}
				}
				else {
				
				
					if( $std == "true") {
					   $checked = 'checked="checked"';
					}
					else {
						$checked = '';
					}
				}
				
				
				$output .= '<input type="checkbox" class="checkbox pvw-input" name="'.  $name .'" id="'. $value['id'] .'" value="true" '. $checked .' />';
				//$output .= 'CHECKED: '.$checked;
				//$output .= '<input type="checkbox" class="checkbox pvw-input" name="'.  $name .'" id="'. $value['id'] .'" value="true" '. checked($checked, true, false) .' />';

			break;
			case "multicheck":
			
				$std =  $value['std'];         
				
				foreach ($value['options'] as $key => $option) {
												 
				$tz_key = $value['id'] . '_' . $key;
				$saved_std = self::pvw_plugin_get_saved_option($tz_key);
						
				if(!empty($saved_std)) 
				{ 
					  if($saved_std == 'true'){
						 $checked = 'checked="checked"';  
					  } 
					  else{
						  $checked = '';     
					  }    
				} 
				elseif( $std == $key) {
				   $checked = 'checked="checked"';
				}
				else {
					$checked = '';                                                                                    }
				$output .= '<input type="checkbox" class="checkbox pvw-input" name="'. $tz_key .'" id="'. $tz_key .'" value="true" '. $checked .' /><label for="'. $tz_key .'">'. $option .'</label><br />';
											
				}
			break;
			
			case "button":
			
				$button_count++;
				
				$text =  $value['text'];  
				$action = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
				
				if ($button_count == 1)	{$output .= '<form action="#" method="post" id="nested-form-bug"></form>'; }
				
				
				$output .= '<form name="'.$value['id'].'" method="post" action="'.$action.'">';
				$output .= '<input type="hidden" name="'.$value['id'].'" value=\'Y\'>';						
				$output .= '<input type="submit" class="button" value="'.$text.'" name="button_'.$value['id'].'"  >';
				$output .= '</form>';
				
				//$buttons .= 'if(isset($_POST["'.$value['id']'"])) { ';
				$buttons .= ' if(isset($_POST["'.$value['id'].'"]) && $_POST["'.$value['id'].'"] == \'Y\'  )';
				
				if (isset($value['std']) && $value['std'] == 'debug') {
				
					$buttons .= '{ slp_pvw_plugin_framework::'.$value['method'].'();';
				
				}
				else {
				
					$buttons .= '{ '.SLP_PVW_PLUGIN_CLASS.'::'.$value['method'].'();';
				
				}
				
			
						
				$buttons .= ' echo \'<div class="updated"><p><strong>'.$value['message'].'</strong></p></div>\'; } ';	
				//$buttons .= ' }';		
				
			
			break;
			
			case "debug":
			
				$output .= '<div class="info"><p>'. self::pvw_get_debug_info('html').'</p></div>';
				
				
			break;
			
					
			case "note":
			
				$output .= '<div class="notes"><p>'. $value['message'] .'</p></div>';
				
				
			break;
			
			case "donation":
			
				
			
				if ($button_count == 1)	{$output .= '<form action="#" method="post" id="nested-form-bug"></form>'; }
				
				$donate_msg = '';
				if(isset($value['message'])) {$donate_msg = $value['message']; }
				
				$output .=  $donate_msg;
				$output .=  '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="R6BY3QARRQP2Q">
							<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
							<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
							</form>
							' ;
				
				
			break;
			
			case "intro":
			
				$output .= '<div class="intro"><p>'. $value['message'] .'</p></div>';
				
				
			break;
						
			case "info":
				$output .= '<div class="info"><p>'. $value['message'] .'</p></div>';
			break;  

			case "subheading":
				
				//$output .= '<h3>'.$value['name'].'</h3>'."\n";
				
			break; 
			
			case "heading":
				
				if($counter >= 2){
				   $output .= '</div>'."\n";
				}
				$jquery_click_hook = ereg_replace("[^A-Za-z0-9]", "", strtolower($value['name']) );
				$jquery_click_hook = "pvw-option-" . $jquery_click_hook;
				$menu .= '<li><a title="'.  $value['name'] .'" href="#'.  $jquery_click_hook  .'">'.  $value['name'] .'</a></li>';
				$output .= '<div class="group" id="'. $jquery_click_hook  .'"><h2>'.$value['name'].'</h2>'."\n";
			break; 
			
			} 
			
			// if TYPE is an array, formatted into smaller inputs... ie smaller values
			if ( is_array($value['type'])) {
				foreach($value['type'] as $array){
				
						$id = $array['id']; 
						$std = $array['std'];
						$saved_std = self::pvw_plugin_get_saved_option($id);
						if($saved_std != $std){$std = $saved_std;} 
						$meta = $array['meta'];
						
						if($array['type'] == 'text') { // Only text at this point
							 
							 $output .= '<input class="input-text-small pvw-input" name="'. $id .'" id="'. $id .'" type="text" value="'. $std .'" />';  
							 $output .= '<span class="meta-two">'.$meta.'</span>';
						}
					}
			}
			if ( $value['type'] != "heading" ) { 
				if ( $value['type'] != "checkbox" ) 
					{ 
					$output .= '<br/>';
					}
				if(!isset($value['desc'])){ $explain_value = ''; } else{ $explain_value = $value['desc']; } 
				$output .= '</div><div class="explain">'. $explain_value .'</div>'."\n";
				$output .= '<div class="clear"> </div></div></div>'."\n";
				}
				
		}	
		$output .= '</div>';
		
		//$buttons .= ' else { }';
		
		return array($output,$menu,$buttons);

	}

	function pvw_plugin_settings() {


		$options = get_option('pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_template');		
		
		$return = self::pvw_plugin_options_generator($options);

		eval($return[2]);

	?>
			<div id="pvw_settings" class="wrap">	
				<!-- BEGIN Wrap -->
				<div class="wrap" id="pvw_container">
				
					
					<form method="post" action="options.php">
						<fieldset>
							<div id="header">
								<div>
									<h2 class="logo"><?php echo SLP_PVW_PLUGIN_NAME; ?> <span> v<?php echo SLP_PVW_PLUGIN_VERSION; ?></span></h2>
								</div>
								
								<?php $api = new slp_setlistfm();
														
									$api_check = $api->api_check();
									
									
														
								?>

								<div class="pvw_<?php echo $api_check[0]; ?>">
									<p>
										<?php echo $api_check[1]; ?>								
									</p>
								</div>
					
								
								<div class="clear"></div>
							</div>
							
							<?php 	
								settings_fields('pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_plugin_options');
									
							?>
					
							<div id="main">
								<div id="pvw-nav">
									<ul>
									<?php echo $return[1] ?>
									</ul>
								</div>
								<div id="content"> 
																								
								<?php echo $return[0]; /* Settings */ ?> 
								
								</div>
								<div class="clear"></div>
							</div>
							<div class="save_bar_top">
							
								<input type="submit" class="button-primary" value="<?php _e('Save Changes', SLP_PVW_PLUGIN_LINK) ?>" />
							
								<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ) ?>" method="post" style="display:inline" id="ofform-reset">
									<span class="submit-footer-reset">
										<input name="reset" type="submit" value="<?php _e('Reset Options', SLP_PVW_PLUGIN_LINK) ?>" class="button submit-button reset-button" onclick="return confirm('<?php _e('Click OK to reset. Any settings will be lost!', SLP_PVW_PLUGIN_LINK) ?>');" />
										<input type="hidden" name="pvw_reset" value="reset" />
									</span>
								</form>
							
							</div>						
						</fieldset>
					</form>
								
						
		
				<!-- END Wrap -->			
				</div>
				<div class="clear"></div>
				<!-- BEGIN Footer -->
				<div id="pvw_footer">
					
					<div id="links">
						<b><?php echo SLP_PVW_PLUGIN_NAME; ?> v<?php echo SLP_PVW_PLUGIN_VERSION; ?></b> | <?php _e('We hope you enjoy the plugin ', SLP_PVW_PLUGIN_LINK); ?>
						<br/>
						<a href="http://www.polevaultweb.com/support/forum/<?php echo  SLP_PVW_PLUGIN_LINK; ?>-plugin/" title="<?php _e('Visit the support forum for', SLP_PVW_PLUGIN_LINK) ?> <?php echo SLP_PVW_PLUGIN_NAME; ?>"><?php _e('Support Forum', SLP_PVW_PLUGIN_LINK) ?></a> |
						<a href="http://www.polevaultweb.com/plugins/<?php echo SLP_PVW_PLUGIN_LINK; ?>/" title="<?php _e('Visit the site for', SLP_PVW_PLUGIN_LINK) ?> <?php echo SLP_PVW_PLUGIN_NAME; ?>"><?php _e('Plugin Site', SLP_PVW_PLUGIN_LINK) ?></a> |
						<a title="<?php _e('Follow us on Twitter for updates', SLP_PVW_PLUGIN_LINK) ?>" href="http://twitter.com/#!/polevaultweb">@polevaultweb</a> |
						<a title="<?php _e('Donations are much appreciated', SLP_PVW_PLUGIN_LINK) ?>" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R6BY3QARRQP2Q"><?php _e('Donate with PayPal', SLP_PVW_PLUGIN_LINK) ?></a> |
						<a href="http://db.tt/Y1ovFX6" title="<?php _e('Free storage space for you and us', SLP_PVW_PLUGIN_LINK) ?>"><?php _e('Donate by using Dropbox - Referral Link', SLP_PVW_PLUGIN_LINK) ?></a> |
						<a href="http://wordpress.org/extend/plugins/<?php echo SLP_PVW_PLUGIN_LINK; ?>/" title="<?php _e('Rate the Plugin on WordPress', SLP_PVW_PLUGIN_LINK) ?>"><?php _e('Rate the Plugin', SLP_PVW_PLUGIN_LINK) ?> ★★★★★</a>
					</div>
					
					<div id="pvw">
						<a id="logo" href="http://www.polevaultweb.com/" title="Plugin by polevaultweb.com" target="_blank"><img src="<?php echo SLP_PVW_PLUGIN_URL; ?>admin/images/pvw_logo.png" alt="polevaultweb logo" width="190" /></a>
					</div>
			
				</div>
				<!-- END Footer -->
				<div class="clear"></div>
			</div>	
				
		<?php 
			
			
	}


} //end of admin framework class

}

?>