<?php
/**
 * Plugin Name: Blog Post Service
 * Plugin URI: #
 * Description: A RESTful service for serving Blog Posts
 * Version: 0.0.1
 * Author: Tobin W. Curran, Boston Financial Data Services, Web Services
 * Author URI: http://tobinwcurran.com / http://bostonfinancial.com
 * Text Domain: Optional. Plugin's text domain for localization. Example: mytextdomain
 * Domain Path: Optional. Plugin's relative directory path to .mo files. Example: /locale/
 * Network: Optional. Whether the plugin can only be activated network wide. Example: true
 * License: The MIT License (MIT)
 */
 

define('TWC_BPS_PLUGIN_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );

function bps_get_page_id_by_slug($slugName){
	global $wpdb;
	$id = $wpdb->get_var("SELECT ID FROM $wpdb->post WHERE post_name = '" . $slugName ."'");
	return $id;
}

$twcBPSpostPartsArray = array(
	'Post Author' => array(
		'wordpressKey' => 'post_author',
		'partID' => 'post-author'
	),
	'Post Date' => array(
		'wordpressKey' => 'post_date',
		'partID' => 'post-date'
	),
	'Post Type' => array(
		'wordpressKey' => 'post_type',
		'partID' => 'post-type'
	),
	'Post Title' => array(
		'wordpressKey' => 'post_title',
		'partID' => 'post-title'
	),
	'Post Content' => array(
		'wordpressKey' => 'post_content',
		'partID' => 'post-content'
	),
	'Post Excerpt' => array(
		'wordpressKey' => 'post_excerpt',
		'partID' => 'post-excerpt'
	)
);

require_once 'blog-post-service-init.php';
require_once 'blog-post-service-uri.php';
require_once 'blog-post-service-admin.php';
require_once 'blog-post-service-template-builder.php';

if ( !defined( 'ABSPATH' ) ) { //Prevent direct access to these classes.
	exit; // Exit if accessed directly
}else {
	if ( is_admin() ) {
		new blog_post_service_admin();
	} //is_admin()
	new blog_post_service_init();
	new blog_post_service_URI();
}


