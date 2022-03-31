<?php
/**
 * WooCommerce Collections Template Functions.
 *
 * @package storefront
 */

function storefront_collections_content() {
  $data = storefront_get_collection_data();
  storefront_collections_share($data);
  storefront_collections_filters();
  storefront_collections_list($data);
}

function storefront_get_collection_data() {
  $data = null;

  if (have_rows('songs')):
    while (have_rows('songs')): the_row();
      $product_id = get_sub_field('ml_song_sub_repeater');
      $product = wc_get_product($product_id);
      $data[] = array(
        'product_id'      => $product_id,
        'title'           => $product->get_title(),
        'song_image'      => wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'full')[0],
        'song_url'        => get_field('preview_song_file', $product_id),
        'artist'          => $product->get_attribute('artist'),
        'length'          => $product->get_attribute('length'),
        'genre'           => $product->get_attribute('genre'),
        'inst'            => $product->get_attribute('inst'),
        'mood'            => $product->get_attribute('mood'),
        'tempo'           => $product->get_attribute('tempo'),
        'require_login'   => get_sub_field('require_login'),
        'class_variation' => (! is_user_logged_in() && $require_login === '1') ? 'lock' : 'play',
      );
    endwhile;
  endif;

  return $data;
}

/**
 * Share
 * Output the Share dialog
 *
 * @since   1.0.0
 *
 * @param   array $data The Collection data
 * @return  void
 */
function storefront_collections_share($data) {
  // button
  if (! is_user_logged_in() || ! current_user_can( 'manage_options' )) {
    return;
  }
  ?>
  <div id="music-list-share" class="music-list-share">
    <p><button id="btn-share-dialog" class="btn btn--share btn--share-dialog" data-mfp-src="#share-dialog">Share</button></p>

    <div id="share-dialog" class="share-dialog mfp-hide">
      <h3 class="title title--share-dialog">Share Via Email</h3>
      <h4>Sharing: "<?php the_title() ?>"</h4>

      <div class="share-dialog__status"></div>
      
      <form id="share-form" class="share-form" method="post">
        <div class="form-group">
					<label for="name" class="visuallyhidden"><?php _e('From Name', 'audio-library')?></label>
					<input type="text" id="name" class="form-control" name="name" value="" placeholder="From Name" required />
				</div>

				<div class="form-group">
					<label for="email" class="visuallyhidden"><?php _e('Recipient Email','audio-library')?></label>
					<input type="email" id="email" class="form-control" name="email" value="" placeholder="Email" required />
				</div>

				<div class="form-group">
					<label for="subject" class="visuallyhidden"><?php _e('Subject','audio-library')?></label>
					<input type="text" id="subject" class="form-control" value="" name="subject" placeholder="Subject" required/>
				</div>

				<div class="form-group">
					<label for="message" class="visuallyhidden"><?php _e('Message','audio-library')?></label>
					<textarea id="message" class="form-control" rows="5" name="message" value="" placeholder="Message" required></textarea>
				</div>

        <div class="form-group">
          <button type="submit" id="btn-send-email" class="btn btn--submit btn--send-email">Send</button>
        </div>

        <input id="url" type="hidden" name="url" value="<?php echo esc_attr(get_permalink()) ?>" />

        <?php wp_nonce_field('ml_share_email_nonce') ?>

        <input id="collection-data" type="hidden" name="collection-data" value="<?php echo esc_attr(json_encode($data)) ?>" />
      </form>
    </div>
  </div>
  <?php
}

/**
 * Collections Filters
 * Output the Audio Library filters
 *
 * @since   1.0.0
 *
 * @param   array $taxonomies The Taxonomies to display as filters
 * @return  void
 */
function storefront_collections_filters($taxonomies = array()) {
  $taxonomies = array('artist', 'duration', 'genre', 'instrument', 'mood', 'tempo');

  $attribute_array = array();
  $attribute_terms = array();
  $attribute_taxonomies = wc_get_attribute_taxonomies();

  // Get non-variation attributes (taxonomies)
  if ( ! empty( $attribute_taxonomies ) ) {
    foreach ( $attribute_taxonomies as $tax ) {
      if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) && in_array( $tax->attribute_name, $taxonomies ) ) {
        $attribute_array[ $tax->attribute_name ] = $tax->attribute_label;
      }
    }
  }

  // get terms
  if ( ! empty( $attribute_array ) ) {
    foreach ( $attribute_array as $k => $v ) {
      $attribute_terms[$k] = get_terms( array('taxonomy' => wc_attribute_taxonomy_name($k), 'hide_empty' => false) );
    }
  }

  if ( ! empty( $attribute_terms ) ) {
    echo '<form id="music-list-filters" class="music-list-filters">';
      foreach ( $attribute_terms as $key => $terms ) {
        echo '<div class="select-wrap music-list-filters__select-wrap">';
          $attr_label = wc_attribute_label(wc_attribute_taxonomy_name($key));
          echo '<label for="select-'. wc_attribute_taxonomy_name($key) .'" class="visuallyhidden">'. esc_html($attr_label) .'</label>';
          echo '<select id="select-'. wc_attribute_taxonomy_name($key) .'" name="'. esc_attr($key) .'" data-taxonomy-name="'. wc_attribute_taxonomy_name($key) .'">';
            // use key as label and first option
            echo '<option value="">'. esc_html( $attr_label ) .'...</option>';
            foreach ( $terms as $term ) {
              echo '<option value="'. esc_attr( $term->slug ) .'">'. esc_html( $term->name ) .'</option>';
            }
          echo '</select>';
        echo '</div>';
      }
      echo '<input type="submit" class="button music-list-filters__submit" value="Filter">';
    echo '</form>';
  }
}


/**
 * Collections List
 * Output the Collections list
 *
 * @since   1.0.0
 *
 * @param   array $data The Collection data
 * @return  void
 */
function storefront_collections_list($data) {
?>
  <div
    id="music-list"
    class="music-list"
    data-nonce-products="<?php echo esc_attr(ml_do_nonce('get_products')) ?>"
    data-nonce-favorites="<?php echo esc_attr(ml_do_nonce('get_favorites')) ?>"
    data-nonce-create-favorite="<?php echo esc_attr(ml_do_nonce('create_favorite')) ?>"
    data-nonce-delete-favorite="<?php echo esc_attr(ml_do_nonce('delete_favorite')) ?>">
    <div id="music-list__table">
      <div class="music-list__body">
        <?php
        if (!empty($data)):
          $favorites = ml_get_favorites();
          foreach ($data as $v):
            $product_id      = $v['product_id'];
            $title           = $v['title'];
            $song_image      = $v['song_image'];
            $song_url        = $v['song_url'];
            $artist          = $v['artist'];
            $length          = $v['length'];
            $genre           = $v['genre'];
            $inst            = $v['inst'];
            $mood            = $v['mood'];
            $tempo           = $v['tempo'];
            $require_login   = $v['require_login'];
            $class_variation = $v['class_variation'];
        ?>
            <div class="music-list__row" data-song-id="<?php echo esc_attr($product_id) ?>">
              <div class="music-list__cell music-list__cell--main">
                <div class="music-list__row-inner">
                  <div class="music-list__cell-inner music-list__cell-inner--play-pause">
                    <button
                      class="btn music-list__btn-play-pause"
                      data-song-id="<?php echo esc_attr($product_id) ?>"
                      data-song-url="<?php echo esc_attr($song_url) ?>"
                      data-song-title="<?php echo esc_attr($title) ?>"
                      data-song-artist="<?php echo esc_attr($artist) ?>"
                      data-song-image="<?php echo esc_attr($song_image) ?>"
                    >
                        <span class="visuallyhidden">Play</span>
                    </button>
                  </div>
                  <div class="music-list__cell-inner music-list__cell-inner--title-artist">
                    <div class="music-list__title">
                      <a href="#"><?php echo esc_html($title) ?></a>
                    </div>
                    <div class="music-list__artist">
                      <?php echo esc_html($artist) ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="music-list__cell music-list__cell--length">
                <p class="music-list__length"><?php echo esc_html($length) ?></p>
              </div>
              <div class="music-list__cell music-list__cell--genre">
                <p class="music-list__genre"><?php echo esc_html($genre) ?></p>
              </div>
              <div class="music-list__cell music-list__cell--mood">
                <?php $mood = implode(' | ', explode(', ', $mood)); ?>
                  <p class="mood music-list__mood"><?php echo esc_html($mood) ?></p>
              </div>
              <div class="music-list__cell music-list__cell--actions">
                <button
                  type="button"
                  <?php if ( ml_is_favorite($product_id, $favorites) ): ?>
                    data-is-favorite="1"
                    class="music-list__btn btn btn--favorite btn--is-favorite"
                  <?php else: ?>
                    data-is-favorite="0"
                    class="music-list__btn btn btn--favorite"
                  <?php endif; ?>
                  data-song-id="<?php echo esc_attr($product_id) ?>"
                  data-song-title="<?php echo esc_attr($title) ?>"
                  data-song-artist="<?php echo esc_attr($artist) ?>"
                  data-song-image="<?php echo esc_attr($song_image) ?>"
                  <?php if ( ! is_user_logged_in() ): ?>
                    data-redirect-url="<?php echo esc_attr( add_query_arg('redirect', rawurlencode(get_the_permalink()), wc_get_page_permalink('myaccount')) ) ?>"
                    data-dialog-title="<?php echo esc_attr( 'Add to Favorites' ) ?>"
                    data-dialog-description="<?php echo esc_attr( 'Sign in to add this song to your Favorites.' ) ?>"
                    data-mfp-src="#confirm-dialog"
                  <?php endif; ?>
                >
                  <span class="visuallyhidden">Favorite</span>
                </button>
                <button
                  type="button"
                  class="music-list__btn btn btn--license"
                  data-song-id="<?php echo esc_attr($product_id) ?>"
                  data-song-url="<?php echo esc_attr($song_url) ?>"
                  data-song-title="<?php echo esc_attr($title) ?>"
                  data-song-artist="<?php echo esc_attr($artist) ?>"
                  data-song-image="<?php echo esc_attr($song_image) ?>"
                  <?php if ( ! is_user_logged_in() ): ?>
                    data-redirect-url="<?php echo esc_attr( add_query_arg('redirect', rawurlencode(get_the_permalink()), wc_get_page_permalink('myaccount')) ) ?>"
                    data-dialog-title="<?php esc_attr_e( 'Purchase' ) ?>"
                    data-dialog-description="<?php esc_attr_e( 'Sign in or create an account to purchase this item.' ) ?>"
                    data-mfp-src="#confirm-dialog"
                  <?php else: ?>
                    data-mfp-src="#license-dialog"
                  <?php endif; ?>
                >
                  License
                </button>
              </div>
            </div>

          <?php endforeach; ?>

        <?php else: ?>

          <div class="music-list__error">
            No Songs Found.
          </div>

        <?php endif; ?>
      </div>
    </div>

    <div class="music-list__pagination">
      <button class="btn btn--load-more" style="display:none;">
        Load More
      </button>
    </div>
  </div>

  <div id="license-dialog" class="license-dialog mfp-hide" data-nonce-product="<?php echo esc_attr(ml_do_nonce('get_product')) ?>">
    <h3 class="title--license-dialog">License Details</h3>
    <div class="license-dialog__song-details clear">
      <div class="license-dialog__song-image">
        <a href="javascript:;" class="license-dialog__song-link">
          <img src="#" alt="" />
        </a>
      </div>
      <div class="license-dialog__song-text">
        <div class="license-dialog__song-title"></div>
        <div class="license-dialog__song-artist"></div>
      </div>
    </div>
    <div class="license-form-wrap">
      <form id="license-form" class="license-form" action="" method="post">
        <div id="product-variations" class="product-variations"></div>

        <div class="license-form__summary">
          <div id="product-variation-price" class="product-variation-price"></div>
          <button id="btn-add-to-cart" class="btn btn--add-to-cart" disabled data-nonce-add-to-cart-variation="<?php echo esc_attr(ml_do_nonce('add_to_cart_variation')) ?>">Add to Cart</button>
        </div>
      </form>
    </div>
  </div>

<?php
}

function storefront_collections_build_email($subject, $message, $url, $collectionData) {
  ob_start();
  $template = file_get_contents(get_stylesheet_directory_uri().'/inc/templates/email-template.php');

  $tableRows = '';
  foreach ($collectionData as $v):
    $tableRows .= "<tr><td style='padding: 10px;'>".$v['title']."</td><td style='padding: 10px;'>".$v['artist']."</td><td style='padding: 10px;'>".$v['genre']."</td></tr>";
  endforeach;

  echo sprintf($template, $subject, $message, $url, $tableRows);

  return ob_get_clean();
}
