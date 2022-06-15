import events from './Events.js';
import utils from './Utilities.js';

const confirmDialog = (function() {

  const ID_CONFIRM_DIALOG        = 'confirm-dialog',
        ID_CONFIRM_DIALOG_TITLE  = 'confirm-dialog__title',
        ID_CONFIRM_DIALOG_DESC   = 'confirm-dialog__description',
        ID_CONFIRM_DIALOG_ACCEPT = 'confirm-dialog__accept',
        ID_CONFIRM_DIALOG_REJECT = 'confirm-dialog__reject';

  let $confirmDialog,
      $confirmDialogTitle,
      $confirmDialogDesc,
      $btnConfirmDialogAccept,
      $btnConfirmDialogReject;

  function init() {
    if (!cacheDom()) { return; }
    bindEvents();
    render();
  }

  function cacheDom() {
    $confirmDialog          = jQuery('#' + ID_CONFIRM_DIALOG);
    $confirmDialogTitle     = $confirmDialog.find('#' + ID_CONFIRM_DIALOG_TITLE);
    $confirmDialogDesc      = $confirmDialog.find('#' + ID_CONFIRM_DIALOG_DESC);
    $btnConfirmDialogAccept = $confirmDialog.find('#' + ID_CONFIRM_DIALOG_ACCEPT);
    $btnConfirmDialogReject = $confirmDialog.find('#' + ID_CONFIRM_DIALOG_REJECT);

    return ($confirmDialog.length) ? true : false;
  }

  function bindEvents() {
    $btnConfirmDialogAccept.on('click', handleConfirmDialogAccept);
    $btnConfirmDialogReject.on('click', handleConfirmDialogReject);

    events.on('openConfirmDialog', openConfirmDialog);
  }

  function render() {

  }

  function openConfirmDialog(e) {
    const url   = e.target.dataset.redirectUrl,
          title = e.target.dataset.dialogTitle,
          desc  = e.target.dataset.dialogDescription,
          accept = e.target.dataset.dialogAccept,
          reject = e.target.dataset.dialogReject;

    $btnConfirmDialogAccept.attr('data-redirect-url', url);

    if (title) {
      $confirmDialogTitle.text(title);
    }

    if (desc) {
      $confirmDialogDesc.text(desc);
    }

    if (accept) {
      $btnConfirmDialogAccept.text(accept);
    }

    if (reject) {
      $btnConfirmDialogReject.text(reject);
    }

    // Open confirmation dialog
    jQuery(e.target).magnificPopup({
      type: 'inline',
      mainClass: 'mfp-confirm-popup mfp-fade',
      closeOnBgClick: true,
      removalDelay: 300,
      // closeMarkup: '<button title="%title%" type="button" class="mfp-close">&times;</button>',
      showCloseBtn: false
    }).magnificPopup('open');
  }

  function handleConfirmDialogAccept(e) {
    e.preventDefault();
    utils.redirectToUrl(e.target.dataset.redirectUrl);
  }

  function handleConfirmDialogReject(e) {
    e.preventDefault();
    resetRedirectUrl();
    utils.closeMagnificPopup();
  }

  function resetRedirectUrl() {
    $btnConfirmDialogAccept.attr('data-redirect-url', '');
  }

  return {
    init
  };

})()

export default confirmDialog;
