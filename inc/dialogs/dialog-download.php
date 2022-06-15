<div id="download-dialog" class="download-dialog mfp-hide" data-nonce-get-download-files="<?php echo esc_attr(ml_do_nonce('get_download_files')) ?>">
  <h3 class="title--download-dialog">Download Details</h3>
  <div class="download-dialog__song-details clear">
    <div class="download-dialog__song-image">
      <a href="javascript:;" class="download-dialog__song-link">
        <img src="#" alt="" />
      </a>
    </div>
    <div class="download-dialog__song-text">
      <div class="download-dialog__song-title"></div>
      <div class="download-dialog__song-artist"></div>
    </div>
  </div>
  <div class="download-form-wrap">
    <form id="download-form" class="download-form" action="" method="post">
    <div id="download-files" class="download-files"></div>

      <div class="download-form__summary">
        <a id="btn-download" class="btn btn--download button disabled" data-nonce-download-file="<?php echo esc_attr(ml_do_nonce('download_file')) ?>" href="javascript:;" target="_blank">Download</a>
      </div>
    </form>
  </div>
</div>