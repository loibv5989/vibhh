<?php
if (!defined('ABSPATH')) exit;

class Lock_Modified_Date {

    const META_KEY = 'lbv_lock_modified_date';

    public static function init() {
        $instance = new self();

        add_action( 'init', [ $instance, 'register_meta' ] );
        add_action( 'enqueue_block_editor_assets', [ $instance, 'enqueue_editor_assets' ] );
        add_action( 'rest_api_init', [ $instance, 'init_rest_api' ] );
        add_filter( 'wp_insert_post_data', [ $instance, 'update_modified_date' ], 999, 2 );
    }

    public function init_rest_api() {
        $post_types = get_post_types( [ 'public' => true ] );
        foreach ( $post_types as $post_type ) {
            add_filter( "rest_pre_insert_{$post_type}", [ $this, 'update_last_modified_parameter' ], 99, 2 );
        }
    }

    public function update_last_modified_parameter( $prepared_post, $request ) {
        $params = $request->get_params();
        if ( isset( $params['meta'][ self::META_KEY ] ) ) {
            $prepared_post->lock_modified_date = ! empty( $params['meta'][ self::META_KEY ] );
        }
        return $prepared_post;
    }

    public function update_modified_date( $data, $postarr ) {
        $post_id = ! empty( $postarr['ID'] ) ? $postarr['ID'] : 0;

        if ( ! $post_id ) {
            return $data;
        }

        if ( ! isset( $postarr['post_modified'], $postarr['post_modified_gmt'] ) ) {
            return $data;
        }

        $is_locked = $this->is_locked( $postarr, $post_id );

        if ( ! $is_locked ) {
            return $data;
        }

        $data['post_modified']     = $postarr['post_modified'];
        $data['post_modified_gmt'] = $postarr['post_modified_gmt'];

        return $data;
    }

    private function is_locked( $postarr, $post_id ) {
        if ( isset( $postarr['lock_modified_date'] ) ) {
            return (bool) $postarr['lock_modified_date'];
        }
        return (bool) get_post_meta( $post_id, self::META_KEY, true );
    }

    public function register_meta() {
        $post_types = get_post_types( [ 'public' => true ] );

        foreach ( $post_types as $post_type ) {
            register_post_meta( $post_type, self::META_KEY, [
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => 'boolean',
                'default'       => false,
                'auth_callback' => function () {
                    return current_user_can( 'edit_posts' );
                },
            ] );
        }
    }

    public function enqueue_editor_assets() {
        wp_register_script(
            'lock-modified-date-editor',
            '',
            [ 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n' ],
            null,
            true
        );

        wp_enqueue_script( 'lock-modified-date-editor' );

        wp_add_inline_script(
            'lock-modified-date-editor',
            'var LBV_LOCK_META_KEY = ' . wp_json_encode( self::META_KEY ) . ';',
            'before'
        );

        wp_add_inline_script( 'lock-modified-date-editor', $this->get_editor_script(), 'after' );
    }

    private function get_editor_script() {
        return <<<'JS'
( function( wp ) {
    var el              = wp.element.createElement;
    var ToggleControl   = wp.components.ToggleControl;
    var useSelect       = wp.data.useSelect;
    var useDispatch     = wp.data.useDispatch;
    var registerPlugin  = wp.plugins.registerPlugin;
    
    var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;

    var META_KEY = ( typeof LBV_LOCK_META_KEY !== 'undefined' ) ? LBV_LOCK_META_KEY : 'lbv_lock_modified_date';

    function LockModifiedDateToggle() {
        var meta = useSelect( function( select ) {
            return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
        } );

        var editPost = useDispatch( 'core/editor' ).editPost;

        var isLocked = !! meta[ META_KEY ];

        function onChange( value ) {
            var newMeta = {};
            newMeta[ META_KEY ] = value;
            editPost( { meta: newMeta } );
        }

        return el(
            PluginPostStatusInfo,
            null,
            el( ToggleControl, {
                label:    'Lock Modified Date',
                checked:  isLocked,
                onChange: onChange,
                __nextHasNoMarginBottom: true,
            } )
        );
    }

    registerPlugin( 'lock-modified-date', {
        render: LockModifiedDateToggle,
    } );

} )( window.wp );
JS;
    }
}

Lock_Modified_Date::init();