<?php

class blog_post_service_URI{
		
	private $bpsTypeName = 'blog_post_service';
	
	public function __construct() {
		add_action( 'init', array( $this, 'register_bps_post_type' ) );
		add_action('admin_menu', array($this, 'hide_add_new_bps'));
		
		//add_action('admin_head', array($this, 'hide_that_stuff'));
	}
	
	/* Filter the single_template with our custom function*/


	function my_custom_template($single) {
	    global $post;
	
		/* Checks for single template by post type */
		if ($post->post_type == $bpsTypeName){
		    if(file_exists(plugin_dir_path( __FILE__ ). 'blog-post-service-template.php')){
		    	 return plugin_dir_path( __FILE__ ). 'blog-post-service-template.php';
		    }      
		}
	    return $single;
	}
	
	function hide_that_stuff() {
	    if('agents' == get_post_type())
	  echo '<style type="text/css">
	    #favorite-actions {display:none;}
	    .add-new-h2{display:none;}
	    .tablenav{display:none;}
	    </style>';
	}
	
	public function custom_post_type_init(){
		$this->register_bps_post_type();
	}
	
	public function hide_add_new_bps() {
	    global $submenu;
	    // replace my_type with the name of your post type
	    unset($submenu['edit.php?post_type=' . $bpsTypeName][10]);
	}

	public function register_bps_post_type() {
		register_post_type( $this->bpsTypeName,
	        array(
	            'labels' => array(
	                'name' => 'Blog Post Services',
	                'singular_name' => 'Blog Post Review',
	                //'add_new' => 'Add New',
	                //'add_new_item' => 'Add New Blog Post Service Review',
	                'edit' => 'Edit',
	                //'edit_item' => 'Edit Movie Review',
	                //'new_item' => 'New Movie Review',
	                'view' => 'View',
	                'view_item' => 'View Blog Post Service',
	               // 'search_items' => 'Search Movie Reviews',
	                'not_found' => 'No Blog Post Services found',
	                'not_found_in_trash' => 'No Blog Post Services found in Trash',
	                //'parent' => 'Parent Movie Review'
	            ),
	 
	            'public' => true,
	            'menu_position' => 15,
	            'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'page-attributes' ),
	            'taxonomies' => array( '' ),
	            'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
	            'has_archive' => true
	        )
	    );

	}

	public function create_service_page($slug){
			
		global $user_ID;
		
		$wp_error = true; //(bool) (optional) Allow return of WP_Error object on failure Default: false
	
		$post = array(
		  //'ID'             => '',//[ <post id> ] // Are you updating an existing post?
		  //'post_content'   => '',//[ <string> ] // The full text of the post.
		  'post_name'      => $slug,//[ <string> ] // The name (slug) for your post
		  'post_title'     => 'Blog Post Service',//[ <string> ] // The title of your post.
		  'post_status'    => 'publish',//[ 'draft' | 'publish' | 'pending'| 'future' | 'private' | custom registered status ] // Default 'draft'.
		  'post_type'      => $this->bpsTypeName,//[ 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type ] // Default 'post'.
		  'post_author'    => $user_ID,//[ <user ID> ] // The user ID number of the author. Default is the current user ID.
		  //'ping_status'    => '',//[ 'closed' | 'open' ] // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
		  'post_parent'    => 0,//[ <post ID> ] // Sets the parent of the new post, if any. Default 0.
		  //'menu_order'     => '',//[ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
		  //'to_ping'        => '',// Space or carriage return-separated list of URLs to ping. Default empty string.
		  //'pinged'         => '',// Space or carriage return-separated list of URLs that have been pinged. Default empty string.
		  //'post_password'  => '',//[ <string> ] // Password for post, if any. Default empty string.
		  //'guid'           => '',// Skip this and let Wordpress handle it, usually.
		  //'post_content_filtered' => '',//// Skip this and let Wordpress handle it, usually.
		  //'post_excerpt'   => '',//[ <string> ] // For all your post excerpt needs.
		  //'post_date'      => '',//[ Y-m-d H:i:s ] // The time post was made.
		  //'post_date_gmt'  => '',//[ Y-m-d H:i:s ] // The time post was made, in GMT.
		  //'comment_status' => '',//[ 'closed' | 'open' ] // Default is the option 'default_comment_status', or 'closed'.
		  //'post_category'  => '',//[ array(<category id>, ...) ] // Default empty.
		  //'tags_input'     => '',//[ '<tag>, <tag>, ...' | array ] // Default empty.
		  //'tax_input'      => '',//[ array( <taxonomy> => <array | string> ) ] // For custom taxonomies. Default empty.
		 'page_template'  => $dir = plugin_dir_path( __FILE__ ) . 'blog-post-service-template.php'//[ <string> ] // Requires name of template file, eg template.php. Default empty.
		);
		
		$thisPostID = wp_insert_post( $post, $wp_error );
		
		return $thisPostID;		
	}
	
	public function my_theme_redirect() {
		global $wp;
		$plugindir = dirname( __FILE__ );
	
		//A Specific Custom Post Type
		if ($wp->query_vars["post_type"] == $this->bpsTypeName) {
			$templatefilename = 'blog-post-service-template.php';
			if(file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
				$return_template = TEMPLATEPATH . '/' . $templatefilename;
			}else{
				$return_template = $plugindir . '/' . $templatefilename;
			}
			do_theme_redirect($return_template);
		}
	}

	public function delete_service_page($slugName){
		$postID = bps_get_page_id_by_slug($slugName);
		wp_delete_post($postID);
	}
	
	public function unregister_bps_post_type(){
		
		$post_type = 'blog_post_service_post';
		
		if ( ! function_exists( 'unregister_post_type' ) ) :
			function unregister_post_type( $post_type ) {
			    global $wp_post_types;
			    if ( isset( $wp_post_types[ $post_type ] ) ) {
			        unset( $wp_post_types[ $post_type ] );
			        return true;
			    }
			    return false;
			}
		endif;
	}
}//END blog_post_service_class;
