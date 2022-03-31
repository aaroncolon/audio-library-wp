import events from './Events.js';
import utils from './Utilities.js';

const collections = (function() {

  const ID_SHARE_WRAPPER     = 'music-list-share',
        ID_BTN_SHARE         = 'btn-share-dialog',
        ID_SHARE_DIALOG      = 'share-dialog',
        ID_FORM_SHARE        = 'share-form',
        CLASS_STATUS         = `${ID_SHARE_DIALOG}__status`,
        CLASS_STATUS_VISIBLE = `${CLASS_STATUS}--visible`,
        CLASS_STATUS_SUCCESS = `${CLASS_STATUS}--success`,
        CLASS_STATUS_ALERT   = `${CLASS_STATUS}--alert`,
        CLASS_LOADING        = `${ID_SHARE_DIALOG}--loading`,
        SHARE_SUCCESS        = 'Email Send Success.',
        SHARE_FAIL           = 'Email Send Failure.';

  let $shareWrapper,
      $shareDialog,
      $shareStatus,
      $btnShare,
      $formShare;

  function init() {
    if (!cacheDom()) { return; }
    bindEvents();
  }

  function cacheDom() {
    $shareWrapper = jQuery(`#${ID_SHARE_WRAPPER}`);
    $shareDialog  = $shareWrapper.find(`#${ID_SHARE_DIALOG}`);
    $shareStatus  = $shareWrapper.find(`.${CLASS_STATUS}`);
    $btnShare     = $shareWrapper.find(`#${ID_BTN_SHARE}`);
    $formShare    = $shareWrapper.find(`#${ID_FORM_SHARE}`);
    return ($shareWrapper.length) ? true : false;
  }

  function bindEvents() {
    $btnShare.on('click', handleClickShare);
    $formShare.on('submit', handleFormSubmit);
  }

  function resetForm() {
    $formShare.find('input:not([type="hidden"]), textarea').val('');
    $formShare.find('button[type="submit"]').attr('disabled', false);
    $shareStatus
      .text('')
      .removeClass([CLASS_STATUS_VISIBLE, CLASS_STATUS_SUCCESS, CLASS_STATUS_ALERT]);
  }

  function handleFormSubmit(e) {
    e.preventDefault();

    $formShare.addClass(CLASS_LOADING);
    $formShare.find('button').attr('disabled', true);

    const form           = new FormData($formShare.get(0)),
          name           = form.get('name'),
          email          = form.get('email'),
          subject        = form.get('subject'),
          message        = form.get('message'),
          url            = form.get('url'),
          collectionData = form.get('collection-data'),
          nonce          = form.get('_wpnonce');

    jQuery.ajax({
      url      : ml_js_data.ajax_url,
      method   : 'POST',
      dataType : 'json',
      data     : {
        'action'         : 'ml_share_email',
        'name'           : name,
        'email'          : email,
        'subject'        : subject,
        'message'        : message,
        'url'            : url,
        'collectionData' : collectionData,
        '_wpnonce'       : nonce
      }
    })
    .done(function(data, textStatus, jqXHR) {
      console.log('done', data);
      if (data.success) {
        $formShare.removeClass(CLASS_LOADING);
        $formShare.hide();
        resetForm();
        $shareStatus
          .text(SHARE_SUCCESS)
          .addClass([CLASS_STATUS_VISIBLE, CLASS_STATUS_SUCCESS]);
      } else {
        $formShare.removeClass(CLASS_LOADING);
        $shareStatus
          .text(SHARE_FAIL)
          .addClass([CLASS_STATUS_VISIBLE, CLASS_STATUS_ALERT]);
        $formShare.find('button[type="submit"]').attr('disabled', false);
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      $formShare.removeClass(CLASS_LOADING);
      $formShare.find('button[type="submit"]').attr('disabled', false);
      $shareStatus
        .text(SHARE_FAIL)
        .addClass([CLASS_STATUS_VISIBLE, CLASS_STATUS_ALERT]);
    });
  }

  function handleClickShare(e) {
    e.preventDefault();

    $formShare.show();

    // init Magnific Popup
    jQuery(this).magnificPopup({
      // alignTop: true,
      type: 'inline',
      mainClass: 'mfp-share-popup mfp-fade',
      closeOnBgClick: true,
      removalDelay: 300,
      closeMarkup: '<button title="%title%" type="button" class="mfp-close">&times;</button>',
      callbacks: {
        open: function() {
          events.trigger('mfpOpenShareDialog');
        },
        close: function() {
          events.trigger('mfpCloseShareDialog');
          resetForm();
        }
      }
    }).magnificPopup('open');
  }

  return {
    init
  }

})();

export default collections;
