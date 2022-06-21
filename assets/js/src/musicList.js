import events from './Events.js';
import utils from './Utilities.js';
import favorites from './Favorites.js';

const musicList = (function() {

  const ID_LICENSE_DIALOG            = 'license-dialog',
        ID_MUSIC_LIST                = 'music-list',
        ID_MUSIC_LIST_TABLE          = 'music-list__table',
        CLASS_BTN_FAVORITE           = 'btn--favorite',
        CLASS_BTN_IS_FAVORITE        = 'btn--is-favorite',
        CLASS_BTN_LICENSE            = 'btn--license',
        CLASS_BTN_DOWNLOAD           = 'btn--download',
        CLASS_BTN_UPGRADE            = 'btn--upgrade',
        CLASS_BTN_PLAY_PAUSE         = 'music-list__btn-play-pause',
        CLASS_BTN_PLAY_PAUSE_LOADING = 'music-list__btn-play-pause--loading',
        CLASS_BTN_PLAY_PAUSE_PLAYING = 'music-list__btn-play-pause--playing';

  let $musicList,
      $musicListTable,
      $nonceFavoriteCreate,
      $nonceFavoriteDelete;

  function init() {
    if (!cacheDom()) { return; }
    bindEvents();
  }

  function cacheDom() {
    $musicList           = jQuery('#' + ID_MUSIC_LIST);
    $musicListTable      = $musicList.find('#' + ID_MUSIC_LIST_TABLE);
    $nonceFavoriteCreate = $musicList.attr('data-nonce-create-favorite');
    $nonceFavoriteDelete = $musicList.attr('data-nonce-delete-favorite');

    return ($musicListTable.length) ? true : false;
  }

  function bindEvents() {
    // Music List Table
    $musicListTable.on('click', '.' + CLASS_BTN_PLAY_PAUSE, handleClickPlayPause);
    $musicListTable.on('click', '.' + CLASS_BTN_FAVORITE, handleClickFavorite);
    $musicListTable.on('click', '.' + CLASS_BTN_LICENSE, handleClickLicense);
    $musicListTable.on('click', '.' + CLASS_BTN_DOWNLOAD, handleClickDownload);
    $musicListTable.on('click', '.' + CLASS_BTN_UPGRADE, handleClickUpgrade);

    events.on('songStateChange', handleSongStateChange, this);
    events.on('createFavoriteSuccess', handleCreateFavoriteSuccess, this);
    events.on('deleteFavoriteSuccess', handleDeleteFavoriteSuccess, this);
    events.on('closeLicenseDialog', handleCloseLicenseDialog, this);
    events.on('closeDownloadDialog', handleCloseDownloadDialog, this);
  }

  function handleClickPlayPause(e) {
    e.preventDefault();
    events.trigger('clickPlayPauseList', e);
  }

  function handleClickFavorite(e) {
    e.preventDefault();

    if (e.currentTarget.dataset.redirectUrl) {
      events.trigger('openConfirmDialog', e);
      return;
    }

    const id = e.currentTarget.dataset.songId;
    const isFavorite = Number(e.currentTarget.dataset.isFavorite);

    if (isFavorite) {
      favorites.deleteFavorite(id, $nonceFavoriteDelete);
    } else {
      favorites.createFavorite(id, $nonceFavoriteCreate);
    }
  }

  function handleClickLicense(e) {
    e.preventDefault();

    if (e.currentTarget.dataset.redirectUrl) {
      events.trigger('openConfirmDialog', e);
      return;
    }

    this.disabled = true;

    // get product data for license dialog
    const data = {
      'artist' : this.dataset.songArtist,
      'id'     : this.dataset.songId,
      'image'  : this.dataset.songImage,
      'title'  : this.dataset.songTitle,
      'url'    : this.dataset.songUrl
    };
    // set product data for license dialog
    events.trigger('clickLicense', data);

    // init Magnific Popup
    jQuery(this).magnificPopup({
      // alignTop: true,
      type: 'inline',
      mainClass: 'mfp-license-popup mfp-fade',
      closeOnBgClick: true,
      removalDelay: 300,
      closeMarkup: '<button title="%title%" type="button" class="mfp-close">&times;</button>',
      callbacks: {
        open: function() {
          events.trigger('mfpOpenLicenseDialog');
        },
        close: function() {
          events.trigger('mfpCloseLicenseDialog');
        }
      }
    }).magnificPopup('open');
  }

  /**
   * Handle Download Button Click
   * 
   * @param {Object} e event object
   * @returns 
   */
  function handleClickDownload(e) {
    e.preventDefault();
    
    if (e.currentTarget.dataset.redirectUrl) {
      events.trigger('openConfirmDialog', e);
      return;
    }

    this.disabled = true;

    // get product data for download dialog
    const data = {
      'artist'      : this.dataset.songArtist,
      'id'          : this.dataset.songId, // ID used for Simple Product
      'variationId' : this.dataset.songVariationId, // vID used for Variable Product
      'image'       : this.dataset.songImage,
      'title'       : this.dataset.songTitle,
      'url'         : this.dataset.songUrl,
      'key'         : this.dataset.songKey
    };
    // set product data for Download Dialog
    events.trigger('clickDownload', data);

    // init Magnific Popup
    jQuery(this).magnificPopup({
      // alignTop: true,
      type: 'inline',
      mainClass: 'mfp-download-popup mfp-fade',
      closeOnBgClick: true,
      removalDelay: 300,
      closeMarkup: '<button title="%title%" type="button" class="mfp-close">&times;</button>',
      callbacks: {
        open: function() {
          events.trigger('mfpOpenDownloadDialog');
        },
        close: function() {
          events.trigger('mfpCloseDownloadDialog');
        }
      }
    }).magnificPopup('open');
  }

  /**
   * Handle Upgrade Button Click
   * 
   * @param {Object} e event object
   * @returns 
   */
   function handleClickUpgrade(e) {
    e.preventDefault();
    
    if (e.currentTarget.dataset.redirectUrl) {
      events.trigger('openConfirmDialog', e);
      return;
    }
  }

  function handleSongStateChange(id, state) {
    doSongClasses(id, state);
  }

  function doSongClasses(id, { isLoading = false, isPlaying, isError }) {
    let $songItem = $musicListTable.find('button[data-song-id='+ id +'].music-list__btn-play-pause');

    if (! $songItem.length) {
      console.error('musicList no match found for Product ID: ', id);
      return;
    }

    if (isLoading) {
      doSongLoading($songItem);
      return;
    }

    if (isPlaying) {
      doSongPause($songItem);
      return;
    }

    if (!isPlaying) {
      doSongPlay($songItem);
      return;
    }

    if (isError) {
      doSongError($songItem);
      return;
    }
  }

  function doSongLoading($el) {
    $el
      .removeClass(CLASS_BTN_PLAY_PAUSE_PLAYING)
      .addClass(CLASS_BTN_PLAY_PAUSE_LOADING)
      .find('span')
      .text('Loading');
  }

  function doSongPlay($el) {
    $el
      .removeClass(CLASS_BTN_PLAY_PAUSE_LOADING)
      .removeClass(CLASS_BTN_PLAY_PAUSE_PLAYING)
      .find('span')
      .text('Play');
  }

  function doSongPause($el) {
    $el
      .removeClass(CLASS_BTN_PLAY_PAUSE_LOADING)
      .addClass(CLASS_BTN_PLAY_PAUSE_PLAYING)
      .find('span')
      .text('Pause');
  }

  function doSongError($el) {
    $el
      .removeClass(CLASS_BTN_PLAY_PAUSE_LOADING)
      .removeClass(CLASS_BTN_PLAY_PAUSE_PLAYING)
      .find('span')
      .text('Play');
  }

  function handleCreateFavoriteSuccess(id) {
    $musicListTable
      .find('button[data-song-id="'+ id +'"].' + CLASS_BTN_FAVORITE)
      .attr('data-is-favorite', 1)
      .addClass(CLASS_BTN_IS_FAVORITE);
  }

  function handleDeleteFavoriteSuccess(id) {
    $musicListTable
      .find('button[data-song-id="'+ id +'"].' + CLASS_BTN_FAVORITE)
      .attr('data-is-favorite', 0)
      .removeClass(CLASS_BTN_IS_FAVORITE);
  }

  function handleCloseLicenseDialog(id) {
    utils.closeMagnificPopup();

    let $btnLicense = $musicListTable.find('button[data-song-id='+ id +'].btn--license');

    if (! $btnLicense.length) {
      return;
    }

    $btnLicense.removeAttr('disabled');
  }

  function handleCloseDownloadDialog(id) {
    utils.closeMagnificPopup();

    let $btnDownload = $musicListTable.find('button[data-song-id='+ id +'].btn--download');

    if (! $btnDownload.length) {
      return;
    }

    $btnDownload.removeAttr('disabled');
  }

  return {
    init
  };

})();

export default musicList;
