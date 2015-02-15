<?php

if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

function plugin_section_text(){

?>

	<div class="wrap">
		<?php plugin_section_text(); ?>
		<form method="post" action="options.php">
			<?php settings_fields('twc_bps_options'); ?>
			<h2>Blog Post Service Options</h2>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="table-data">Table Header</label>
					</th>
					<td>
						<input name="" type="text" id="table-data" value="Table Data" class="regular-text" />
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="number-of-posts">Number of Posts</label>
					</th>
					<td>
						<input name="" type="number" step="1" min="1" id="number-of-posts" value="1" class="small-text" />
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="table-data">Service Slug</label><br>
					</th>
					<td>
						<input name="" type="text" id="table-data" value="blog-service" class="regular-text" />
						<p>
							This is the URL name that you would like to serve your Blog Post Service from<br>
							For Example "http://your-site-name.org/blog-post-service"
						</p>
					</td>
				</tr>
			</table>
			<h3>Blog Parts</h3>
			<hr>
			<p>
				Please select which parts of the blog post you wish to display.
			</p>
			<table class="form-table">
				<?php
					foreach ($postPartKeyArray as $key=>$value) {
				?>
					<tr>
						<th scope="row">
							<label for="post-part-<?php echo $value; ?>"><?php echo $key; ?></label><br>
						</th>
						<td>
							<input name="<?php echo $value;?>" type="checkbox" id="post-part-<?php echo $value; ?>" value="1" />
						</td>
					</tr>
				<?php
					}
				?>
			</table>
		</form>
	</div>
<?php
}

echo plugin_section_text();
?>