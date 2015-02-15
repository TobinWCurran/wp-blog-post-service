<?php
class blog_post_service_admin{
	
	private $settingsName = 'blog_post_service';
	private $options; //Holds the values to be used in the fields callbacks
	
	private $bpsURIClass;
	
	private $bpsOptions;
	
	public function __construct() {
		$this->bpsURIClass = new blog_post_service_URI();
		$this->bpsURIClass->my_theme_redirect();
		
		$this->bpsOptions = get_option($this->settingsName);
		
		add_action( 'admin_menu', array( &$this, 'add_admin_page' ) );
		add_action( 'admin_init', array( &$this, 'admin_menu_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_admin_page() {
		// This page will be under "Settings"
		add_options_page(
			'Blog Post Service Settings', //The text to be displayed in the <title> tags of the page when the menu is selected
			'Blog Post Service', //The text to be used for the Title on the page
			'manage_options', //Only available is the user has the right to manage options
			'bps-admin', //this is the Menu Page Slug
			array( &$this, 'create_admin_page' ) //The function to be called to output the content for this page.
		);
		
		//add_option(
			//'bps_admin_options',
		//);
	}
	
	public function admin_menu_init(){
		//You need to explicitly point to the global $twcBPSpostPartsArray
		global $twcBPSpostPartsArray;
		
		register_setting(
			'blog-post-service-setting', // Option group ID. This must be the same as in the settings_fields function call
			'blog_post_service', // Option name; this is the name of the DB field as well
			array( $this, 'sanitize_callback' ) // Sanitize);
		);		
		
		//Set the General Settings section of the admin page
		add_settings_section(
			'blog_post_service_general', // ID - String for use in the 'id' attribute of the Section
			'General Settings', // Title
			array( $this, 'print_general_settings_section' ), // Callback
			'bps-admin' // The menu page on which to display this section. Should match Menu Page Slug.
		);
		
		//Set a field in the General Settings section of the admin page
		add_settings_field(
			'service_slug', // ID - String for use in the 'id' attribute of tags.
			'Service Slug', // Title of the field. 
			array( $this, 'service_slug_callback' ), // Callback
			'bps-admin', // The menu page on which to display this section. Should match Menu Page Slug. 
			'blog_post_service_general' // Section. Must match the ID set in add_settings_section        
		);
		
		//Set a field in the General Settings section of the admin page
		add_settings_field(
			'service_slug_id', // ID - String for use in the 'id' attribute of tags.
			//'Service Slug ID', // Title of the field. 
			array( $this, 'service_slug_id_callback' ), // Callback
			'bps-admin', // The menu page on which to display this section. Should match Menu Page Slug. 
			'blog_post_service_general' // Section. Must match the ID set in add_settings_section        
		);
		

		
		//Set a field in the General Settings section of the admin page
		add_settings_field(
			'number_of_posts', // ID - String for use in the 'id' attribute of tags.
			'Number of Posts', // Title of the field. 
			array( $this, 'number_of_posts_field_callback' ), // Callback
			'bps-admin', // The menu page on which to display this section. Should match Menu Page Slug.
			'blog_post_service_general' // Section. Must match the ID set in add_settings_section        
		);
		
		add_settings_section(
			'blog-post-service-parts', // ID - String for use in the 'id' attribute of the Section
			'Blog Parts', // Title
			array( $this, 'print_blog_part_section' ), // Callback
			'bps-admin' // The menu page on which to display this section. Should match Menu Page Slug. 
		);

		foreach ($twcBPSpostPartsArray as $key => $value) {
			//Set the Blog Post Parts section of the admin page
			
			$partID = $value['wordpressKey'];
			$wordpressKey = $value['wordpressKey'];
			
			$args = array(
				'wordpressKey' => $wordpressKey,
				'partID' => $partID
				);
			
			add_settings_field(
				'blog_post_service_' . $partID, // ID - String for use in the 'id' attribute of the Section
				$key, // Title
				array($this, 'blog_parts_callback'), // Callback
				'bps-admin', // Page -  The menu page on which to display this section. Should match $menu_slug from Function Reference/add theme
				'blog-post-service-parts', //Section
				$args//args
			);	
		}//END foreach
	}// END admin_menu_init()
	
	public function sanitize_callback($input){
		
		$currentServicePageSlug = $this->bpsOptions['service_slug'];
		$currentServicePageSlugID = $this->bpsOptions['service_slug_id'];
		
		$newServicePageSlug = $input['service_slug'];	
		
		if($currentServicePageSlug == $newServicePageSlug){
			//If the Page Slug hasn't changed, we just return the $input as-is
			return $input;		
		}else{
			//If the slug has changed, we first run the create_service_page method
			//It will create a new service page with the new slug, and then it will
			//return the post_id for the new page
			$newServicePageID = $this->bpsURIClass->create_service_page($newServicePageSlug);
			
			//Now we delete the old Blog Post Service page, based on the ID that was picked up from
			//BPS options, which was set in the hidden input field
			wp_delete_post($input['service_slug_id']);
			
			//Finally, we update the $input array with our new Service Page ID
			//which gets stored for reference in the BPS options
			$input['service_slug_id'] = $newServicePageID;

			return $input;
		}			
	}
	
	public function create_admin_page(){?>
		
		<div class="wrap">
			<h2>Blog Post Service Settings</h2>
			<form id="form-blog-post-service-admin" method="post" action="options.php">
				<?php
					settings_fields( 'blog-post-service-setting' ); //This must match the Option Group ID in register_setting()
					do_settings_sections( 'bps-admin' ); 	//The slug name of the page whose settings sections you want to output.
															//This should match the page name used in add_settings_section().
					submit_button();
				?>
			</form>
		</div>
		
	<?php }
	
	public function print_general_settings_section(){

		$slugID = $this->bpsOptions['service_slug_id'];
		
		var_dump($slugID);

		$settingFieldID = 'service_slug_id'; //<-----------------CHANGE THIS ONE
		$cssIDForEcho = 'service-slug-id'; //<------------------AND THIS ONE TOO
		$nameForEcho = $this->settingsName . '[' . $settingFieldID . ']';
		$slugName = $this->bpsOptions['service_slug'];
		$optionsValue = $this->bpsOptions[$settingFieldID];
		
		if (empty($optionsValue)){
			$valueForEcho = '';
		}else{
			$valueForEcho = $optionsValue;
		}
		
		echo '<input type="hidden" id="' . $cssIDForEcho . '" name="' . $nameForEcho . '" value="' . $valueForEcho . '">';
		
		//if ($post->post_type == "blog_post_service"){
			if(file_exists(plugin_dir_path( __FILE__ ). 'blog-post-service-template.php')){
				echo '<h3>' . plugin_dir_path( __FILE__ ) . 'blog-post-service-template.php </h3>';
				
				
				//	$path		(string) (optional) Path relative to the site url.
				//				Default: None
				//	$scheme		(string) (optional)
				//				Scheme to give the site url context. Currently 'http', 'https', 'login', 'login_post', 'admin' or 'relative'.
				//				Default: null
				
				$path = '';
				$scheme = null;
				echo '<h3>' . get_site_url( $slugID, $path, $scheme ) . '/' . $slugName . '</h3>';
			}else{
				echo 'Nope';
			}
		//}
	}
	
	public function print_blog_part_section(){
		echo "<p>Here is where we can put some explanatory text</p>";
	}
	
	public function service_slug_callback(){
			
		$settingFieldID = 'service_slug'; //<-----------------CHANGE THIS ONE
		$cssIDForEcho = 'service-slug'; //<------------------AND THIS ONE TOO
		$nameForEcho = $this->settingsName . '[' . $settingFieldID . ']';
		
		if(!empty($this->bpsOptions[$settingFieldID])){
			//If the value has already been set, then we're goign to go and get
			//the page slug from the wp_sandbox_posts table in the database
			$post_id = $this->bpsOptions['service_slug_id'];
			$post = get_post($post_id);
			$value = $post->post_name;
			$valueForEcho = $value;
		}else{
			$valueForEcho = 'blog-post-service';
		}
		
		echo '<input type="text" id="' . $cssIDForEcho . '" name="' . $nameForEcho . ' " value="' . $valueForEcho . '" />';
		
		?>
		
			<p class="description" style="margin-bottom: 16px">
				Please select a slug for your Blog Post Service. The slug will be used for the service URL<br>
				(e.g.: your-wordpress-site.com/blog-post-service/).
			</p>
			<p class="description" style="margin-bottom: 16px">
				It should be concise and descriptive. If in doubt, the default value is a pretty good choice.
			</p>
			<p class="description">
				<span style="color: #FF1111; font-weight: bold;">Warning:</span> Once you have other sites relying
				on this Blog Post Service, changing the<br>
				slug name will break their connections to it.
			</p>
		
		<?php
	}

	public function service_slug_id_callback(){
		//We could add some future functionality here.
	}

	public function number_of_posts_field_callback(){

		$settingFieldID = 'number_of_posts'; //<-----------------CHANGE THIS ONE
		$cssIDForEcho = 'number-of-posts'; //<------------------AND THIS ONE TOO
		$nameForEcho = $this->settingsName . '[' . $settingFieldID . ']';
		
		if(!empty($this->bpsOptions)){
			$value = $this->bpsOptions[$settingFieldID];
		}
		if (empty($value)){
			$valueForEcho = '1';
		}else{
			$valueForEcho = $value;
		}
		echo '<input type="number" id="' . $cssIDForEcho . '" name="' . $nameForEcho .'"  step="1" min="1" value="' . $valueForEcho . '" class="small-text" />';
		?>
			<p class="description" style="margin-bottom: 16px">
				This is the number of posts that will be published by the Blog Post Service.
			</p>
			<p class="description">
				The minimum number is 1. Posts will be published newest on top.
			</p>
		<?php
	}
	
	public function blog_parts_callback($args){
		$checked;
		$partID = $args['partID'];
		$wordpressKey = $args['wordpressKey'];
		$settingFieldID = 'blog_post_service_' . $partID; //<-----------------CHANGE THIS ONE
		$cssIDForEcho = 'blog-part-' . $partID; //<------------------AND THIS ONE TOO
		$nameForEcho = $this->settingsName . '[' . $settingFieldID . ']';

		if(!empty($this->bpsOptions)){
			$value = $this->bpsOptions[$settingFieldID];
		}	
		
		if($value == 'on'){
			$checked = 'checked="checked"';
		}else{
			$checked = '';
		}
		echo '<input type="checkbox" id="' . $cssIDForEcho . '" name="' . $nameForEcho . '"' . $checked . ' />';
	}
}//END Class blog_post_service_admin

?>