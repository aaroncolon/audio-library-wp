import events from './Events.js';

class Favorites {

  /**
   * Get Favorites
   */
  getFavorites(nonce) {
    jQuery.ajax({
      url      : ml_js_data.ajax_url,
      method   : 'POST',
      dataType : 'json',
      data: {
        'action' : 'ml_get_favorites',
        'nonce'  : nonce
      }
    })
    .done(function(data, textStatus, jqXHR) {
      ml_js_data.favorites = data.data;
      events.trigger('getFavoritesDone', data);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.log('getFavorites fail', errorThrown);
      events.trigger('getFavoritesFail', errorThrown);
    });
  };

  /**
   * Create Favorite
   *
   * @param {Number} id the Product ID to add to favorites
   */
  createFavorite(id, nonce) {
    jQuery.ajax({
      url      : ml_js_data.ajax_url,
      method   : 'POST',
      context  : this,
      dataType : 'json',
      data: {
        'action' : 'ml_create_favorite',
        'nonce'  : nonce,
        'songId' : id
      }
    })
    .done(function(data, textStatus, jqXHR) {
      if (data.data.meta_res) {
        ml_js_data.favorites[id] = id;
        events.trigger('createFavoriteSuccess', id);
        events.trigger('favoritesCountChanged', this.countFavorites());
      } else {
        events.trigger('createFavoriteFail', id);
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.log('fail', errorThrown);
    });
  };

  /**
   * Delete Favorite
   *
   * @param {Number} id the Product ID to delete from favorites
   */
  deleteFavorite(id, nonce) {
    jQuery.ajax({
      url      : ml_js_data.ajax_url,
      method   : 'POST',
      context  : this,
      dataType : 'json',
      data: {
        'action' : 'ml_delete_favorite',
        'nonce'  : nonce,
        'songId' : id
      }
    })
    .done(function(data, textStatus, jqXHR) {
      if (data.data.meta_res) {
        delete ml_js_data.favorites[id];
        events.trigger('deleteFavoriteSuccess', id);
        events.trigger('favoritesCountChanged', this.countFavorites());
      } else {
        events.trigger('deleteFavoriteFail', id);
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.log('fail', errorThrown);
    });
  };

  /**
   * Determine if a Product is a favorite
   *
   * @param {Number} id the Product ID to search user's favorites for
   * @return {boolean} true if product is a favorite, false if not
   */
  isFavorite(id) {
    return (ml_js_data.favorites && ml_js_data.favorites[id]) ? true : false;
  };

  countFavorites() {
    return Object.keys(ml_js_data.favorites).length;
  }

};

const favorites = new Favorites();

export default favorites;
