import events from './Events.js';
import utils from './Utilities.js';
import userDownloads from './UserDownloads.js';

const downloadDialog = (function() {

  const ID_BTN_DOWNLOAD           = 'btn-download',
        ID_CLOSE_DIALOG           = 'btn-close-download-dialog',
        ID_DOWNLOAD_DIALOG        = 'download-dialog',
        ID_DOWNLOAD_FILES         = 'download-files',
        CLASS_DOWNLOAD_FILES_LOAD = 'download-files--loading',
        CLASS_ARTIST              = 'download-dialog__song-artist',
        CLASS_IMAGE               = 'download-dialog__song-image',
        CLASS_LINK                = 'download-dialog__song-link',
        CLASS_TITLE               = 'download-dialog__song-title';

  let $body,
      $downloadDialog,
      $btnDownload,
      $nonceGetDownloadFiles,
      $nonceDownloadFile,
      $downloadFiles,
      $songArtist,
      $songImage,
      $songLink,
      $songTitle;
  
  let state = {
    productId: null
  };

  function init() {
    if (!cacheDom()) { return; }
    bindEvents();
  }

  function cacheDom() {
    $body                  = jQuery('body');
    $downloadDialog        = $body.find('#' + ID_DOWNLOAD_DIALOG);
    $btnDownload           = $downloadDialog.find('#' + ID_BTN_DOWNLOAD);
    $downloadFiles         = $downloadDialog.find('#' + ID_DOWNLOAD_FILES);
    $songArtist            = $downloadDialog.find('.' + CLASS_ARTIST);
    $songImage             = $downloadDialog.find('.' + CLASS_IMAGE + ' img');
    $songLink              = $downloadDialog.find('.' + CLASS_LINK);
    $songTitle             = $downloadDialog.find('.' + CLASS_TITLE);
    $nonceGetDownloadFiles = $downloadDialog.attr('data-nonce-get-download-files');
    $nonceDownloadFile     = $downloadDialog.attr('data-nonce-download-file');
    
    return ($downloadDialog.length) ? true : false;
  }

  function bindEvents() {
    $songLink.on('click', handleClickSongLink);

    events.on('clickDownload', handleDownloadClick, this);
    events.on('getDownloadFilesDone', handleGetDownloadFilesDone, this);
    events.on('mfpCloseDownloadDialog', handleCloseDialog, this);
  }

  function handleClickSongLink(e) {
    e.preventDefault();
    events.trigger('clickPlayPauseList', e);
  }

  function handleDownloadClick(data) {
    console.log('handleDownloadClick', data);

    // reset existing download dialog
    reset();

    // get download files
    $downloadFiles.toggleClass(CLASS_DOWNLOAD_FILES_LOAD);

    // use Product Variation ID if present
    if (data.variationId) {
      getDownloadFiles(data.variationId, data.id, data.key);
    } else {
      getDownloadFiles(data.id, data.id, data.key);
    }

    utils.setState({
      'productId': data.id 
    }, state);

    console.log('state', state);

    // render product details
    renderProductDetails(data);

    // show();
  }

  /**
   * Get Product Download URL
   * 
   * @param {String} product_id Product ID || Variation ID
   * @param {String} parent_id Product ID || Parent Product ID if using Variable Products
   * @param {String} key the download file's unique key
   */
  function getDownloadFiles(product_id, parent_id, key) {
    console.log('getDownloadFiles');

    jQuery.ajax({
      url      : ml_js_data.ajax_url,
      method   : 'POST',
      dataType : 'json',
      data     : {
        'action' : 'ml_get_download_files',
        'id'     : product_id,
        'pid'    : parent_id, // pid is used to determine PMPro Membership Access
        'key'    : key,
        'nonce'  : $nonceGetDownloadFiles
      }
    })
    .done(function(data, textStatus, jqXHR) {
      console.log('getDownloadFiles data', data);

      if (data.error) {
        console.log('error, redirecting');
      }

      events.trigger('getDownloadFilesDone', data);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.log('getDownloadFiles fail', errorThrown);
    });
  }

  function handleGetDownloadFilesDone(data) {
    console.log('handleGetDownloadFilesDone', data);

    $btnDownload.get(0).href = data.data.d_url;
    $btnDownload.toggleClass('disabled');
    $downloadFiles.toggleClass(CLASS_DOWNLOAD_FILES_LOAD);
  }

  function handleCloseDialog() {
    reset();
    events.trigger('closeDownloadDialog', state.productId);
  }

  /**
   * Render Product details
   * @param {object} data the Product data
   */
  function renderProductDetails(data) {
    $songArtist.text(data.artist);
    $songImage.attr('src', data.image);
    $songImage.attr('alt', data.title);
    $songLink.attr('data-song-artist', data.artist);
    $songLink.attr('data-song-id', data.id);
    $songLink.attr('data-song-image', data.image);
    $songLink.attr('data-song-title', data.title);
    $songLink.attr('data-song-url', data.url);
    $songTitle.text(data.title);
  }

  /**
   * Reset Download Dialog
   */
  function reset() {
    $songArtist.text('');
    $songImage.attr('src', '#');
    $songImage.attr('alt', '');
    $songLink.attr('data-song-artist', '');
    $songLink.attr('data-song-id', '');
    $songLink.attr('data-song-image', '');
    $songLink.attr('data-song-title', '');
    $songLink.attr('data-song-url', '');
    $songTitle.text('');
    $downloadFiles.empty();
    $btnDownload.get(0).href = 'javascript:;';
    $btnDownload.addClass('disabled');
  }

  return {
    init
  };

})();

export default downloadDialog;
