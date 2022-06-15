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