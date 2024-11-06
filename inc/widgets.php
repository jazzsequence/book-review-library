<?php
/**
 * All the widgets live here
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPL-3.0
 * @link      http://chrisreynolds.io
 * @copyright 2024 Chris Reynolds
 */

/**
 * Book Review Related Books Widget
 *
 * @since   1.0.0
 */
class Book_Review_Widget extends WP_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_options = [
			'classname' => 'book_review_widget',
			'description' => __( 'Displays a list of related books by common taxonomies. This widget only displays when viewing a single book review or a book review archive.', 'book-review-library' ),
		];

		$control_options = [ 'id_base' => 'book-review-widget' ];

		parent::__construct( 'book-review-widget', 'Similar Books', $widget_options, $control_options );
	}

	/**
	 * Similar Books Widget display
	 * 
	 * @param array $args The widget arguments
	 * @param array $instance The widget instance
	 */
	public function widget( $args, $instance ) {
		global $wp_query;

		$this_post = $wp_query->post->ID;

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		/**
		 * Count is the number of items to show.
		 * Image toggles whether to display thumbnails.
		 */
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		} else {
			$title = __( 'Similar Books', 'book-review-library' ); }
		if ( isset( $instance['count'] ) ) {
			$count = $instance['count'];
		} else {
			$count = 3; }
		if ( isset( $instance['image'] ) ) {
			$image = $instance['image'];
		} else {
			$image = false; }


		if ( is_singular( 'book-review' ) ) {
			$genres = wp_get_post_terms( $this_post, 'genre' );
			$subjects = wp_get_post_terms( $this_post, 'genre' );

			if ( $subjects || $genres ) {

				$subject_ids = [];
				foreach ( $subjects as $individual_subject ) {
					$subject_ids[] = $individual_subject->term_id;
				}

				$genre_ids = [];
				foreach ( $genres as $individual_genre ) {
					$genre_ids[] = $individual_genre->term_id;
				}

				$query_args = [
					'tax_query' => [ //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						[
							'taxonomy'  => 'genre',
							'terms'     => $genre_ids,
							'operator'  => 'IN',
						],
					],
					'post__not_in'          => [ $this_post ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
					'posts_per_page'        => $count,
					'ignore_sticky_posts'   => 1,
					'orderby'               => 'rand',
				];
				$related_query = new WP_Query( $query_args );
			}

			echo wp_kses_post( $args['before_widget'] );
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );

			if ( $related_query->have_posts() ) { ?>
				<div class="related">
					<ul>
						<?php
						while ( $related_query->have_posts() ) {
							$related_query->the_post();
							?>
							<li>
								<?php if ( $image && has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'tiny', [ 'class' => 'alignleft' ] ); ?>
								</a>
									<?php 
								endif;
								printf( 
									/* translators: 1: title, 2: author */
									wp_kses_post( __( '%1$s by %2$s', 'book-review-library' ) ), 
									'<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a><br />', 
									get_book_author() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								); 
							?>
							</li>
						<?php } ?>
					</ul>
				</div>
				<?php
			} else {
				esc_html_e( 'No similar books were found.', 'book-review-library' );
			}
			echo wp_kses_post( $args['after_widget'] );
		} elseif ( is_tax() ) {
			$taxonomy = get_query_var( 'taxonomy' );
			$term = get_query_var( 'term' );
			$term_id = get_term_by( 'slug', $term, $taxonomy )->term_id;

			$tax_args = [
				'tax_query' => [ //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					[
						'taxonomy' => $taxonomy,
						'terms' => [ $term_id ],
						'operator' => 'IN',
					],
				],
				'post__not_in' => [ $this_post ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				'post_per_page' => $count,
				'ignore_sticky_posts' => 1,
				'orderby' => 'rand',
			];

			$tax_query = new WP_Query( $tax_args );

			if ( $tax_query->have_posts() ) {
				echo wp_kses_post( $args['before_widget'] );
				echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
				?>
				<div class="related">
					<ul>
						<?php
						while ( $tax_query->have_posts() ) {
							$tax_query->the_post();
							?>
							<li>
								<?php if ( $image && has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'tiny', [ 'class' => 'alignleft' ] ); ?>
								</a>
								<?php endif; ?>
								<?php
								if ( get_book_author() ) {
									printf( 
										/* translators:  1: title, 2: author */
										wp_kses_post( __( '%1$s by %2$s', 'book-review-library' ) ), 
										'<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a><br />',
										get_book_author()  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									);
								} else {
									echo '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
								}
								?>
							</li>
						<?php } ?>
					</ul>
				</div>
				<?php
				echo wp_kses_post( $args['after_widget'] );
			}
		}
	}

	/**
	 * Similar Books Widget form
	 * 
	 * @param array $instance The instance
	 */
	public function form( $instance ) {
		$defaults = [
			'title' => __( 'Similar Books', 'book-review-library' ),
			'count' => 3,
			'image' => false,
		];
		$instance = wp_parse_args( (array) $instance, $defaults );

		$values = [
			[
				'id' => false,
				'text' => __( 'No', 'book-review-library' ),
			],
			[
				'id' => true,
				'text' => __( 'Yes', 'book-review-library' ),
			],
		];

		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		} else {
			$title = $defaults['title']; }
		if ( isset( $instance['count'] ) ) {
			$count = $instance['count'];
		} else {
			$count = $defaults['count']; }
		if ( isset( $instance['image'] ) ) {
			$image = $instance['image'];
		} else {
			$image = $defaults['image']; }
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'book-review-library' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			<span class="description"><?php esc_html_e( 'The title that displays above the widget.', 'book-review-library' ); ?></span>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>"><?php esc_html_e( 'Count:', 'book-review-library' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" size="3" /><br />
			<span class="description"><?php esc_html_e( 'How many reviews to display.', 'book-review-library' ); ?></span>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>"><?php esc_html_e( 'Display Images:', 'book-review-library' ); ?></label>
			<?php echo book_review_select_box( $this->get_field_name( 'image' ), $values, $image ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><br />
			<span class="description"><?php esc_html_e( 'Display thumbnail images next to the titles?', 'book-review-library' ); ?></span>
		</p>
		<?php
	}

	/**
	 * Update the widget settings.
	 * 
	 * @param array $new_instance The new settings
	 * @param array $old_instance The old settings
	 * 
	 * @return array The updated settings
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? wp_strip_all_tags( $new_instance['count'] ) : '';
		$instance['image'] = ( ! empty( $new_instance['image'] ) ) ? wp_strip_all_tags( $new_instance['image'] ) : '';

		return $instance;
	}
}

/**
 * Book Review Recent Books Widget
 *
 * @since   1.0.0
 */
class Book_Review_Recent_Widget extends WP_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_options = [
			'classname' => 'recent_book_review_widget',
			'description' => __( 'Displays a list of recent book reviews.', 'book-review-library' ),
		];

		$control_options = [ 'id_base' => 'recent-book-review-widget' ];

		parent::__construct( 'recent-book-review-widget', __( 'Recent Book Reviews', 'book-review-library' ), $widget_options, $control_options );
	}

	/**
	 * Widget display
	 * 
	 * @param array $args The widget arguments
	 * @param array $instance The widget instance
	 */
	public function widget( $args, $instance ) {
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		/**
		 * Count is the number of items to show.
		 * Image toggles whether to display thumbnails.
		 */
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		} else {
			$title = __( 'Recent Book Reviews', 'book-review-library' ); }
		if ( isset( $instance['count'] ) ) {
			$count = $instance['count'];
		} else {
			$count = 3; }
		if ( isset( $instance['image'] ) ) {
			$image = $instance['image'];
		} else {
			$image = false; }

				$query_args = [
					'post_type' => 'book-review',
					'posts_per_page'        => $count,
					'ignore_sticky_posts'   => 1,
				];
				$recent_query = new WP_Query( $query_args );

				echo wp_kses_post( $args['before_widget'] );
				echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );

				if ( $recent_query->have_posts() ) {
					?>
				<div class="related">
					<ul>
							<?php
							while ( $recent_query->have_posts() ) {
								$recent_query->the_post();
								?>
							<li>
								<?php if ( $image && has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'tiny', [ 'class' => 'alignleft' ] ); ?>
								</a>
								<?php endif; ?>
								<?php
								if ( get_book_author() ) {
									printf( 
										/* translators: 1: title, 2: author */
										wp_kses_post( __( '%1$s by %2$s', 'book-review-library' ) ), 
										'<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a><br />', 
										get_book_author() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									);
								} else {
									echo '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
								}
								?>
							</li>
							<?php } ?>
					</ul>
				</div>
					<?php
				} else {
					esc_html_e( 'No books were found.', 'book-review-library' );
				}
				echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Recent Book Reviews Widget form
	 * 
	 * @param array $instance The instance
	 */
	public function form( $instance ) {
		$defaults = [
			'title' => __( 'Recent Book Reviews', 'book-review-library' ),
			'count' => 3,
			'image' => false,
		];
		$instance = wp_parse_args( (array) $instance, $defaults );

		$values = [
			[
				'id' => false,
				'text' => __( 'No', 'book-review-library' ),
			],
			[
				'id' => true,
				'text' => __( 'Yes', 'book-review-library' ),
			],
		];

		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		} else {
			$title = $defaults['title']; }
		if ( isset( $instance['count'] ) ) {
			$count = $instance['count'];
		} else {
			$count = $defaults['count']; }
		if ( isset( $instance['image'] ) ) {
			$image = $instance['image'];
		} else {
			$image = $defaults['image']; }
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'book-review-library' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			<span class="description"><?php esc_html_e( 'The title that displays above the widget.', 'book-review-library' ); ?></span>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>"><?php esc_html_e( 'Count:', 'book-review-library' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" size="3" /><br />
			<span class="description"><?php esc_html_e( 'How many reviews to display.', 'book-review-library' ); ?></span>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>"><?php esc_html_e( 'Display Images:', 'book-review-library' ); ?></label>
			<?php echo book_review_select_box( $this->get_field_name( 'image' ), $values, $image ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><br />
			<span class="description"><?php esc_html_e( 'Display thumbnail images next to the titles?', 'book-review-library' ); ?></span>
		</p>
		<?php
	}

	/**
	 * Update the widget settings.
	 * 
	 * @param array $new_instance The new settings
	 * @param array $old_instance The old settings
	 * 
	 * @return array The updated settings
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? wp_strip_all_tags( $new_instance['count'] ) : '';
		$instance['image'] = ( ! empty( $new_instance['image'] ) ) ? wp_strip_all_tags( $new_instance['image'] ) : '';

		return $instance;
	}
}
