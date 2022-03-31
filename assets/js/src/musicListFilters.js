import events from './Events.js';
import utils from './Utilities.js';
import favorites from './Favorites.js';

const musicListFilters = (function() {

  const FORM_FILTERS_ID = 'music-list-filters',
        MUSIC_LIST_ID   = 'music-list',
        MUSIC_LIST_BODY = MUSIC_LIST_ID + '__body',
        MFP_POPUP_ID    = 'license-dialog', // Magnific Popup
        CLASS_LOADING   = 'music-list--loading',
        BTN_LOAD_MORE   = 'btn--load-more';

  let prerender = false,
      $formFilters,
      $musicList,
      $musicListTable,
      $btnLoadMore,
      $nonceProducts,
      $nonceFavorites,
      fnMusicListRow_,
      fnMusicListRowError_;

  let state = {
    btnLoadMore : false,
    maxNumPages : 1,
    page        : 1,
    queryArgs   : []
  };

  function init() {
    if (!cacheDom()) { return }

    if (window.location.pathname.indexOf('collections') !== -1) {
      prerender = true;
    }

    if (prerender) {
      bindEvents();
      getFavorites();
    } else {
      $musicList.toggleClass(CLASS_LOADING);
      bindEvents();
      getFavorites();
    }
  }

  function cacheDom() {
    $formFilters    = jQuery('#' + FORM_FILTERS_ID);
    $musicList      = jQuery('#' + MUSIC_LIST_ID);
    $musicListTable = $musicList.find('.' + MUSIC_LIST_BODY);
    $btnLoadMore    = $musicList.find('.' + BTN_LOAD_MORE);
    $nonceProducts  = $musicList.attr('data-nonce-products');
    $nonceFavorites = $musicList.attr('data-nonce-favorites');

    // js templates
    fnMusicListRow_      = wp.template('music-list-row');
    fnMusicListRowError_ = wp.template('music-list-row-error');

    return ($formFilters.length) ? true : false;
  }

  function bindEvents() {
    $formFilters.on('submit', handleSubmit);
    $btnLoadMore.on('click', handleLoadMore);

    events.on('getFavoritesDone', handleGetFavorites, this);
    events.on('getFavoritesFail', handleGetFavorites, this);
    events.on('getPostsDone', handleGetPostsDone, this);
    events.on('getPostsFail', handleGetPostsFail, this);
  }

  function getFavorites() {
    if (! ml_js_data.user_logged_in) {
      ml_js_data.favorites = [];
      handleGetFavorites();
    } else {
      favorites.getFavorites($nonceFavorites);
    }
  }

  function searchByQueryString() {
    const params = utils.parseQueryString();
    let optionValid = false;

    // collect valid params
    params.forEach((value, key) => {
      if ($formFilters.has('select[name="'+ key +'"]').length) {
        let $select = $formFilters.find('select[name="'+ key +'"]');
        if ($select.has('option[value="'+ value +'"]').length) {
          $select.val(value);
          optionValid = true;
        }
      }
    });

    if (optionValid) {
      $formFilters.trigger('submit');
    } else {
      utils.getPosts(state.queryArgs, 1, $nonceProducts);
    }
  }

  function handleGetFavorites() {
    // prerender == true if on server-side rendered Music List
    if (prerender) {
      console.log('prerendered, returning', prerender);
      return; }

    // Parse query string and set filters
    if (window.location.search) {
      searchByQueryString();
    } else {
      utils.getPosts(state.queryArgs, 1, $nonceProducts);
    }
  }

  function handleGetPostsDone(data, page, append) {
    // set Pagination data
    utils.setState({
      'maxNumPages': data.data.max_num_pages,
      'page': data.data.page
    }, state);

    if (page < data.data.max_num_pages) {
      utils.setState({
        'btnLoadMore': true
      }, state);
      showLoadMore();
    } else {
      utils.setState({
        'btnLoadMore': false
      }, state);
      hideLoadMore();
    }

    render(data.data.products, append);
  }

  function handleGetPostsFail(errorThrown) {
    console.log('getPostsFail: ', errorThrown);
  }

  function handleSubmit(e) {
    e.preventDefault();
    // reset state pagination data
    resetPaginationState();
    // process form data
    let formData = processFormData(e);
    // set form data for "load more" requests
    saveQueryArgs(formData);
    // get the posts
    $musicList.addClass(CLASS_LOADING);
    utils.getPosts(state.queryArgs, 1, $nonceProducts);
  }

  function handleLoadMore() {
    $musicList.addClass(CLASS_LOADING);
    utils.getPosts(state.queryArgs, ++state.page, $nonceProducts, true);
  }

  function processFormData(e) {
    let data = [];
    for (let i = 0; i < e.target.elements.length; i++) {
      if (e.target.elements[i].tagName === 'SELECT' && e.target.elements[i].value !== '') {
        data.push(
          {
            taxName   : e.target.elements[i].dataset.taxonomyName,
            termValue : e.target.elements[i].value
          }
        );
      }
    }
    return data;
  }

  function saveQueryArgs(data) {
    utils.setState({
      'queryArgs': data
    }, state);
  }

  function resetPaginationState() {
    utils.setState({
      'maxNumPages': 1,
      'page': 1
    }, state);
  }

  function showLoadMore() {
    $btnLoadMore.css('display', 'inline-block');
  }

  function hideLoadMore() {
    $btnLoadMore.css('display', 'none');
  }

  function render(data, append = false) {
    if (! append) {
      resetList();
    }

    $musicList.removeClass(CLASS_LOADING);

    if (data.length) {
      for (let i = 0; i < data.length; i++) {
        const blob = utils.createBlob(data[i].preview_song_url, '');

        data[i].preview_song_url = blob;

        // check if item is current_song_id and is playing
        data[i].isCurrentSongPlaying = (utils.isCurrentSong(data[i].id) && utils.isCurrentSongPlaying()) ? true : false;

        // check if item is in the user's favorites
        data[i].isFavorite = favorites.isFavorite(data[i].id);

        // check if user is logged in
        data[i].isUserLoggedIn = utils.isUserLoggedIn();

        // pass the data to the template function
        $musicListTable.append( fnMusicListRow_(data[i]) );
      }
    } else {
      let data = {
        message: 'No Songs Found'
      };
      $musicListTable.append( fnMusicListRowError_(data) );
    }
  }

  function resetList() {
    $musicListTable.empty(); // removes elements and listeners
  }

  return {
    init
  };

})();

export default musicListFilters;
