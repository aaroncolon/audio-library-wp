<?php
function storefront_projects() {
?>
<div class="project-posts">
  <div class="row">
    <?php
    $current_url = get_the_permalink();
    $args = array(
      'post_type'      => 'ml_projects',
      'posts_per_page' => -1,
      'order'          => 'ASC',
    );
    $projects = new WP_Query($args);

    if ($projects->have_posts()) :
      while ( $projects->have_posts() ) : $projects->the_post();
      ?>
        <?php
        // // Add New Row Every 5th Item
        // if ($projects->current_post == 0 || ($projects->current_post % 4) == 0) {
        //   echo '<div class="row">';
        // }
        ?>
          <div class="project-post project-post--<?php echo $projects->current_post; ?> col-xs-12 col-sm-4 matchHeight--byRow">
            <a href="javascript:;" class="open-popup-player" data-mfp-src="#popup-<?php esc_attr_e(get_the_ID()) ?>">
              <div class="project-post__thumbnail-wrap">
                <?php the_post_thumbnail(); ?>
              </div>
            </a>
            <div class="mfp-hide" id="popup-<?php esc_attr_e(get_the_ID()) ?>">
              <div class="popup-player">
                <div class="inner clearfix">

                  <div class="popup-player__image-wrap">
                    <div class="popup-player__image">
                      <?php the_post_thumbnail(); ?>
                    </div>
                  </div>

                  <div class="popup-player__song-list-wrap">
                    <div class="popup-player__table-wrap">
                      <table class="table popup-player__table has-background">
                        <thead class="popup-player__table-thead">
                          <tr>
                            <th class="popup-player__product-name-th">
                              <h3 class="popup-player__product-heading popup-player__product-heading--title">Title</h3>
                            </th>
                            <th class="popup-player__product-length-th">
                              <h3 class="popup-player__product-heading popup-player__product-heading--length">Length</h3>
                            </th>
                          </tr>
                        </thead>
                        <tbody class="popup-player__table-tbody">
                          <?php
                          // Get Product details from WooCommerce Product ID
                          $songs = (int) get_post_meta(get_the_ID(), 'songs', true);

                          if ($songs):
                            for ($i = 0; $i < $songs; $i++):
                              $product_id      = get_post_meta(get_the_ID(), 'songs_'.$i.'_ml_song_sub_repeater', true);
                              $product         = wc_get_product($product_id);
                              $product_title   = $product->get_title();
                              $song_url        = get_field('preview_song_file', $product_id);
                              $duration        = $product->get_attribute('duration');
                              $artists         = $product->get_attribute('artist');
                              $require_login   = get_post_meta(get_the_ID(), 'songs_'.$i.'_require_login', true);
                              $class_variation = (! is_user_logged_in() && $require_login === '1') ? 'lock' : 'play';
                          ?>
                              <tr class="popup-player__table-tr popup-player__table-tr--<?php echo $class_variation; ?>">
                                <td class="popup-player__product-name">
                                  <a
                                    class="popup-player__product-link popup-player__product-link--<?php echo $class_variation; ?>"
                                    href="javascript:;"
                                    <?php if (! is_user_logged_in() && $require_login === '1') : ?>
                                      title="Sign-In Required"
                                      data-redirect-url="<?php esc_attr_e( add_query_arg('redirect', rawurlencode($current_url), wc_get_page_permalink('myaccount')) ) ?>"
                                      data-dialog-title="<?php esc_attr_e( 'Purchase' ) ?>"
                                      data-dialog-description="<?php esc_attr_e( 'Sign in or create an account to listen to this item.' ) ?>"
                                      data-mfp-src="#confirm-dialog"
                                    <?php endif; ?>
                                    data-song-id="<?php esc_attr_e($product_id) ?>"
                                    data-song-url="<?php esc_attr_e($song_url) ?>"
                                    data-img="<?php echo get_the_post_thumbnail_url($product_id) ?>"
                                    data-song-artist="<?php esc_attr_e($artists) ?>"
                                    data-song-title="<?php esc_attr_e($product_title) ?>"
                                  >
                                    <?php esc_html_e(ucwords($product_title)); ?>
                                  </a>
                                </td>
                                <td class="popup-player__product-length">
                                  <?php esc_html_e($duration); ?>
                                </td>
                              </tr>

                            <?php endfor; ?>

                          <?php else: ?>

                            <tr class="popup-player__table-tr">
                              <td class="popup-player__product-name">
                                No Songs Found.
                              </td>
                              <td class="popup-player__product-length">
                                &nbsp;
                              </td>
                            </tr>

                          <?php endif; ?>

                        </tbody>
                      </table>
                    </div><!-- / .popup-player__table-wrap -->
                  </div><!-- / .popup-player__song-list-wrap -->

                </div><!-- / .inner -->
              </div><!-- / .popup-player -->
            </div><!-- / .mfp-hide -->
          </div><!-- / .project-post -->

        <?php
        // // Add Closing Row After Every 4rd Item or after the last item
        // if ( (($projects->current_post + 1) % 4 == 0) || ($projects->current_post + 1) == $projects->found_posts) {
        //   echo '</div><!-- / .row -->';
        // }
        ?>
      <?php
      endwhile;
      wp_reset_postdata();
    else :
      echo '<p>No Songs Found.</p>';
    endif;
    ?>
  </div><!-- / .row -->
</div><!-- / .project-posts -->
<?php
}
