<?php
/*
	Plugin Name: Ez Load More
	Description: Easily add ajax load more buttons to WordPress queries
	Author: Paul Huisman
	Version: 0.1
*/

// Helper functions
require_once dirname( __FILE__ ) . '/functions.php';

if ( !class_exists( 'EzLoadMore' ) ) :

/**
 * Load more button functionality  
 * Uses WP pagination
 */
class EzLoadMore {

	/**
	 * Add hooks
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_ez_load_more', array( $this, 'ez_load_more_posts' ) );
		add_action( 'wp_ajax_nopriv_ez_load_more', array( $this, 'ez_load_more_posts' ) );
	}

	/**
	 * Enqueue the necessary assets
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'ez-load-more-csss', plugins_url( '/', __FILE__ ) . '/css/ez-load-more.css');
		wp_enqueue_script( 'ez-load-more-js', plugins_url( '/', __FILE__ ) . '/js/ez-load-more.js', array( 'jquery' ), '0.1', true );
		wp_localize_script( 'ez-load-more-js', 'wp_ajax_url', admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Spit out the button html
	 * @param $text. (optional) Text to display on button
	 * @param $paged. (optional) WP query var
	 * @return void
	 */
	public function ez_load_more_button( $args, $paged ) {
		global $wp_query;

		// Lets recreate the current query within our ajax call
		wp_localize_script( 'ez-load-more-js', 'load_more_data', array( 'query' => $wp_query->query ) );

		echo '<div id="ez-load-more-area-wrapper"></div>';
		wp_nonce_field( 'ez-load-more-nonce-' . $args['context'], 'ez-load-more-nonce' );
		if(isset($args['custom_loader'])) {
			echo get_template_part('ajax-loader'); 
		}
		else {
			echo '<div class="lds-ring"><div></div><div></div><div></div><div></div></div>';
		}
		echo '<div id="ez-load-more-error" class="ez-load-more-error error" style="display:none;">' . esc_html__( 'Something has gone wrong. Please try again.', 'ez-load-more' ) . '</div>';
		
		$button_attributes = [
			'class'          => isset($args['button_class']) ? $args['button_class']: 'ez-load-more-button',
			'data-paged'     => intval($paged),
			'data-max-pages' => intval($wp_query->max_num_pages),
			'data-context'   => esc_attr__( $args['context'], 'ez-load-more'),
			'data-template'  => esc_attr__( $args['template'], 'ez-load-more'),
			'data-loader'    => isset($args['custom_loader']) ? $args['custom_loader']: false,
		];

		$button_string = '<button id="ez-load-more"';
		foreach($button_attributes as $button_attr => $button_value) {
			$button_string .= $button_attr . '="' . $button_value . '" ';
		}
		$button_string .= '>' . esc_html__( $args['label'], 'ez-load-more' );
		$button_string .= '</button>';
		
		echo $button_string;
	}

	/**
	 * Ajax handler for load more posts
	 */
	public function ez_load_more_posts() {
		if ( empty( $_POST['nonce'] ) || empty( $_POST['template'] ) || empty( $_POST['paged'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ez-load-more-nonce-'  . $_POST['context'] ) ) {
		   exit;
		} else {
			global $post; // required by setup post data
			$context = ( ! empty( $_POST['context'] ) ) ? sanitize_text_field( $_POST['context'] ) : 'default';
			$args = (array) $_POST['query'];
			$args['paged'] = sanitize_text_field( $_POST['paged'] );

			// A filter if you want to customize the query
			$args = apply_filters( 'ez-load-more-args-' . sanitize_text_field( $_POST['context'] ), $args );
			
			$query = new WP_Query( $args );
			$posts = $query->get_posts();
			
			foreach( $posts as $post ) {
				if ( class_exists( 'Timber' ) ) {
					$context = \Timber::get_context();
					$context['item'] = new \TimberPost($post->ID);
					
					\Timber::render($_POST['template'] . '.twig', $context);
				}
				else {
					setup_postdata( $post );
					get_template_part( 'content', $template );
				}
				wp_reset_postdata();
			}
		}
		exit;
	}
}
new EzLoadMore();

endif;

