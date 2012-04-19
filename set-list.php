<?php
/*  
Plugin Name: Set List
Plugin URI: http://www.polevaultweb.com/plugins/set-list/  
Description: Add set lists from gigs you have seen to posts, pages and sidebars. Set list data provided by the setlist.fm API.
Author: polevaultweb 
Version: 0.1.1
Author URI: http://www.polevaultweb.com/

Copyright 2012  polevaultweb  (email : info@polevaultweb.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

//plugin version
define( 'SLP_PVW_PLUGIN_VERSION', '0.1.1');
//plugin name
define( 'SLP_PVW_PLUGIN_NAME', 'Set List');
//plugin shortcode
define( 'SLP_PVW_PLUGIN_SHORTCODE', 'slp');

//plugin text domain
define( 'SLP_PVW_PLUGIN_SETTINGS', str_replace(" ","",strtolower(SLP_PVW_PLUGIN_NAME)));
//plugin linking
define( 'SLP_PVW_PLUGIN_LINK',  str_replace(" ","-",strtolower(SLP_PVW_PLUGIN_NAME)));
//plugin class
define( 'SLP_PVW_PLUGIN_CLASS',  str_replace(" ","_",strtolower(SLP_PVW_PLUGIN_NAME)));

//helpful paths
define( 'SLP_PVW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SLP_PVW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SLP_PVW_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'SLP_PVW_PLUGIN_DIR',dirname( plugin_basename( __FILE__ ) ));


//require ADMIN file for plugin settings
require_once SLP_PVW_PLUGIN_PATH.'admin/admin-page.php';

//require OPTIONS file for plugin settings
require_once SLP_PVW_PLUGIN_PATH.'admin/admin-options.php';

//require WIDGETS
require_once plugin_dir_path( __FILE__ ).'widgets/get_setlist.php';

//require INCLUDES other php files for API handlers etc
require_once plugin_dir_path( __FILE__ ).'includes/setlist.fm.php';

if (!class_exists(SLP_PVW_PLUGIN_CLASS)) {

class set_list {

		//BEGIN - FUNCTIONS FOR PLUGIN FRAMEWORK //
		//---------------------------------------------------------------------------------------------------------------------------------------------------//
		
		/* Plugin loading method */
		public static function load_plugin() {
			
			// -- BEGIN PLUGIN FRAMEWORK ---------------------------------------------------------------------------------------- //
			
			//language support
			add_action('init', get_class()  . '::load_language_support');
			
			//settings menu
			add_action('admin_menu',get_class()  . '::register_settings_menu' );
			//settings link
			add_filter('plugin_action_links', get_class()  . '::register_settings_link', 10, 2 );
			//styles and scripts
			add_action('admin_init', get_class()  . '::register_styles');
			//register custom css from settings
			add_action('wp_head',  get_class()  . '::custom_head_css');
			//register default css from settings
			add_action('get_header', get_class()  . '::custom_css');
			
			//register settings options
			add_action('admin_init', get_class()  . '::register_settings');
			
			//register default settings template on activation
			////add_action('admin_init', 'pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_options_template_init' );
			register_activation_hook(__FILE__, 'pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_options_template_init' );
			//register default settings options on activation
			register_activation_hook(__FILE__, get_class()  . '::options_setup' );

			//register upgrade check function
			add_action('admin_init', get_class()  . '::upgrade_check');
			
			//register uninstall hook
			register_uninstall_hook(__FILE__,  get_class()  . '::plugin_uninstall');
			
			// -- END PLUGIN FRAMEWORK ---------------------------------------------------------------------------------------- //
			
			// -- SHORTCODES ---------------------------------------------------------------------------------------- //
			
			//add shortcode for shots
			add_shortcode(SLP_PVW_PLUGIN_SHORTCODE.'_get_setlist', array(SLP_PVW_PLUGIN_CLASS, SLP_PVW_PLUGIN_SHORTCODE.'_get_setlist_sc') );
			
			
			// -- WIDGETS ---------------------------------------------------------------------------------------- //
			
			//register widget for shots
			add_action( 'widgets_init', create_function( '', 'register_widget( "'.SLP_PVW_PLUGIN_SHORTCODE.'_get_setlist_widget" );' ) );
			
			
			// -- CUSTOM REGISTRATIONS ---------------------------------------------------------------------------------------- //
				
		}
		
		/* Add language support */
		public static function load_language_support() {
		
			//load_plugin_textdomain( SLP_PVW_PLUGIN_LINK, false, SLP_PVW_PLUGIN_DIR . '/languages' );
		
		}
		
		/* Add settings options for plugin  */
		public static function register_settings() {  
		
			//register settings options
			register_setting( 'pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_plugin_options', 'pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_options',SLP_PVW_PLUGIN_SHORTCODE.'_pvw_validate_setting' );
		}
		
		/* Add hook to admin framework for options set up */
		public static function options_setup() {
				
			slp_pvw_plugin_framework::pvw_options_setup();
			
		}
   		  			
		/* Add menu item for plugin to Settings Menu */
		public static function register_settings_menu() {  
   		  			
   			add_options_page( SLP_PVW_PLUGIN_NAME, SLP_PVW_PLUGIN_NAME, 1, SLP_PVW_PLUGIN_SETTINGS, get_class() . '::settings_page' );
	  				
		}

		/* Add settings link to Plugin page */
		public static function register_settings_link($links, $file) {  
   		  		
			static $this_plugin;
				if (!$this_plugin) $this_plugin = SLP_PVW_PLUGIN_BASE;
				 
				if ($file == $this_plugin){
				$settings_link = '<a href="options-general.php?page='.SLP_PVW_PLUGIN_SETTINGS.'">' . __('Settings', SLP_PVW_PLUGIN_SETTINGS) . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
				
		}
		
		/* Register custom stylesheets and script files */
		public static function register_styles() {
		 		
			if (isset($_GET['page']) && $_GET['page'] == SLP_PVW_PLUGIN_SETTINGS) {
		 
				//register styles
				wp_register_style( SLP_PVW_PLUGIN_SHORTCODE.'_adminstyle', SLP_PVW_PLUGIN_URL . 'admin/admin-style.css');
				
				//enqueue styles	
				wp_enqueue_style(SLP_PVW_PLUGIN_SHORTCODE.'_adminstyle' );
				wp_enqueue_style('dashboard');
				
				//enqueue scripts
				wp_enqueue_script('admin-tabs-script', SLP_PVW_PLUGIN_URL . 'admin/scripts/admin-tabs.js');
				wp_enqueue_script('dashboard');
				wp_enqueue_script('jquery-ui-core');
				
				//add script for reset admin options
				if (isset($_POST['pvw_reset']))  {
				
					if ($_POST['pvw_reset'] == 'reset' ) {
						slp_pvw_plugin_framework::pvw_options_reset();
						header("Location: ".$_SERVER['REQUEST_URI']);
						exit();	
					}
				
				}
			}
		}
		
		/* Register custom upgrade check function */
		public static function upgrade_check() {
		
			$saved_version = get_option( 'pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_version' );
			$current_version = isset($saved_version) ? $saved_version : 0;

			if ( version_compare( $current_version, SLP_PVW_PLUGIN_VERSION, '==' ) )
				return;
				
			//specific version checks on upgrade
			//if ( version_compare( $current_version, '0.1', '<' ) ) {}	
				
			//update the database version
			update_option( 'pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_version', SLP_PVW_PLUGIN_VERSION );
		
		} 
		
		/* Register custom uninstall function */
		public static function plugin_uninstall() {
		
			//delete settings and template options
			delete_option('pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_template'); 
			delete_option('pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_options'); 

			//delete version
			delete_option('pvw_'.SLP_PVW_PLUGIN_SHORTCODE.'_version');
			
			//clear transients
			self::clear_cache();
		
		}
		
		/* Plugin Settings page and settings data */
		public static function settings_page() {
					
			slp_pvw_plugin_framework::pvw_plugin_settings();
		
		}
		
		//END - FUNCTIONS FOR PLUGIN FRAMEWORK //
		//---------------------------------------------------------------------------------------------------------------------------------------------------//
			
		/* Custom CSS Script */
		public static function custom_head_css() {
		
			slp_pvw_plugin_framework::pvw_head_css();
		
		}
		
		/* Custom CSS Scripts */
		public static function custom_css() {
		
			if (!is_admin()) {
			
				$shortname =  SLP_PVW_PLUGIN_SHORTCODE;	
				$saved_options = get_option('pvw_'.$shortname.'_options');
				
				$css_option = $saved_options[$shortname.'_css_options'];
				
				if ($css_option == 'default') {
				
					//register styles
					wp_register_style( SLP_PVW_PLUGIN_SHORTCODE.'_defaultstyle', SLP_PVW_PLUGIN_URL . 'css/default.css');
					
					//enqueue styles	
					wp_enqueue_style(SLP_PVW_PLUGIN_SHORTCODE.'_defaultstyle' );
				
				
				} 	
			}
		
		}
			
		/* Function used by shortcode to display shots dribbble data */
		public static function slp_get_setlist_sc($atts, $content = null) {
		
			//extract shortcode parameters
			extract(shortcode_atts(array(	'artist' => 'Drake',
											'date' => '01-03-2012',
											'venue' => ''
										), $atts));
			

			$html = self::get_setlist($artist, $date, $venue,'sc');
			
			return $html;
		
		}
		
		/* General function to display shots from Dribbble */
		public static function get_setlist($artist, $date, $venue, $type) {
		
			$shortname = SLP_PVW_PLUGIN_SHORTCODE;
			$saved_options = get_option( 'pvw_'.$shortname.'_options' );
			$cache = $saved_options[$shortname.'_cache_config' ];
			//get cache expiry
			$cache_exp = $saved_options[$shortname.'_cache_exp' ];
			//get credit link setting 
			$credit_link = $saved_options[$shortname.'_sl_credit_link'];
							
			//instantiate setlistfm object
			$setlistfm = new slp_setlistfm();
			
			$cachename = $shortname.'_getsetlist_'.$artist.'_'.str_replace('-','',$date).'_'.$type ;
			
			//check if caching on 
						
			if ( (false === ( $value = get_transient( $cachename) )  && $cache == "true" ) || $cache == "false"   ) {
				

				try {
						//get setlist		
						$setlist = $setlistfm->get_setlist($artist,$date, $venue);
					
						//set transiet only if caching one
						if ($cache ) {
						
							set_transient( $cachename , $setlist, $cache_exp );
						}
						
						//set marker for cache
						$render_type = 'Dynamic';
					
					}
					catch(Exception $e) {
					
						$html = $e->getMessage();
						$setlist = null;
					
					}
			
			}
			else {
						
				$setlist = get_transient($cachename );
				
				//set marker for cache
				$render_type = 'Cached';
			
			}
				
			
			
			//this code runs when there is no valid transient set
			
			if ($setlist) {
			
				$html  = "\n<!--".$render_type." setlist.fm API data served by ".SLP_PVW_PLUGIN_NAME.", a WordPress plugin by polevaultweb.com -->\n";
				$html  .= "<div class='setlist'>"."\n";
					
				
						
				//var_dump($setlist);
				
				$venue = $setlistfm->get_set_venue($setlist);
				$artist = $setlistfm->get_set_artist($setlist);
				$encore = $setlistfm->get_set_encore($setlist);
				$html .= '<p><span class="artist">'.$artist->name.'</span> at <span class="venue">'.$venue->name.'</span> on <span class="date">'.$setlist->eventDate.'</span></p>';
				
				$songs = $setlistfm->get_set_songs($setlist);
							
				$html .= '<ul>';
				foreach($songs as $song):
			
					//BEGIN div shot
					$html .="<li>";
					$html .= $song->name;
					//END div shot
					$html .= "</li>";				
													
				endforeach;
				$html .= '</ul>';
				
				if ($encore) {
				
					$html .= '<span class="encore">Encore</span>';
					
					$html .= '<ul>';
					
					foreach($encore as $song):
				
						//BEGIN div shot
						$html .="<li>";
						$html .= $song->name;
						//END div shot
						$html .= "</li>";				
														
					endforeach;
					$html .= '</ul>';
				
				
				}
				
				
				
				//Credit link to plugin			
				if ($credit_link == "true") {
					
					$html .= '<div class="plugin_credit">Set list served by <a href="http://wordpress.org/extend/plugins/'.SLP_PVW_PLUGIN_LINK.'" title="'.SLP_PVW_PLUGIN_NAME.' - a WordPress Plugin">'.SLP_PVW_PLUGIN_NAME.' </a> from <a href="http://www.polevaultweb.com">polevaultweb</a></div>';	
				
				}

				//END div shot
				$html .="</div>";
			
			}
		
			return $html;
			
		}
		
		/* Clear transient cache */
		public static function clear_cache() {
		
			global $wpdb;
			
			//delete all setlist transient
			$cache_del = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%".SLP_PVW_PLUGIN_SHORTCODE."_getsetlist_%'";
			
			$wpdb->query( $cache_del );
		
		}
			
	}
	
}

if (class_exists(SLP_PVW_PLUGIN_CLASS)) {

	//Load plugin
	set_list::load_plugin();
	
}
?>