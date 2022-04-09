<?php $default_image = get_field('ml_default_image', 'option'); ?>
<div id="player" class="player clear">
  <div class="player__left">
    <div class="player__song-image">
      <img loading="lazy" src="<?php echo esc_url($default_image['url']); ?>" alt="<?php echo esc_attr($default_image['alt']) ?>" />
    </div>
    <div class="player__controls">
      <button class="btn player__btn player__btn-play-pause"><span class="visuallyhidden">Play</span></button>
    </div>
    <div class="player__song-details">
      <div class="player__song-title">Song Title</div>
      <div class="player__song-artist">Song Artist</div>
    </div>
  </div>

  <div class="player__right">
    <div id="player__waveform" class="player__waveform"></div>
  </div>
</div>
