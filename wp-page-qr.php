<?php
/*
Plugin Name: WP Page QR
Plugin URI: http://wordpress.org/extend/plugins/wppageqr
Description: Auto generate QR Code for page's URL to allow to share by this way
Version: 1.1.3
Author: David Ansermot
Author URI: http://www.ansermot.ch
License: GPL2
*/

/*  
    Copyright 2010-2012  David "mArm" Ansermot  (email : webmaster@ansermot.ch)

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


// *************************************
// Check of PHP5 version
// *************************************
if (version_compare(phpversion(), '5.2', '<')) {
	die ('WP Page QR requires PHP 5.2.0 or higher.');
}

// *************************************
// Plugin's working paths
// *************************************
define('PQR_ROOT', dirname(__FILE__).'/');
define('PQR_CORE', PQR_ROOT.'core/');
define('PQR_CACHE', PQR_ROOT.'cache/');
define('PQR_INSTALL', PQR_CORE.'install/');

// *************************************
// Class declaration
// *************************************
if (!class_exists('WPPageQR')) {
	require_once(PQR_CORE.'class.wppageqr.php');
}

// *************************************
// Plugin init
// *************************************
if (!isset($GLOBALS['wpPageQR'])) {
	$wpPageQR = new WPPageQR(PQR_CACHE);
	$GLOBALS['wpPageQR'] = $wpPageQR;
}

// *************************************
// Hooks management
// *************************************
if (isset($wpPageQR)) {
	
	// Actions
	add_filter('the_content', array(&$wpPageQR, 'processContent'));
	
	// Installation hooks
	register_activation_hook(__FILE__, array(&$wpPageQR, 'install'));
	register_deactivation_hook(__FILE__, array(&$wpPageQR, 'uninstall'));
	
	// Add setting menu entry for the plugin
	add_action('admin_menu', array(&$wpPageQR, 'setupSettingPanel'));
}

// *************************************
// Template tags 
// *************************************
if (isset($wpPageQR)) {
	
	/**
	 * Display QR Code
	 *
	 * @param void
	 * @return void
	 */
	function wppageqr_code(array $params = null) {
		
		global $wpPageQR;
		
		echo $wpPageQR->processContent('', true, $params);
		
	}
	
}

?>
