!function(){"use strict";const t=new class{constructor(t){this.eventSet=t,this.eventHandlers={}}on(t,e,a){if(this.eventSet&&-1===this.eventSet.indexOf(t))throw"Unknown event: "+t;(this.eventHandlers[t]||(this.eventHandlers[t]=[])).push({callback:e,context:a})}off(t,e,a){function n(t){return e&&e!=t.callback||a&&a!=t.context}for(const e in this.eventHandlers)t&&t!=e||(this.eventHandlers[e]=this.eventHandlers[e].filter(n))}trigger(t,e){if(this.eventSet&&-1===this.eventSet.indexOf(t))throw"Unknown event: "+t;const a=this.eventHandlers[t];if(a)for(var n=0;n<a.length;n++)a[n].callback.apply(a[n].context,Array.prototype.slice.call(arguments,1))}};var e=t;var a=new class{setState(t,a){let n=!1;for(const e in t)t.hasOwnProperty(e)&&(a[e]=t[e],n=!0);n&&e.trigger("stateUpdate",a)}getPosts(t,a,n,i){let o=a||1;jQuery.ajax({url:ml_js_data.ajax_url,method:"POST",dataType:"json",data:{action:"ml_get_posts",nonce:n,page:o,pageTemplate:ml_js_data.page_template_slug,filters:JSON.stringify(t)}}).done((function(t,n,o){console.log("getPosts",t),e.trigger("getPostsDone",t,a,i)})).fail((function(o,r,s){e.trigger("getPostsFail",s,t,a,n,i)}))}parseQueryString(){return new URLSearchParams(window.location.search)}isUserLoggedIn(){return!!ml_js_data.user_logged_in}getMonetizationModel(){return ml_js_data.monetization_model}getMembershipData(){return"membership"!==this.getMonetizationModel()?null:ml_js_data.membership_data}getMembershipLevel(){return null===this.getMembershipData()?null:this.getMembershipData().id}getMembershipLevelName(){return null===this.getMembershipData()?null:this.getMembershipData().name}isCurrentSong(t){return t===this.getCurrentSongId()}getCurrentSongId(){return ml_js_data.current_song.id}setCurrentSongId(t){return ml_js_data.current_song.id=Number(t),ml_js_data.current_song.id}resetCurrentSongId(){ml_js_data.current_song.id=0}isCurrentSongPlaying(){return ml_js_data.current_song.isPlaying}setCurrentSongPlayingState(t){ml_js_data.current_song.isPlaying=t}resetCurrentSongPlayingState(){this.setCurrentSongPlayingState(!1)}closeMagnificPopup(){jQuery.magnificPopup.close()}redirectToUrl(t){window.location.href=t}createBlob(t,e=""){var a=new Blob([t],{type:e});return t=URL.createObjectURL(a)}};var n=function(){const t="player__waveform",n="player__btn-play-pause",i="player__song-title",o="player__song-artist",r="player__btn-play-pause--loading",s="player__btn-play-pause--playing";let l,d,c,u,g,f,p,m,v,h={isDefaultSong:!1,isLoading:!1,isPlaying:!1,isError:!1,currSongId:null,prevSongId:null,songTitle:null,songArtist:null,songImage:null};function _(){S()?x():w(),A(),e.trigger("songStateChange",h.currSongId,h)}function y(t){const n=t.currentTarget.dataset.songId,i=t.currentTarget.dataset.songUrl,o=t.currentTarget.dataset.songTitle,r=t.currentTarget.dataset.songArtist,s=t.currentTarget.dataset.songImage;n!==h.currSongId?(a.setState({prevSongId:h.currSongId},h),a.setState({isDefaultSong:!1,currSongId:n,songArtist:r,songImage:s,songTitle:o},h),S()&&(x(),A(),a.setCurrentSongPlayingState(!1),e.trigger("songStateChange",h.prevSongId,h)),D(i)):(S()?x():w(),A(),e.trigger("songStateChange",h.currSongId,h))}function S(){return h.isPlaying}function C(t=null){d=WaveSurfer.create({audioContext:t,container:c.get(0),barGap:2,barWidth:1,height:60,responsive:200,pixelRatio:1,scrollParent:!1,ignoreSilenceMode:!0}),d.on("ready",b),d.on("finish",P),d.on("error",j)}function b(){if(h.isDefaultSong)return a.setState({isDefaultSong:!1,isLoading:!1,isPlaying:!1},h),void u.removeClass(r).find("span").text("Play");!function(t){if(!t)return;t.audioEl.removeEventListener("loadstart",t.handle),t.audioEl.remove(),t.tmpEl.remove(),t.audioEl=null,t.tmpEl=null,t.handle=null,t=null}(v),w(null,null,!0)}function P(){a.setCurrentSongPlayingState(!1),a.setState({isLoading:!1,isPlaying:!1,isError:!1},h),A(),e.trigger("songStateChange",h.currSongId,h)}function j(t){a.setCurrentSongPlayingState(!1),a.setState({isLoading:!1,isPlaying:!1,isError:!0},h),A(),e.trigger("songStateChange",h.currSongId,h),a.setState({currSongId:h.prevSongId},h)}function D(t){!d||h.isDefaultSong||m?h.isDefaultSong||(d.destroy(),C(m),v=WaveSurfer.util.ignoreSilenceMode(!0)):(m=new(window.AudioContext||window.webkitAudioContext),d.destroy(),C(m),v=WaveSurfer.util.ignoreSilenceMode(!0)),-1!==t.indexOf("blob:")?fetch(t).then((t=>t.text())).then((t=>{d.load(t)})):d.load(t),a.setState({isLoading:!0},h),A(),e.trigger("songStateChange",h.currSongId,h)}function w(t=null,n=null,i=!1){let o=d.play(t,n,i);void 0!==o?o.then((()=>{a.setState({isLoading:!1,isPlaying:!0,isError:!1},h),a.setCurrentSongId(h.currSongId,h),a.setCurrentSongPlayingState(!0),I(),A(),e.trigger("songStateChange",h.currSongId,h)})).catch((t=>{a.setState({isLoading:!1,isPlaying:!1,isError:!0},h),a.setCurrentSongId(h.currSongId,h),a.setCurrentSongPlayingState(!1),I(),A(),e.trigger("songStateChange",h.currSongId,h)})):(a.setState({isLoading:!1,isPlaying:!0,isError:!1},h),a.setCurrentSongId(h.currSongId,h),a.setCurrentSongPlayingState(!0),I(),A(),e.trigger("songStateChange",h.currSongId,h))}function x(){console.log("pauseSong"),d.pause(),a.setState({isLoading:!1,isPlaying:!1,isError:!1},h),a.setCurrentSongPlayingState(!1)}function A(){h.isError?u.removeClass(s).removeClass(r).find("span").text("Play"):h.isLoading?function(t){t.removeClass(s).addClass(r).find("span").text("Loading")}(u):h.isPlaying?function(t){t.removeClass(r).addClass(s).find("span").text("Pause")}(u):h.isPlaying||function(t){t.removeClass(r).removeClass(s).find("span").text("Play")}(u)}function I(){const t=h.songImage?h.songImage:ml_js_data.default_image;p.attr("src",t),p.attr("alt",h.songTitle),g.text(h.songTitle),f.text(h.songArtist)}return{init:function(){l=jQuery("#player"),c=l.find("#"+t),u=l.find("."+n),g=l.find("."+i),f=l.find("."+o),p=l.find(".player__song-image img"),l.length&&(function(){u.on("click",_),e.on("clickPlayPauseList",y,this)}(),C(),function(){const t=ml_js_data.default_song.id,e=ml_js_data.default_song.preview_song_url,n=ml_js_data.default_song.title,i=ml_js_data.default_song.artist,o=ml_js_data.default_song.song_image;a.setState({isDefaultSong:!0,currSongId:t,songArtist:i,songImage:o,songTitle:n},h),I(),D(e)}())}}}();var i=function(){const t="confirm-dialog__description";let n,i,o,r,s;function l(t){const e=t.target.dataset.redirectUrl,a=t.target.dataset.dialogTitle,n=t.target.dataset.dialogDescription,l=t.target.dataset.dialogAccept,d=t.target.dataset.dialogReject;r.attr("data-redirect-url",e),a&&i.text(a),n&&o.text(n),l&&r.text(l),d&&s.text(d),jQuery(t.target).magnificPopup({type:"inline",mainClass:"mfp-confirm-popup mfp-fade",closeOnBgClick:!0,removalDelay:300,showCloseBtn:!1}).magnificPopup("open")}function d(t){t.preventDefault(),a.redirectToUrl(t.target.dataset.redirectUrl)}function c(t){t.preventDefault(),r.attr("data-redirect-url",""),a.closeMagnificPopup()}return{init:function(){n=jQuery("#confirm-dialog"),i=n.find("#confirm-dialog__title"),o=n.find("#"+t),r=n.find("#confirm-dialog__accept"),s=n.find("#confirm-dialog__reject"),n.length&&(r.on("click",d),s.on("click",c),e.on("openConfirmDialog",l))}}}();var o=function(){let t;function a(e){t.attr("data-favorites",e)}return{init:function(){t=jQuery(".site-header-favorites__link"),t.length&&function(){e.on("favoritesCountChanged",a,this)}()}}}();var r=new class{getFavorites(t){jQuery.ajax({url:ml_js_data.ajax_url,method:"POST",dataType:"json",data:{action:"ml_get_favorites",nonce:t}}).done((function(t,a,n){ml_js_data.favorites=t.data,e.trigger("getFavoritesDone",t)})).fail((function(t,a,n){console.log("getFavorites fail",n),e.trigger("getFavoritesFail",n)}))}createFavorite(t,a){jQuery.ajax({url:ml_js_data.ajax_url,method:"POST",context:this,dataType:"json",data:{action:"ml_create_favorite",nonce:a,songId:t}}).done((function(a,n,i){a.data.meta_res?(ml_js_data.favorites[t]=t,e.trigger("createFavoriteSuccess",t),e.trigger("favoritesCountChanged",this.countFavorites())):e.trigger("createFavoriteFail",t)})).fail((function(t,e,a){console.log("fail",a)}))}deleteFavorite(t,a){jQuery.ajax({url:ml_js_data.ajax_url,method:"POST",context:this,dataType:"json",data:{action:"ml_delete_favorite",nonce:a,songId:t}}).done((function(a,n,i){a.data.meta_res?(delete ml_js_data.favorites[t],e.trigger("deleteFavoriteSuccess",t),e.trigger("favoritesCountChanged",this.countFavorites())):e.trigger("deleteFavoriteFail",t)})).fail((function(t,e,a){console.log("fail",a)}))}isFavorite(t){return!(!ml_js_data.favorites||!ml_js_data.favorites[t])}countFavorites(){return Object.keys(ml_js_data.favorites).length}};var s=function(){const t="btn--favorite",n="btn--is-favorite",i="music-list__btn-play-pause",o="music-list__btn-play-pause--loading",s="music-list__btn-play-pause--playing";let l,d,c,u;function g(t){t.preventDefault(),e.trigger("clickPlayPauseList",t)}function f(t){if(t.preventDefault(),t.currentTarget.dataset.redirectUrl)return void e.trigger("openConfirmDialog",t);const a=t.currentTarget.dataset.songId;Number(t.currentTarget.dataset.isFavorite)?r.deleteFavorite(a,u):r.createFavorite(a,c)}function p(t){if(t.preventDefault(),t.currentTarget.dataset.redirectUrl)return void e.trigger("openConfirmDialog",t);this.disabled=!0;const a={artist:this.dataset.songArtist,id:this.dataset.songId,image:this.dataset.songImage,title:this.dataset.songTitle,url:this.dataset.songUrl};e.trigger("clickLicense",a),jQuery(this).magnificPopup({type:"inline",mainClass:"mfp-license-popup mfp-fade",closeOnBgClick:!0,removalDelay:300,closeMarkup:'<button title="%title%" type="button" class="mfp-close">&times;</button>',callbacks:{open:function(){e.trigger("mfpOpenLicenseDialog")},close:function(){e.trigger("mfpCloseLicenseDialog")}}}).magnificPopup("open")}function m(t){if(t.preventDefault(),t.currentTarget.dataset.redirectUrl)return void e.trigger("openConfirmDialog",t);this.disabled=!0;const a={artist:this.dataset.songArtist,id:this.dataset.songId,variationId:this.dataset.songVariationId,image:this.dataset.songImage,title:this.dataset.songTitle,url:this.dataset.songUrl,key:this.dataset.songKey};e.trigger("clickDownload",a),jQuery(this).magnificPopup({type:"inline",mainClass:"mfp-download-popup mfp-fade",closeOnBgClick:!0,removalDelay:300,closeMarkup:'<button title="%title%" type="button" class="mfp-close">&times;</button>',callbacks:{open:function(){e.trigger("mfpOpenDownloadDialog")},close:function(){e.trigger("mfpCloseDownloadDialog")}}}).magnificPopup("open")}function v(t){t.preventDefault(),t.currentTarget.dataset.redirectUrl&&e.trigger("openConfirmDialog",t)}function h(t,e){!function(t,{isLoading:e=!1,isPlaying:a,isError:n}){let i=d.find("button[data-song-id="+t+"].music-list__btn-play-pause");if(!i.length)return void console.error("musicList no match found for Product ID: ",t);if(e)return void i.removeClass(s).addClass(o).find("span").text("Loading");if(a)return void function(t){t.removeClass(o).addClass(s).find("span").text("Pause")}(i);if(!a)return void function(t){t.removeClass(o).removeClass(s).find("span").text("Play")}(i);if(n)!function(t){t.removeClass(o).removeClass(s).find("span").text("Play")}(i)}(t,e)}function _(e){d.find('button[data-song-id="'+e+'"].'+t).attr("data-is-favorite",1).addClass(n)}function y(e){d.find('button[data-song-id="'+e+'"].'+t).attr("data-is-favorite",0).removeClass(n)}function S(t){a.closeMagnificPopup();let e=d.find("button[data-song-id="+t+"].btn--license");e.length&&e.removeAttr("disabled")}function C(t){a.closeMagnificPopup();let e=d.find("button[data-song-id="+t+"].btn--download");e.length&&e.removeAttr("disabled")}return{init:function(){l=jQuery("#music-list"),d=l.find("#music-list__table"),c=l.attr("data-nonce-create-favorite"),u=l.attr("data-nonce-delete-favorite"),d.length&&function(){d.on("click","."+i,g),d.on("click",".btn--favorite",f),d.on("click",".btn--license",p),d.on("click",".btn--download",m),d.on("click",".btn--upgrade",v),e.on("songStateChange",h,this),e.on("createFavoriteSuccess",_,this),e.on("deleteFavoriteSuccess",y,this),e.on("closeLicenseDialog",S,this),e.on("closeDownloadDialog",C,this)}()}}}();var l=function(){const t="music-list-filters",n="music-list--loading";let i,o,s,l,d,c,u,g,f=!1,p={btnLoadMore:!1,maxNumPages:1,page:1,queryArgs:[]};function m(){i.on("submit",S),l.on("click",C),e.on("getFavoritesDone",h,this),e.on("getFavoritesFail",h,this),e.on("getPostsDone",_,this),e.on("getPostsFail",y,this)}function v(){ml_js_data.user_logged_in?r.getFavorites(c):(ml_js_data.favorites=[],h())}function h(){f?console.log("prerendered, returning",f):window.location.search?function(){const t=a.parseQueryString();let e=!1;t.forEach(((t,a)=>{if(i.has('select[name="'+a+'"]').length){let n=i.find('select[name="'+a+'"]');n.has('option[value="'+t+'"]').length&&(n.val(t),e=!0)}})),e?i.trigger("submit"):a.getPosts(p.queryArgs,1,d)}():a.getPosts(p.queryArgs,1,d)}function _(t,e,i){a.setState({maxNumPages:t.data.max_num_pages,page:t.data.page},p),e<t.data.max_num_pages?(a.setState({btnLoadMore:!0},p),l.css("display","inline-block")):(a.setState({btnLoadMore:!1},p),l.css("display","none")),function(t,e=!1){e||s.empty();if(o.removeClass(n),t.length)for(let e=0;e<t.length;e++){const n=a.createBlob(t[e].previewSongUrl,"");t[e].previewSongUrl=n,t[e].isCurrentSongPlaying=!(!a.isCurrentSong(t[e].id)||!a.isCurrentSongPlaying()),t[e].isFavorite=r.isFavorite(t[e].id),t[e].isUserLoggedIn=a.isUserLoggedIn(),t[e].monetizationModel=a.getMonetizationModel(),t[e].membershipLevel=a.getMembershipLevel(),s.append(u(t[e]))}else{let t={message:"No Songs Found"};s.append(g(t))}}(t.data.products,i)}function y(t){console.log("getPostsFail: ",t)}function S(t){t.preventDefault(),a.setState({maxNumPages:1,page:1},p);let e=function(t){let e=[];for(let a=0;a<t.target.elements.length;a++)"SELECT"===t.target.elements[a].tagName&&""!==t.target.elements[a].value&&e.push({taxName:t.target.elements[a].dataset.taxonomyName,termValue:t.target.elements[a].value});return e}(t);var i;i=e,a.setState({queryArgs:i},p),o.addClass(n),a.getPosts(p.queryArgs,1,d)}function C(){o.addClass(n),a.getPosts(p.queryArgs,++p.page,d,!0)}return{init:function(){i=jQuery("#"+t),o=jQuery("#music-list"),s=o.find(".music-list__body"),l=o.find(".btn--load-more"),d=o.attr("data-nonce-products"),c=o.attr("data-nonce-favorites"),u=wp.template("music-list-row"),g=wp.template("music-list-row-error"),i.length&&(-1!==window.location.pathname.indexOf("collections")&&(f=!0),f?(m(),v()):(o.toggleClass(n),m(),v()))}}}();var d=function(){const t="product-variation-price",n="product-variations--loading",i="license-dialog__song-artist",o="license-dialog__song-image",r="license-dialog__song-link",s="license-dialog__song-title";let l,d,c,u,g,f,p,m,v,h,_,y,S={productId:null,termData:null,variations:null,variationAttrsAll:null,variationLabels:null,variationAttrs:null,selectedAttrs:null,individualType:!1};function C(t){t.preventDefault(),e.trigger("clickPlayPauseList",t)}function b(t){console.log("handleAddToCartClick"),t.preventDefault();let a=this.dataset.productId,n=this.dataset.quantity,i=this.dataset.variationId,o=this.dataset.variationData,r={product_id:a,quantity:n,variation_id:i,variation_data:o};l.trigger("adding_to_cart",[jQuery(this),r]),function(t,a,n,i){jQuery.ajax({url:ml_js_data.ajax_url,method:"POST",dataType:"json",data:{action:"ml_add_to_cart_variation",product_id:t,quantity:a,variation_id:n,variation_data:i,nonce:g}}).done((function(t,a,n){console.log("addToCart data",t),t.error&&t.product_url?(window.location=t.product_url,console.log("error, redirecting")):l.trigger("added_to_cart",[t.fragments,t.cart_hash]),e.trigger("addVariationToCartDone",t)})).fail((function(t,e,a){console.log("addToCart fail",a)}))}(a,n,i,o)}function P(t){console.log("handleAddVariationToCartDone",t),M(),e.trigger("closeLicenseDialog",S.productId)}function j(){M(),e.trigger("closeLicenseDialog",S.productId)}function D(t){if(t.target.options[t.target.selectedIndex].value?a.setState({selectedAttrs:{...S.selectedAttrs,[t.target.id]:t.target.options[t.target.selectedIndex].value}},S):a.setState({selectedAttrs:{...S.selectedAttrs,[t.target.id]:null}},S),ml_js_data.customer_type_individual){if(Q())return void console.log("variationValuesReset");"select-pa_customer-type"===t.target.id?(!function(t){const e=p.index("#"+t.target.id);S.individualType||"individual"!==S.selectedAttrs[t.target.id]?S.individualType?(p.each((function(t){t!==e&&(this.value="",a.setState({selectedAttrs:{...S.selectedAttrs,[this.id]:null}},S),this.disabled=!1)})),a.setState({individualType:!1},S),T(S.selectedAttrs,t)):T(S.selectedAttrs,t):(p.each((function(t){t!==e&&(this.value="",a.setState({selectedAttrs:{...S.selectedAttrs,[this.id]:null}},S))})),T(S.selectedAttrs,t),p.each((function(t){t!==e&&(this.value=this.options[1].value?this.options[1].value:"",a.setState({selectedAttrs:{...S.selectedAttrs,[this.id]:this.value}},S),this.disabled=!0)})),a.setState({individualType:!0},S))}(t),w()):(T(S.selectedAttrs,t),w())}else{if(Q())return void console.log("variationValuesReset");T(S.selectedAttrs,t),w()}}function w(){let t=!0;p.each((function(){if(!this.value)return t=!1,!1})),function(t){const e=k();t&&e?(c.removeAttr("disabled"),function(t){y.html(t.price_html)}(e),y.css("display","block"),function(t){let e=S.productId,a=t.variation_id,n=1,i=JSON.stringify(function(){const t={};for(const e in S.selectedAttrs)if(S.selectedAttrs.hasOwnProperty(e)){t[e.replace("select-","attribute_")]=S.selectedAttrs[e]}return t}());c.attr("data-product-id",e),c.attr("data-variation-id",a),c.attr("data-quantity",n),c.attr("data-variation-data",i)}(e)):(c.attr("disabled",""),y.css("display","none"),x(),c.attr("data-product-id",""),c.attr("data-variation-id",""),c.attr("data-quantity","0"),c.attr("data-variation-data",""))}(t)}function x(){y.empty()}function A(t){var i;M(),f.toggleClass(n),i=t.id,jQuery.ajax({url:ml_js_data.ajax_url,method:"POST",dataType:"json",data:{action:"ml_get_product_variations",id:i,nonce:u}}).done((function(t,n,i){console.log("getProductVariations data",t),a.setState({productId:t.data.product_id,termData:t.data.term_data,variations:t.data.variations,variationAttrsAll:t.data.variation_attrs,variationLabels:t.data.variation_labels},S),e.trigger("getProductVariationsDone",S)})).fail((function(t,e,a){console.log("getProductVariations fail",a)})),function(t){m.text(t.artist),v.attr("src",t.image),v.attr("alt",t.title),h.attr("data-song-artist",t.artist),h.attr("data-song-id",t.id),h.attr("data-song-image",t.image),h.attr("data-song-title",t.title),h.attr("data-song-url",t.url),_.text(t.title)}(t)}function I(t){!function(){let t=function(){console.log("collectAllProductVariationAttributes");let t=S.variations.map(((t,e)=>e)),e={};for(let a=0;a<t.length;a++){let n=t[a],i=S.variations[n].attributes;for(let t in i)if(i.hasOwnProperty(t))if(e[t])if(e[t].length)for(let a=0,n=!0;a<e[t].length;a++){if(i[t]&&i[t]===e[t][a]){n=!1;break}a===e[t].length-1&&i[t]&&!0===n&&e[t].push(i[t])}else i[t]&&i[t]!==e[t][0]&&e[t].push(i[t]);else e[t]=[],i[t]&&e[t].push(i[t])}for(let t in e)e.hasOwnProperty(t)&&e[t].sort(((t,e)=>{let a=t.toUpperCase(),n=e.toUpperCase();return a<n?-1:a>n?1:0}));return e}(),e={};for(const a in t)t.hasOwnProperty(a)&&(e[a.replace("attribute_","")]=t[a]);a.setState({variationAttrs:e},S),console.log("setVarAttrs",S)}(),f.toggleClass(n),O(t)}function k(){let t=L(S.selectedAttrs);return S.variations[t]||null}function T(t,e){console.log("productDrillDown",t);let n=null,i=null,o={};for(const e in t)t.hasOwnProperty(e)&&null!==t[e]&&(o[e]=t[e]);n=L(o),n&&n.length||(function(t){for(const e in S.selectedAttrs)if(S.selectedAttrs.hasOwnProperty(e)){if(t===e)continue;a.setState({selectedAttrs:{...S.selectedAttrs,[e]:null}},S)}p.each((function(e){!this.id===t&&(this.value="")})),console.log("resetSelectedAttributes",S.selectedAttrs)}(e.target.id),n=L([S.selectedAttrs[e.target.id]])),i=F(n),function(t){for(const e in t){let a=e.replace("attribute_","select-");if(S.selectedAttrs[a])continue;let n=jQuery("#"+a),i=n.get(0).options[0];n.empty(),n.append(i);for(let a=0,i=t[e];a<i.length;a++){let t=document.createElement("option");t.value=i[a],t.appendChild(document.createTextNode(S.termData[i[a]])),n.append(t)}}}(i)}function L(t){if(console.log("collectProdVarsByVals",t),!S.variations)return!1;let e=[];for(let a=0;a<S.variations.length;a++){let n=S.variations[a].attributes,i=0;for(const e in t)if(t.hasOwnProperty(e)){let a=e.replace("select-","attribute_");""!==n[a]&&n[a]!==t[e]||i++}i!==Object.keys(t).length||e.push(a)}return e}function F(t){let e=t,a={};for(let t=0;t<e.length;t++){let n=e[t],i=S.variations[n].attributes;for(let t in i)if(i.hasOwnProperty(t))if(a[t])if(a[t].length)for(let e=0,n=!0;e<a[t].length;e++){if(i[t]&&i[t]===a[t][e]){n=!1;break}e===a[t].length-1&&i[t]&&!0===n&&(console.log("unique! _attrs[prop]",i[t]),a[t].push(i[t]))}else i[t]&&i[t]!==a[t][0]&&(console.log("val is truthy",i[t]),a[t].push(i[t]));else a[t]=[],""===i[t]?(console.log("any/wildcard, adding all attributes from: ",t,S.variationAttrs[t.replace("attribute_","")]),a[t]=S.variationAttrs[t.replace("attribute_","")]):i[t]&&(console.log("_attrs[prop]",i[t]),a[t].push(i[t]))}for(let t in a)a.hasOwnProperty(t)&&a[t].sort(((t,e)=>{let a=t.toUpperCase(),n=e.toUpperCase();return a<n?-1:n>a?1:0}));return console.log("allAttributes",a),a}function O(t){for(const e in t.variationAttrs){if(!t.variationAttrs.hasOwnProperty(e))continue;let a=document.createElement("div");a.className="select-wrap";let n=document.createElement("label");n.htmlFor="select-"+e,n.className="visuallyhidden",n.appendChild(document.createTextNode(t.variationLabels[e]));let i=document.createElement("select");i.id="select-"+e;let o=document.createElement("option");o.value="",o.appendChild(document.createTextNode(t.variationLabels[e]+"...")),i.appendChild(o);for(let a=0,n=t.variationAttrs[e];a<n.length;a++){let e=document.createElement("option");e.value=n[a],e.appendChild(document.createTextNode(t.termData[n[a]])),i.appendChild(e)}a.appendChild(n),a.appendChild(i),f.append(a)}p=f.find("select")}function Q(){console.log("maybeResetVariationValues");for(const t in S.selectedAttrs)if(null!==S.selectedAttrs[t])return!1;return console.log("resetVariationValues"),f.empty(),O(S),!0}function M(){m.text(""),v.attr("src","#"),v.attr("alt",""),h.attr("data-song-artist",""),h.attr("data-song-id",""),h.attr("data-song-image",""),h.attr("data-song-title",""),h.attr("data-song-url",""),_.text(""),f.empty(),x(),a.setState({termData:null,variations:null,variationAttrsAll:null,variationLabels:null,variationAttrs:null,selectedAttrs:null,individualType:!1},S)}return{init:function(){l=jQuery("body"),d=l.find("#license-dialog"),c=d.find("#btn-add-to-cart"),f=d.find("#product-variations"),m=d.find("."+i),v=d.find("."+o+" img"),h=d.find("."+r),_=d.find("."+s),y=d.find("#"+t),u=d.attr("data-nonce-product"),g=c.attr("data-nonce-add-to-cart-variation"),d.length&&function(){h.on("click",C),c.on("click",b),d.on("change","select",D),e.on("clickLicense",A,this),e.on("getProductVariationsDone",I,this),e.on("addVariationToCartDone",P,this),e.on("mfpCloseLicenseDialog",j,this)}()}}}();var c=function(){const t="open-popup-player",a="popup-player__product-link",n="popup-player__product-link--lock",i="popup-player__product-link--loading",o="popup-player__product-link--play",r="popup-player__product-link--playing",s="popup-player__table-tr--playing";let l,d;function c(t){t.preventDefault(),t.stopImmediatePropagation(),function(t){return!!jQuery(t.target).hasClass(n)}(t)?function(t){e.trigger("openConfirmDialog",t)}(t):e.trigger("clickPlayPauseList",t)}function u(t,{isLoading:e=!1,isPlaying:n,isError:l}){let c=d.find('a[data-song-id="'+t+'"].'+a);c.length&&(l?function(t){console.log("doSongError"),t.removeClass(r).removeClass(i).addClass(o).closest("tr").removeClass(s)}(c):e?function(t){t.removeClass(o).addClass(i)}(c):n?function(t){t.removeClass(o).removeClass(i).addClass(r).closest("tr").addClass(s)}(c):n||function(t){t.removeClass(r).addClass(o).closest("tr").removeClass(s)}(c))}return{init:function(){l=jQuery(".project-posts"),d=jQuery(".popup-player"),l.length&&(!function(){d.on("click","."+a,c),e.on("songStateChange",u,this)}(),l.magnificPopup({delegate:"."+t,type:"inline",mainClass:"mfp-player-popup mfp-fade",closeOnBgClick:!0,removalDelay:300,closeMarkup:'<button title="%title%" type="button" class="mfp-close">&times;</button>'}),jQuery(".matchHeight--byRow").matchHeight({byRow:!0}))}}}();var u=function(){const t="btn-share-dialog",a="share-dialog__status",n=`${a}--visible`,i=`${a}--success`,o=`${a}--alert`,r="share-dialog--loading",s="Email Send Failure.";let l,d,c,u,g;function f(){g.find('input:not([type="hidden"]), textarea').val(""),g.find('button[type="submit"]').attr("disabled",!1),c.text("").removeClass([n,i,o])}function p(t){t.preventDefault(),g.addClass(r),g.find("button").attr("disabled",!0);const e=new FormData(g.get(0)),a=e.get("name"),l=e.get("email"),d=e.get("subject"),u=e.get("message"),p=e.get("url"),m=e.get("collection-data"),v=e.get("_wpnonce");jQuery.ajax({url:ml_js_data.ajax_url,method:"POST",dataType:"json",data:{action:"ml_share_email",name:a,email:l,subject:d,message:u,url:p,collectionData:m,_wpnonce:v}}).done((function(t,e,a){console.log("done",t),t.success?(g.removeClass(r),g.hide(),f(),c.text("Email Send Success.").addClass([n,i])):(g.removeClass(r),c.text(s).addClass([n,o]),g.find('button[type="submit"]').attr("disabled",!1))})).fail((function(t,e,a){g.removeClass(r),g.find('button[type="submit"]').attr("disabled",!1),c.text(s).addClass([n,o])}))}function m(t){t.preventDefault(),g.show(),jQuery(this).magnificPopup({type:"inline",mainClass:"mfp-share-popup mfp-fade",closeOnBgClick:!0,removalDelay:300,closeMarkup:'<button title="%title%" type="button" class="mfp-close">&times;</button>',callbacks:{open:function(){e.trigger("mfpOpenShareDialog")},close:function(){e.trigger("mfpCloseShareDialog"),f()}}}).magnificPopup("open")}return{init:function(){l=jQuery("#music-list-share"),d=l.find("#share-dialog"),c=l.find(`.${a}`),u=l.find(`#${t}`),g=l.find("#share-form"),l.length&&(u.on("click",m),g.on("submit",p))}}}();var g=function(){const t="download-files--loading",n="download-dialog__song-artist",i="download-dialog__song-image",o="download-dialog__song-link",r="download-dialog__song-title";let s,l,d,c,u,g,f,p,m,v,h={productId:null};function _(t){t.preventDefault(),e.trigger("clickPlayPauseList",t)}function y(e){console.log("handleDownloadClick",e),P(),g.toggleClass(t),e.variationId?S(e.variationId,e.id,e.key):S(e.id,e.id,e.key),a.setState({productId:e.id},h),console.log("state",h),function(t){f.text(t.artist),p.attr("src",t.image),p.attr("alt",t.title),m.attr("data-song-artist",t.artist),m.attr("data-song-id",t.id),m.attr("data-song-image",t.image),m.attr("data-song-title",t.title),m.attr("data-song-url",t.url),v.text(t.title)}(e)}function S(t,a,n){console.log("getDownloadFiles"),jQuery.ajax({url:ml_js_data.ajax_url,method:"POST",dataType:"json",data:{action:"ml_get_download_files",id:t,pid:a,key:n,nonce:c}}).done((function(t,a,n){console.log("getDownloadFiles data",t),t.error&&console.log("error, redirecting"),e.trigger("getDownloadFilesDone",t)})).fail((function(t,e,a){console.log("getDownloadFiles fail",a)}))}function C(e){console.log("handleGetDownloadFilesDone",e),d.get(0).href=e.data.d_url,d.toggleClass("disabled"),g.toggleClass(t)}function b(){P(),e.trigger("closeDownloadDialog",h.productId)}function P(){f.text(""),p.attr("src","#"),p.attr("alt",""),m.attr("data-song-artist",""),m.attr("data-song-id",""),m.attr("data-song-image",""),m.attr("data-song-title",""),m.attr("data-song-url",""),v.text(""),g.empty(),d.get(0).href="javascript:;",d.addClass("disabled")}return{init:function(){s=jQuery("body"),l=s.find("#download-dialog"),d=l.find("#btn-download"),g=l.find("#download-files"),f=l.find("."+n),p=l.find("."+i+" img"),m=l.find("."+o),v=l.find("."+r),c=l.attr("data-nonce-get-download-files"),u=l.attr("data-nonce-download-file"),l.length&&function(){m.on("click",_),e.on("clickDownload",y,this),e.on("getDownloadFilesDone",C,this),e.on("mfpCloseDownloadDialog",b,this)}()}}}();n.init(),i.init(),o.init(),s.init(),l.init(),d.init(),g.init(),c.init(),u.init()}();