<script type="text/template" id="tmpl-music-list-row">
  <div class="music-list__row" data-song-id="{{ data.id }}">
    <div class="music-list__cell music-list__cell--main">
      <div class="music-list__row-inner">
        <div class="music-list__cell-inner music-list__cell-inner--play-pause">
          <button
            <# if ( data.isCurrentSongPlaying ) { #>
              class="btn music-list__btn-play-pause music-list__btn-play-pause--playing"
            <# } else { #>
              class="btn music-list__btn-play-pause"
            <# } #>
            data-song-id="{{ data.id }}"
            data-song-url="{{{ data.preview_song_url }}}"
            data-song-title="{{ data.title }}"
            data-song-artist="{{ data.artist }}"
            data-song-image="{{{ data.song_image }}}"
          >
              <span class="visuallyhidden">Play</span>
          </button>
        </div>
        <div class="music-list__cell-inner music-list__cell-inner--title-artist">
          <div class="music-list__title">
            <a href="#">{{ data.title }}</a>
          </div>
          <div class="music-list__artist">
            {{ data.artist }}
          </div>
        </div>
      </div>
    </div>
    <div class="music-list__cell music-list__cell--length">
      <p class="music-list__length">{{ data.length }}</p>
    </div>
    <div class="music-list__cell music-list__cell--genre">
      <p class="music-list__genre">{{ data.genre }}</p>
    </div>
    <div class="music-list__cell music-list__cell--mood">
      <# if (Array.isArray(data.mood)) { #>
        <# var moods = data.mood.map(el => `<p class="mood music-list__mood">${el}</p>`); #>
        {{{ moods.join('') }}}
      <# } else { #>
        <p class="mood music-list__mood">{{ data.mood }}</p>
      <# } #>
    </div>
    <div class="music-list__cell music-list__cell--actions">
      <button
        type="button"
        <# if ( data.isFavorite ) { #>
          data-is-favorite="1"
          class="music-list__btn btn btn--favorite btn--is-favorite"
        <# } else { #>
          data-is-favorite="0"
          class="music-list__btn btn btn--favorite"
        <# } #>
        data-song-id="{{ data.id }}"
        data-song-title="{{ data.title }}"
        data-song-artist="{{ data.artist }}"
        data-song-image="{{{ data.song_image }}}"
        <# if ( ! data.isUserLoggedIn ) { #>
          data-redirect-url="<?php esc_attr_e( add_query_arg('redirect', rawurlencode(get_the_permalink()), wc_get_page_permalink('myaccount')) ) ?>"
          data-dialog-title="<?php esc_attr_e( 'Add to Favorites' ) ?>"
          data-dialog-description="<?php esc_attr_e( 'Sign in to add this song to your Favorites.' ) ?>"
          data-mfp-src="#confirm-dialog"
        <# } #>
      >
        <span class="visuallyhidden">Favorite</span>
      </button>
      <button
        type="button"
        class="music-list__btn btn btn--license"
        data-song-id="{{ data.id }}"
        data-song-title="{{ data.title }}"
        data-song-artist="{{ data.artist }}"
        data-song-image="{{{ data.song_image }}}"
        data-song-url="{{{ data.preview_song_url }}}"
        <# if ( ! data.isUserLoggedIn ) { #>
          data-redirect-url="<?php esc_attr_e( add_query_arg('redirect', rawurlencode(get_the_permalink()), wc_get_page_permalink('myaccount')) ) ?>"
          data-dialog-title="<?php esc_attr_e( 'Purchase' ) ?>"
          data-dialog-description="<?php esc_attr_e( 'Sign in or create an account to purchase this item.' ) ?>"
          data-mfp-src="#confirm-dialog"
        <# } else { #>
          data-mfp-src="#license-dialog"
        <# } #>
      >
        License
      </button>
    </div>
  </div>
</script>

<script type="text/template" id="tmpl-music-list-row-error">
  <div class="music-list__error">
    {{ data.message }}
  </div>
</script>
