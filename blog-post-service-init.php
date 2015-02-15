<?php
class blog_post_service_init {
	
	private $bpsURIClass;
	
	public function __construct(){
		
		$this->bpsURIClass = new blog_post_service_URI();
		/* Runs when plugin is activated */
		register_activation_hook( __FILE__, array(&$this, 'on_activation') );
		/* Runs when plugin is deactivated */
		register_deactivation_hook( __FILE__, array(&$this, 'on_deactivation') );
		
		
	}
	
	public function on_activation(){
		//blog_post_service_URI::create_service_page();
		//blog_post_service_URI::register_bps_post_type();
	}
	
	public function on_deactivation(){
	 	$this->bpsURIClass->unregister_bps_post_type();
	 	//blog_post_service_URI::unregister_bps_post_type(); //this syntax can only be used if the method is "public static"
	}
}
?>