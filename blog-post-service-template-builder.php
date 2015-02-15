<?php
// Thanks to Harri Bell-Thomas (http://www.wpexplorer.com/author/harri/)
// at http://www.wpexplorer.com/wordpress-page-templates-plugin/

class PageTemplater {
		/**
         * A Unique Identifier
         */
		 protected $plugin_slug; // The Plugin Slug: This is simply used as a unique identifier for the plugin.

        /**
         * A reference to an instance of this class.
         */
        private static $instance; // Class Instance: As we are adding an instance of this class to WordPress’ head, we’d better store it.

        /**
         * The array of templates that this plugin tracks.
         */
        protected $templates; // Template Array: As you can probably guess, this is an array holding the template names and titles.


        /**
         *  Returns an instance of this class. 
         */
         // As I said previously, we’ll be adding an instance of our class to the WordPress header using the add_filter() function.
         // Therefore we will need a method which will return (or create) this instance for us.
         
        public static function get_instance() { //This will be the method called when our class is added to the WordPress head using ‘add_action()’.

                if( null == self::$instance ) { //This syntax is how to call the private static properties and functions
                        self::$instance = new PageTemplater();
                } 

                return self::$instance;

        }
		
		// WordPress Filters

		// Now we’ve sorted out the ‘get_instance’ method, we need to sort out what happens when it is actually instantiated.
		
		// We will use WordPress’s inbuilt add_filter() function to add an instance of our class into key points along the
		// WordPress initialisation timeline. Using this method we will insert our page templates’ data into relevant slots, 
		// such as telling WordPress what file to use as a template when the page is called, and the title to display on
		// the dropdown menu on the Page Editor.
		
		
		// For this we need to use the ‘__construct’ method (this will be run when the class is instantiated).
		
        /**
         * Initializes the plugin by setting filters and administration functions.
         */
       public function __construct() {

                $this->templates = array();


                // Add a filter to the attributes metabox to inject template into the cache.
                add_filter(
					'page_attributes_dropdown_pages_args',
					 array( $this, 'register_project_templates' ) 
				);


                // Add a filter to the save post to inject out template into the page cache
                add_filter(
					'wp_insert_post_data', 
					array( $this, 'register_project_templates' ) 
				);


                // Add a filter to the template include to determine if the page has our 
				// template assigned and return it's path
                add_filter(
					'template_include', 
					array( $this, 'view_project_template') 
				);


                // Add your templates to this array.
                $this->templates = array(
                	'blog-post-service-template.php' => 'Blog Post Service',
                );
				
				add_filter('single_template', array($this, 'my_custom_template'));
				
        }
		
		public function my_custom_template($single){
			
			global $wp_query, $post;

			if ($post->post_type == "blog_post_service"){
			    if(file_exists(plugin_dir_path( __FILE__ ). 'blog-post-service-template.php')){
			    	 return plugin_dir_path( __FILE__ ) . 'blog-post-service-template.php';
			    }
			}
			
			return $single;
		}
		

        public function register_project_templates( $atts ) {

                // Create the key used for the themes cache
                $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

                // Retrieve the cache list. 
				// If it doesn't exist, or it's empty prepare an array
                $templates = wp_get_theme()->get_page_templates();
                if ( empty( $templates ) ) {
                        $templates = array();
                } 

                // New cache, therefore remove the old one
                wp_cache_delete( $cache_key , 'themes');

                // Now add our template to the list of templates by merging our templates
                // with the existing templates array from the cache.
                $templates = array_merge( $templates, $this->templates );

                // Add the modified cache to allow WordPress to pick it up for listing
                // available templates
                wp_cache_add( $cache_key, $templates, 'themes', 1800 );

                return $atts;

        } 

        /**
         * Checks if the template is assigned to the page
         */
        public function view_project_template( $template ) {

                global $post;

                if (!isset($this->templates[get_post_meta( 
					$post->ID, '_wp_page_template', true 
				)] ) ) {
					
                        return $template;
						
                } 

                $file = plugin_dir_path(__FILE__). get_post_meta( 
					$post->ID, '_wp_page_template', true 
				);
				
                // Just to be safe, we check if the file exist first
                if( file_exists( $file ) ) {
                        return $file;
                } 
				else { echo $file; }

                return $template;

        } 


} 

add_action( 'plugins_loaded', array( 'PageTemplater', 'get_instance' ) );


?>