<?php
/*
Plugin Name: Infinite Scroll helper plugin for Graphene
Plugin URI: http://www.prasannasp.net/wordpress-plugins/infinite-scroll-helper-plugin-for-graphene/
Description: Infinite Scroll helper plugin for the Graphene Theme. JetPack plugin should be installed first.
Author: Prasanna SP
Version: 1.0
Author URI: http://www.prasannasp.net/
*/
// Do not activate the plugin if the current theme is not Graphene
$is_graphene_active_template = get_template();
	if($is_graphene_active_template != 'graphene')
		wp_die( __('You are not using the Graphene Theme!'),
            		__('Error')
        	);

// Add theme support for infinite scroll module in the JetPack plugin

function infinite_scroll_graphene_main() {
	$options = get_option('isgh_options');
	$type = $options['isgh_type'];
	$footer_widgets = $options['isgh_footer_widgets'];
	$wrapper = $options['isgh_wrapper'];
	$posts_per_page = $options['isgh_posts_per_page'];

add_theme_support( 'infinite-scroll', array(
 	'type'           => ''.$type.'',
	'footer_widgets' => ''.$footer_widgets.'',
	'container'      => 'content-main',
	'wrapper'        => ''.$wrapper.'',
	'render'         => 'infinite_scroll_graphene_render',
	'posts_per_page' => ''.$posts_per_page.''
) );
}

// render posts
function infinite_scroll_graphene_render() {
        while ( have_posts() ) {
                the_post();
                get_template_part( 'loop', get_post_format() );
        }
}

// call the function
infinite_scroll_graphene_main();

// enqueue stylesheet to hide posts navigation when infinite scroll is active
wp_enqueue_style( 'infinite-scroll-graphene', plugins_url( '/css/infinite-scroll-graphene.css' , __FILE__ ), array(), '', false );

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'isgh_add_defaults');
register_uninstall_hook(__FILE__, 'isgh_delete_plugin_options');
add_action('admin_init', 'isgh_init' );
add_action('admin_menu', 'isgh_add_options_page');
add_filter('plugin_action_links', 'isgh_plugin_action_links', 10, 2 );

// Delete options table entries ONLY when plugin deactivated AND deleted
function isgh_delete_plugin_options() {
	delete_option('isgh_options');
}

function isgh_add_defaults() {
	$tmp = get_option('isgh_options');
	if(($tmp['isgh_default_options_db']=='1')||(!is_array($tmp))) {
		$arr = array(	"isgh_type" => "scroll",
				"isgh_footer_widgets" => "false",
				"isgh_wrapper" => "true",
				"isgh_posts_per_page" => "false",
				"isgh_default_options_db" => ""

		);
		update_option('isgh_options', $arr);
	}
   }

function isgh_init(){
	register_setting( 'isgh_plugin_options', 'isgh_options', 'isgh_validate_options' );
}

function isgh_add_options_page() {
	add_options_page('Infinite Scroll helper plugin Options Page', 'Infinite Scroll helper plugin', 'manage_options', __FILE__, 'isgh_options_page_form');
}

/*
** Thanks to David Gwyer for Plugin Options Starter Kit plugin! wordpress.org/extend/plugins/plugin-options-starter-kit/
*/

function isgh_options_page_form() {
	?>
	<div class="wrap">

		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Infinite Scroll helper plugin Options</h2>
		<h3>Set your options for Infinite Scroll helper plugin for the <a href="http://wordpress.org/extend/themes/graphene" title="Download Graphene Theme" target="_blank">Graphene Theme</a>. See <a href="http://jetpack.me/support/infinite-scroll/" title="Infinite Scroll support" target="_blank">this page</a> for more details</h3>

		<form method="post" action="options.php">
			<?php settings_fields('isgh_plugin_options'); ?>
			<?php $options = get_option('isgh_options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Scroll Type</th>
					<td>
						<label><input name="isgh_options[isgh_type]" type="radio" value="scroll" <?php checked('scroll', $options['isgh_type']); ?> /> Scroll </label><br />

						<label><input name="isgh_options[isgh_type]" type="radio" value="click" <?php checked('click', $options['isgh_type']); ?> /> Click </label><br />
					</td>
				</tr>
				<tr>
					<th scope="row">Have footer widgets?:</th>
					<td>
						<input type="text" size="50" name="isgh_options[isgh_footer_widgets]" value="<?php echo $options['isgh_footer_widgets']; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">Wrapper:</th>
					<td>
						<input type="text" size="50" name="isgh_options[isgh_wrapper]" value="<?php echo $options['isgh_wrapper']; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">Posts per page:</th>
					<td>
						<input type="text" size="50" name="isgh_options[isgh_posts_per_page]" value="<?php echo $options['isgh_posts_per_page']; ?>" />
					</td>
				</tr>
				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options:</th>
					<td>
						<label><input name="isgh_options[isgh_default_options_db]" type="checkbox" value="1" <?php if (isset($options['isgh_default_options_db'])) { checked('1', $options['isgh_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
<hr />
<p style="margin-top:15px;font-size:12px;">If you have found this plugin is useful, please consider making a <a href="http://prasannasp.net/donate/" target="_blank">donation</a> to support the further development of this plugin. Thank you!</p>
	</div>
	<?php	
}

// Sanitize and validate input
function isgh_validate_options($input) {
	 // strip html from textboxes
	$input['isgh_type'] =  wp_filter_nohtml_kses($input['isgh_type']); // strip html tags, and escape characters
	$input['isgh_footer_widgets'] =  wp_filter_nohtml_kses($input['isgh_footer_widgets']);
	$input['isgh_isgh_wrapper'] =  wp_filter_nohtml_kses($input['isgh_isgh_wrapper']);
	$input['isgh_posts_per_page'] =  wp_filter_nohtml_kses($input['isgh_posts_per_page']);
	return $input;
}

// Display a Settings link on the main Plugins page
function isgh_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$isgh_link = '<a href="'.get_admin_url().'options-general.php?page=graphene-infinite-scroll-helper-plugin/infinite-scroll-helper-plugin-for-graphene.php" title="Infinite Scroll helper plugin">'.__('Settings').'</a>';
		
		// make the 'Settings' link appear first
		array_unshift( $links, $isgh_link );
	}

	return $links;
}

// Donate link on manage plugin page
function isgh_pluginspage_links( $links, $file ) {

$plugin = plugin_basename(__FILE__);

// create links
if ( $file == $plugin ) {
return array_merge(
$links,
array( '<a href="http://www.prasannasp.net/donate/" target="_blank" title="Donate for this plugin via PayPal">Donate</a>',
'<a href="http://www.prasannasp.net/wordpress-plugins/" target="_blank" title="View more plugins from the developer">More Plugins</a>',
'<a href="http://twitter.com/prasannasp" target="_blank" title="Follow me on twitter!">twitter!</a>'
 )
);
			}
return $links;

	}
add_filter( 'plugin_row_meta', 'isgh_pluginspage_links', 10, 2 );
