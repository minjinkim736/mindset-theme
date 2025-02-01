<?php


if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function mindset_blocks_mindset_blocks_block_init()
{
	register_block_type(__DIR__ . '/build/copyright');
	register_block_type(__DIR__ . '/build/company-address'); //folder name
	register_block_type(__DIR__ . '/build/company-email'); //folder name
	register_block_type(__DIR__ . '/build/service-posts', array('render_callback' => 'fwd_render_service_posts')); //folder name
}
add_action('init', 'mindset_blocks_mindset_blocks_block_init');

/**
 * Registers the custom fields for some blocks.
 *
 * @see https://developer.wordpress.org/reference/functions/register_post_meta/
 */
function mindset_register_custom_fields()
{
	register_post_meta(
		'page',
		'company_email',
		array(
			'type'         => 'string',
			'show_in_rest' => true,
			'single'       => true
		)
	);
	register_post_meta(
		'page',
		'company_address',
		array(
			'type'         => 'string',
			'show_in_rest' => true,
			'single'       => true
		)
	);
}
add_action('init', 'mindset_register_custom_fields');

// service posts
function fwd_render_service_posts($attributes) {
	ob_start();
?>
	<div <?php echo get_block_wrapper_attributes(); ?>>
		<?php
		// Add a WP_Query() custom loop here
		// See slides for details
		// query for service posts

		//copy of WP_Qeury() custom loop for navigation
		$args_nav = array(
			'post_type'      => 'service',
			'posts_per_page' => -1,
			'order'          => 'ASC',
		);

		$query_nav = new WP_Query($args_nav);

		if ($query_nav->have_posts()) {
			echo '<nav class="service-nav">';
			while ($query_nav->have_posts()) {
				$query_nav->the_post();
				echo '<a href="#post-' . get_the_ID() . '">' . get_the_title() . '</a>';
			}
			echo '</nav>';
			wp_reset_postdata();
		}

		$args = array(
			'post_type'      => 'service',
			'posts_per_page' => -1,
			'order'          => 'ASC',
		);

		$query = new WP_Query($args);
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();

				echo '<article id="post-' . get_the_ID() . '">';
				echo '<h2>' . get_the_title() . '</h2>'; // Title in h2
				the_content(); // Content from block editor
				echo '</article>';
			}
			wp_reset_postdata();
		}

		?>
	</div>
<?php
	return ob_get_clean();
}
