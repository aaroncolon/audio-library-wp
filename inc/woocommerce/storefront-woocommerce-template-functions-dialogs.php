<?php
/**
 * WooCommerce Dialog Functions.
 *
 * @package storefront
 */

function storefront_confirm_dialog() {
?>
<div id="confirm-dialog" class="confirm-dialog music-list__confirm-dialog mfp-hide">
  <h3 id="confirm-dialog__title" class="confirm-dialog__title title--confirm-dialog">Sign-In Required</h3>
  <p id="confirm-dialog__description" class="confirm-dialog__description">You must sign in to perform this action.</p>
  <div>
    <button id="confirm-dialog__reject" class="confirm-dialog__btn confirm-dialog__btn--reject">Cancel</button>
    <button id="confirm-dialog__accept" class="confirm-dialog__btn confirm-dialog__btn--accept">Sign In</button>
  </div>
</div>
<?php
}
