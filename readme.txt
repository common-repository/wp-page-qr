=== Plugin Name ===
Contributors: mArm
Donate link: http://www.ansermot.ch/
Tags: code,share,qr,mobile,sharing,social,post,qrcode,image
Requires at least: 3.0.0
Tested up to: 3.4
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allow you to insert **QR Code** in your post content.

== Description ==

This plugin allow you to insert **QR Code** in your post content.<br />
This allow visitors to read very easily your post on a mobile phone, by taking a photo of the code.<br /><br />

The plugin works in 3 modes
<ul>
	<li>Insert the marker [QR] (can be changed in settings) in your post content and user the css class to customize</li>
	<li>Auto insert the code in your post content</li>
	<li>Use a template tag to insert it in your template</li>
</ul>

== Installation ==

1. Upload `wp-page-qr` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Define the cache folder (or use the default one) and then set the rights 777 on it
4. Place `<?php wppageqr_code(); ?>` in your templates if you use "Template Tag Mode"

== Frequently Asked Questions ==

= Can I display QR Code on my other pages ? =
Yes you can, you have to use "Template Tag" mode and the template tag. Pass the parameter *doap* set at 1.

== Template Tag parameters ==
Parameters are passed as array : array('doap' => 1, 'ecl' => 3) for exemple
* doap (0 / 1) : Allow displaying on all pages if set to 1. Else, it process only single pages
* ecl (1,3,5) : Error correction level for QR code
* size (1 - 8) : QR Code's quality size ratio

== Screenshots ==

1. QR Code inserted by placer [QR] in the post content. Marker can me changed.
2. Settings panel
3. QR Code inserted for all pages in Template Tag mode (doap = 1 parameter)

== Changelog ==

= 1.1.3 =
* Updated database to 1.1
* Added update script
* WP 3.4.1 compatibility update
* PHP 5.2 is the minimal version required now !

= 1.1.2 =
* WP 3.1 compatibility update

= 1.1.1 =
* Reversed "bad qr code url" fix

= 1.1.0 =
* Added "doap" parameter for Template Tag
* Added "size" parameter for Template Tag
* Added "ecl" parameter for Template Tag
* Added some comments
* Cleaned code
* Added parameters support in create() function
* Added marker removing when post is not single
* Fixed bad qr code url

= 1.0.2 =
* Fixed bad image url causing image not displaying in posts

= 1.0.1 =
* Renamed wp-pageqr.php to wp-page-qr.php
* Fixed readme.txt

= 1.0.0 =
* First version
* 3 modes available : Template tag, post marker and auto insert
* Process only single posts


