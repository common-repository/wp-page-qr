<?php

/**
 * Install script for version 1.0
 *
 * @author David Ansermot <dev@ansermot.ch>
 * @filename install-1.0.php
 * @package wp_page_qr
 */ 

add_option('pqr_marker', '[QR]');
add_option('pqr_ecl', WPPageQR::ECL_LOW);
add_option('pqr_size', 4);
add_option('pqr_mode', WPPageQR::MODE_DISABLED);
add_option('pqr_cache_folder', '');
add_option('pqr_css_code_class', 'qrCode');

?>