import events from './Events.js';

const popupPlayer = (function() {

  const CLASS_PROJECT_POSTS_WRAP = 'project-posts',
        CLASS_POPUP              = 'open-popup-player',
        CLASS_PRODUCT_LINK       = 'popup-player__product-link',
        CLASS_LOCK               = 'popup-player__product-link--lock',
        CLASS_LOADING            = 'popup-player__product-link--loading',
        CLASS_PLAY               = 'popup-player__product-link--play',
        CLASS_PLAYING            = 'popup-player__product-link--playing',
        CLASS_PLAYING_TR         = 'popup-player__table-tr--playing';

  let $projectsWrap,
      $popupPlayer;

  function init() {
    if (!cacheDom()) { return; }
    bindEvents();
    initMagnificPopup();
    initMatchHeight();
  }

  function cacheDom() {
    $projectsWrap = jQuery('.' + CLASS_PROJECT_POSTS_WRAP);
    $popupPlayer = jQuery('.popup-player'); // delegation

    return ($projectsWrap.length) ? true : false;
  }

  function bindEvents() {
    $popupPlayer.on('click', '.' + CLASS_PRODUCT_LINK, handleClickSongLink);

    events.on('songStateChange', handleSongStateChange, this);
  }

  function initMatchHeight() {
    jQuery('.matchHeight--byRow').matchHeight({
      byRow: true
    });
  }

  function initMagnificPopup() {
    $projectsWrap.magnificPopup({
      delegate: '.' + CLASS_POPUP,
      // alignTop: true,
      type: 'inline',
      mainClass: 'mfp-player-popup mfp-fade',
      closeOnBgClick: true,
      removalDelay: 300,
      closeMarkup: '<button title="%title%" type="button" class="mfp-close">&times;</button>'
    });
  }

  function handleClickSongLink(e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    if (itemIsLocked(e)) {
      handleSongLinkLock(e);
    } else {
      events.trigger('clickPlayPauseList', e);
    }
  }

  function handleSongLinkLock(e) {
    events.trigger('openConfirmDialog', e);
    return;
  }

  /**
   * Update popup player UI
   *
   * @param {Number} id the product id
   * @param {Boolean} isLoading true if song is loading
   * @param {Boolean} isPlaying true if song is playing
   * @param {Boolean} isError true if WaveSurfer threw an error loading the song
   */
  function handleSongStateChange(id, { isLoading = false, isPlaying, isError }) {
    let $songItem = $popupPlayer.find('a[data-song-id="'+ id +'"].'+ CLASS_PRODUCT_LINK);

    if (! $songItem.length) {
      return;
    }

    if (isError) {
      doSongError($songItem);
      return;
    }

    if (isLoading) {
      doSongLoad($songItem);
      return;
    }

    if (isPlaying) {
      doSongPlay($songItem);
      return;
    }

    if (! isPlaying) {
      doSongPause($songItem);
      return;
    }
  }

  function doSongLoad($songItem) {
    $songItem
      .removeClass(CLASS_PLAY)
      .addClass(CLASS_LOADING);
  }

  function doSongPlay($songItem) {
    $songItem
      .removeClass(CLASS_PLAY)
      .removeClass(CLASS_LOADING)
      .addClass(CLASS_PLAYING)
      .closest('tr')
      .addClass(CLASS_PLAYING_TR);
  }

  function doSongPause($songItem) {
    $songItem
      .removeClass(CLASS_PLAYING)
      .addClass(CLASS_PLAY)
      .closest('tr')
      .removeClass(CLASS_PLAYING_TR);
  }

  function doSongError($songItem) {
    console.log('doSongError');
    $songItem
      .removeClass(CLASS_PLAYING)
      .removeClass(CLASS_LOADING)
      .addClass(CLASS_PLAY)
      .closest('tr')
      .removeClass(CLASS_PLAYING_TR);
  }

  function itemIsLocked(e) {
    return (jQuery(e.target).hasClass(CLASS_LOCK)) ? true : false;
  }

  return {
    init
  };

})();

export default popupPlayer;
