<?php
function storefront_genres() {
  // @link https://www.advancedcustomfields.com/resources/adding-fields-taxonomy-term/
  $layout           = get_field('layout');
  $hide_genres      = get_field('hide_specific_genres');
  $show_hide_genres = get_field('show_hide_genres');

  if ($hide_genres) {
    $genres = get_terms(array(
      'include' => $show_hide_genres,
      'taxonomy' => 'pa_genre'
    ));
  } else {
    $genres = get_terms(array(
      'taxonomy' => 'pa_genre'
    ));
  }

  if ( empty($genres) ) { return; }

  $hide_title = get_field('hide_title');
  ?>

  <div class="genres">
    <div class="row">
      <?php
      $pageId      = get_page_id_by_template('template-audio-library.php');
      $pageUrlBase = get_permalink($pageId[0]);

      foreach ($genres as $genre) :
        $image    = get_field('image', 'term_' . $genre->term_taxonomy_id);
        $thumbUrl = $image['url'];
        $alt      = $image['alt'];
        $pageUrl  = add_query_arg( 'genre', $genre->slug, $pageUrlBase);
        $columnClasses = ($layout === 'single-column') ? 'col-xs-12' : 'col-xs-12 col-sm-4';
      ?>
        <div class="<?php echo esc_attr($columnClasses) ?>">
          <div class="genre">
            <div class="genre__thumbnail-wrap">
              <a href="<?php echo esc_url($pageUrl) ?>">
                <?php if ($thumbUrl) : ?>
                  <img loading="lazy" src="<?php echo esc_url($thumbUrl) ?>" alt="<?php esc_attr_e($alt) ?>" />
                <?php else: ?>
                  <?php echo $genre->name; ?>
                <?php endif; ?>
              </a>
            </div>
          </div>
        </div>
      <?php
      endforeach;
      ?>
    </div>
  </div>

  <?php
}
