<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Book_Reviews
 * @author    Chris Reynolds <hello@chrisreynolds.io>
 * @license   GPL-3.0
 * @link      http://chrisreynolds.io
 * @copyright 2014 Chris Reynolds
 */
?>
<?php
	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	require_once( 'setup-options.php' );
?>
<div class="wrap">
	<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Book Review options saved', 'book-review-library' ); ?></strong></p></div>
	<?php endif; ?>
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<div id="poststuff" class="metabox-holder">
		<div id="post-body" class="metabox-holder columns-1">
			<div id="post-body-content">
				<form method="post" action="options.php">
					<?php settings_fields( 'book_reviews_settings' ); ?>
					<?php book_reviews_do_options(); ?>
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'book-review-library' ); ?>" />
						<input type="hidden" name="book-review-settings-submit" value="Y" />
					</p>
				</form>
			</div><!-- closes post-body-content -->
		</div><!-- closes post-body -->
	</div><!-- closes poststuff -->
</div>