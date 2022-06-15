import events from './Events.js';

class Utilities {

  /**
   * Set a module's state
   *
   * @param {Object} obj the state object to set
   * @param {Object} state the local state object
   */
  setState(obj, state) {
    let updated = false;
    for (const prop in obj) {
      if (obj.hasOwnProperty(prop)) {
        state[prop] = obj[prop];
        updated = true;
      }
    }
    if (updated) {
      events.trigger('stateUpdate', state);
    }
  }

  /**
   * Get Posts
   *
   * @param {array}   _args  Filter arguments
   * @param {number}  _page  The page to retrieve
   * @param {String}  nonce the nonce
   * @param {boolean} append True to append data to list. False to empty list.
   */
  getPosts(_args, _page, nonce, append) {
    let page = _page || 1;

    jQuery.ajax({
      // contentType: 'application/json',
      // processData: false,
      url      :  ml_js_data.ajax_url,
      method   : 'POST',
      dataType : 'json',
      data: {
        'action'       : 'ml_get_posts',
        'nonce'        : nonce,
        'page'         : page,
        'pageTemplate' : ml_js_data.page_template_slug,
        'filters'      : JSON.stringify(_args)
      }
    })
    .done(function(data, textStatus, jqXHR) {
      console.log('getPosts', data);
      events.trigger('getPostsDone', data, _page, append);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      events.trigger('getPostsFail', errorThrown, _args, _page, nonce, append);
    });
  }

  parseQueryString() {
    return new URLSearchParams(window.location.search);
  }

  /**
   * Determines if user is logged in
   *
   * @return {Boolean} true if user is logged in. false if user is logged out.
   */
  isUserLoggedIn() {
    return (ml_js_data.user_logged_in) ? true : false;
  }

  /**
   * Gets current site monetization model
   *
   * @return {String}
   */
  getMonetizationModel() {
    return ml_js_data.monetization_model;
  }

  /**
   * Gets current user's membership data
   *
   * @return {Object|null}
   */
  getMembershipData() {
    if (this.getMonetizationModel() !== 'membership') {
      return null;
    }
    return ml_js_data.membership_data;
  }

  /**
   * Gets current user's membership level ID
   *
   * @return {String|null}
   */
  getMembershipLevel() {
    if (this.getMembershipData() === null) {
      return null;
    }
    return this.getMembershipData().id;
  }

  /**
   * Gets current user's membership level name
   *
   * @return {String|null}
   */
   getMembershipLevelName() {
    if (this.getMembershipData() === null) {
      return null;
    }
    return this.getMembershipData().name;
  }

  /**
   * Check if a Product is the currently loaded Product
   *
   * @param {Number} id the Product ID
   * @return {Boolean}
   */
  isCurrentSong(id) {
    return (id === this.getCurrentSongId()) ? true : false;
  }

  /**
   * Gets the current Product ID
   *
   * @return {Number} the current Product ID
   */
  getCurrentSongId() {
    return ml_js_data.current_song.id;
  }

  /**
   * Set the current Product ID
   *
   * @return {Number} the current Product ID
   */
  setCurrentSongId(id) {
    ml_js_data.current_song.id = Number(id);
    return (ml_js_data.current_song.id);
  }

  /**
   * Reset current Product ID
   */
  resetCurrentSongId() {
    ml_js_data.current_song.id = 0;
  }

  isCurrentSongPlaying() {
    return ml_js_data.current_song.isPlaying;
  }

  /**
   * Set the current Product playing state
   *
   * @param {Boolean} isPlaying true if the song is playing
   */
  setCurrentSongPlayingState(isPlaying) {
    ml_js_data.current_song.isPlaying = isPlaying;
  }

  resetCurrentSongPlayingState() {
    this.setCurrentSongPlayingState(false);
  }

  /**
   * Close Magnific Popup via API
   */
  closeMagnificPopup() {
    jQuery.magnificPopup.close();
  }

  /**
   * Redirect to URL
   *
   * @param {String} url the URL to redirect to
   */
  redirectToUrl(url) {
    window.location.href = url;
  }

  /**
   * Create Blob
   */
  createBlob(url, mimeType = '') {
    var blob = new Blob([url], {type: mimeType});
    var url = URL.createObjectURL(blob);    
    return url;
  }

}; // Utilities

const utils = new Utilities();

export default utils;
