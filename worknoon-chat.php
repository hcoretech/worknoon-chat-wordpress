<?php
/**
 * Plugin Name: Worknoon Real-Time Chat Engine
 * Description: Multi-role real-time eCommerce messaging hub with WooCommerce order & product context synchronization.
 * Version:     1.0.0
 * Author:      Worknoon Full-Stack Developer Operative
 * License:     GPLv2 or later
 * Text Domain: worknoon-chat
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Safety Guard: Block direct file execution pipelines
}

class Worknoon_Chat_Plugin {
    
    public function __construct() {
        // Core initialization hooks
        add_action( 'init', array( $this, 'register_chat_session_post_type' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_widget_assets' ) );
        add_shortcode( 'worknoon_chat_widget', array( $this, 'render_chat_shortcode_widget' ) );
        add_action( 'wp_footer', array( $this, 'inject_floating_widget_to_footer' ) );
    }

    /**
     * 🚀 REQUIREMENT: Custom Post Type "Chat Session"
     */
    public function register_chat_session_post_type() {
        $labels = array(
            'name'               => _x( 'Chat Sessions', 'post type general name', 'worknoon-chat' ),
            'singular_name'      => _x( 'Chat Session', 'post type singular name', 'worknoon-chat' ),
            'menu_name'          => _x( 'Worknoon Chats', 'admin menu', 'worknoon-chat' ),
            'add_new_item'       => __( 'Add New Chat Session', 'worknoon-chat' ),
            'edit_item'          => __( 'Edit Chat Session', 'worknoon-chat' ),
            'all_items'          => __( 'All Chat Sessions', 'worknoon-chat' ),
            'view_item'          => __( 'View Chat Session', 'worknoon-chat' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false, 
            'show_ui'            => true,  
            'show_in_menu'       => true,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_icon'          => 'dashicons-bubbles',
            'supports'           => array( 'title', 'editor', 'custom-fields' ),
            'show_in_rest'       => true, 
        );

        register_post_type( 'chat_session', $args );
    }

    /**
     * Enqueue layout scripts and external styles
     */
    public function enqueue_widget_assets() {
        // Enqueue Socket.io Client mirroring frontend implementation requirements
        wp_enqueue_script( 'socket-io', 'https://socket.io', array(), '4.7.5', true );
        
        // Inject Tailwind CDN framework dynamically into child template pages
        wp_enqueue_style( 'tailwind-cdn', 'https://jsdelivr.net', array(), '2.2.19' );
        
        // Inject core custom plugin logic engine script file layout
        wp_enqueue_script( 'worknoon-chat-core', plugin_dir_url( __FILE__ ) . 'assets/js/chat-core.js', array( 'jquery', 'socket-io' ), '1.0.0', true );

        // Localize dynamic site script parameters down to Javascript layers safely
        wp_localize_script( 'worknoon-chat-core', 'worknoonChatSettings', array(
            'rootUrl'     => esc_url_raw( rest_url( 'worknoon-chat/v1' ) ),
            'nonce'       => wp_create_nonce( 'wp_rest' ),
            'nodeServer'  => 'http://localhost:9000', 
            'currentUser' => wp_get_current_user()->display_name ? wp_get_current_user()->display_name : 'Guest User',
            'currentRole' => (!empty(wp_get_current_user()->roles)) ? wp_get_current_user()->roles[0] : 'customer',
            
            // ✅ SAFELY ENCAPSULATED: Resolves your WooCommerce helper constraints without crashes
            'currentProductId' => ( function_exists( 'is_product' ) && is_product() ) ? get_the_ID() : null,
            'currentOrderId'   => ( function_exists( 'is_view_order_page' ) && is_view_order_page() ) ? global_get_order_id_fallback() : null,
        ) );
    } // 🚀 FIXED: Enqueue function is now closed successfully here

    /**
     * 🚀 REQUIREMENT: Shortcode for Chat Widget Layout Rendering
     */
    public function render_chat_shortcode_widget() {
        if ( ! is_user_logged_in() ) {
            return '<div style="font-size:11px; font-weight:bold; color:#ef4444; padding:12px; background-color:#fef2f2; border:1px solid #fee2e2; border-radius:12px;">Please log into your account to initialize the chat workspace.</div>';
        }
        
        ob_start();
        include plugin_dir_path( __FILE__ ) . 'templates/chat-widget-template.php';
        return ob_get_clean();
    }

    /**
     * Automatically mount floating overlay tracking panels directly onto footer blocks globally
     */
    public function inject_floating_widget_to_footer() {
        if ( is_user_logged_in() ) {
            echo do_shortcode( '[worknoon_chat_widget]' );
        }
    }
}

// Helper boundary wrapper to safely resolve WooCommerce Order ID configurations on template pages
function global_get_order_id_fallback() {
    global $wp;
    return isset( $wp->query_vars['view-order'] ) ? intval( $wp->query_vars['view-order'] ) : null;
}

// Launch application plugin engine instance
new Worknoon_Chat_Plugin();

// 🚀 REQUIREMENT: REST API Routing Integration Handshakes file link
require_once plugin_dir_path( __FILE__ ) . 'includes/api-routes.php';
