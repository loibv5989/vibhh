<?php
/**
 * Cloudflare Cache Purge for WordPress
 * API Docs: https://developers.cloudflare.com/cache/how-to/purge-cache/
 * Updated: 2025
 */

function cloudflare_purge_urls( array $urls ): bool {
    $zone_id   = defined( 'CL_ZONE_ID'  ) ? CL_ZONE_ID  : '';
    $api_token = defined( 'CL_API_TOKEN') ? CL_API_TOKEN : '';

    if ( ! $zone_id || ! $api_token ) {
        error_log( '[Cloudflare Purge] Thiếu CL_ZONE_ID hoặc CL_API_TOKEN.' );
        return false;
    }

    if ( empty( $urls ) ) {
        return true;
    }

    $chunks = array_chunk( $urls, 30 );

    foreach ( $chunks as $chunk ) {
        $response = wp_remote_post(
            "https://api.cloudflare.com/client/v4/zones/{$zone_id}/purge_cache",
            [
                'method'  => 'POST',
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_token,  // ← Bearer token (mới)
                    'Content-Type'  => 'application/json',
                ],
                'body'    => wp_json_encode( [ 'files' => array_values( $chunk ) ] ),
                'timeout' => 15,
            ]
        );

        if ( is_wp_error( $response ) ) {
            error_log( '[Cloudflare Purge] WP Error: ' . $response->get_error_message() );
            return false;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $body['success'] ) ) {
            $errors = $body['errors'] ?? [];
            error_log( '[Cloudflare Purge] API Error: ' . wp_json_encode( $errors ) );
            return false;
        }
    }

    return true;
}

function purge_cloudflare_cache_on_update( int $post_ID, WP_Post $post, bool $update ): void {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_ID ) )                 return;
    if ( $post->post_status !== 'publish' )                return;
    if ( ! empty( $_REQUEST['meta-box-loader'] ) )         return;

    $urls = [];

    // 1. URL bài viết
    $post_url = get_permalink( $post_ID );
    if ( $post_url ) {
        $urls[] = $post_url;
    }

    // 2. Category archives
    $categories = get_the_category( $post_ID );
    foreach ( $categories as $cat ) {
        $cat_url = get_category_link( $cat->term_id );
        if ( $cat_url ) {
            $urls[] = $cat_url;
        }
    }

    // 3. Author archive
    $author_url = get_author_posts_url( $post->post_author );
    if ( $author_url ) {
        $urls[] = $author_url;
    }

    $urls = array_unique( $urls );
    cloudflare_purge_urls( $urls );
}
add_action( 'save_post', 'purge_cloudflare_cache_on_update', 10, 3 );
