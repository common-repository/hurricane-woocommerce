<?php
/**
 * Hurricane WooCommerce Integration
 *
 * @package  HC_WooIntegration
 * @category Integration
 * @author   Lee Blue
 */

if ( ! class_exists( 'HC_WooIntegration' ) ) :

class HC_WooCommerceIntegration extends WC_Integration {

	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		global $woocommerce;


		// Describe the Hurricane integration
        $description = __( 'Connect your WooCommerce store to your Hurricane affiliate program.', 'hc-woo-integration' );
        $invitation_info = __( 'Do you need a Hurricane affiliate account?', 'hc-woo-integration' );
        $invitation_link = __( 'Get your free account here.', 'hc-woo-integration' );
        $invitation = '<br><strong>' . $invitation_info . '</strong> <a href="http://dashboard.hurricane.io/accounts/register" target="_blank">' . $invitation_link . '</a>';

		$this->id                 = 'hurricane-woocommerce';
		$this->method_title       = __( 'Hurricane', 'hc-woo-integration' );
		$this->method_description = $description . $invitation;

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();


		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou', array( $this, 'show_commission_snippet' ) );
        
	}

	/**
	 * Initialize integration settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'subdomain' => array(
				'title'             => __( 'Hurricane subdomain', 'hc-woo-integration' ),
				'type'              => 'text',
				'description'       => __( 'The subdomain is the part of the URL in bold http://<strong>SUBDOMAIN</strong>.hurricane.io', 'hc-woo-integration' ),
				'desc_tip'          => false,
				'default'           => ''
			),
		);
	}
  
    public function validate_subdomain_field( $key ) {
        // get the posted value
		$value = $_POST[ $this->plugin_id . $this->id . '_' . $key ];
		// make sure the subdomain doesn't look like a full URL
		if ( isset( $value ) && 0 < strlen( $value ) ) {
            $pattern = '/^[^-][0-9a-zA-Z-]+[^-]$/';
            if (!preg_match($pattern, $value)) {
    			$this->errors[] = __( '<strong>Invalid subdomain:</strong> Your Hurricane subdomain is the part of the URL in bold http://<strong>SUBDOMAIN</strong>.hurricane.io', 'hc-woo-integration' );
                return false;
            }
		}
		return $value;
    }
  
    public function display_errors() {
        // loop through each error and display it
    	foreach ( $this->errors as $key => $value ) {
    		?>
    		<div class="error">
    			<p><?php echo $value ?></p>
    		</div>
    		<?php
    	}
    }
    
    public function show_commission_snippet( $orderId ) {
        $subdomain = $this->get_option( 'subdomain' );
        $order = new WC_Order($orderId);

        if ($order && $subdomain) {
            $orderTotal = $order->get_total();
            $shipping = $order->get_total_shipping();
            $tax = $order->get_total_tax();
            $commissionTotal = $orderTotal - $shipping - $tax;

            // If the commission total > 0 show the commission pixel
            if ($commissionTotal > 0) {
                echo "<img src='https://$subdomain.hurricane.io/sales/$orderId?total=$commissionTotal' style='display: none;' />";
            }
            
        }
    }
}

endif;

