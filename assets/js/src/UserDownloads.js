import events from './Events.js';

class UserDownloads {

  /**
   * Get Downloads
   */
  getDownloads(nonce) {
    jQuery.ajax({
      url      : ml_js_data.ajax_url,
      method   : 'POST',
      dataType : 'json',
      data : {
        'action' : 'ml_get_user_downloads',
        'nonce'  : nonce
      }
    })
    .done(function(data, textStatus, jqXHR) {
      ml_js_data.user_downloads = data.data;
      events.trigger('getUserDownloadsDone', data);
      console.log('getDownloads', data);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.error('getUserDownloads fail', errorThrown);
      events.trigger('getUserDownloadsFail', errorThrown);
    });
  };

  /**
   * Create Download
   *
   * @param {Number} id the Product ID to add to downloads
   */
  createDownload(id, nonce) {
    jQuery.ajax({
      url      : ml_js_data.ajax_url,
      method   : 'POST',
      context  : this,
      dataType : 'json',
      data : {
        'action' : 'ml_create_user_download',
        'nonce'  : nonce,
        'songId' : id
      }
    })
    .done(function(data, textStatus, jqXHR) {
      if (data.data.meta_res) {
        ml_js_data.user_downloads[id] = id;
        events.trigger('createDownloadSuccess', id);
        events.trigger('downloadsCountChanged', this.countDownloads());
      } else {
        events.trigger('createDownloadFail', id);
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.error('fail', errorThrown);
    });
  };

  /**
   * Determine if a Product is a download
   *
   * @param {Number} id the Product ID to search user's downloads for
   * @return {boolean} true if product is a download
   */
  isDownload(id) {
    return (ml_js_data.user_downloads && ml_js_data.user_downloads[id]) ? true : false;
  };

  countDownloads() {
    return Object.keys(ml_js_data.user_downloads).length;
  }

};

const userDownloads = new UserDownloads();

export default userDownloads;
