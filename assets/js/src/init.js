// Modules
import player from './player.js';
import confirmDialog from './confirmDialog.js';
import favoritesHeader from './favoritesHeader.js';
import musicList from './musicList.js';
import musicListFilters from './musicListFilters.js';
import licenseDialog from './licenseDialog.js';
import popupPlayer from './popupPlayer.js';
import collections from './collections.js';
import downloadDialog from './downloadDialog.js';

// Init
export default (function() {
  player.init();
  confirmDialog.init();
  favoritesHeader.init();
  musicList.init();
  musicListFilters.init();
  licenseDialog.init();
  downloadDialog.init();
  popupPlayer.init();
  collections.init();
})()
