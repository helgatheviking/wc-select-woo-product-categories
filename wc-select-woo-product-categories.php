<?php
/**
 * Plugin Name: WooCommerce SelectWoo Product Categories
 * Plugin URI: http://github.com/helgatheviking/select-woo-product-categories
 * Description: Replace product categories metabox with SelectWoo
 * Donation URI: http://paypal.me/helgatheviking
 * Version: 1.0.0-beta-1
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 * Developer: Kathy Darling
 * Developer URI: http://kathyisawesome.com/
 *
 * Copyright: © 2020 Kathy Darling
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */



namespace SelectWooProductCat;

/**
 * Add hooks and filters
 */
function init() {


	// Load translation files.
	add_action( 'init', __NAMESPACE__ . '\load_plugin_textdomain' );

	// Remove old taxonomy meta box
	add_action( 'admin_menu', __NAMESPACE__ . '\remove_meta_box' );

	// Add new taxonomy meta box
	add_action( 'add_meta_boxes', __NAMESPACE__ . '\add_meta_box' );

}


/*-----------------------------------------------------------------------------------*/
/* Localization */
/*-----------------------------------------------------------------------------------*/


/**
 * Make the plugin translation ready
 *
 * @return void
 */
function load_plugin_textdomain() {
	\load_plugin_textdomain( 'select-woo-product-cat' , false , dirname( plugin_basename( __FILE__ ) ) .  '/languages/' );
}



/*-----------------------------------------------------------------------------------*/
/* Admin */
/*-----------------------------------------------------------------------------------*/



/**
* Remove the default metabox
*/
function remove_meta_box() {
	\remove_meta_box( 'product_catdiv', 'product', 'side' );
}

/**
* Add our new customized metabox
*/
function add_meta_box() {
	$taxonomy = get_taxonomy( 'product_cat' );
	if ( $taxonomy ) {
		$label        = sprintf( __( 'Select %s', 'select-woo-product-cat' ), $taxonomy->labels->name );
		$search_label = sprintf( __( 'Search for a %s&hellip;', 'select-woo-product-cat' ), $taxonomy->labels->singular_name );
		\add_meta_box( 'product_catwoo', $taxonomy->labels->singular_name, __NAMESPACE__ . '\metabox_callback', 'product', 'side', 'core', array( 'label' => $label, 'search_label' => $search_label ) );
	}
}


/**
* Display new customized metabox
*/
function metabox_callback( $post, $box ) { ?>

	<div id="taxonomy-product_cat" class="categorydiv">


		<p class="form-field">
			<label for="product_categories"><?php echo esc_html( $box['args']['label'] ); ?></label>
			<select id="product_categories" name="tax_input[product_cat][]" style="width: 90%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php echo esc_attr( $box['args']['search_label'] ); ?>" data-allow_clear="true">
				<?php
				$product_categories = get_the_terms( $post, 'product_cat' );
				$category_ids = $product_categories ? wp_list_pluck( $product_categories, 'term_id', null ) : array();
				$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

				if ( $categories ) {
					foreach ( $categories as $cat ) {
						echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
					}
				}
				?>
			</select>
		</p>


	</div>

<?php
}


/*-----------------------------------------------------------------------------------*/
/* Helpers */
/*-----------------------------------------------------------------------------------*/

/**
 * Plugin URL.
 *
 * @return string
 */
function get_plugin_url() {
	return plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename(__FILE__) );
}

/**
 * Plugin path.
 *
 * @return string
 */
function get_plugin_path() {
	return untrailingslashit( plugin_dir_path( __FILE__ ) );
}


/*-----------------------------------------------------------------------------------*/
/* Launch the whole plugin. */
/*-----------------------------------------------------------------------------------*/
add_action( 'woocommerce_loaded', __NAMESPACE__ . '\init' );
