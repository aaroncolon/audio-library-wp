import events from './Events.js';
import utils from './Utilities.js';

const licenseDialog = (function() {

  const ID_BTN_ADD_TO_CART      = 'btn-add-to-cart',
        ID_CLOSE_DIALOG         = 'btn-close-license-dialog',
        ID_LICENSE_DIALOG       = 'license-dialog',
        ID_PRODUCT_VARIATIONS   = 'product-variations',
        ID_VARIATION_PRICE      = 'product-variation-price',
        CLASS_PRODUCT_VARS_LOAD = 'product-variations--loading',
        CLASS_ARTIST            = 'license-dialog__song-artist',
        CLASS_IMAGE             = 'license-dialog__song-image',
        CLASS_LINK              = 'license-dialog__song-link',
        CLASS_TITLE             = 'license-dialog__song-title';

  let $body,
      $licenseDialog,
      $btnAddToCart,
      $nonceProduct,
      $nonceAddToCartVariation,
      $productVariations,
      $selects,
      $songArtist,
      $songImage,
      $songLink,
      $songTitle,
      $variationPrice;

  let state = {
    'productId'         : null,
    'termData'          : null,
    'variations'        : null,
    'variationAttrsAll' : null,
    'variationLabels'   : null,
    'variationAttrs'    : null,
    'selectedAttrs'     : null, // selected Product Variation attributes (drill down)
    'individualType'    : false
  };

  function init() {
    if (!cacheDom()) { return; }
    bindEvents();
  }

  function cacheDom() {
    $body                    = jQuery('body');
    $licenseDialog           = $body.find('#' + ID_LICENSE_DIALOG);
    $btnAddToCart            = $licenseDialog.find('#' + ID_BTN_ADD_TO_CART);
    $productVariations       = $licenseDialog.find('#' + ID_PRODUCT_VARIATIONS);
    $songArtist              = $licenseDialog.find('.' + CLASS_ARTIST);
    $songImage               = $licenseDialog.find('.' + CLASS_IMAGE + ' img');
    $songLink                = $licenseDialog.find('.' + CLASS_LINK);
    $songTitle               = $licenseDialog.find('.' + CLASS_TITLE);
    $variationPrice          = $licenseDialog.find('#' + ID_VARIATION_PRICE);
    $nonceProduct            = $licenseDialog.attr('data-nonce-product');
    $nonceAddToCartVariation = $btnAddToCart.attr('data-nonce-add-to-cart-variation');

    return ($licenseDialog.length) ? true : false;
  }

  function bindEvents() {
    $songLink.on('click', handleClickSongLink);
    $btnAddToCart.on('click', handleAddToCartClick);
    $licenseDialog.on('change', 'select', handleSelectChange);

    events.on('clickLicense', handleLicenseClick, this);
    events.on('getProductVariationsDone', handleGetProductVariationsDone, this);
    events.on('addVariationToCartDone', handleAddVariationToCartDone, this);
    events.on('mfpCloseLicenseDialog', handleCloseDialog, this);
  }

  function handleClickSongLink(e) {
    e.preventDefault();
    events.trigger('clickPlayPauseList', e);
  }

  function handleAddToCartClick(e) {
    console.log('handleAddToCartClick');
    e.preventDefault();

    let product_id     = this.dataset.productId;
    let quantity       = this.dataset.quantity;
    let variation_id   = this.dataset.variationId;
    let variation_data = this.dataset.variationData; // displayed on cart.php if included
    let data = {
      'product_id'     : product_id,
      'quantity'       : quantity,
      'variation_id'   : variation_id,
      'variation_data' : variation_data
    };

    // trigger jQuery WooCommerce events
    $body.trigger('adding_to_cart', [jQuery(this), data]);

    addToCart(product_id, quantity, variation_id, variation_data);
  }

  function addToCart(product_id, quantity, variation_id, variation_data) {
    jQuery.ajax({
      url      : ml_js_data.ajax_url,
      method   : 'POST',
      dataType : 'json',
      data     : {
        'action'         : 'ml_add_to_cart_variation',
        'product_id'     : product_id,
        'quantity'       : quantity,
        'variation_id'   : variation_id,
        'variation_data' : variation_data,
        'nonce'          : $nonceAddToCartVariation
      }
    })
    .done(function(data, textStatus, jqXHR) {
      console.log('addToCart data', data);

      if (data.error && data.product_url) {
        window.location = data.product_url;
        console.log('error, redirecting');
      } else {
        // trigger jQuery WooCommerce event
        $body.trigger('added_to_cart', [data.fragments, data.cart_hash, ]);
      }

      events.trigger('addVariationToCartDone', data);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.log('addToCart fail', errorThrown);
    });
  }

  function handleAddVariationToCartDone(data) {
    console.log('handleAddVariationToCartDone', data);
    reset();
    events.trigger('closeLicenseDialog', state.productId);
  }

  function handleCloseDialog() {
    reset();
    events.trigger('closeLicenseDialog', state.productId);
  }

  function handleSelectChange(e) {
    // save / remove <select> id from state
    if (e.target.options[e.target.selectedIndex].value) {
      utils.setState({
        'selectedAttrs' : {
          ...state.selectedAttrs,
          [e.target.id] : e.target.options[e.target.selectedIndex].value
        }
      }, state);
    } else {
      utils.setState({
        'selectedAttrs' : {
          ...state.selectedAttrs,
          [e.target.id] : null
        }
      }, state);
    }

    // handle special case "Customer Type: Individual"
    if (ml_js_data.customer_type_individual) {
      // resets all selects to default values
      if (maybeResetVariationValues()) {
        console.log('variationValuesReset');
        return;
      }

      // handle Customer Type select
      if (e.target.id === 'select-pa_customer-type') {
        handleCustomerTypeIndividual(e);

        maybeToggleAddToCartButton();
      } else {
        productDrillDown(state.selectedAttrs, e);

        maybeToggleAddToCartButton();
      }
    } else {
      // resets all selects to default values
      if (maybeResetVariationValues()) {
        console.log('variationValuesReset');
        return;
      }

      productDrillDown(state.selectedAttrs, e);

      maybeToggleAddToCartButton();
    }
  }

  function handleCustomerTypeIndividual(e) {
    const indexSkip = $selects.index('#' + e.target.id);

    if (! state.individualType && state.selectedAttrs[e.target.id] === 'individual') {
      // reset dropdowns
      $selects.each(function(index) {
        if (index !== indexSkip) {
          this.value = '';
          utils.setState({
            'selectedAttrs' : {
              ...state.selectedAttrs,
              [this.id] : null
            }
          }, state);
        }
      });

      // productDrillDown populates selects with `individual` attribute options
      productDrillDown(state.selectedAttrs, e);

      // set all other attributes to Individual
      // $selects.each(function(index) {
      //   if (index > 0) {
      //     this.value = 'individual';
      //
      //     utils.setState({
      //       'selectedAttrs' : {
      //         ...state.selectedAttrs,
      //         [this.id] : this.value,
      //       }
      //     }, state);
      //
      //     this.disabled = true;
      //   }
      // });

      // select first option of remaining selects
      $selects.each(function(index) {
        if (index !== indexSkip) {
          this.value = (this.options[1].value) ? this.options[1].value : '';

          utils.setState({
            'selectedAttrs' : {
              ...state.selectedAttrs,
              [this.id] : this.value,
            }
          }, state);

          this.disabled = true;
        }
      });

      utils.setState({
        'individualType' : true
      }, state);
    }
    else if (state.individualType) {
      // reset all dropdown values
      $selects.each(function(index) {
        if (index !== indexSkip) {
          this.value = '';

          utils.setState({
            'selectedAttrs' : {
              ...state.selectedAttrs,
              [this.id] : null
            }
          }, state);

          this.disabled = false;
        }
      });

      utils.setState({
        'individualType' : false
      }, state);

      productDrillDown(state.selectedAttrs, e);
    } else {
      productDrillDown(state.selectedAttrs, e);
    }
  }

  function maybeToggleAddToCartButton() {
    let toggle = true;
    $selects.each(function() {
      if (! this.value) {
        toggle = false;
        return false; // break .each()
      }
    });
    toggleAddToCartButton(toggle);
  }

  function toggleAddToCartButton(enable) {
    // get varation data
    const variation_data = getProductVariationDataBySelectedAttributes();

    if (enable && variation_data) {
      // enable
      $btnAddToCart.removeAttr('disabled');
      // update variation price with Product data
      updateVariationPrice(variation_data);
      // show variation price
      showVariationPrice();
      // update button with Product data
      updateAddToCartButtonData(variation_data);
    } else {
      // disable
      $btnAddToCart.attr('disabled', '');
      // hide variation price
      hideVariationPrice();
      // reset variation price
      resetVariationPrice();
      // reset button data
      resetAddToCartButtonData();
    }
  }

  function updateVariationPrice(variation_data) {
    // const variation_data = getProductVariationDataBySelectedAttributes();
    $variationPrice.html(variation_data.price_html);
  }

  function resetVariationPrice() {
    $variationPrice.empty();
  }

  function showVariationPrice() {
    $variationPrice.css('display', 'block');
  }

  function hideVariationPrice() {
    $variationPrice.css('display', 'none');
  }

  function updateAddToCartButtonData(variation_data) {
    let product_id      = state.productId;
    // let variation_id   = getProductVariationIdBySelectedAttributes();
    let variation_id    = variation_data.variation_id;
    let quantity        = 1;
    let variation_attrs = JSON.stringify(getSelectedAttributes()); // array displayed on cart.php

    $btnAddToCart.attr('data-product-id', product_id);
    $btnAddToCart.attr('data-variation-id', variation_id);
    $btnAddToCart.attr('data-quantity', quantity);
    $btnAddToCart.attr('data-variation-data', variation_attrs);
  }

  function resetAddToCartButtonData() {
    $btnAddToCart.attr('data-product-id', '');
    $btnAddToCart.attr('data-variation-id', '');
    $btnAddToCart.attr('data-quantity', '0');
    $btnAddToCart.attr('data-variation-data', '');
  }

  function handleLicenseClick(data) {
    // reset existing product variations and license dialog
    reset();

    // get product variations
    $productVariations.toggleClass(CLASS_PRODUCT_VARS_LOAD);
    getProductVariations(data.id);

    // render product details
    renderProductDetails(data);

    // show();
  }

  function handleGetProductVariationsDone(data) {
    setVariationAttributes();
    $productVariations.toggleClass(CLASS_PRODUCT_VARS_LOAD);
    render(data);
  }

  function getSelectedAttributes() {
    const _attrs = {};
    for (const prop in state.selectedAttrs) {
      if (state.selectedAttrs.hasOwnProperty(prop)) {
        let _prop = prop.replace('select-', 'attribute_');
        _attrs[_prop] = state.selectedAttrs[prop];
      }
    }
    return _attrs;
  }

  /**
   * Set variation attributes for active products
   * Called in event handler for dependency on data from server
   */
  function setVariationAttributes() {
    let _attrs = collectAllProductVariationAttributes();
    let attrs = {};

    for (const prop in _attrs) {
      if (_attrs.hasOwnProperty(prop)) {
        attrs[prop.replace('attribute_', '')] = _attrs[prop];
      }
    }

    utils.setState({
      'variationAttrs' : attrs
    }, state);

    console.log('setVarAttrs', state);
  }

  /**
   * Get Product Variations
   *
   * @param {Number} id the Product ID
   */
  function getProductVariations(id) {
    jQuery.ajax({
      url      : ml_js_data.ajax_url,
      method   : 'POST',
      dataType : 'json',
      data     : {
        'action' : 'ml_get_product_variations',
        'id'     : id,
        'nonce'  : $nonceProduct
      }
    })
    .done(function(data, textStatus, jqXHR) {
      console.log('getProductVariations data', data);

      utils.setState({
        'productId'         : data.data.product_id,
        'termData'          : data.data.term_data,
        'variations'        : data.data.variations,
        'variationAttrsAll' : data.data.variation_attrs,
        'variationLabels'   : data.data.variation_labels
      }, state);

      events.trigger('getProductVariationsDone', state);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.log('getProductVariations fail', errorThrown);
    });
  }

  function getProductVariationDataBySelectedAttributes() {
    let index = collectProductVariationsByValues(state.selectedAttrs);
    let variation_data = state.variations[index];

    return variation_data || null;
  }

  function getProductVariationIdBySelectedAttributes() {
    let variation_data = getProductVariationDataBySelectedAttributes();
    let variation_id = variation_data.variation_id;

    return variation_id || null;
  }

  /**
   * Filter the Product Variations
   *
   * @param {object} selectedAttrs the selected attributes
   * @param {object} e the <select> `change` event
   */
  function productDrillDown(selectedAttrs, e) {

    console.log('productDrillDown', selectedAttrs);

    // collect all product variations that match value(s)
    let matches = null;       // Product Variation Indexes
    let allAttributes = null; // Product Variation Attributes of matched Product Variations
    let _selectedAttrs = {};  // !== null Selected Attributes

    for (const prop in selectedAttrs) {
      if (selectedAttrs.hasOwnProperty(prop) && selectedAttrs[prop] !== null) {
        _selectedAttrs[prop] = selectedAttrs[prop];
      }
    }

    matches = collectProductVariationsByValues(_selectedAttrs);

    // collect all product variation attributes
    if (matches && matches.length) {
      allAttributes = collectProductVariationAttributes(matches);
    } else {
      // no matching Product Variations
      // reset all <select>s except the last selected one
      resetSelectedAttributes(e.target.id);

      // search for Product Variations by the last selected <select>
      matches = collectProductVariationsByValues([state.selectedAttrs[e.target.id]]);
      allAttributes = collectProductVariationAttributes(matches);
    }

    // re-render available options
    updateVariationValues(allAttributes);
  }

  /**
   * Reset Selected Attributes
   *
   * @param {string} selectId the <select> ID to skip resetting
   */
  function resetSelectedAttributes(selectId) {
    for (const prop in state.selectedAttrs) {
      if (state.selectedAttrs.hasOwnProperty(prop)) {
        if (selectId === prop) {
          continue;
        } else {
          utils.setState({
            'selectedAttrs' : {
              ...state.selectedAttrs,
              [prop] : null
            }
          }, state)
        }
      }
    }

    $selects.each(function(index) {
      if (! this.id === selectId) {
        this.value = '';
      }
    });

    console.log('resetSelectedAttributes', state.selectedAttrs);
  }

  /**
   * Collect Product Variations by Values
   *
   * @param {object} values the values to search for
   * @return boolean|array Product Variation indexes matching test
   */
  function collectProductVariationsByValues(values) {
    console.log('collectProdVarsByVals', values);
    if (! state.variations) {
      return false;
    }

    let variationMatches = []; // variation index matches

    // search each product variation
    for (let i = 0; i < state.variations.length; i++) {
      let _attrs = state.variations[i].attributes;
      let attrMatches = 0; // how many matches found

      // search for each value in this variation's attributes
      for (const prop in values) {
        if (values.hasOwnProperty(prop)) {
          let _prop = prop.replace('select-', 'attribute_');

          if (_attrs[_prop] === "" || _attrs[_prop] === values[prop]) {
            attrMatches++;
          }
        }
      }

      // if all values have been found
      if (attrMatches === Object.keys(values).length) {
        // save this variation index to variationMatches
        variationMatches.push(i);
        continue; // break and search next product variation
      }
    } // variations

    return variationMatches;
  }

  /**
   * Collect Product Variations by Values
   *
   * @param {array} values the values to search for
   * @return boolean|array Product Variation indexes matching test
   */
  function collectProductVariationsByValuesOrig(values) {
    console.log('collectProdVarsByVals', values);
    if (! state.variations) {
      return false;
    }

    let variationMatches = []; // variation index matches

    // search each product variation
    for (let i = 0; i < state.variations.length; i++) {
      let _attrs = state.variations[i].attributes;
      let attrMatches = 0; // how many matches found

      // search for each value
      for (let j = 0; j < values.length; j++) {
        // search each attribute for each value
        // a `match` is === `value` or `any` (represented by an empty string)
        for (let prop in _attrs) {
          if (_attrs.hasOwnProperty(prop)
              && (values[j] === _attrs[prop] || "" === _attrs[prop])) {
            // increment attrMatches
            attrMatches++;
            break; // break _attr loop and search for next value;
          } // values[j] === attribute
        } // attributes

        // if all values have been found
        if (attrMatches === values.length) {
          // save this variation index to variationMatches
          variationMatches.push(i);
          break; // break and search next product variation
        }
      } // values
    } // variations

    return variationMatches;
  }

  /**
   * Collect Product Variations by Value
   *
   * @param array
   * @return boolean|array Product IDs matching test
   */
  function collectProductVariationsByValue(value) {
    console.log('collectProdVarsByVal', value);
    if (! state.variations) {
      return false;
    }

    let matches = [];

    for (let i = 0; i < state.variations.length; i++) {
      let _attrs = state.variations[i].attributes;

      for (let prop in _attrs) {
        console.log('prop in attrs', prop);
        if (value === _attrs[prop]) {
          console.log('value:', value, ' === ', _attrs[prop]);
          // save id to matches
          matches.push(i);
          // break
          break;
        }
      }
    }

    console.log('matches', matches);
    return matches;
  }

  /**
   * Collect All Product Variation Attributes by Product Variation IDs
   *
   * @param {array} productIds the Product Variation IDs
   * @return boolean|object Product Variations and Attributes matching test
   */
  function collectProductVariationAttributes(productIds) {
    let _productIds = productIds;
    let allAttributes = {};

    for (let i = 0; i < _productIds.length; i++) {
      let index = _productIds[i]; // the index of the matched Product Variation
      let _attrs = state.variations[index].attributes;

      // iterate through variation attributes object
      for (let prop in _attrs) {
        if (! _attrs.hasOwnProperty(prop)) {
          continue;
        }

        // check if key exists in allAttributes object
        if (! allAttributes[prop]) {
          // add it as new key and initialize it as array
          allAttributes[prop] = [];

          // if current value === `any/wildcard`, add all attributes from current key
          if (_attrs[prop] === "") {
            console.log('any/wildcard, adding all attributes from: ', prop, state.variationAttrs[prop.replace('attribute_', '')]);
            allAttributes[prop] = state.variationAttrs[prop.replace('attribute_', '')];
          }
          // add current value to array if truthy
          else if (_attrs[prop]) {
            console.log('_attrs[prop]', _attrs[prop]);
            allAttributes[prop].push(_attrs[prop]);
          }
        } else {
          // key already exists in allAttributes object
          // test for value uniqueness
          if (allAttributes[prop].length) {
            for (let j = 0, unique = true; j < allAttributes[prop].length; j++) {
              // if value is truthy and a duplicate, break
              if (_attrs[prop] && _attrs[prop] === allAttributes[prop][j]) {
                unique = false;
                break;
              }

              // if tested all items && unique == true
              if (j === allAttributes[prop].length - 1 && _attrs[prop] && unique === true) {
                console.log('unique! _attrs[prop]', _attrs[prop]);
                allAttributes[prop].push(_attrs[prop]);
              }
            }
          } else {
            // test if value is truthy and unique
            if (_attrs[prop] && _attrs[prop] !== allAttributes[prop][0]) {
              console.log('val is truthy', _attrs[prop]);
              allAttributes[prop].push(_attrs[prop]);
            }
          }
        }
      }

    }

    // Sort Attributes Alphabetically
    for (let prop in allAttributes) {
      if (allAttributes.hasOwnProperty(prop)) {
        allAttributes[prop].sort((a, b) => {
          let valueA = a.toUpperCase();
          let valueB = b.toUpperCase();

          if (valueA < valueB) {
            return -1;
          }
          if (valueB > valueA) {
            return 1;
          }
          return 0;
        });
      }
    }

    console.log('allAttributes', allAttributes);
    return allAttributes;
  }

  /**
   * Collect All Product Variation Attributes
   *
   * @return boolean|object Product Variations and Attributes matching test
   */
  function collectAllProductVariationAttributes() {
    console.log('collectAllProductVariationAttributes');

    let _productIds = state.variations.map((value, index) => index);
    let allAttributes = {};

    for (let i = 0; i < _productIds.length; i++) {
      let index = _productIds[i]; // the index of the matched Product Variation

      // iterate through variation attributes object
      let _attrs = state.variations[index].attributes;

      for (let prop in _attrs) {
        if (! _attrs.hasOwnProperty(prop)) {
          continue;
        }

        // check if key exists in allAttributes object
        if (! allAttributes[prop]) {
          // add it as new key and initialize it as array
          allAttributes[prop] = [];

          // add current value to array if truthy
          if (_attrs[prop]) {
            allAttributes[prop].push(_attrs[prop]);
          }
        } else {
          // key already exists in allAttributes object
          // test for value uniqueness
          if (allAttributes[prop].length) {
            for (let j = 0, unique = true; j < allAttributes[prop].length; j++) {
              // if value is truthy and a duplicate, break
              if (_attrs[prop] && _attrs[prop] === allAttributes[prop][j]) {
                unique = false;
                break;
              }

              // if tested all items && unique == true
              if (j === allAttributes[prop].length - 1 && _attrs[prop] && unique === true) {
                allAttributes[prop].push(_attrs[prop]);
              }
            }
          } else {
            // test if value is truthy and unique
            if (_attrs[prop] && _attrs[prop] !== allAttributes[prop][0]) {
              allAttributes[prop].push(_attrs[prop]);
            }
          }
        }
      }
    }

    // Sort Attributes
    for (let prop in allAttributes) {
      if (allAttributes.hasOwnProperty(prop)) {
        allAttributes[prop].sort((a, b) => {
          let valueA = a.toUpperCase();
          let valueB = b.toUpperCase();
          if (valueA < valueB) {
            return -1;
          }
          if (valueA > valueB) {
            return 1;
          }
          return 0;
        });
      }
    }

    return allAttributes;
  }

  /**
   * Render the Product Variation Attributes
   *
   * @param {object} data the data to render
   */
  function render(data) {
    for (const attr in data.variationAttrs) {
      if (! data.variationAttrs.hasOwnProperty(attr)) {
        continue;
      }

      // create a <div> for each attributes
      let div = document.createElement('div');
      div.className = 'select-wrap';

      // create a <label> for each attribute
      let label = document.createElement('label');
      label.htmlFor = 'select-' + attr;
      label.className = 'visuallyhidden';
      label.appendChild(document.createTextNode(data.variationLabels[attr]));

      // create a <select> for each attribute
      let select = document.createElement('select');
      select.id = 'select-' + attr;

      // create the default <option> for this <select>
      let optionDefault = document.createElement('option');
      optionDefault.value = '';
      optionDefault.appendChild(document.createTextNode(data.variationLabels[attr] + '...'));
      select.appendChild(optionDefault);

      // create an <option> for each term
      for (let i = 0, terms = data.variationAttrs[attr]; i < terms.length; i++) {
        let option = document.createElement('option');
        option.value = terms[i];
        option.appendChild(document.createTextNode(data.termData[terms[i]]));
        select.appendChild(option);
      }

      div.appendChild(label);
      div.appendChild(select);
      $productVariations.append(div);
      // $productVariations.append(select);
      // $selects = null;
      // $selects = $productVariations.find('select');
    }
    // cache <select> els
    $selects = $productVariations.find('select');
  }

  /**
   * Update Product Variation values
   * @param {object} attributes the Product Variation Attributes
   */
  function updateVariationValues(attributes) {
    for (const prop in attributes) {
      // get selectId
      let selectId = prop.replace('attribute_' ,'select-');

      // skip <select> els with value
      if (state.selectedAttrs[selectId]) {
        continue;
      }

      let $select = jQuery('#' + selectId);
      let defaultSelect = $select.get(0).options[0];

      $select.empty()
      $select.append(defaultSelect);

      // create an <option> for each term
      for (let j = 0, terms = attributes[prop]; j < terms.length; j++) {
        let option = document.createElement('option');
        option.value = terms[j];
        option.appendChild(document.createTextNode(state.termData[terms[j]]));
        $select.append(option);
      }
    }
  }

  /**
   * Reset variation values if all <select> elements are set to default values
   * @return {boolean} true if reset. false otherwise.
   */
  function maybeResetVariationValues() {
    console.log('maybeResetVariationValues');

    // check if all state.selectedAttrs are all null
    for (const prop in state.selectedAttrs) {
      // if a <select> has a value, don't reset
      if (state.selectedAttrs[prop] !== null) {
        return false;
      }
    }

    resetVariationValues();
    return true;
  }

  /**
   * Reset Variation values to their defaults
   */
  function resetVariationValues() {
    console.log('resetVariationValues');
    $productVariations.empty();
    render(state);
  }

  /**
   * Render Product details
   * @param {object} data the Product data
   */
  function renderProductDetails(data) {
    $songArtist.text(data.artist);
    $songImage.attr('src', data.image);
    $songImage.attr('alt', data.title);
    $songLink.attr('data-song-artist', data.artist);
    $songLink.attr('data-song-id', data.id);
    $songLink.attr('data-song-image', data.image);
    $songLink.attr('data-song-title', data.title);
    $songLink.attr('data-song-url', data.url);
    $songTitle.text(data.title);
  }

  /**
   * Reset License Dialog
   */
  function reset() {
    $songArtist.text('');
    $songImage.attr('src', '#');
    $songImage.attr('alt', '');
    $songLink.attr('data-song-artist', '');
    $songLink.attr('data-song-id', '');
    $songLink.attr('data-song-image', '');
    $songLink.attr('data-song-title', '');
    $songLink.attr('data-song-url', '');
    $songTitle.text('');
    $productVariations.empty();
    resetVariationPrice();

    utils.setState({
      'termData'          : null,
      'variations'        : null,
      'variationAttrsAll' : null,
      'variationLabels'   : null,
      'variationAttrs'    : null,
      'selectedAttrs'     : null,
      'individualType'    : false
    }, state);
  }

  return {
    init
  };

})();

export default licenseDialog;
