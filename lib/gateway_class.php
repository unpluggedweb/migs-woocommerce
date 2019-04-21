<?php
class WC_Gateway_Unplugged_MIGS extends WC_Payment_Gateway {
    public function __construct($currency = 'egp', $icon = false){
        $this->currency = $currency;
        $this->id           = 'unplugged_migs_' . $currency; //Unique ID for your gateway
        $this->icon         =  $icon ? wp_get_attachment_image_url($icon, 'medium') : false; //If you want to show an image next to the gateway’s name on the frontend
        $this->has_fields   = false; //Can be set to true if you want payment fields to show on the checkout (if doing a direct integration)
        $this->method_title = sprintf( __('Credit Card (MIGS) %s', 'unplugged_migs'), strtoupper($currency) ); //Title of the payment method shown on the admin page
        $this->method_description = __('Pay with Credit Card using MIGS Gateway.', 'unplugged_migs'); //Description for the payment method shown on the admin page.
        $this->gateway_url = 'https://migs.mastercard.com.au/vpcpay';
        $this->notify_url = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) ) );

        // init_form_fields() basically defines your settings that are then loaded with init_settings()
        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );
        $this->merchant_id = $this->get_option( 'merchant_id' );
        $this->access_code = $this->get_option( 'access_code' );
        $this->hash_secret = $this->get_option( 'hash_secret' );
        //To have your options save, you simply have to hook in the process_admin_options function in your constructor. Payment gateways hook into the gateway save action
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
        add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'migs_response' ) );
    }

    // Defines Settings Fields for Gateway
    function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'unplugged_migs' ),
                'type' => 'checkbox',
                'label' => __( 'Enable MIGS', 'unplugged_migs' ),
                'default' => 'no'
            ),
            'title' => array(
                'title' => __( 'Title', 'unplugged_migs' ),
                'type' => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'unplugged_migs' ),
                'default' => sprintf( __('Credit Card (MIGS) %s', 'unplugged_migs'), strtoupper($this->currency) ),
                'desc_tip'      => true,
            ),
            'description' => array(
                'title' => __( 'Description', 'unplugged_migs' ),
                'description' => __( 'Payment method desctiption (will be shown in checkout).', 'unplugged_migs' ),
                'type' => 'textarea',
                'default' => ''
            ),
            'merchant_id' => array(
                'title' => __( 'Merchant ID', 'unplugged_migs' ),
                'type' => 'text',
                'description' => __( 'Provided By Your Bank', 'unplugged_migs' ),
                'default' => '',
                'desc_tip'      => true,
            ),
            'access_code' => array(
                'title' => __( 'Access code', 'unplugged_migs' ),
                'type' => 'text',
                'description' => __( 'Provided By Your Bank', 'unplugged_migs' ),
                'default' => '',
                'desc_tip'      => true,
            ),
            'hash_secret' => array(
                'title' => __( 'Hash Secret', 'unplugged_migs' ),
                'type' => 'text',
                'description' => __( 'Provided By Your Bank', 'unplugged_migs' ),
                'default' => '',
                'desc_tip'      => true,
            ),
        );
    }

    // Generates Settings Page for Gateway
    function admin_options() {
        ?>
        <h2><?php _e('Credit Card (MIGS)','unplugged_migs'); ?></h2>
        <table class="form-table">
        <?php $this->generate_settings_html(); ?>
        </table> 
        <?php
    }

    function receipt_page($order){
        echo $this->generate_migs_url($order);
    }

    function process_payment( $order_id ) {
        global $woocommerce;
        $order = new WC_Order( $order_id );
        
        return array(
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url( true )
        );
    }

    function migs_response(){
        global $woocommerce;
        $txnResponseCode = $_GET["vpc_TxnResponseCode"];
        $order_id = $_GET["vpc_MerchTxnRef"];
        $amount = $_GET["vpc_Amount"];
        $receiptNo = $_GET["vpc_ReceiptNo"];

        $message = 'Unexpected Error';
        $message_type = 'error';

        if($order_id) {
            $order = new WC_Order($order_id); //get order
            if($txnResponseCode === "0") {					
                $order->payment_complete(); //update status
                $order->add_order_note( __('MIGS Payment Completed', 'unplugged_migs') ); //add confirmation note					
                $woocommerce->cart->empty_cart(); //empty the cart

                $message = getResultDescription($txnResponseCode) . ' <b>Amount: </b>' . $amount/100 . ' EGP <b>Order ID:</b>' .  $order_id . ' <b>Receipt Number:</b>' . $receiptNo;
                $message_type = 'success';
                $redirect_url = $order->get_view_order_url();
            } else {
                $message = getResultDescription($txnResponseCode);
                $order->add_order_note( $message ); //add faliure response in an order note
                $message_type = 'error';
                $redirect_url = $order->get_checkout_payment_url();
            }
        }
        //display success message and redirect
        wc_add_notice( $message, $message_type );			
        wp_redirect( $redirect_url );
    }

    public function generate_migs_url($order_id){
        global $woocommerce;
        $order = new WC_Order($order_id);
        
        //MIGS steps as in example
        $migs_args = array(
            'vpc_Version' => '1',
            'vpc_Command' => 'pay',
            'vpc_AccessCode' =>  $this->access_code,
            'vpc_MerchTxnRef' => $order_id,
            'vpc_Merchant' =>  $this->merchant_id,
            'vpc_OrderInfo' => $order->billing_email,
            'vpc_Amount' => floatval($order->order_total)*100,
            'vpc_Locale' => 'en',
            'vpc_ReturnURL' => $this->notify_url
        );
        ksort ($migs_args);

        $vpcURL =  esc_url( $this->gateway_url );
        $conn = new VPCPaymentConnection();
        $secureSecret = $this->hash_secret;

        $conn->setSecureSecret($secureSecret);

        foreach($migs_args as $key => $value) {
            if (strlen($value) > 0) {
                $conn->addDigitalOrderField($key, $value);
            }
        }

        $secureHash = $conn->hashAllFields();
        $conn->addDigitalOrderField("vpc_SecureHash", $secureHash);
        $conn->addDigitalOrderField("vpc_SecureHashType", "SHA256");

        $vpcURL = $conn->getDigitalOrder($vpcURL);
        header("Location: " . $vpcURL);
        return;
    }

}