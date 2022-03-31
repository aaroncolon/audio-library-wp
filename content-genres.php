<?php
/**
 * The template used for displaying page content in template-genres.php
 *
 * @package storefront
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
    /**
     * Functions hooked in to storefront_genres
     *
     * @hooked storefront_genres_content - 10
     */
    do_action( 'storefront_genres' );
    ?>
</div><!-- #post-## -->
