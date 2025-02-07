<?php

/**
 * A helper class with useful functions for getting product data.
 */
class Dicha_Skroutz_Feed_Data_Helper {

	private array $options;
	private string $feed_type;
	private int $default_qty_for_instock;
	private array $categories_to_exclude;
	private string $default_vat;
	private string $availability_meta_key;
	private string $weight_unit_for_export;

	/**
	 * Initializes the helper class.
	 *
	 * @param $options   array The plugin options (user settings).
	 * @param $feed_type string The feed type.
	 */
	public function __construct( array $options, string $feed_type ) {

		$this->options                 = $options;
		$this->feed_type               = $feed_type;
		$this->default_qty_for_instock = (int) apply_filters( 'dicha_skroutz_feed_default_qty_for_products_instock', 10, $this->feed_type );
		$this->categories_to_exclude   = apply_filters( 'dicha_skroutz_feed_excluded_cats_from_category_tree', [], $this->feed_type );
		$this->default_vat             = apply_filters( 'dicha_skroutz_feed_default_vat', '24', $this->feed_type );
		$this->availability_meta_key   = apply_filters( 'dicha_skroutz_feed_availability_meta_key', 'dicha_skroutz_feed_custom_availability', $this->feed_type );
		$this->weight_unit_for_export  = strtolower( trim( apply_filters( 'dicha_skroutz_feed_weight_unit_for_export', 'g', $this->feed_type ) ) );
		$this->weight_unit_for_export  = in_array( $this->weight_unit_for_export, [ 'g', 'kg' ] ) ? $this->weight_unit_for_export : 'g';
	}


	/**
	 * Checks if a product should be excluded from XML.
	 *
	 * @param $product WC_Product
	 *
	 * @return bool True to exclude.
	 */
	public function skroutz_exclude_product_from_xml( WC_Product $product ): bool {

		return (bool) apply_filters( 'dicha_skroutz_feed_exclude_product_from_xml', false, $product, $this->feed_type );
	}


	/**
	 * Checks if a variation should be excluded from XML.
	 *
	 * @param $variation WC_Product_Variation
	 * @param $parent_product WC_Product_Variable
	 *
	 * @return bool True to exclude.
	 */
	public function skroutz_exclude_variation_from_xml( $variation, WC_Product_Variable $parent_product ): bool {

		if ( ! $variation instanceof WC_Product_Variation ) return true;

		return (bool) apply_filters( 'dicha_skroutz_feed_exclude_variation_from_xml', false, $variation, $parent_product, $this->feed_type );
	}


	/**
	 * Getter for product name.
	 *
	 * @param $product   WC_Product
	 * @param $name_base string|NULL
	 *
	 * @return string
	 */
	public function skroutz_get_name( WC_Product $product, ?string $name_base = NULL ): string {

		// Find default name
		$name_base = is_string( $name_base ) ? $name_base : $product->get_name();

		/*
		 * Filter to short-circuit product name.
		 * This is useful for building an entirely new name from scratch.
		 * If you want to make minor changes to the final title, use the filter in the end of this function.
		 *
		 * If this filter is used and anything else than NULL is returned, then this value will be returned immediately.
		 * This saves time because the rest function code will not be executed at all.
		 */
		$pre_product_name = apply_filters( 'dicha_skroutz_feed_custom_product_name_pre', NULL, $product, $name_base, $this->feed_type );

		if ( NULL !== $pre_product_name ) {
			return $pre_product_name;
		}


		// Find attributes or custom taxonomies to add to product name
		$term_names_to_append        = [];
		$allowed_taxonomies_in_title = array_filter( array_map( 'sanitize_title', $this->options['title_attributes'] ) ); // checked ok with greek slugs
		$attributes                  = $product->get_attributes();

		if ( ! empty( $allowed_taxonomies_in_title ) ) {

			// Get allowed attributes that are not used for variations (Variation attributes are included in $name_base already)
			$attributes_to_add = array_filter(
				$attributes,
				function( $val, $key ) use( $allowed_taxonomies_in_title ) {
					return in_array( $key, $allowed_taxonomies_in_title ) && ! $val->get_variation();
				}, ARRAY_FILTER_USE_BOTH );

			// transform WC_Product_Attribute objects to WP_Term objects
			$terms_to_add = array_map( function( $wc_attribute ) { return $wc_attribute->get_terms(); }, $attributes_to_add );

			// Add support for native WooCommerce Brands
			if ( in_array( 'pa_woo__product_brand', $allowed_taxonomies_in_title ) ) {
				$terms_to_add = array_merge( $terms_to_add, [ 'woo__product_brand' => $this->get_wc_brands_native( $product ) ] );
			}
		}

		if ( ! empty( $terms_to_add ) ) {

			// Split name to meaningful words to detect attribute names.
			// Better than comparing whole string with strpos,
			// because some attrs have small names like "S" (Small size) and won't be added if letter S exists in some random word.
			// Search with strpos only when attribute name has more than one word.
			$name_base_parts = preg_split( '/[\s,()]+/', $name_base, -1, PREG_SPLIT_NO_EMPTY );

			foreach ( $terms_to_add as $term_objects ) {

				foreach ( $term_objects as $term ) {

					if ( count( explode( ' ', $term->name ) ) > 1 ) { // for multi-word names -> direct search in title
						if ( strpos( $name_base, $term->name ) === false ) {
							$term_names_to_append[] = $term->name;
						}
					}
					elseif ( ! in_array( $term->name, $name_base_parts ) ) {
						$term_names_to_append[] = $term->name;
					}
				}
			}
		}

		$product_name = ! empty( $term_names_to_append ) ? $name_base . ' ' . implode( ' ', $term_names_to_append ) : $name_base;

		return apply_filters( 'dicha_skroutz_feed_custom_product_name', $product_name, $product, $name_base, $allowed_taxonomies_in_title, $this->feed_type );
	}


	/**
	 * Getter for product link.
	 *
	 * @param      $product            WC_Product
	 * @param      $remove_size_params bool
	 *
	 * @return string
	 */
	public function skroutz_get_url( WC_Product $product, bool $remove_size_params = false ): string {

		$link = $product->get_permalink();

		if ( $remove_size_params && ! empty( $this->options['size'] ) ) {
			$size_params = array_map( function( $size_slug ) { return "attribute_$size_slug"; }, $this->options['size'] );
			$link        = remove_query_arg( $size_params, $link );
		}

		return apply_filters( 'dicha_skroutz_feed_custom_link', $link, $product, $this->feed_type );
	}


	/**
	 * Getter for product main image URL.
	 *
	 * @param $product WC_Product
	 *
	 * @return string|WP_Error
	 */
	public function skroutz_get_main_image_url( WC_Product $product ) {

		$main_image = wp_get_attachment_url( $product->get_image_id() );
		$main_image = apply_filters( 'dicha_skroutz_feed_custom_image', $main_image, $product, $this->feed_type );

		if ( ! $main_image ) {
			return new WP_Error( '40', 'Προϊόν χωρίς εικόνα.' );
		}

		return $main_image;
	}


	/**
	 * Getter for product gallery images URLs.
	 *
	 * @param $product WC_Product
	 *
	 * @return array
	 */
	public function skroutz_get_additional_images( WC_Product $product ): array {

		$additional_images = [];
		$gallery_images    = $product->get_gallery_image_ids();

		if ( sizeof( $gallery_images ) > 0 ) {
			foreach ( $gallery_images as $gallery_image_id ) {
				$additional_images[] = wp_get_attachment_url( $gallery_image_id );
			}
		}

		return apply_filters( 'dicha_skroutz_feed_custom_additional_images', $additional_images, $product, $this->feed_type );
	}


	/**
	 * Getter for product category tree.
	 *
	 * @param $product WC_Product
	 *
	 * @return string|WP_Error
	 */
	public function skroutz_get_category( WC_Product $product ) {

		/*
		 * Filter to short-circuit product category.
		 * This is useful for building an entirely new category tree from scratch.
		 * If you want to make minor changes to the final title, use the filter in the end of this function.
		 *
		 * If this filter is used and anything else than NULL is returned, then this value will be returned immediately.
		 * This saves time because the rest function code will not be executed at all.
		 */
		$pre_main_product_cat = apply_filters( 'dicha_skroutz_feed_custom_category_pre', NULL, $product, $this->categories_to_exclude, $this->feed_type );

		if ( NULL !== $pre_main_product_cat ) {
			return $pre_main_product_cat;
		}

		$categories_trees = [];

		$parent_id    = $product->get_parent_id();
		$id_to_search = $parent_id > 0 ? $parent_id : $product->get_id();

		$product_cats = wp_get_post_terms(
			$id_to_search,
			'product_cat',
			[
				'fields'       => 'ids',
				'orderby'      => 'none',
				'exclude_tree' => $this->categories_to_exclude
			]
		);

		$separator = ' > ';

		foreach ( $product_cats as $category_id ) {

			$category_parent_list = get_term_parents_list(
				$category_id,
				'product_cat',
				[
					'separator' => $separator,
					'link'      => false,
				]
			);

			if ( ! empty( $category_parent_list ) && is_string( $category_parent_list ) ) {
				$categories_trees[ $category_id ] = trim( $category_parent_list, $separator );
			}
		}

		$main_product_cat = '';
		$depth            = 0;

		if ( ! empty( $categories_trees ) ) {

			foreach ( $categories_trees as $cat_tree ) {

				$cur_depth = count( explode( $separator, $cat_tree ) );

				if ( $cur_depth > $depth ) {
					$main_product_cat = $cat_tree;
					$depth = $cur_depth;
				}
			}
		}

		if ( empty( $main_product_cat ) ) {
			return new WP_Error( '50', 'Προϊόν χωρίς κατηγορία.' );
		}

		return apply_filters( 'dicha_skroutz_feed_custom_category', $main_product_cat, $product, $this->categories_to_exclude, $this->feed_type );
	}


	/**
	 * Getter for product description.
	 *
	 * @param $product WC_Product
	 *
	 * @return string
	 */
	public function skroutz_get_description( WC_Product $product ): string {

		$description = $this->options['description'] === 'short' ? $product->get_short_description() : $product->get_description();
		$description = apply_filters( 'dicha_skroutz_feed_custom_description', $description, $product, $this->options['description'], $this->feed_type );

		return wp_filter_nohtml_kses( $description );
	}


	/**
	 * Getter for product EAN code.
	 *
	 * @param $product WC_Product
	 *
	 * @return string
	 */
	public function skroutz_get_ean( WC_Product $product ): string {

		$dicha_skroutz_feed_enable_ean_field = get_option( 'dicha_skroutz_feed_enable_ean_field' );

		if ( wc_string_to_bool( $dicha_skroutz_feed_enable_ean_field ) ) {
			$ean = $product->get_meta( 'dicha_skroutz_feed_ean_barcode' );
		}
		else {
			$ean = $product->get_meta( '_global_unique_id' );
		}

		return apply_filters( 'dicha_skroutz_feed_custom_ean', $ean, $product, $this->feed_type );
	}


	/**
	 * Getter for product MPN (SKU) code.
	 *
	 * @param $product WC_Product
	 * @param $context string Options: 'view' or 'not_inherit_from_parent'.
	 *
	 * @return string
	 */
	public function skroutz_get_mpn( WC_Product $product, string $context = 'view' ): string {

		$mpn = $product->get_sku( $context );

		return apply_filters( 'dicha_skroutz_feed_custom_mpn', $mpn, $product, $context, $this->feed_type );
	}


	/**
	 * Getter for product price.
	 *
	 * @param $product WC_Product
	 *
	 * @return string|WP_Error
	 */
	public function skroutz_get_price( WC_Product $product ) {

		$price_incl_tax = wc_get_price_including_tax( $product );

		$price = apply_filters( 'dicha_skroutz_feed_custom_price', $price_incl_tax, $product, $this->feed_type );

		if ( empty( $price ) ) {
			return new WP_Error( '30', 'Η τιμή προϊόντος πρέπει να είναι μεγαλύτερη του 0.' );
		}

		return wc_format_decimal( $price, 2, true );
	}


	/**
	 * Getter for product VAT.
	 *
	 * @param $product WC_Product
	 *
	 * @return float|string
	 */
	public function skroutz_get_vat( WC_Product $product ) {

		$vat = $this->default_vat;

		if ( wc_tax_enabled() ) {

			if ( $product->is_taxable() ) {
				$tax_rates = WC_Tax::get_rates( $product->get_tax_class() );

				if ( $tax_rates && is_array( $tax_rates ) ) {
					$vat = end( $tax_rates )['rate'];
				}
			}
			else {
				$vat = 0;
			}
		}

		return apply_filters( 'dicha_skroutz_feed_custom_vat', $vat, $product, $this->default_vat, $this->feed_type );
	}


	/**
	 * Getter for product manufacturer.
	 *
	 * @param $product WC_Product
	 *
	 * @return string
	 */
	public function skroutz_get_manufacturer( WC_Product $product ): string {

		$manufacturer = 'OEM';

		if ( empty( $this->options['manufacturer'] ) || ! is_array( $this->options['manufacturer'] ) ) return $manufacturer;

		foreach ( $this->options['manufacturer'] as $manufacturer_attribute ) {

			if ( 'pa_woo__product_brand' === $manufacturer_attribute ) {
				// Support for native WooCommerce Brands taxonomy
				$manufacturer_name = implode( ', ', wp_list_pluck( $this->get_wc_brands_native( $product ), 'name' ) );
			}
			else {
				// Get regular attribute value
				$manufacturer_name = $product->get_attribute( $manufacturer_attribute );
			}

			// keep only the value from the first non-empty attribute/custom taxonomy
			if ( ! empty( $manufacturer_name ) ) {
				$manufacturer = $manufacturer_name;
				break;
			}
		}

		return apply_filters( 'dicha_skroutz_feed_custom_manufacturer', $manufacturer, $product, $this->options['manufacturer'], $this->feed_type );
	}


	/**
	 * Retrieves the native WooCommerce product brands associated with a given product.
	 *
	 * @param WC_Product $product
	 *
	 * @return array A WP_Term[] array with product brand term objects or an empty array if no brands are found or an error occurs.
	 */
	private function get_wc_brands_native( WC_Product $product ): array {

		$wc_brands = get_the_terms( $product->get_id(), 'product_brand' );

		if ( empty( $wc_brands ) || is_wp_error( $wc_brands ) ) {
			return [];
		}

		return $wc_brands;
	}


	/**
	 * Getter for product weight.
	 *
	 * @param $product WC_Product
	 *
	 * @return float|int|string
	 */
	public function skroutz_get_weight( WC_Product $product ) {

		$weight = wc_get_weight( $product->get_weight(), $this->weight_unit_for_export );
		$weight = apply_filters( 'dicha_skroutz_feed_custom_weight', $weight, $product, $this->weight_unit_for_export, $this->feed_type );

		if ( $weight == 0 ) return '';

		return 'kg' === $this->weight_unit_for_export ? $weight . ' kg' : $weight;
	}


	/**
	 * Getter for product weight.
	 *
	 * @param $product WC_Product
	 *
	 * @return float|int|string
	 */
	public function skroutz_get_shipping( WC_Product $product ) {

		$shipping = '';

		if ( isset( $this->options['flat_rate'] ) && $this->options['flat_rate'] != '' ) {

			if ( ! empty( $this->options['flat_rate_free'] ) && floatval( $product->get_price() ) >= floatval( $this->options['flat_rate_free'] ) ) {
				$shipping = 0;
			}
			else {
				$shipping = wc_format_decimal( (float) $this->options['flat_rate'], 2, true );
			}
		}

		return apply_filters( 'dicha_skroutz_feed_custom_shipping', $shipping, $product, $this->options['flat_rate'], $this->options['flat_rate_free'], $this->feed_type );
	}


	/**
	 * Getter for product quantity.
	 *
	 * @param $product WC_Product
	 *
	 * @return int|WP_Error
	 */
	public function skroutz_get_quantity( WC_Product $product ) {

		$product_type = $product->get_type();

		if ( 'simple' === $product_type || 'variation' === $product_type ) {

			if ( $product->is_on_backorder( 1 ) ) {

				// skip if backorders should be excluded (from settings)
				// this really only applies to variations, because simple products are already excluded from the initial products query
				if ( ! wc_string_to_bool( $this->options['include_backorders'] ) ) {
					return new WP_Error( '10-2', 'Τα προϊόντα σε λίστα αναμονής (backorder) εξαιρούνται από το XML βάσει ρυθμίσεων' );
				}

				$quantity = apply_filters( 'dicha_skroutz_feed_default_qty_for_products_on_backorder', 5, $product, $this->feed_type );
			}
			elseif ( $product->managing_stock() ) {
				$quantity = max( $product->get_stock_quantity(), 0 );
			}
			else {
				$quantity = $product->is_in_stock() ? $this->default_qty_for_instock : 0;
			}
		}
		else {
			return new WP_Error( '90-1', 'Η μέθοδος `skroutz_get_quantity` δεν πρέπει να καλείται σε WC_Product_Variable objects' );
		}

		$quantity = (int) apply_filters( 'dicha_skroutz_feed_custom_quantity', $quantity, $product, $this->default_qty_for_instock, $this->feed_type );

		return min( $quantity, 10000000 );
	}


	/**
	 * Getter for product color.
	 *
	 * @param $product WC_Product
	 *
	 * @return string
	 */
	public function skroutz_get_color( WC_Product $product ): string {

		$color = '';

		if ( empty( $this->options['color'] ) || ! is_array( $this->options['color'] ) ) return $color;

		$product_type = $product->get_type();

		if ( 'simple' === $product_type || 'variable' === $product_type ) {

			foreach ( $this->options['color'] as $color_attribute ) {

				$color_names = $product->get_attribute( $color_attribute );

				if ( ! empty( $color_names ) ) {
					// if multiple color options exist, then keep only first for skroutz
					$color = explode( ',', $color_names, 2 )[0];
					break;
				}
			}
		}
		elseif ( 'variation' === $product_type ) {

			$variation_colors = [];

			// if a variation has multiple size attributes, keep all of them and separate with "/"
			foreach ( $this->options['color'] as $color_attribute ) {

				$color_value = $product->get_attribute( $color_attribute );

				/**
				 * Mod for greek (non-english) slugs.
				 *
				 * WC_Product_Variation::get_attribute() works differently from WC_Product::get_attribute()
				 * because it returns the option slug for attributes with greek slug, instead of name.
				 * So we need to fetch the term name for display ourselves.
				 */
				if ( ! taxonomy_exists( sanitize_title( $color_attribute ) ) && taxonomy_exists( $color_attribute ) ) {

					$term        = get_term_by( 'slug', $color_value, $color_attribute );
					$color_value = ! is_wp_error( $term ) && $term ? $term->name : $color_value;
				}

				if ( ! empty( $color_value ) ) {
					$variation_colors[] = $color_value;
				}
			}

			$color = implode( '/', $variation_colors );
		}

		return apply_filters( 'dicha_skroutz_feed_custom_color', $color, $product, $this->options['color'], $this->feed_type );
	}


	/**
	 * Getter for product size.
	 *
	 * @param $product WC_Product
	 *
	 * @return string
	 */
	public function skroutz_get_size( WC_Product $product ): string {

		$size = '';

		if ( empty( $this->options['size'] ) || ! is_array( $this->options['size'] ) ) return $size;

		$product_type = $product->get_type();

		if ( 'simple' === $product_type || 'variable' === $product_type ) {

			foreach ( $this->options['size'] as $size_attribute ) {

				$size_values = $product->get_attribute( $size_attribute );

				if ( ! empty( $size_values ) ) {
					// if multiple size options exist in simple products, then keep only first for skroutz
					$size = explode( ',', $size_values, 2 )[0];
					break;
				}
			}
		}
		elseif ( 'variation' === $product_type ) {

			$variation_sizes = [];

			// if a variation has multiple size attributes, keep all of them and separate with "/"
			foreach ( $this->options['size'] as $size_attribute ) {

				$size_value = $product->get_attribute( $size_attribute );

				/**
				 * Mod for greek (non-english) slugs.
				 *
				 * WC_Product_Variation::get_attribute() works differently from WC_Product::get_attribute()
				 * because it returns the option slug for attributes with greek slug, instead of name.
				 * So we need to fetch the term name for display ourselves.
				 */
				if ( ! taxonomy_exists( sanitize_title( $size_attribute ) ) && taxonomy_exists( $size_attribute ) ) {

					$term       = get_term_by( 'slug', $size_value, $size_attribute );
					$size_value = ! is_wp_error( $term ) && $term ? $term->name : $size_value;
				}

				if ( ! empty( $size_value ) ) {
					$variation_sizes[] = $size_value;
				}
			}

			$size = implode( '/', $variation_sizes );
		}

		return apply_filters( 'dicha_skroutz_feed_custom_size', $size, $product, $this->options['size'], $this->feed_type );
	}


	/**
	 * Getter for product availability.
	 *
	 * @param $product WC_Product
	 *
	 * @return string|WP_Error
	 */
	public function skroutz_get_availability( WC_Product $product ) {

		$availability_value = $product->get_meta( $this->availability_meta_key );

		// if empty, try to get parent product's value
		if ( empty( $availability_value ) ) {

			$parent_id = $product->get_parent_id();

			if ( $parent_id > 0 ) {
				$availability_value = get_post_meta( $parent_id, $this->availability_meta_key, true );
			}
		}

		// if still empty, get default value from settings
		if ( empty( $availability_value ) ) {
			$availability_value = $this->options['xml_availability'];
		}

		$availability_text = $this->skroutz_get_availability_text( $availability_value );

		if ( is_wp_error( $availability_text ) ) return $availability_text;

		return apply_filters( 'dicha_skroutz_feed_custom_availability', $availability_text, $product, $availability_value, $this->options['xml_availability'], $this->feed_type );
	}


	/**
	 * Get availability display text based on availability value.
	 *
	 * @param $availability_value string
	 *
	 * @return string|WP_Error
	 */
	public function skroutz_get_availability_text( string $availability_value ) {

		$availability_options = Dicha_Skroutz_Feed_Admin::skroutz_get_availability_options();
		$availability_text    = $availability_options[ $availability_value ] ?? '';

		if ( empty( $availability_text ) ) {
			return new WP_Error( '20', 'Μη έγκυρη τιμή διαθεσιμότητας' );
		}
		elseif( '5' === $availability_value ) {
			return new WP_Error( '60', 'Απόκρυψη από το XML λόγω ρύθμισης' );
		}

		return $availability_text;
	}

}