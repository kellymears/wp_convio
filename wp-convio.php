<?php

/*
	Plugin Name: WordPress - Convio Open API
	Plugin URI: http://endgenocide.org/code
	Description: Convio Luminate Integration for WordPress.
	Version: 0.1
	Author: Kelly Mears
	Author URI: http://kellymears.me
	License: GPL2
*/

/*  
	Copyright 2012 United To End Genocide (email : info@endgenocide.org)

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

require_once('ConvioOpenAPI.php');

if (!class_exists("wp_convio")) {
	
	class wp_convio extends WP_Widget {
	
		
		public $convio_api;
		public $convio_data;
		
		var $admin_options_name = "wp_convio_admin_options";
		var $admin_options;
		
		function __construct() {
		
			$this->admin_options = $this->get_admin_options();

		}
		
		function configure_convio() {
		
			$this->convio_api = new ConvioOpenAPI;
		
			$this->convio_api->host = $this->admin_options['host'];
			$this->convio_api->short_name = $this->admin_options['short_name'];
			$this->convio_api->api_key = $this->admin_options['api_key'];
			$this->convio_api->login_name = $this->admin_options['login_name'];
			$this->convio_api->login_password = $this->admin_options['login_password'];
		}
		
		function create_convio_post_types() {

			register_post_type('action', array(
				'label' => __('Convio Actions'),
				'singular_label' => __('Convio Action'),
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => false,
				'query_var' => false,
				'supports' => array('title', 'editor', 'author')
			));
			
			register_post_type('event', array(
				'label' => __('Convio Events'),
				'single_label' => __('Convio Event'),
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => false,
				'query_var' => false,
				'supports' => array('title', 'editor', 'author')
			));
			
		}
		
		function get_admin_options() {
			
			$wp_convio_admin_options = array('host' => 'Enter Host Here',
						                     'short_name' => 'Enter Short Name Here',
						                     'api_key' => 'Enter API Key Here',
						                     'login_name' => 'Enter Login Name Here',
						                     'login_password' => 'Enter Login Password Here');

										   
			$admin_options = get_option('wp_convio_admin_options');
			
			if (!empty($admin_options)) {
				foreach ($admin_options as $key => $option)
					$wp_convio_admin_options[$key] = $option;
			}
			
			update_option($this->admin_options_name, $wp_convio_admin_options);
			
			return $wp_convio_admin_options;
		}
				
		// Prints out the admin page
		function print_admin_page() {
											
			if (isset($_POST['update_wp_convio_options'])) { 
			
				if (isset($_POST['wp_convio_host'])) {
					$this->admin_options['host'] = $_POST['wp_convio_host'];
				}
					
				if (isset($_POST['wp_convio_short_name'])) {
					$this->admin_options['short_name'] = $_POST['wp_convio_short_name'];
				}
					
				if (isset($_POST['wp_convio_api_key'])) {
					$this->admin_options['api_key'] = $_POST['wp_convio_api_key'];
				}
				
				if (isset($_POST['wp_convio_login_name'])) {
					$this->admin_options['login_name'] = $_POST['wp_convio_login_name'];
				}
				
				if (isset($_POST['wp_convio_login_name'])) {
					$this->admin_options['login_password'] = $_POST['wp_convio_login_password'];
				}
					
				update_option($this->admin_options_name, $this->admin_options);
				
				?>
				
				<div class="updated"><p><strong><?php _e("Settings Updated.", "wp_convio");?></strong></p></div>
				
			<?php } ?>
			
			<div class="wrap">	
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					
					<h2>WordPress - Convio Options</h2>
					
					<p><strong>This plugin is a work in progress.<br /></strong>Much love,<br /> K.</p>
					
					<h3>Convio Host</h3>
					<input type="text" name="wp_convio_host" value="<?php echo $this->admin_options['host']; ?>">
					
					<h3>Convio Short Name</h3>
					<input type="text" name="wp_convio_short_name" value="<?php echo $this->admin_options['short_name']; ?>">
					
					<h3>Convio API Key</h3>
					<input type="text" name="wp_convio_api_key" value="<?php echo $this->admin_options['api_key']; ?>">
					
					<h3>Convio Login Name</h3>
					<input type="text" name="wp_convio_login_name" value="<?php echo $this->admin_options['login_name']; ?>">
					
					<h3>Convio Login Password</h3>
					<input type="text" name="wp_convio_login_password" value="<?php echo $this->admin_options['login_password']; ?>">

					<div class="submit">
						<input type="submit" name="update_wp_convio_options" value="<?php _e('Update Settings', 'wp_convio') ?>" />
					</div>
					
				</form>
			</div>
			
		<?php } 
		
		// End function print_admin_page()
				
		function get_alert_by_zip($attr) {
						
			
			if (isset($_POST['wp_convio_submit'])) { 
			
				$this->configure_convio();
				
				$this->convio_data['alert_type'] = 'action';
				$this->convio_data['subject'] = 'subject';
				$this->convio_data['title'] = 'Ms';
				
				if(isset($attr['alert_id'])) {
					$this->convio_data['alert_id'] = $attr['alert_id'];
				}
				if(isset($_POST['wp_convio_first_name'])) { 
					$this->convio_data['first_name'] = $_POST['wp_convio_first_name'];
				}
				if(isset($_POST['wp_convio_last_name'])) { 
					$this->convio_data['last_name'] = $_POST['wp_convio_last_name'];
				}
				if(isset($_POST['wp_convio_street1'])) { 
					$this->convio_data['street1'] = $_POST['wp_convio_street1'];
				}
				if(isset($_POST['wp_convio_city'])) { 
					$this->convio_data['city'] = $_POST['wp_convio_city'];
				}
				if(isset($_POST['wp_convio_state'])) { 
					$this->convio_data['state'] = $_POST['wp_convio_state'];
				}
				if(isset($_POST['wp_convio_phone'])) { 
					$this->convio_data['phone'] = $_POST['wp_convio_phone'];
				}
				if(isset($_POST['wp_convio_zip'])) { 
					$this->convio_data['zip'] = $_POST['wp_convio_zip'];
				}
				if(isset($_POST['wp_convio_email'])) { 
					$this->convio_data['email'] = $_POST['wp_convio_email'];
				}
				
				$this->configure_convio();
				
				// Make API call
				$response = $this->convio_api->call('SRAdvocacyAPI_takeAction', $this->convio_data);
				
				if($response) { ?>
					<div class="updated">
						<p><strong><?php _e("Thank You For Gently Prodding Your Representative With A Digital Stick.", "wp_convio"); ?></strong></p>
					</div>
				<?php } else { ?>
					<div class="updated"><p><strong>There was a problem with your request.</strong></p></div>
				<?php }
			 
			 } else { 
	
				$shortcode = '<div class="wp_convio_post"><form method="post" action="'. $_SERVER['REQUEST_URI'] .'"><h3>First Name</h3><input type="text" name="wp_convio_first_name" value="'. $this->convio_data['first_name'] .'"><h3>Last Name</h3<input type="text" name="wp_convio_last_name" value="'. $this->convio_data['last_name'] .'"><h3>Street</h3><input type="text" name="wp_convio_street1" value="'. $this->convio_data['street1'] .'"><h3>City</h3><input type="text" name="wp_convio_city" value="'. $this->convio_data['city'] .'"><h3>State</h3><input type="text" name="wp_convio_state" value="'. $this->convio_data['state'] .'"><h3>Zip</h3><input type="text" name="wp_convio_zip" value="'. $this->convio_data['zip'] .'"><h3>Phone</h3><input type="text" name="wp_convio_phone" value="'. $this->convio_data['phone'] .'"><h3>Email</h3><input type="text" name="wp_convio_email" value="'. $this->convio_data['email'] .'"><div class="submit"><input type="submit" name="wp_convio_submit" value="Submit" /></div></form></div>';	
				
				return $shortcode; 
			}
		}
	}
}

// Instantiate
if (class_exists("wp_convio")) {
	$wp_convio = new wp_convio();
}

// Instantiate the Admin Panel
if (!function_exists("wp_convio_ap")) {
	function wp_convio_ap() {
	
		global $wp_convio;
		
		if (function_exists('add_options_page')) {
			add_options_page('WP Convio', 'WP Convio', 9, basename(__FILE__), array(&$wp_convio, 'print_admin_page'));
		}
	}
}

//Actions and Filters	
if (isset($wp_convio)) {

	// Shortcode
	add_shortcode('convioaction', array(&$wp_convio, 'get_alert_by_zip'));

	//Actions
	add_action('admin_menu', 'wp_convio_ap');
	add_action('init',  array(&$wp_convio, 'create_convio_post_types'));
	// add_action('plugins_loaded', array(&$wp_convio, 'register_widget'));
	
	
	//Filters
	// add_filter('the_content', array(&$wp_convio, 'get_alert_by_zip'));

}

?>