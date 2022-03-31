import events from './Events.js';
import utils from './Utilities.js';

const player = (function() {

  const ID_PLAYER         = 'player',
        ID_WAVEFORM       = 'player__waveform',
        CLASS_PLAY_PAUSE  = 'player__btn-play-pause',
        CLASS_SONG_IMAGE  = 'player__song-image',
        CLASS_SONG_TITLE  = 'player__song-title',
        CLASS_SONG_ARTIST = 'player__song-artist',
        CLASS_LOADING     = 'player__btn-play-pause--loading',
        CLASS_PLAYING     = 'player__btn-play-pause--playing';

  let $player,
      $wavesurfer,
      $waveform,
      $playPause,
      $songTitle,
      $songArtist,
      $songImage,
      audioCtx,
      ignoreSilenceEls;

  let state = {
    isDefaultSong : false, // true when default song is initialized
    isLoading     : false,
    isPlaying     : false,
    isError       : false,
    currSongId    : null,
    prevSongId    : null,
    songTitle     : null,
    songArtist    : null,
    songImage     : null
  };

  function init() {
    if (!cacheDom()) { return; }
    bindEvents();
    initWaveSurfer();
    initDefaultSong();
  }

  function cacheDom() {
    $player     = jQuery('#' + ID_PLAYER);
    $waveform   = $player.find('#' + ID_WAVEFORM);
    $playPause  = $player.find('.' + CLASS_PLAY_PAUSE);
    $songTitle  = $player.find('.' + CLASS_SONG_TITLE);
    $songArtist = $player.find('.' + CLASS_SONG_ARTIST);
    $songImage  = $player.find('.' + CLASS_SONG_IMAGE + ' img');

    return ($player.length) ? true : false;
  }

  function bindEvents() {
    $playPause.on('click', handleClickPlayerPlayPause);

    events.on('clickPlayPauseList', handleClickPlayPauseList, this);
  }

  function handleClickPlayerPlayPause() {
    if (isPlaying()) {
      pauseSong();
    } else {
      playSong();
    }
    updatePlayerButton();
    events.trigger('songStateChange', state.currSongId, state);
  }

  function handleClickPlayPauseList(e) {
    const id         = e.currentTarget.dataset.songId;
    const url        = e.currentTarget.dataset.songUrl;
    const songTitle  = e.currentTarget.dataset.songTitle;
    const songArtist = e.currentTarget.dataset.songArtist;
    const songImage  = e.currentTarget.dataset.songImage;

    // Load song if different than current
    if (id !== state.currSongId) {
      // set the prevSongId locally
      utils.setState({
        'prevSongId' : state.currSongId
      }, state);

      // set the currSongId locally
      utils.setState({
        'isDefaultSong' : false,
        'currSongId'    : id,
        'songArtist'    : songArtist,
        'songImage'     : songImage,
        'songTitle'     : songTitle
      }, state);

      // set the currSongId globally (in play())
      // utils.setCurrentSongId(id);

      // pause prev song
      if (isPlaying()) {
        pauseSong();
        updatePlayerButton();
        utils.setCurrentSongPlayingState(false);
        // trigger songStateChange for subscribed modules (UI updates, etc)
        events.trigger('songStateChange', state.prevSongId, state);
      }

      // load the new song
      loadSong(url); // @NOTE async
    } else {
      if (isPlaying()) {
        pauseSong();
      } else {
        playSong();
      }
      updatePlayerButton();
      events.trigger('songStateChange', state.currSongId, state);
    }
  }

  function isPlaying() {
    return state.isPlaying;
  }

  function initAudioContext() {
    // create new AudioContext after user interaction
    return new (window.AudioContext || window.webkitAudioContext)()
  }

  function initDefaultSong() {
    const songId     = ml_js_data.default_song.id,
          songUrl    = ml_js_data.default_song.preview_song_url,
          songTitle  = ml_js_data.default_song.title,
          songArtist = ml_js_data.default_song.artist,
          songImage  = ml_js_data.default_song.song_image;

    utils.setState({
      'isDefaultSong' : true,
      'currSongId'    : songId,
      'songArtist'    : songArtist,
      'songImage'     : songImage,
      'songTitle'     : songTitle
    }, state);

    updatePlayerDetails();
    loadSong(songUrl); // @async
  }

  function initWaveSurfer(ctx = null) {
    $wavesurfer = WaveSurfer.create({
      audioContext: ctx,
      container: $waveform.get(0),
      // closeAudioContext: true,
      barGap: 2,
      barWidth: 1,
      height: 60,
      responsive: 200,
      pixelRatio: 1,
      scrollParent: false,
      ignoreSilenceMode: true
    });

    $wavesurfer.on('ready', handleWaveSurferReady);
    $wavesurfer.on('finish', handleWaveSurferFinish);
    $wavesurfer.on('error', handleWaveSurferError);
  }

  function handleWaveSurferReady() {
    if (state.isDefaultSong) {
      utils.setState({
        'isDefaultSong': false,
        'isLoading': false,
        'isPlaying': false
      }, state);
      updatePlayerButtonDefault();
      return;
    }

    // destroy silence mode
    destroySilenceMode(ignoreSilenceEls);

    // play song in async mode
    playSong(null, null, true);
  }

  function handleWaveSurferFinish() {
    // set global state here for other modules to re-render proper UI state (e.g. AJAX)
    utils.setCurrentSongPlayingState(false);
    utils.setState({
      'isLoading': false,
      'isPlaying': false,
      'isError': false
    }, state);

    updatePlayerButton();

    events.trigger('songStateChange', state.currSongId, state);
  }

  function handleWaveSurferError(errorText) {
    // set global state here for other modules to re-render proper UI state (e.g. AJAX)
    utils.setCurrentSongPlayingState(false);
    utils.setState({
      'isLoading': false,
      'isPlaying': false,
      'isError': true
    }, state);

    updatePlayerButton();

    events.trigger('songStateChange', state.currSongId, state);

    // reset currSongId to prevSongId
    utils.setState({
      currSongId: state.prevSongId
    }, state);
  }

  /**
   * Destroy Silence Mode temporary elements
   * @param {object} obj object containing the temporary elements
   */
  function destroySilenceMode(obj) {
    if (!obj) {
      return;
    }

    obj.audioEl.removeEventListener('loadstart', obj.handle);
    obj.audioEl.remove();
    obj.tmpEl.remove();
    obj.audioEl = null;
    obj.tmpEl = null;
    obj.handle = null;
    obj = null;
  }

  function loadSong(url) {
    // destroy previous $wavesurfer instance
    if ($wavesurfer && !state.isDefaultSong && !audioCtx) {
      // create new AudioContext based on user interaction
      audioCtx = initAudioContext();

      // destroy initial wavesurfer
      // to work around iOS Safari bug drawing the waveform
      $wavesurfer.destroy();

      // re-init wavesurfer with audioCtx
      initWaveSurfer(audioCtx);

      // begin looping silent sound to bypass hardware mute toggle
      // this sound will be destroyed in the $wavesurfer `ready` event
      ignoreSilenceEls = WaveSurfer.util.ignoreSilenceMode(true);
    } else if (!state.isDefaultSong) {
      $wavesurfer.destroy();
      initWaveSurfer(audioCtx);

      // begin looping silent sound to bypass hardware mute toggle
      // this sound will be destroyed in the $wavesurfer `ready` event
      ignoreSilenceEls = WaveSurfer.util.ignoreSilenceMode(true);
    }

    // fetch(url)
    //   .then(res => res.blob())
    //   .then(blob => {
    //     $wavesurfer.loadBlob(blob);
    //   });

    if (url.indexOf('blob:') !== -1) {
      // for MediaElement backend
      // let audio = new Audio();
      // audio.src = url;
      // $wavesurfer.load(audio);

      // $wavesurfer.loadBlob(url);

      fetch(url)
        .then(res => res.text())
        .then(data => {
          $wavesurfer.load(data);
        });
    } else {
      $wavesurfer.load(url);
    }

    utils.setState({
      'isLoading': true
    }, state);

    updatePlayerButton();

    events.trigger('songStateChange', state.currSongId, state);
  }

  function playSong(start = null, end = null, async = false) {
    let playPromise = $wavesurfer.play(start, end, async);

    if (playPromise !== undefined) {
      playPromise.then(() => {
        // playback started
        utils.setState({
          'isLoading': false,
          'isPlaying': true,
          'isError': false
        }, state);
        utils.setCurrentSongId(state.currSongId, state);
        utils.setCurrentSongPlayingState(true);
        updatePlayerDetails();
        updatePlayerButton();
        events.trigger('songStateChange', state.currSongId, state);
      })
      .catch(error => {
        utils.setState({
          'isLoading': false,
          'isPlaying': false,
          'isError': true
        }, state);
        utils.setCurrentSongId(state.currSongId, state);
        utils.setCurrentSongPlayingState(false);
        updatePlayerDetails();
        updatePlayerButton();
        events.trigger('songStateChange', state.currSongId, state);
      })
    } else {
      // playback started
      utils.setState({
        'isLoading': false,
        'isPlaying': true,
        'isError': false
      }, state);
      utils.setCurrentSongId(state.currSongId, state);
      utils.setCurrentSongPlayingState(true);
      updatePlayerDetails();
      updatePlayerButton();
      events.trigger('songStateChange', state.currSongId, state);
    }
  }

  function pauseSong() {
    console.log('pauseSong');
    $wavesurfer.pause();
    utils.setState({
      'isLoading': false,
      'isPlaying': false,
      'isError': false
    }, state);
    utils.setCurrentSongPlayingState(false);
  }

  function stopSong() {
    $wavesurfer.stop();
    utils.setState({
      'isLoading': false,
      'isPlaying': false,
      'isError': false
    }, state);
    utils.setCurrentSongPlayingState(false);
  }

  function updatePlayerButton() {
    if (state.isError) {
      doSongError($playPause);
      return;
    }

    if (state.isLoading) {
      doSongLoading($playPause);
      return;
    }

    if (state.isPlaying) {
      doSongPlay($playPause);
      return;
    }

    if (! state.isPlaying) {
      doSongPause($playPause);
      return;
    }
  }

  function updatePlayerButtonDefault() {
    $playPause.removeClass(CLASS_LOADING)
      .find('span')
      .text('Play');
  }

  function doSongLoading($el) {
    $el
      .removeClass(CLASS_PLAYING)
      .addClass(CLASS_LOADING)
      .find('span')
      .text('Loading');
  }

  function doSongPause($el) {
    $el
      .removeClass(CLASS_LOADING)
      .removeClass(CLASS_PLAYING)
      .find('span')
      .text('Play');
  }

  function doSongPlay($el) {
    $el
      .removeClass(CLASS_LOADING)
      .addClass(CLASS_PLAYING)
      .find('span')
      .text('Pause');
  }

  function doSongError($el) {
    $el
      .removeClass(CLASS_PLAYING)
      .removeClass(CLASS_LOADING)
      .find('span')
      .text('Play');
  }

  /**
   * Update player with selected song data
   */
  function updatePlayerDetails() {
    $songImage.attr('src', state.songImage);
    $songImage.attr('alt', state.songTitle);
    $songTitle.text(state.songTitle);
    $songArtist.text(state.songArtist);
  }

  return {
    init
  };

})();

export default player;
