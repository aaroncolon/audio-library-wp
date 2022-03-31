<?php
/**
 * Template part for displaying page content in page_rate.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package _s
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">
		<?php
		the_content();
		?>

		<div class="rater-wrap">
			<h1>Rate</h1>
			<div class="rater" style="display: none;">

				<div class="rater__points">
					Points Remaining: <span class="rater__points-amount">0</span>
				</div>

				<form id="form-rater" class="rater__form" method="post">
					<div class="form-group">
						<label for="mood">Mood</label>
						<input id="mood" name="mood" type="number" min="0" max="10" step="1" value="0" required />
					</div>
					<div class="form-group">
						<label for="focus">Focus</label>
						<input id="focus" name="focus" type="number" min="0" max="10" step="1" value="0" required />
					</div>
					<div class="form-group">
						<label for="flow">Flow</label>
						<input id="flow" name="flow" type="number" min="0" max="10" step="1" value="0" required />
					</div>
					<input id="product-id" type="hidden" value="<?php esc_attr_e(filter_input(INPUT_GET, 'product_id', FILTER_SANITIZE_NUMBER_FLOAT)) ?>" />
					<input id="nonce" type="hidden" value="<?php esc_attr_e(wp_create_nonce('nonce_anon_rate')) ?>" />
					<input id="submit" type="submit" value="Rate" disabled />
				</form>

			</div>

			<div class="rater-notification" style="display: none;">
				<p>Thank you for rating this product!</p>
			</div>
		</div>

	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
