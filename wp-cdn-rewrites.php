<?php
/*
Plugin Name: WP CDN Rewrites
Description: This plugin rewrites the host of static files into a CDN. <br />The CDN url is taken from the wp-config. The files to be rewriten are (CSS, JS, images, docs and gallery folder) <br />Images: png, jpg. jpeg, gif, ico. Media: avi, wmv, mpg, wav, mp3. Documents: txt, rtf, doc, xls, rar, zip, tar, gz, exe.
Version: 1.1
Date: 2012.December
Author: Michelle Torres
Author URI: http://michelletorres.mx/acerca/

 * Version 1.1
 * Added validation to not rewrite the origin of iframes for galleries
 * Converted to a global variable


INSTRUCTIONS
============
In the wp-config file add the url for you CDN without the trailing slash
 
 example:
 	//Setting the CDN url in Edgecast
	define('CDN_URL','http://assets.michelletorres.mx');

*/ 

require_once(dirname(__FILE__) . '/cdn_class.php');

global $cdnObject;
$cdnObject = new WP_CDNRewrites();

if (!is_admin()) 
{
    add_action('get_header', array($cdnObject, 'pre_content'), PHP_INT_MAX);
    add_action('wp_footer', array($cdnObject, 'post_content'), PHP_INT_MAX);
}

/* Options */
register_activation_hook ( __FILE__, array($cdnObject , 'activate') );
register_deactivation_hook ( __FILE__, array($cdnObject , 'deactivation') );

/* Admin menu */
add_action('admin_menu', array($cdnObject,'adminmenu'));
add_action('admin_notices', array($cdnObject,'addNotices'));
add_action ( 'admin_init' , array($cdnObject , 'init' ));
