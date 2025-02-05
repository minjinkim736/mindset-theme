<?php
// Ensure the block editor functions are available
if ( ! function_exists( 'get_block_wrapper_attributes' ) ) {
	require_once ABSPATH . WPINC . '/blocks.php';
}


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
	register_block_type(__DIR__ . '/build/testimonial-slider', array('render_callback' => 'fwd_render_testimonial_slider'));
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
function fwd_render_service_posts($attributes)
{
	ob_start();
?>
	<div <?php echo get_block_wrapper_attributes(); ?>>
		<?php
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


		//day07 custom loop for service posts
		$terms = get_terms(
			array(
				'taxonomy' => 'fwd-service-type',
			)
		);
		if ($terms && ! is_wp_error($terms)) {
			foreach ($terms as $term) {
				$args = array(
					'post_type'      => 'service',
					'posts_per_page' => -1,
					'order'          => 'ASC',
					'tax_query' => array(
						array(
							'taxonomy' => 'fwd-service-type',
							'field'    => 'slug',
							'terms'    => $term->slug,
						),
					),
				);

				$query = new WP_Query($args);
				if ($query->have_posts()) {
					echo "<section>";
					echo '<h2>' . esc_html($term->name) . '</h2>';
					while ($query->have_posts()) {
						$query->the_post();
						echo '<article id="post-' . get_the_ID() . '">';
						echo '<h3>' . get_the_title() . '</h3>';
						the_content();
						echo '</article>';
					}
					echo '</section>';
					wp_reset_postdata();
				}
			}
		}
		?>
	</div>
<?php
	return ob_get_clean();
}


// Callback function for the Testimonial Slider
function fwd_render_testimonial_slider( $attributes, $content ) {
    ob_start();
    $swiper_settings = array(
        'pagination' => $attributes['pagination'],
        'navigation' => $attributes['navigation']
    );

	// a PHP variable that stores a string which contains the CSS custom property name defined in edit.js and the arrowColor attribute as the value.
	$arrow_color = '--arrow-color: ' . $attributes['arrowColor'] . ';';
	$inactive_dot_color = '--inactive-dot-color: '	. $attributes['inactiveDotColor'] . ';';
    
	$style = $arrow_color .' '. $inactive_dot_color;
	?>
    <div <?php echo get_block_wrapper_attributes(array( 'style' => $style )); ?>>
        <script>
            const swiper_settings = <?php echo json_encode( $swiper_settings ); ?>;
        </script>
        <?php
        $args = array(
            'post_type'      => 'fwd-testimonial',
            'posts_per_page' => -1
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) : ?>
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <div class="swiper-slide">
                            <?php the_content(); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php if ( $attributes['pagination'] ) : ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
            <?php if ( $attributes['navigation'] ) : ?>
                <button class="swiper-button-prev"></button>
                <button class="swiper-button-next"></button>
            <?php endif; ?>
            <?php
            wp_reset_postdata();
        endif;
        ?>
    </div>
    <?php
    return ob_get_clean();
}