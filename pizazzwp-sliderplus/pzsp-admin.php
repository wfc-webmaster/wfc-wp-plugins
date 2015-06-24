<?php
/**
 * Created by JetBrains PhpStorm.
 * User: chrishoward
 * Date: 6/08/13
 * Time: 8:21 PM
 * To change this template use File | Settings | File Templates.
 */

//add_action( 'admin_menu', 'pizazzwp_admin_menu' );
function pizazzwp_admin_menu() {
	add_options_page( 'PizazzWP', 'PizazzWP', 'manage_options', 'pizazzwp-settings', 'pizazzwp_settings_page' );
}

add_action( 'admin_init', 'pizazzwp_admin_init' );
function pizazzwp_admin_init() {
	register_setting( 'sliderplus-settings-group', 'pizazzwp_sliderplus_settings' );
	add_settings_section( 'sliderplus-settings-section', 'SliderPlus Settings', 'sliderplus_settings_section_callback', 'pizazzwp-settings' );
	add_settings_field( 'pizazzwp-sliderplus-admin-access', 'Minimum admin access', 'pzsp_admin_access_field_callback', 'pizazzwp-settings', 'sliderplus-settings-section' );
}

function sliderplus_settings_section_callback() {
	echo 'Configure SliderPlus settings here';
}

function pzsp_admin_access_field_callback() {
	$pzsp_settings = (array) get_option( 'pizazzwp_sliderplus_settings' ) ;
	$pzsp_admin_access_value = esc_attr($pzsp_settings['admin_access']);
	echo '<select name="pizazzwp_sliderplus_settings[admin_access]">
  <option '.($pzsp_admin_access_value == 'administrator'?'selected':'').' value="administrator">Administrator</option>
  <option '.($pzsp_admin_access_value == 'editor'?'selected':'').' value="editor">Editor</option>
  <option '.($pzsp_admin_access_value == 'author'?'selected':'').' value="author">Author</option>
  <option '.($pzsp_admin_access_value =='contributor'?'selected':'').' value="contributor">Contributor</option>
  <option '.($pzsp_admin_access_value =='subscriber'?'selected':'').' value="subscriber">Subscriber</option>
</select>';

}

function pizazzwp_settings_page() {
	?>
	<div class="wrap">
		<h2>PizazzWP Settings</h2>
		<form action="options.php" method="POST">
			<?php settings_fields( 'sliderplus-settings-group' ); ?>
			<?php do_settings_sections( 'pizazzwp-settings' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}