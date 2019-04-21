<?php

include('migs/VPCPaymentConnection.php');

/*Payment gateways should be created as additional plugins that hook into WooCommerce. Inside the plugin, you need to create a class after plugins are loaded */
add_action( 'plugins_loaded', 'unplugged_migs_gateway' );


/*It is also important that your gateway class extends the WooCommerce base gateway class, so you have access to important methods and the settings API */
function unplugged_migs_gateway() {
    include('gateway_class.php');
}


/*As well as defining your class, you need to also tell WooCommerce (WC) that it exists. Do this by filtering woocommerce_payment_gateways*/
function unplugged_migs_add_gateway( $methods ) {
    $options = get_option( 'unplugged_migs_options' );
    $currencies = array();
    $icon = false;
    if(isset($options['unplugged_migs_field_currency']) && is_array($options['unplugged_migs_field_currency'])) {
        $currencies = $options['unplugged_migs_field_currency'];
    }
    if(isset($options['unplugged_migs_field_icon']) && $options['unplugged_migs_field_icon']) {
        $icon = $options['unplugged_migs_field_icon'];
    }
    foreach ($currencies as $currency) {
        $methods[] = new WC_Gateway_Unplugged_MIGS(strtolower($currency), $icon); 
    }
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'unplugged_migs_add_gateway' );

/* Only display gateways of the current currency */
function unplugged_migs_payment_gateway_disable_currency( $available_gateways ) {
    global $woocommerce;
    $current_currency = get_woocommerce_currency();
    $options = get_option( 'unplugged_migs_options' );
    $currencies = array();
    if(isset($options['unplugged_migs_field_currency']) && is_array($options['unplugged_migs_field_currency'])) {
        $currencies = $options['unplugged_migs_field_currency'];
    }
    foreach($currencies as $currency) {
        if($current_currency !== $currency) {
            unset($available_gateways['unplugged_migs_' . strtolower($currency)]);
        }
    }
    return $available_gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'unplugged_migs_payment_gateway_disable_currency' );