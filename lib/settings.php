<?php
function unplugged_migs_settings_init() {
    // register a new setting for "unplugged_migs" page
    register_setting( 'unplugged_migs', 'unplugged_migs_options' );

    add_settings_section(
        'unplugged_migs_section_gateways',
        __( 'Gateway Options', 'unplugged_migs' ),
        'unplugged_migs_section_gateways_cb',
        'unplugged_migs'
    );
 
    // register a new field in the "unplugged_migs_section_gateways" section, inside the "unplugged_migs" page
    add_settings_field(
        'unplugged_migs_field_currency', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __( 'Currencies', 'unplugged_migs' ),
        'unplugged_migs_field_currency_cb',
        'unplugged_migs',
        'unplugged_migs_section_gateways',
        array(
            'label_for' => 'unplugged_migs_field_currency',
        )
    );

    add_settings_field(
        'unplugged_migs_field_icon', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __( 'Icon', 'unplugged_migs' ),
        'unplugged_migs_field_icon_cb',
        'unplugged_migs',
        'unplugged_migs_section_gateways',
        array(
            'label_for' => 'unplugged_migs_field_icon',
        )
    );
}
 
/**
 * register our unplugged_migs_settings_init to the admin_init action hook
 */
add_action( 'admin_init', 'unplugged_migs_settings_init' );
 
/**
 * custom option and settings:
 * callback functions
 */
 
// developers section cb
 
// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function unplugged_migs_section_gateways_cb( $args ) {
?>
    <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Add and remove MIGS gateways from here.', 'unplugged_migs' ); ?></p>
<?php
}
 
// currency field cb
 
// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function unplugged_migs_field_currency_cb( $args ) {
    include_once('currencies.php');
    // get the value of the setting we've registered with register_setting()
    $options = get_option( 'unplugged_migs_options' );
    $chosen = array();
    if(isset($options[$args['label_for']]) && is_array($options[ $args['label_for']])) {
        $chosen = $options[ $args['label_for']];
    }
?>
    <select 
        style="height: 300px;"
        multiple="true"
        id="<?php echo esc_attr( $args['label_for'] ); ?>"
        name="unplugged_migs_options[<?php echo esc_attr( $args['label_for'] ); ?>][]"
    >
        <?php foreach($currencies as $key => $value) { ?>
            <option value="<?php echo $key ?>" <?php echo in_array($key, $chosen) ? 'selected="selected"' : '' ?>>
                <?php echo $value; ?>
            </option>
        <?php } ?>
    </select>
    <p class="description">
        <?php esc_html_e( 'Choose your supported MIGS currencies here. Then, go to WooCommerce > Settings > Payments in order to enable the gateway and fill its details.', 'unplugged_migs' ); ?>
    </p>
<?php
}
function unplugged_migs_field_icon_cb( $args ) {
    // get the value of the setting we've registered with register_setting()
    $options = get_option( 'unplugged_migs_options' );
    $icon = false;
    if(isset($options[$args['label_for']])) {
        $icon = $options[$args['label_for']];
    }
?>
    <div>
        <input 
            id="<?php echo esc_attr( $args['label_for'] ); ?>" 
            type="hidden" 
            name="unplugged_migs_options[<?php echo esc_attr( $args['label_for'] ); ?>]" 
            value="<?php echo esc_attr($icon); ?>" 
        />
        <?php 
        if($icon) { ?>
            <div  id="unplugged_migs_upload_icon_image" style="margin-bottom: 10px; width: 170px">
                <img style="width: 100%" src="<?php echo wp_get_attachment_image_url($icon, 'medium') ?>" />
            </div>
        <?php } ?>
        <input id="unplugged_migs_upload_icon_button" type="button" class="button-secondary" value="<?php _e('Insert Image', 'unplugged_migs') ?>" />
    </div>
    <p class="description">
        <?php esc_html_e( 'This icon will appear beside the payment option in the checkout. It could be the bank\'s logo.', 'unplugged_migs' ); ?>
    </p>
<?php
}
 
/**
 * options page
 */
function unplugged_migs_options_page() {
    // add an options page in the settings menu
    add_options_page(
        'MIGS WooCommerce',
        'MIGS WooCommerce Options',
        'manage_options',
        'unplugged_migs_options',
        'unplugged_migs_options_page_html'
    );
}
 
/**
 * register our unplugged_migs_options_page to the admin_menu action hook
 */
add_action( 'admin_menu', 'unplugged_migs_options_page' );
 
/**
 * options page:
 * callback functions
 */
function unplugged_migs_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
                // output security fields for the registered setting "unplugged_migs"
                settings_fields( 'unplugged_migs' );
                // output setting sections and their fields
                // (sections are registered for "unplugged_migs", each field is registered to a specific section)
                do_settings_sections( 'unplugged_migs' );
                // output save settings button
                submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
<?php
}