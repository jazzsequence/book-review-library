<?php
/**
 * All the widgets live here
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPL-3.0
 * @link      http://chrisreynolds.io
 * @copyright 2014 Chris Reynolds
 */

/**
 * Book Review Related Books Widget
 *
 * @since 	1.0.0
 */
class Book_Review_Widget extends WP_Widget {
	public function __construct() {
		$widget_options = array( 'classname' => 'book_review_widget', 'description' => __('Displays a list of related books by common taxonomies. This widget only displays when viewing a single book review or a book review archive.', 'book-review-library') );

		$control_options = array( 'id_base' => 'book-review-widget' );

		parent::__construct( 'book-review-widget', 'Similar Books', $widget_options, $control_options );
	}

	public function widget( $args, $instance ) {
		global $wp_query;

		$this_post = $wp_query->post->ID;

		extract($args);
		// count is the number of items to show
		// image toggles whether to display thumbnails
		if ( isset( $instance['title'] ) ) { $title = apply_filters( 'widget_title', $instance['title'] ); } else { $title = __('Similar Books', 'book-review-library'); }
		if ( isset( $instance['count'] ) ) { $count = $instance['count']; } else { $count = 3; }
		if ( isset( $instance['image'] ) ) { $image = $instance['image']; } else { $image = false; }


		if (  is_singular( 'book-review' ) ) {
			$genres = wp_get_post_terms($this_post, 'genre');
			$subjects = wp_get_post_terms($this_post, 'genre');

			if ($subjects || $genres) {

				$subject_ids = array();
				foreach($subjects as $individual_subject) $subject_ids[] = $individual_subject->term_id;

				$genre_ids = array();
				foreach($genres as $individual_genre) $genre_ids[] = $individual_genre->term_id;

				$query_args=array(
					'tax_query' => array(
					array(
			            'taxonomy'  => 'genre',
			            'terms'     => $genre_ids,
			            'operator'  => 'IN'
						)
			        ),
			    	'post__not_in'          => array( $this_post ),
			    	'posts_per_page'        => $count,
			    	'ignore_sticky_posts'   => 1,
			    	'orderby' 				=> 'rand'
				);
				$related_query = new WP_Query( $query_args );
			}

			echo $args['before_widget'];
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

 			if( $related_query->have_posts() ) { ?>
				<div class="related">
					<ul>
						<?php while( $related_query->have_posts() ) {
						$related_query->the_post(); ?>
							<li>
								<?php if ( $image && has_post_thumbnail()) : ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'tiny', array('class' => 'alignleft') ); ?>
								</a>
								<?php endif; ?>
								<?php /* translators: 1: title, 2: author */ ?>
								<?php echo sprintf( __('%1$s by %2$s', 'book-review-library'), '<a href="' . get_permalink() . '">' . get_the_title() . '</a><br />', get_book_author() ); ?>
							</li>
						<?php } ?>
					</ul>
				</div>
			<?php
			} else {
				_e ('No similar books were found.', 'book-review-library');
			}
			echo $args['after_widget'];
		} elseif ( is_tax() ) {
			$taxonomy = get_query_var('taxonomy');
			$term = get_query_var('term');
			$term_id = get_term_by( 'slug', $term, $taxonomy )->term_id;

			$tax_args = array(
				'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'terms' => array($term_id),
					'operator' => 'IN'
				)),
				'post__not_in' => array( $this_post ),
				'post_per_page' => $count,
				'ignore_sticky_posts' => 1,
				'orderby' => 'rand'
			);

			$tax_query = new WP_Query($tax_args);

			if ( $tax_query->have_posts() ) {
			echo $args['before_widget'];
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; ?>
				<div class="related">
					<ul>
						<?php while( $tax_query->have_posts() ) {
						$tax_query->the_post(); ?>
							<li>
								<?php if ( $image && has_post_thumbnail()) : ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'tiny', array('class' => 'alignleft') ); ?>
								</a>
								<?php endif; ?>
								<?php if ( get_book_author() ) {
									/* translators:  1: title, 2: author */
									echo sprintf( __('%1$s by %2$s', 'book-review-library'), '<a href="' . get_permalink() . '">' . get_the_title() . '</a><br />', get_book_author() );
								} else {
									echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
								} ?>
							</li>
						<?php } ?>
					</ul>
				</div>
				<?php echo $args['after_widget'];
			}
		}
	}

	public function form( $instance ) {
		$defaults = array( 'title' => __('Similar Books', 'book-review-library'), 'count' => 3, 'image' => false );
		$instance = wp_parse_args((array) $instance, $defaults);

		$values = array(
				array('id' => false, 'text' => __('No', 'book-review-library')),
				array('id' => true, 'text' => __('Yes', 'book-review-library')));

		if ( isset( $instance['title'] ) ) { $title = apply_filters( 'widget_title', $instance['title'] ); } else { $title = $defaults['title']; }
		if ( isset( $instance['count'] ) ) { $count = $instance['count']; } else { $count = $defaults['count']; }
		if ( isset( $instance['image'] ) ) { $image = $instance['image']; } else { $image = $defaults['image']; }
		?>
		<p>
			<label for="<?php echo $this->get_field_name('title'); ?>"><?php _e( 'Title:', 'book-review-library' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			<span class="description"><?php _e('The title that displays above the widget.', 'book-review-library'); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('count'); ?>"><?php _e( 'Count:', 'book-review-library' ); ?></label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" size="3" /><br />
			<span class="description"><?php _e( 'How many reviews to display.','book-review-library'); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('image'); ?>"><?php _e( 'Display Images:', 'book-review-library' ); ?></label>
			<?php echo the_select_box($this->get_field_name('image'), $values, $instance['image']); ?><br />
			<span class="description"><?php _e('Display thumbnail images next to the titles?', 'book-review-library'); ?></span>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( !empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
		$instance['image'] = ( !empty( $new_instance['image'] ) ) ? strip_tags( $new_instance['image'] ) : '';

		return $instance;
	}
}

/**
 * Book Review Recent Books Widget
 *
 * @since 	1.0.0
 */
class Book_Review_Recent_Widget extends WP_Widget {
	public function __construct() {
		$widget_options = array( 'classname' => 'recent_book_review_widget', 'description' => __('Displays a list of recent book reviews.', 'book-review-library') );

		$control_options = array( 'id_base' => 'recent-book-review-widget' );

		parent::__construct( 'recent-book-review-widget', __('Recent Book Reviews', 'book-review-library'), $widget_options, $control_options );
	}

	public function widget( $args, $instance ) {
		extract($args);
		// count is the number of items to show
		// image toggles whether to display thumbnails
		if ( isset( $instance['title'] ) ) { $title = apply_filters( 'widget_title', $instance['title'] ); } else { $title = __('Recent Book Reviews', 'book-review-library'); }
		if ( isset( $instance['count'] ) ) { $count = $instance['count']; } else { $count = 3; }
		if ( isset( $instance['image'] ) ) { $image = $instance['image']; } else { $image = false; }

				$query_args=array(
					'post_type' => 'book-review',
			    	'posts_per_page'        => $count,
			    	'ignore_sticky_posts'   => 1,
				);
				$recent_query = new WP_Query( $query_args );

			echo $args['before_widget'];
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

 			if( $recent_query->have_posts() ) { ?>
				<div class="related">
					<ul>
						<?php while( $recent_query->have_posts() ) {
						$recent_query->the_post(); ?>
							<li>
								<?php if ( $image && has_post_thumbnail()) : ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'tiny', array('class' => 'alignleft') ); ?>
								</a>
								<?php endif; ?>
								<?php if ( get_book_author() ) {
									/* translators: 1: title, 2: author */
									echo sprintf( __('%1$s by %2$s', 'book-review-library'), '<a href="' . get_permalink() . '">' . get_the_title() . '</a><br />', get_book_author() );
								} else {
									echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
								} ?>
							</li>
						<?php } ?>
					</ul>
				</div>
			<?php
			} else {
				_e ('No books were found.', 'book-review-library');
			}
			echo $args['after_widget'];
	}

	public function form( $instance ) {
		$defaults = array( 'title' => __('Recent Book Reviews', 'book-review-library'), 'count' => 3, 'image' => false );
		$instance = wp_parse_args((array) $instance, $defaults);

		$values = array(
				array('id' => false, 'text' => __('No', 'book-review-library')),
				array('id' => true, 'text' => __('Yes', 'book-review-library')));

		if ( isset( $instance['title'] ) ) { $title = apply_filters( 'widget_title', $instance['title'] ); } else { $title = $defaults['title']; }
		if ( isset( $instance['count'] ) ) { $count = $instance['count']; } else { $count = $defaults['count']; }
		if ( isset( $instance['image'] ) ) { $image = $instance['image']; } else { $image = $defaults['image']; }
		?>
		<p>
			<label for="<?php echo $this->get_field_name('title'); ?>"><?php _e( 'Title:', 'book-review-library' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			<span class="description"><?php _e('The title that displays above the widget.', 'book-review-library'); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('count'); ?>"><?php _e( 'Count:', 'book-review-library' ); ?></label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" size="3" /><br />
			<span class="description"><?php _e( 'How many reviews to display.','book-review-library'); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('image'); ?>"><?php _e( 'Display Images:', 'book-review-library' ); ?></label>
			<?php echo the_select_box($this->get_field_name('image'), $values, $instance['image']); ?><br />
			<span class="description"><?php _e('Display thumbnail images next to the titles?', 'book-review-library'); ?></span>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( !empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
		$instance['image'] = ( !empty( $new_instance['image'] ) ) ? strip_tags( $new_instance['image'] ) : '';

		return $instance;
	}
}
