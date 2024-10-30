<?php
/**
 * Plugin Name: WooCommerce Hurricane Integration
 * Plugin URI: http://hurricane.io/integrations/woocommerce
 * Description: Connect your WooCommerce store to your Hurricane affilaite account
 * Author: Reality66
 * Author URI: http://hurricane.io
 * Version: 1.1
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

if ( ! class_exists( 'HC_WooCommerce' ) ) :

class HC_WooCommerce {

	/**
	 * Construct the plugin.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {

		// Checks if WooCommerce is installed.
		if ( class_exists( 'WC_Integration' ) ) {
			// Include our integration class.
			include_once 'class-hc-woocommerce-integration.php';

			// Register the integration.
			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
            
            // Add settings link to plugin screen
            $plugin = plugin_basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin, array( $this, 'plugin_action_links' ) );
            
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_required') );
		}
	}
    

	/**
	 * Add a new integration to WooCommerce.
	 */
	public function add_integration( $integrations ) {
		$integrations[] = 'HC_WooCommerceIntegration';
		return $integrations;
	}
    
	/**
	 * Show action links on the plugin screen.
	 *
	 * @access	public
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public function plugin_action_links( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=hurricane-woocommerce' ) . '" title="' . 
            esc_attr( __( 'View Hurricane Settings', 'hc-woo-integration' ) ) . '">' . __( 'Settings', 'hc-woo-integration' ) . '</a>';
		$action_links = array( 'settings' => $settings_link);
		return array_merge( $action_links, $links );
	}
    

    public function woocommerce_required() {
    ?>
    <div class="error">
      <p><?php _e( '<strong>Woocommerce Hurricane Integration</strong> requires WooCommerce to be installed and activated', 'hc-woo-integration') ?></p>
    </div>
    <?php
    }

}

$hurricane = new HC_WooCommerce( __FILE__ );

endif;
