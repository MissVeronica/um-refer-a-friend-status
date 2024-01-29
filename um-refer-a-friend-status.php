<?php
/**
 * Plugin Name:     Ultimate Member - Refer a Friend Status
 * Description:     Extension to Ultimate Member for integration of the "Refer A Friend for WooCommerce by WPGens" plugin with the current Referral Status at an UM User Account page tab.
 * Version:         1.0.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.8.2
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;

class UM_Refer_a_Friend_Status {

    function __construct() {

        add_filter( 'um_account_content_hook_referral',  array( $this, 'um_account_content_hook_referral' ), 10, 1 );
        add_filter( 'um_account_page_default_tabs_hook', array( $this, 'my_custom_tab_referral' ), 100 );
    }

    public function my_custom_tab_referral( $tabs ) {

        if ( class_exists( 'Gens_RAF' ) && class_exists( 'Gens_RAF_Public' )) {
        
            $tabs[800]['referral']['icon']        = 'um-faicon-link';
            $tabs[800]['referral']['title']       = __( 'Refer a Friend Status', 'ultimate-member' );
            $tabs[800]['referral']['custom']      = true;
            $tabs[800]['referral']['show_button'] = false;
        }

        return $tabs;
    }

    public function um_account_content_hook_referral( $output ) {

        $user = wp_get_current_user();
        $referral_ID = get_user_meta( $user->ID, "gens_referral_id", true );

        $output .= '<div class="um-field">';

        if ( empty( $referral_ID )) {

           $output .= sprintf( __( 'No referral URL found for User ID %s', 'ultimate-member' ), $user->ID );

        } else {

            ob_start();

            $gens_raf_plugin        = new Gens_RAF();
            $gens_raf_plugin_public = new Gens_RAF_Public( $gens_raf_plugin->get_gens_raf(), $gens_raf_plugin->get_version() );

            $gens_raf_plugin_public->account_page_show_link();
            $gens_raf_plugin_public->account_page_show_coupons();

            $output .= str_replace( array( 'class="woocommerce-message"',
                                           '<h2>',
                                           '</h2>',
                                           '<td>',
                                        ),

                                    array( '',
                                           '</div><div class="um-field"><div class="um-account-heading">',
                                           '</div></div><div class="um-field">',
                                           '<td style="border-bottom:none">',
                                        ),

                                    ob_get_clean());
        }

        $output .= '</div>';

        return $output;
    }

}

new UM_Refer_a_Friend_Status();
