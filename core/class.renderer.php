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
 * Plugin's layout renderer class
 *
 * @author David Ansermot <dev@ansermot.ch>
 * @filename class.renderer.php
 * @package wp_page_qr
 * @final
 */

final class Renderer {
	
	// Reference to plugin root class
	protected $piRef = null;
	
	/**
	 * Contructor
	 *
	 * @param &obj $pi: Reference to the plugin root class
	 * @return void
	 * @access public
	 */
	public function __construct(&$pi) {
		$this->piRef = $pi;
	}
	
	
	/**
	 * Display setting panel
	 *
	 * @param array $messages: Messages list to display on top
	 * @param bool $return: If true, return the content
	 * @return string The path
	 * @access public
	 */
	public function settingPanel($messages = null, $return = false) {
	
		// Inits
		$out = array();
		$conf = $this->piRef->getHelper()->loadConfigVars();
		
		// Header
		$out[] = '<div class="wrap">';
		$out[] = '<h2>WP Page QR</h2>';
	
		// Messages
		$out[] = $this->HTMLmessagesList($messages);
		
		// Form
		$out[] = '<form method="post" name="pqr_setting_form" id="pqr_setting_form" action="options-general.php?page=wp-page-qr">';
		$out[] = '<input type="hidden" name="pqr_action" id="pqr_action" value="update-settings" />';
		settings_fields('pqr_plugin_settings');
		$out[] = '
		<table class="form-table">	
				 <tr valign="top">
					<th scope="row">Mode</th>
					<td>
						<select name="pqr_mode" id="pqr_mode">
							'.$this->elemOptionTag('Disabled', WPPageQR::MODE_DISABLED, $conf->mode).'
							'.$this->elemOptionTag('Marker', WPPageQR::MODE_MARKER, $conf->mode).'
							'.$this->elemOptionTag('Template tag', WPPageQR::MODE_TT, $conf->mode).'
							'.$this->elemOptionTag('Auto insert', WPPageQR::MODE_AUTO, $conf->mode).'
						</select>
					</td>
        </tr>
		
		 		 <tr valign="top">
					<th scope="row">Cache folder</th>
					<td><input type="text" name="pqr_cache_folder" id="pqr_cache_folder" value="'.$conf->cacheFolder.'" /><br ><span>Default: '.PQR_CACHE.'</span></td>
        </tr>
		
        <tr valign="top">
					<th scope="row">Marker</th>
					<td><input type="text" name="pqr_marker" id="pqr_marker" value="'.$conf->marker.'" /></td>
        </tr>
         
				 <tr valign="top">
					<th scope="row">QR Code CSS class</th>
					<td><input type="text" name="pqr_css_code_class" id="pqr_css_code_class" value="'.$conf->cssQRcodeClass.'" /></td>
        </tr>
				 
        <tr valign="top">
					<th scope="row">Error Correction Level</th>
					<td>
						<select name="pqr_ecl" id="pqr_ecl">
							'.$this->elemOptionTag('Low', (string)WPPageQR::ECL_LOW, $conf->ecl).'
							'.$this->elemOptionTag('Middle', (string)WPPageQR::ECL_MID, $conf->ecl).'
							'.$this->elemOptionTag('High', (string)WPPageQR::ECL_HIGH, $conf->ecl).'
						</select>
					</td>
        </tr>
        
        <tr valign="top">
					<th scope="row">Size</th>
					<td>
						<select name="pqr_size" id="pqr_size">
							'.$this->elemOptionTag('1', '1', $conf->size).'
							'.$this->elemOptionTag('2', '2', $conf->size).'
							'.$this->elemOptionTag('3', '3', $conf->size).'
							'.$this->elemOptionTag('4', '4', $conf->size).'
							'.$this->elemOptionTag('5', '5', $conf->size).'
							'.$this->elemOptionTag('6', '6', $conf->size).'
							'.$this->elemOptionTag('7', '7', $conf->size).'
							'.$this->elemOptionTag('8', '8', $conf->size).'
						</select>
					</td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="'._('Save Changes').'" />
    </p>

</form>';
		
		// End
		$out[] = '</div>';
		
		$html = implode("\n", $out);
		
		if ($return) {
			return $html;
		}
		echo $html;
		
	}
	
	
	/**
	 * Return image html tag
	 *
	 * @param string $imageUrl: The image url
	 * @return string The html tag
	 * @access public
	 */
	public function HTMLqrCodeImage($imageUrl) {
		$conf = $this->piRef->getHelper()->loadConfigVars();
		return '<img src="'.$imageUrl.'" alt="qrCode" class="'.$conf->cssQRcodeClass.'" />';
	}
	
	
	/**
	 * Build a select option tag
	 *
	 * @param string $label: The option label
	 * @param string $value: The option value
	 * @param mixed $compare: The compare value for "selected"
	 * @return string The option tag
	 * @access public
	 * @since 1.0.2
	 */
	public function elemOptionTag($label, $value, $compare) {
		$selected = ($value == $compare) ? ' selected="selected"' : '';
		return '<option value="'.$value.'"'.$selected.'>'._($label).'</option>';
	}
	
	
	/**
	 * Build messages list
	 *
	 * @params array $messages: The messages
	 * @return string The html
	 * @access public
	 * @since 1.1.0
	 */
	public function HTMLmessagesList(array $messages) {
		
		$out = array();
		
		if (isset($messages) && count($messages) > 0) {
			$out[] = '<ul>';
			foreach ($messages as $message) {
				$out[] = '<li>'.$message.'</li>';
			}
			$out[] = '</ul>';
		}
		
		return implode("\n", $out);
	}
	 
	
}

?>
