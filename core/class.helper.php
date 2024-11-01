<?php

/*  
    Copyright 2010  David Ansermot  (email : dev@ansermot.ch)

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

/**
 * Plugin's helper class
 *
 * @author David Ansermot <dev@ansermot.ch>
 * @filename class.helper.php
 * @package wp_page_qr
 * @final
 */

final class Helper {
	
	protected $dbo;
	
	/**
	 * Load plugin's config array
	 *
	 * @param void
	 * @return obj The config stdClass
	 * @access protected
	 */
	public function loadConfigVars() {
		
		$conf = new stdClass;
		
		try {
	
			$conf->marker = get_option('pqr_marker', '');
			$conf->ecl = get_option('pqr_ecl', WPPageQR::ECL_LOW);
			$conf->size = get_option('pqr_size', 4);
			$conf->mode = get_option('pqr_mode', WPPageQR::MODE_DISABLED);
			$conf->cacheFolder = get_option('pqr_cache_folder', '');
			$conf->cssQRcodeClass = get_option('pqr_css_code_class', 'qrCode');
		
		} catch (Exception $e) {
		
			return false;
		
		}
		
		return $conf;
	}
	
	/**
	 * Update plugin's config array in database
	 *
	 * @param stdClass $conf: Ref to configuration array
	 * @return obj The config stdClass
	 * @access protected
	 */
	public function updateConfigVars(&$conf) {
		
		try {
			
			update_option('pqr_marker', $conf->marker);
			update_option('pqr_ecl', $conf->ecl);
			update_option('pqr_size', $conf->size);
			update_option('pqr_mode', $conf->mode);
			update_option('pqr_cache_folder', $conf->cacheFolder);
			update_option('pqr_css_code_class', $conf->cssQRcodeClass);
			
		} catch (Exception $e) {
			
			return false;
			
		}
		
		return true;
	}	
	
	
	/**
	 * Get all the form values from settings form
	 *
	 * @param void
	 * @return array The form values
	 * @access public
	 */
	public function getUserNewSettings() {
		
		$conf = $this->loadConfigVars();
		$settings = new stdClass;
		
		// Loads values
		$settings->ecl = $this->getPostVar('pqr_ecl', $conf->ecl);
		$settings->size = $this->getPostVar('pqr_size', $conf->size);
		$settings->marker = $this->getPostVar('pqr_marker', $conf->marker);
		$settings->mode = $this->getPostVar('pqr_mode', $conf->mode);
		$settings->cacheFolder = $this->getPostVar('pqr_cache_folder', $conf->cacheFolder);
		$settings->cssQRcodeClass = $this->getPostVar('pqr_css_code_class', $conf->cssQRcodeClass);
		
		return $settings;
	}
	
	
	/**
	 * Get page's url
	 *
	 * @param void
	 * @return string The url
	 * @access public
	 */
	public function getPageUrl() {
		
		$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		return $url;
	}
	
	
	/**
	 * Make the filename
	 *
	 * @param void 
	 * @return string The filename
	 * @access public
	 */
	public function getFilename() {
		return 'qrcode_'.md5($this->getPageUrl()).'.png';
	}
	
	
	/**
	 * Retrieve a POST variable
	 *
	 * @param string $var: The variable
	 * @param string $default: optional default value if not found
	 * @return string The value
	 * @access public
	 */
	public function getPostVar($var, $default = '') {
		
		if (isset($_POST[$var])) {
			return $_POST[$var];
		}
		return $default;
	}
	
}

?>
