<?php
/**
 * The template used for displaying page content in template-favorites.php
 *
 * @package storefront
 */

?>
<?php
$featured_image = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="<?php ?>" data-featured-image="<?php echo esc_url( $featured_image ); ?>">
    <?php
    /**
     * Functions hooked in to storefront_favorites
     *
     * @hooked storefront_page_content - 10
     */
    do_action( 'storefront_favorites' );
    ?>
</div><!-- #post-## -->
