<?php
/*
 * Plugin name: Query Loop Variation for Listings CPT
 * Author: Misha Rudrastyh
 * Version: 1.0
 * Description: Creates a custom block variation
 * Author URI: https://rudrastyh.com
 * Plugin URI: https://rudrastyh.com/gutenberg/query-loop-block-variation.html
 */

// CPT
add_action( 'init', function() {

	$args = array(
		'labels'             => array(
			'name'             => 'Listings',
			'singular_name'    => 'Listing',
			'menu_name'        => 'Listings',
			'add_new'          => 'Add New',
			'add_new_item'     => 'Add New Listing',
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_rest'       => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'listing' ),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' ),
	);

	register_post_type( 'listing', $args );

} );


// include assets
add_action( 'enqueue_block_editor_assets', function() {

	$asset = include_once __DIR__ . '/build/index.asset.php';

	wp_enqueue_script(
		'listing-query-variation',
		plugins_url( '', __FILE__ ) . '/build/index.js',
		$asset[ 'dependencies' ],
		$asset[ 'version' ],
		true
	);

} );


// editor part
add_filter( 'rest_listing_query', function( $args, $request ) {

  $city = $request->get_param( 'metaCity' );
	// do nothing if there is no metaCity parameter in the query
	if( ! $city ) {
		return $args;
	}

	// our custom meta query for filtering
	$meta_query = array(
		'key' => 'city',
		'value' => $city,
	);

	if( ! empty( $args[ 'meta_query' ] ) ) {
		 $args[ 'meta_query' ] = array(
			 $args[ 'meta_query' ],
			 $meta_query,
		 );
	} else {
		$args[ 'meta_query' ] = array(
			$meta_query
		);
	}

  return $args;

}, 10, 2 );


// site front end part
add_filter( 'pre_render_block', 'rudr_pre_render_block', 10, 2 );

function rudr_pre_render_block( $pre_render, $block ) {

	if( isset( $block[ 'attrs' ][ 'namespace' ] ) && 'query-listing' === $block[ 'attrs' ][ 'namespace' ] ) {

		add_filter( 'query_loop_block_query_vars', function( $query ) use ( $block ) {

			if( $block[ 'attrs' ][ 'query' ][ 'metaCity' ] ) {

				$meta_query = array(
					'key' => 'city',
					'value' => $block[ 'attrs' ][ 'query' ][ 'metaCity' ],
				);

				if( ! empty( $query[ 'meta_query' ] ) ) {
					 $query[ 'meta_query' ] = array(
						 $args[ 'meta_query' ],
						 $meta_query,
					 );
				} else {
					$query[ 'meta_query' ] = array(
						$meta_query
					);
				}

			}

			return $query;

		} );

	}

	return $pre_render;

}
