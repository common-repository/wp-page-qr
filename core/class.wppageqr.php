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

require_once(PQR_CORE.'phpqrcode/qrlib.php');
require_once(PQR_CORE.'class.renderer.php');
require_once(PQR_CORE.'class.helper.php');


/**
 * Plugin's main class WPPageQR
 *
 * @author David Ansermot <dev@ansermot.ch>
 * @filename class.wppageqr.php
 * @package wp_page_qr
 * @version 1.1.2
 */
class WPPageQR {
	
	protected $dbVersion = '1.1';
	
	protected $conf = null;
	protected $helper = null;
	protected $renderer = null;
	
	protected $imgPath = null;
	protected $imgUrl = null;
	
	// Error Correction Level consts
	const ECL_LOW = QR_ECLEVEL_L;
	const ECL_MID = QR_ECLEVEL_M;
	const ECL_HIGH = QR_ECLEVEL_H;
	
	// Plugin working modes
	const MODE_DISABLED = 0;
	const MODE_MARKER = 1;
	const MODE_TT = 2;
	const MODE_AUTO = 3;
	
	
	/**
	 * Constructor
	 *
	 * @param string $cacheFolder: The cache folder's path
	 * @param int $ecl: Error correction level for the qrCode
	 * @param int $size: qrCode's size
	 * @return void
	 * @access public
	 */
	public function __construct($cacheFolder = '', $ecl = self::ECL_LOW, $size = 4, $marker = '[QR]') {
		
		$this->helper = new Helper();
		$this->conf = $this->helper->loadConfigVars();
		$this->renderer = new Renderer($this);

		$this->imgPath = '';
		$this->imgUrl = '';
		
	}
	

	/** 
	 * Install the plugin in WordPress
	 *
	 * @param void
	 * @return void
	 * @access public
	 */
	public function install() {
		
		global $wpdb;
		
		$installed = get_option('wp-pageqr-dbversion', null);
		
		// If not installed, include install script
		if ($installed === null) {
			include_once(PQR_INSTALL.'install-1.0.php');
			$installed = '1.0';
		}
		
		// Update database 1.1
		if (version_compare($installed, '1.1', '<')) {
			include_once(PQR_INSTALL.'install-1.1.php');
		}
		
		// Update version number
		if ($installed === null) {
			add_option('wp-pageqr-dbversion', $this->dbVersion);
		} else {
			update_option('wp-pageqr-dbversion', $this->dbVersion);
		}
		
		return true;
	}
	
	
	/**
	 * Uninstall the plugin in WordPress
	 *
	 * @param void
	 * @return void
	 * @access public
	 */
	public function uninstall() {
		
		delete_option('pqr_marker');
		delete_option('pqr_ecl');
		delete_option('pqr_size');
		delete_option('pqr_mode');
		delete_option('pqr_cache_folder');
		delete_option('pqr_css_code_class');
		
		delete_option('wp-pageqr-dbversion');
	}
	
	
	/**
	 * Process the content for replacing 
	 * market by the qrcode image
	 *
	 * @param string $content: Content to parse
	 * @param bool $tt: Set if called by template tag
	 * @param array $params: Optional parameter array for QR Code
	 * @return void
	 * @access public
	 */
	public function processContent($content, $tt = false, $params = null) {
		
		$displayOnAllPages = (isset($params['doap']) && $params['doap'] == '1') ? true : false;
		
		// Process only single posts
		if ($displayOnAllPages || is_single()) {
			
			// Plugin disabled
			if ($this->conf->mode == self::MODE_DISABLED) {
				return $content;
			}
			
			// Create QR Code
			$this->create($params);
			
			// Marker mode
			if ($this->conf->mode == self::MODE_MARKER) {
				
				$qrCodeHTML = $this->renderer->HTMLqrCodeImage($this->getImageUrl());
				return str_replace($this->conf->marker, $qrCodeHTML, $content);
				
			// Auto-add mode
			} else if ($this->conf->mode == self::MODE_AUTO) {
				
				return $content.$this->renderer->HTMLqrCodeImage($this->getImageUrl());
				
			// Use template tag
			} else if ($this->conf->mode == self::MODE_TT && $tt) {
				
				return $this->renderer->HTMLqrCodeImage($this->getImageUrl());
				
			}
		} else if (!is_single() &&  $this->conf->mode == self::MODE_MARKER) {
			return str_replace($this->conf->marker, '', $content);
		} else {
			return $content;
		}
		
	}

	/**
	 * Return the class helper
	 *
	 * @param void
	 * @return &obj Ref to the helper object
	 * @access public
	 */
	public function &getHelper() {
		return $this->helper;
	}


	/**
	 * Return the created image's path
	 *
	 * @param void
	 * @return string The path
	 * @access public
	 */
	public function getImagePath() {
		return $this->imgPath;
	}
	
	
	/**
	 * Return the created image's url
	 *
	 * @param void
	 * @return string The url
	 * @access public
	 */
	public function getImageUrl() {
		return $this->imgUrl;
	}

	/**
	 * Return the post marker
	 *
	 * @param void
	 * @return string The marker
	 * @access public
	 */	
	public function getPostMarker() {
		return $this->conf->marker;
	}


	/**
	 * Set the post marker
	 *
	 * @param string $value: The post marker
	 * @return void
	 * @access public
	 */
	public function setPostMarker($value) {
		$this->conf->marker = trim($value);
	}
	
	
	/**
	 * Set up the option menu entry
	 *
	 * @param voir
	 * @return voir
	 * @access public
	 */
	public function setupSettingPanel() {
		add_options_page('WP Page QR', 'WP Page QR', 'administrator', 'wp-page-qr', array(&$this, 'displaySettingPanel'));
		add_action('admin_init', array(&$this, 'registerSettings'));
	}
	
	
	/**
	 * Register plugin setting options
	 *
	 * @param void
	 * @return void
	 * @access public
	 */
	public function registerSettings() {
		
		register_setting('pqr_plugin_settings', 'pqr_marker');
		register_setting('pqr_plugin_settings', 'pqr_ecl');
		register_setting('pqr_plugin_settings', 'pqr_size');
		register_setting('pqr_plugin_settings', 'pqr_mode');
		register_setting('pqr_plugin_settings', 'pqr_cache_folder');
		register_setting('pqr_plugin_settings', 'pqr_css_code_class');
		
	}
	
	
	/**
	 * Display setting panel
	 *
	 * @param void
	 * @return void
	 * @access public
	 */
	public function displaySettingPanel() {
		
		$messages = array();
		
		// Check if settings must be updated
		if ($this->helper->getPostVar('pqr_action', 'display') == 'update-settings') {
			$newSettingsValues = $this->helper->getUserNewSettings();
			$this->helper->updateConfigVars($newSettingsValues);
			$this->conf = $this->helper->loadConfigVars();
			
			$messages[] = _('Settings updated');
		}
		
		$this->renderer->settingPanel($messages);
	}
	
	/** 
	 * Create the image
	 *
	 * @param array $params: The optionnal override config parameters
	 * @return void
	 * @access protected
	 */
	protected function create($params = null) {
		
		$filename = $this->helper->getFilename();
		
		$cacheFolder = (empty($this->conf->cacheFolder)) ? PQR_CACHE : $this->conf->cacheFolder;
		$this->imgPath = $cacheFolder.$filename;
		$this->imgUrl = plugins_url().'/wp-page-qr/cache/'.$filename;
		
		$ecl = (isset($params['ecl']) && !empty($params['ecl'])) ? intval($params['ecl']) : $this->conf->ecl;
		$size = (isset($params['size']) && !empty($params['size'])) ? intval($params['size']) : $this->conf->size;																																		
		
		// @todo : seems to be bugged
		if (!file_exists($this->getImagePath())) {
			QRcode::png($this->helper->getPageUrl(), $this->imgPath, $ecl, $size);
		}
	
	}
	
	
	/**
	 * Clear the cache folder
	 *
	 * @param void
	 * @return bool
	 * @access protected
	 * @todo code the function...
	 */
	protected function clearCache() {
		return true;
	}
	
}

?>
