import events from './Events.js';

const favoritesHeader = (function() {

  const CLASS_HEADER_FAVORITES_LINK = 'site-header-favorites__link';

  let $favoritesHeaderLink;

  function init() {
    if (!cacheDom()) { return; }
    bindEvents();
  }

  function cacheDom() {
    $favoritesHeaderLink = jQuery('.' + CLASS_HEADER_FAVORITES_LINK);
    return ($favoritesHeaderLink.length) ? true : false;
  }

  function bindEvents() {
    events.on('favoritesCountChanged', handleFavoritesCountChanged, this);
  }

  function handleFavoritesCountChanged(count) {
    $favoritesHeaderLink.attr('data-favorites', count);
  }

  return {
    init
  };

})();

export default favoritesHeader;
