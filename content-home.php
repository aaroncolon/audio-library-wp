<?php
/**
 * The template used for displaying page content in template-home.php
 *
 * @package storefront
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
    /**
     * Functions hooked in to storefront_home
     *
     * @hooked storefront_page_content - 10
     */
    do_action( 'storefront_home' );
    ?>
</div><!-- #post-## -->
