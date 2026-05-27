<?php
/**
 * 📁 File: includes/api-routes.php
 * 🚀 REQUIREMENT: REST API for Cross-Server Integration Handshakes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', 'register_worknoon_chat_custom_endpoints' );

function register_worknoon_chat_custom_endpoints() {
    // POST Request maps to: wp-json/worknoon-chat/v1/sync-session
    register_rest_route( 'worknoon-chat/v1', '/sync-session', array(
        'methods'             => 'POST',
        'callback'            => 'worknoon_chat_handle_incoming_node_sync',
        'permission_callback' => 'worknoon_chat_api_token_permission_check',
    ) );
}

/**
 * Security Handshake Gate: Validates requests originating from your central server architecture
 */
function worknoon_chat_api_token_permission_check( $request ) {
    $auth_header = $request->get_header( 'Authorization' );
    // Production Security Token fallback boundary matching environmental configurations
    return ( $auth_header === 'Bearer worknoon_wordpress_secret_handshake_key' );
}

/**
 * CRUD Synchronization Handler: logs message sessions straight into WordPress custom meta fields
 */
function worknoon_chat_handle_incoming_node_sync( $request ) {
    $params = $request->get_json_params();
    
    $conversation_id = sanitize_text_field( $params['conversationId'] );
    $sender_name     = sanitize_text_field( $params['senderName'] );
    $message_text    = sanitize_textarea_field( $params['text'] );
    $product_context = isset( $params['productId'] ) ? intval( $params['productId'] ) : null;
    $order_context   = isset( $params['orderId'] ) ? intval( $params['orderId'] ) : null;

    // 1. Search if an existing CPT row records matched conversation_id
    $existing_posts = get_posts( array(
        'post_type'   => 'chat_session',
        'meta_key'    => '_worknoon_conv_id',
        'meta_value'  => $conversation_id,
        'post_status' => 'any',
    ) );

    if ( ! empty( $existing_posts ) ) {
        $post_id = $existing_posts[0]->ID;
        // Append incoming live strings down onto existing body text records
        $current_content = $existing_posts[0]->post_content;
        $updated_content = $current_content . "\n" . sprintf( '[%s]: %s', $sender_name, $message_text );
        
        wp_update_post( array(
            'ID'           => $post_id,
            'post_content' => $updated_content,
        ) );
    } else {
        // Generate new Custom Post Type document layout row if missing
        $post_id = wp_insert_post( array(
            'post_title'   => sanitize_text_field( 'Chat Session #' . substr($conversation_id, -6) ),
            'post_content' => sprintf( '[%s]: %s', $sender_name, $message_text ),
            'post_type'    => 'chat_session',
            'post_status'  => 'publish',
        ) );

        // Append indexing meta field anchors securely
        update_post_meta( $post_id, '_worknoon_conv_id', $conversation_id );
        
        // 🚀 BONUS: Product-Based and WooCommerce Order Synchronization Mappings Contexts
        if ( $product_context ) {
            update_post_meta( $post_id, '_woo_product_context_id', $product_context );
        }
        if ( $order_context ) {
            update_post_meta( $post_id, '_woo_order_context_id', $order_context );
        }
    }

    return new WP_REST_Response( array( 'success' => true, 'wp_post_id' => $post_id ), 200 );
}
