<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * dgc_Wallet_Install Class
 */
class dgc_Wallet_Install {

    private static $db_updates = array(
        '1.0.0' => array(
            'dgc_wallet_update_100_db_column'
        ),
        '1.0.8' => array(
            'dgc_wallet_update_108_db_column'
        ),
        '1.1.0' => array(
            'dgc_wallet_update_110_db_column'
        ),
        '1.1.7' => array(
            'dgc_wallet_update_117_db_column'
        ),
        '1.4.0' => array(
            'dgc_wallet_update_140_db_column'
        )
    );
    
    public function __construct() {
        self::update();
    }

    /**
     * Plugin install
     * @return void
     */
    public static function install() {
        if ( ! is_blog_installed() ) {
            return;
        }
        self::create_tables();
        self::cteate_product_if_not_exist();
    }

    /**
     * plugins table creation
     * @global object $wpdb
     */
    private static function create_tables() {
        global $wpdb;
        $wpdb->hide_errors();
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( self::get_schema() );

        /**
         * dgc_API_create_table 
         */        
        global $wpdb;
		//$wpdb->prefix = get_option('prefix_field_option');
        //if  ( null !== get_option('prefix_field_option') ) {
		//	$wpdb->prefix = get_option('prefix_field_option');
		//} else {
		//	dgc_API_prefix();
        //}
        
		//dgc_API_prefix();
	
		$dgc_API_args = array(
			'data'		=> array(),
		);
		$dgc_API_arg = array(
			'name'			=> $wpdb->prefix . 'dgc_wallet_transactions',
			'properties'	=> array(
				array(
					'name'			=> 'transaction_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'blog_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'default'		=> 1,
				),
				array(
					'name'			=> 'publicKey',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'default'		=> 0,
				),
				array(
					'name'			=> 'type',
					'dataType'		=> 4,
				),
				array(
					'name'			=> 'amount',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'balance',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'currency',
					'dataType'		=> 4,
				),
				array(
					'name'			=> 'details',
					'dataType'		=> 4,
				),
				array(
					'name'			=> 'deleted',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'default'		=> 0,
				),
				array(
					'name'			=> 'date',
					'dataType'		=> 3,
					'numberExponent'=> 0,
				)
			)
		);
		$dgc_API_args['data'][] = $dgc_API_arg;
		$dgc_API_res = dgc_API_call('/createTables', 'POST', $dgc_API_args);
		return json_encode($dgc_API_res);

    }

    /**
     * Plugin table schema
     * @global object $wpdb
     * @return string
     */
    private static function get_schema() {
        global $wpdb;
        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }
        $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}dgc_wallet_transactions (
            transaction_id BIGINT UNSIGNED NOT NULL auto_increment,
            blog_id BIGINT UNSIGNED NOT NULL DEFAULT 1,
            user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            type varchar(200 ) NOT NULL,
            amount DECIMAL( 10,2 ) NOT NULL,
            balance DECIMAL( 10,2 ) NOT NULL,
            currency varchar(20 ) NOT NULL,
            details longtext NULL,
            deleted tinyint(1 ) NOT NULL DEFAULT 0,
            date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (transaction_id ),
            KEY user_id (user_id )
        ) $collate;
        CREATE TABLE {$wpdb->base_prefix}dgc_wallet_transaction_meta (
            meta_id BIGINT UNSIGNED NOT NULL auto_increment,
            transaction_id BIGINT UNSIGNED NOT NULL,
            meta_key varchar(255) default NULL,
            meta_value longtext NULL,
            PRIMARY KEY  (meta_id ),
            KEY transaction_id (transaction_id ),
            KEY meta_key (meta_key(32 ) )
        ) $collate;";
        return $tables;
    }
    /**
     * Create rechargeable product if not exist
     */
    public static function cteate_product_if_not_exist(){
        if ( !wc_get_product( get_option( '_dgc_wallet_recharge_product' ) ) ) {
            self::create_product();
        }
    }

    /**
     * create rechargeable product
     */
    private static function create_product() {
        $product_args = array(
            'post_title' => wc_clean( 'Wallet Topup' ),
            'post_status' => 'private',
            'post_type' => 'product',
            'post_excerpt' => '',
            'post_content' => stripslashes(html_entity_decode( 'Auto generated product for wallet recharge please do not delete or update.', ENT_QUOTES, 'UTF-8' ) ),
            'post_author' => 1
        );
        $product_id = wp_insert_post( $product_args );
        if ( ! is_wp_error( $product_id ) ) {
            $product = wc_get_product( $product_id );
            wp_set_object_terms( $product_id, 'simple', 'product_type' );
            update_post_meta( $product_id, '_stock_status', 'instock' );
            update_post_meta( $product_id, 'total_sales', '0' );
            update_post_meta( $product_id, '_downloadable', 'no' );
            update_post_meta( $product_id, '_virtual', 'yes' );
            update_post_meta( $product_id, '_regular_price', '' );
            update_post_meta( $product_id, '_sale_price', '' );
            update_post_meta( $product_id, '_purchase_note', '' );
            update_post_meta( $product_id, '_featured', 'no' );
            update_post_meta( $product_id, '_weight', '' );
            update_post_meta( $product_id, '_length', '' );
            update_post_meta( $product_id, '_width', '' );
            update_post_meta( $product_id, '_height', '' );
            update_post_meta( $product_id, '_sku', '' );
            update_post_meta( $product_id, '_product_attributes', array() );
            update_post_meta( $product_id, '_sale_price_dates_from', '' );
            update_post_meta( $product_id, '_sale_price_dates_to', '' );
            update_post_meta( $product_id, '_price', '' );
            update_post_meta( $product_id, '_sold_individually', 'yes' );
            update_post_meta( $product_id, '_manage_stock', 'no' );
            update_post_meta( $product_id, '_backorders', 'no' );
            update_post_meta( $product_id, '_stock', '' );
            if (version_compare(WC_VERSION, '3.0', '>=' ) ) {
                $product->set_reviews_allowed(false );
                $product->set_catalog_visibility( 'hidden' );
                $product->save();
            }

            update_option( '_dgc_wallet_recharge_product', $product_id );
        }
    }

    /**
     * Get list of DB update callbacks.
     *
     * @since  1.0.8
     * @return array
     */
    public static function get_db_update_callbacks() {
        return self::$db_updates;
    }
    
    /**
     * Update plugin
     */
    private static function update() {
        $current_db_version = get_option( 'dgc_wallet_db_version' );
        if (version_compare(DGC_WALLET_PLUGIN_VERSION, $current_db_version, '=' ) ){
            return;
        }
        foreach ( self::get_db_update_callbacks() as $version => $update_callbacks) {
            if (version_compare( $current_db_version, $version, '<' ) ) {
                foreach ( $update_callbacks as $update_callback) {
                    call_user_func( $update_callback);
                }
            }
        }
        self::update_db_version();
    }

    /**
     * Update DB version to current.
     *
     * @param string|null $version New WooCommerce DB version or null.
     */
    public static function update_db_version( $version = null) {
        delete_option( 'dgc_wallet_db_version' );
        add_option( 'dgc_wallet_db_version', is_null( $version) ? DGC_WALLET_PLUGIN_VERSION : $version );
    }

}

new dgc_Wallet_Install();