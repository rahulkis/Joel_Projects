function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Create the defaults once

  var pluginName = 'SUIAccordion',
      defaults = {}; // The actual plugin constructor

  function SUIAccordion(element, options) {
    this.element = element;
    this.$element = $(this.element);
    this.settings = $.extend({}, defaults, options);
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  } // Avoid Plugin.prototype conflicts


  $.extend(SUIAccordion.prototype, {
    init: function init() {
      var self = this;
      this.$element.on('click', 'div.sui-accordion-item-header, tr.sui-accordion-item', function (event) {
        var getItem = $(this).closest('.sui-accordion-item'),
            getContent = getItem.nextUntil('.sui-accordion-item').filter('.sui-accordion-item-content'),
            getParent = getItem.closest('.sui-accordion'),
            getChart = getItem.find('.sui-chartjs-animated');
        var clickedTarget = $(event.target);
        var flexHeader = $(this),
            flexItem = flexHeader.parent(),
            flexChart = flexItem.find('.sui-chartjs-animated'),
            flexParent = flexItem.parent(),
            flexContent = flexHeader.next('.sui-accordion-item-body').find(' .sui-box');
        var tableItem = $(this),
            tableContent = tableItem.nextUntil('.sui-accordion-item').filter('.sui-accordion-item-content'),
            tableBox = tableContent.find('.sui-box');
        var button = $(this).find('.sui-accordion-open-indicator > .sui-screen-reader-text'),
            buttonText = button === null || button === void 0 ? void 0 : button.text(),
            dataContent = button === null || button === void 0 ? void 0 : button.data('content');

        if (clickedTarget.closest('.sui-accordion-item-action').length) {
          return true;
        } // CHECK: Flexbox


        if (flexHeader.hasClass('sui-accordion-item-header')) {
          if (flexItem.hasClass('sui-accordion-item--disabled')) {
            flexItem.removeClass('sui-accordion-item--open');
          } else {
            if (flexItem.hasClass('sui-accordion-item--open')) {
              flexItem.removeClass('sui-accordion-item--open');
            } else {
              flexItem.addClass('sui-accordion-item--open');
              flexContent.attr('tabindex', '0').trigger('focus');
            }
          } // CHECK: Accordion Blocks


          if (flexParent.hasClass('sui-accordion-block') && 0 !== flexChart.length) {
            flexItem.find('.sui-accordion-item-data').addClass('sui-onload');
            flexChart.removeClass('sui-chartjs-loaded');

            if (flexItem.hasClass('sui-accordion-item--open')) {
              setTimeout(function () {
                flexItem.find('.sui-accordion-item-data').removeClass('sui-onload');
                flexChart.addClass('sui-chartjs-loaded');
              }, 1200);
            }
          }
        } // CHECK: Table


        if (tableItem.hasClass('sui-accordion-item')) {
          if (tableItem.hasClass('sui-accordion-item--disabled')) {
            tableContent.removeClass('sui-accordion-item--open');
          } else {
            if (tableItem.hasClass('sui-accordion-item--open')) {
              tableItem.removeClass('sui-accordion-item--open');
              tableContent.removeClass('sui-accordion-item--open');
            } else {
              tableItem.addClass('sui-accordion-item--open');
              tableContent.addClass('sui-accordion-item--open');
              tableBox.attr('tabindex', '0').trigger('focus');
            }
          }
        } // Change button accessiblity content based on accordin open and close.


        if (dataContent) {
          button.html(dataContent);
          button.data('content', buttonText);
        }

        event.stopPropagation();
      });
    }
  }); // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations

  $.fn[pluginName] = function (options) {
    return this.each(function () {
      // instance of SUIAccordion can be called with $(element).data('SUIAccordion')
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new SUIAccordion(this, options));
      }
    });
  };
})(jQuery, window, document);

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.suiAccordion = function (el) {
    var accordionTable = $(el);

    function init() {
      accordionTable.SUIAccordion({});
    }

    init();
    return this;
  };

  if (0 !== $('.sui-2-12-13 .sui-accordion').length) {
    $('.sui-2-12-13 .sui-accordion').each(function () {
      SUI.suiAccordion(this);
    });
  }
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;

(function ($, ClipboardJS, window, document, undefined) {
  'use strict'; // undefined is used here as the undefined global variable in ECMAScript 3 is
  // mutable (ie. it can be changed by someone else). undefined isn't really being
  // passed in so we can ensure the value of it is truly undefined. In ES5, undefined
  // can no longer be modified.
  // window and document are passed through as local variables rather than global
  // as this (slightly) quickens the resolution process and can be more efficiently
  // minified (especially when both are regularly referenced in your plugin).
  // Create the defaults once

  var pluginName = 'SUICodeSnippet',
      defaults = {
    copyText: 'Copy',
    copiedText: 'Copied!'
  }; // The actual plugin constructor

  function SUICodeSnippet(element, options) {
    this.element = element;
    this.$element = $(this.element); // jQuery has an extend method which merges the contents of two or
    // more objects, storing the result in the first object. The first object
    // is generally empty as we don't want to alter the default options for
    // future instances of the plugin

    this.settings = $.extend({}, defaults, options);
    this._defaults = defaults;
    this._name = pluginName;
    this._clipboardJs = null;
    this._clipboardId = '';
    this.init();
  } // Avoid Plugin.prototype conflicts


  $.extend(SUICodeSnippet.prototype, {
    init: function init() {
      var self = this,
          button = ''; // check if its already wrapped

      if (0 === this.$element.parent('sui-code-snippet-wrapper').length) {
        // build markup
        this.$element.wrap('<div class="sui-code-snippet-wrapper"></div>');
        this._clipboardId = this.generateUniqueId();
        button = '<button type="button" class="sui-button" id="sui-code-snippet-button-' + this._clipboardId + '" data-clipboard-target="#sui-code-snippet-' + this._clipboardId + '">' + this.settings.copyText + '</button>';
        this.$element.attr('id', 'sui-code-snippet-' + this._clipboardId).after(button);
        this._clipboardJs = new ClipboardJS('#sui-code-snippet-button-' + this._clipboardId); // attach events

        this._clipboardJs.on('success', function (e) {
          e.clearSelection();
          self.showTooltip(e.trigger, self.settings.copiedText);
        });

        $('#sui-code-snippet-button-' + this._clipboardId).on('mouseleave.SUICodeSnippet', function () {
          $(this).removeClass('sui-tooltip');
          $(this).removeAttr('aria-label');
          $(this).removeAttr('data-tooltip');
        });
      }
    },
    getClipboardJs: function getClipboardJs() {
      return this._clipboardJs;
    },
    showTooltip: function showTooltip(e, msg) {
      $(e).addClass('sui-tooltip');
      $(e).attr('aria-label', msg);
      $(e).attr('data-tooltip', msg);
    },
    generateUniqueId: function generateUniqueId() {
      // Math.random should be unique because of its seeding algorithm.
      // Convert it to base 36 (numbers + letters), and grab the first 9 characters
      // after the decimal.
      return '_' + Math.random().toString(36).substr(2, 9);
    },
    destroy: function destroy() {
      if (null !== this._clipboardJs) {
        this._clipboardJs.destroy();

        this.$element.attr('id', '');
        this.$element.unwrap('.sui-code-snippet-wrapper');
        $('#sui-code-snippet-button-' + this._clipboardId).remove();
      }
    }
  }); // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations

  $.fn[pluginName] = function (options) {
    return this.each(function () {
      // instance of SUICodeSnippet can be called with $(element).data('SUICodeSnippet')
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new SUICodeSnippet(this, options));
      }
    });
  };
})(jQuery, ClipboardJS, window, document);

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.suiCodeSnippet = function () {
    // Convert all code snippet.
    $('.sui-2-12-13 .sui-code-snippet:not(.sui-no-copy)').each(function () {
      // backward compat of instantiate new accordion
      $(this).SUICodeSnippet({});
    });
  }; // wait document ready first


  $(document).ready(function () {
    SUI.suiCodeSnippet();
  });
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.sliderBack = function (el) {
    var slider = $(el),
        dialog = slider.closest('.sui-dialog'),
        slides = slider.find('.sui-slider-content > li');
    var navigation = slider.find('.sui-slider-navigation'),
        navButtons = navigation.find('button'),
        btnBack = navigation.find('.sui-prev'),
        btnNext = navigation.find('.sui-next');

    if (!dialog.hasClass('sui-dialog-onboard')) {
      return;
    }

    function init() {
      var currSlide = slider.find('.sui-slider-content > li.sui-current'),
          prevSlide = currSlide.prev();

      if (!prevSlide.length) {
        if (slider.hasClass('sui-infinite')) {
          prevSlide = slider.find('.sui-slider-content > li:last');
          currSlide.removeClass('sui-current');
          currSlide.removeClass('sui-loaded');
          prevSlide.addClass('sui-current');
          prevSlide.addClass('fadeInLeft');
          navButtons.prop('disabled', true);
          setTimeout(function () {
            prevSlide.addClass('sui-loaded');
            prevSlide.removeClass('fadeInLeft');
          }, 600);
          setTimeout(function () {
            navButtons.prop('disabled', false);
          }, 650);
        }
      } else {
        currSlide.removeClass('sui-current');
        currSlide.removeClass('sui-loaded');
        prevSlide.addClass('sui-current');
        prevSlide.addClass('fadeInLeft');
        navButtons.prop('disabled', true);

        if (!slider.hasClass('sui-infinite')) {
          btnNext.removeClass('sui-hidden');

          if (slides.first().data('slide') === prevSlide.data('slide')) {
            btnBack.addClass('sui-hidden');
          }
        }

        setTimeout(function () {
          prevSlide.addClass('sui-loaded');
          prevSlide.removeClass('fadeInLeft');
        }, 600);
        setTimeout(function () {
          navButtons.prop('disabled', false);
        }, 650);
      }
    }

    init();
    return this;
  };

  SUI.sliderNext = function (el) {
    var slider = $(el),
        dialog = slider.closest('.sui-dialog'),
        slides = slider.find('.sui-slider-content > li');
    var navigation = slider.find('.sui-slider-navigation'),
        navButtons = navigation.find('button'),
        btnBack = navigation.find('.sui-prev'),
        btnNext = navigation.find('.sui-next');

    if (!dialog.hasClass('sui-dialog-onboard')) {
      return;
    }

    function init() {
      var currSlide = slider.find('.sui-slider-content > li.sui-current'),
          nextSlide = currSlide.next();

      if (!nextSlide.length) {
        if (slider.hasClass('sui-infinite')) {
          nextSlide = slider.find('.sui-slider-content > li:first');
          currSlide.removeClass('sui-current');
          currSlide.removeClass('sui-loaded');
          nextSlide.addClass('sui-current');
          nextSlide.addClass('fadeInRight');
          navButtons.prop('disabled', true);
          setTimeout(function () {
            nextSlide.addClass('sui-loaded');
            nextSlide.removeClass('fadeInRight');
          }, 600);
          setTimeout(function () {
            navButtons.prop('disabled', false);
          }, 650);
        }
      } else {
        currSlide.removeClass('sui-current');
        currSlide.removeClass('sui-loaded');
        nextSlide.addClass('sui-current');
        nextSlide.addClass('fadeInRight');
        navButtons.prop('disabled', true);

        if (!slider.hasClass('sui-infinite')) {
          btnBack.removeClass('sui-hidden');

          if (slides.length === nextSlide.data('slide')) {
            btnNext.addClass('sui-hidden');
          }
        }

        setTimeout(function () {
          nextSlide.addClass('sui-loaded');
          nextSlide.removeClass('fadeInRight');
        }, 600);
        setTimeout(function () {
          navButtons.prop('disabled', false);
        }, 650);
      }
    }

    init();
    return this;
  };

  SUI.sliderStep = function (el) {
    var slider = $(el),
        dialog = slider.closest('.sui-dialog');
    var slides = slider.find('.sui-slider-content'),
        slide = slides.find('> li');
    var steps = slider.find('.sui-slider-steps'),
        step = steps.find('li'),
        button = step.find('button');
    var navigation = slider.find('.sui-slider-navigation'),
        navButtons = navigation.find('button'),
        navBack = navigation.find('.sui-prev'),
        navNext = navigation.find('.sui-next');

    if (!dialog.hasClass('sui-dialog-onboard') && !steps.hasClass('sui-clickable')) {
      return;
    }

    function reset() {
      // Remove current class
      slide.removeClass('sui-current'); // Remove loaded state

      slide.removeClass('sui-loaded');
    }

    function load(element) {
      var button = $(element),
          index = button.data('slide');
      var curSlide = button.closest('li[data-slide]'),
          newSlide = slides.find('> li[data-slide="' + index + '"]');
      newSlide.addClass('sui-current');

      if (curSlide.data('slide') < newSlide.data('slide')) {
        newSlide.addClass('fadeInRight');
      } else {
        newSlide.addClass('fadeInLeft');
      }

      navButtons.prop('disabled', true);

      if (!slider.hasClass('sui-infinite')) {
        if (1 === newSlide.data('slide')) {
          navBack.addClass('sui-hidden');
          navNext.removeClass('sui-hidden');
        }

        if (slide.length === newSlide.data('slide')) {
          navBack.removeClass('sui-hidden');
          navNext.addClass('sui-hidden');
        }
      }

      setTimeout(function () {
        newSlide.addClass('sui-loaded');

        if (curSlide.data('slide') < newSlide.data('slide')) {
          newSlide.removeClass('fadeInRight');
        } else {
          newSlide.removeClass('fadeInLeft');
        }
      }, 600);
      setTimeout(function () {
        navButtons.prop('disabled', false);
      }, 650);
    }

    function init() {
      if (button.length) {
        button.on('click', function (e) {
          reset();
          load(this);
          e.preventDefault();
          e.stopPropagation();
        });
      }
    }

    init();
    return this;
  };

  SUI.dialogSlider = function (el) {
    var slider = $(el),
        dialog = slider.closest('.sui-dialog'),
        btnBack = slider.find('.sui-slider-navigation .sui-prev'),
        btnNext = slider.find('.sui-slider-navigation .sui-next'),
        tourBack = slider.find('*[data-a11y-dialog-tour-back]'),
        tourNext = slider.find('*[data-a11y-dialog-tour-next]'),
        steps = slider.find('.sui-slider-steps');

    if (!dialog.hasClass('sui-dialog-onboard') || slider.hasClass('sui-slider-off')) {
      return;
    }

    function init() {
      if (btnBack.length) {
        btnBack.on('click', function (e) {
          SUI.sliderBack(slider);
          e.preventDefault();
        });
      }

      if (tourBack.length) {
        tourBack.on('click', function (e) {
          SUI.sliderBack(slider);
          e.preventDefault();
        });
      }

      if (btnNext.length) {
        btnNext.on('click', function (e) {
          SUI.sliderNext(slider);
          e.preventDefault();
        });
      }

      if (tourNext.length) {
        tourNext.on('click', function (e) {
          SUI.sliderNext(slider);
          e.preventDefault();
        });
      }

      if (steps.length) {
        SUI.sliderStep(slider);
      }
    }

    init();
    return this;
  };

  $('.sui-2-12-13 .sui-slider').each(function () {
    SUI.dialogSlider(this);
  });
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.linkDropdown = function () {
    function closeAllDropdowns($except) {
      var $dropdowns = $('.sui-2-12-13 .sui-dropdown');

      if ($except) {
        $dropdowns = $dropdowns.not($except);
      }

      $dropdowns.removeClass('open');
    }

    $('body').on('click', '.sui-dropdown-anchor', function (e) {
      var $button = $(this),
          $parent = $button.parent();
      closeAllDropdowns($parent);

      if ($parent.hasClass('sui-dropdown')) {
        $parent.toggleClass('open');
      }

      e.preventDefault();
    });
    $('body').on('mouseup', function (e) {
      var $anchor = $('.sui-2-12-13 .sui-dropdown-anchor');

      if (!$anchor.is(e.target) && 0 === $anchor.has(e.target).length) {
        closeAllDropdowns();
      }
    });
  };

  SUI.linkDropdown();
})(jQuery);;
// This file is to be used for fixing up issues with IE11.
(function ($) {
  var colorpickers = $('.sui-colorpicker-wrap'); // If IE11 remove SUI colorpicker styles.

  if (!!navigator.userAgent.match(/Trident\/7\./) && colorpickers[0]) {
    colorpickers.find('.sui-colorpicker').hide();
    colorpickers.removeClass('sui-colorpicker-wrap');
  }
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function () {
  // Enable strict mode.
  'use strict';

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }
  /**
   * @namespace aria
   */


  var aria = aria || {}; // REF: Key codes.

  aria.KeyCode = {
    BACKSPACE: 8,
    TAB: 9,
    RETURN: 13,
    ESC: 27,
    SPACE: 32,
    PAGE_UP: 33,
    PAGE_DOWN: 34,
    END: 35,
    HOME: 36,
    LEFT: 37,
    UP: 38,
    RIGHT: 39,
    DOWN: 40,
    DELETE: 46
  };
  aria.Utils = aria.Utils || {}; // UTILS: Remove function.

  aria.Utils.remove = function (item) {
    if (item.remove && 'function' === typeof item.remove) {
      return item.remove();
    }

    if (item.parentNode && item.parentNode.removeChild && 'function' === typeof item.parentNode.removeChild) {
      return item.parentNode.removeChild(item);
    }

    return false;
  }; // UTILS: Verify if element can be focused.


  aria.Utils.isFocusable = function (element) {
    if (0 < element.tabIndex || 0 === element.tabIndex && null !== element.getAttribute('tabIndex')) {
      return true;
    }

    if (element.disabled) {
      return false;
    }

    switch (element.nodeName) {
      case 'A':
        return !!element.href && 'ignore' != element.rel;

      case 'INPUT':
        return 'hidden' != element.type && 'file' != element.type;

      case 'BUTTON':
      case 'SELECT':
      case 'TEXTAREA':
        return true;

      default:
        return false;
    }
  };
  /**
   * Simulate a click event.
   * @public
   * @param {Element} element the element to simulate a click on
   */


  aria.Utils.simulateClick = function (element) {
    // Create our event (with options)
    var evt = new MouseEvent('click', {
      bubbles: true,
      cancelable: true,
      view: window
    }); // If cancelled, don't dispatch our event

    var canceled = !element.dispatchEvent(evt);
  }; // When util functions move focus around, set this true so
  // the focus listener can ignore the events.


  aria.Utils.IgnoreUtilFocusChanges = false;
  aria.Utils.dialogOpenClass = 'sui-has-modal';
  /**
   * @desc Set focus on descendant nodes until the first
   * focusable element is found.
   *
   * @param element
   * DOM node for which to find the first focusable descendant.
   *
   * @returns
   * true if a focusable element is found and focus is set.
   */

  aria.Utils.focusFirstDescendant = function (element) {
    for (var i = 0; i < element.childNodes.length; i++) {
      var child = element.childNodes[i];

      if (aria.Utils.attemptFocus(child) || aria.Utils.focusFirstDescendant(child)) {
        return true;
      }
    }

    return false;
  }; // end focusFirstDescendant

  /**
   * @desc Find the last descendant node that is focusable.
   *
   * @param element
   * DOM node for which to find the last focusable descendant.
   *
   * @returns
   * true if a focusable element is found and focus is set.
   */


  aria.Utils.focusLastDescendant = function (element) {
    for (var i = element.childNodes.length - 1; 0 <= i; i--) {
      var child = element.childNodes[i];

      if (aria.Utils.attemptFocus(child) || aria.Utils.focusLastDescendant(child)) {
        return true;
      }
    }

    return false;
  }; // end focusLastDescendant

  /**
   * @desc Set Attempt to set focus on the current node.
   *
   * @param element
   * The node to attempt to focus on.
   *
   * @returns
   * true if element is focused.
   */


  aria.Utils.attemptFocus = function (element) {
    if (!aria.Utils.isFocusable(element)) {
      return false;
    }

    aria.Utils.IgnoreUtilFocusChanges = true;

    try {
      element.focus();
    } catch (e) {// Done.
    }

    aria.Utils.IgnoreUtilFocusChanges = false;
    return document.activeElement === element;
  }; // end attemptFocus
  // Modals can open modals. Keep track of them with this array.


  aria.OpenDialogList = aria.OpenDialogList || new Array(0);
  /**
   * @returns the last opened dialog (the current dialog)
   */

  aria.getCurrentDialog = function () {
    if (aria.OpenDialogList && aria.OpenDialogList.length) {
      return aria.OpenDialogList[aria.OpenDialogList.length - 1];
    }
  };

  aria.closeCurrentDialog = function () {
    var currentDialog = aria.getCurrentDialog();

    if (currentDialog) {
      currentDialog.close();
      return true;
    }

    return false;
  };

  aria.handleEscape = function (event) {
    var key = event.which || event.keyCode;

    if (key === aria.KeyCode.ESC && aria.closeCurrentDialog()) {
      event.stopPropagation();
    }
  };
  /**
   * @constructor
   * @desc Dialog object providing modal focus management.
   *
   * Assumptions: The element serving as the dialog container is present in the
   * DOM and hidden. The dialog container has role='dialog'.
   *
   * @param dialogId
   * The ID of the element serving as the dialog container.
   *
   * @param focusAfterClosed
   * Either the DOM node or the ID of the DOM node to focus when the
   * dialog closes.
   *
   * @param focusFirst
   * Optional parameter containing either the DOM node or the ID of the
   * DOM node to focus when the dialog opens. If not specified, the
   * first focusable element in the dialog will receive focus.
   *
   * @param hasOverlayMask
   * Optional boolean parameter that when is set to "true" will enable
   * a clickable overlay mask. This mask will fire close modal function
   * when you click on it.
   *
   * @param isCloseOnEsc
   * Default: true
   * Optional boolean parameter that when it's set to "true", it will enable closing the
   * dialog with the Esc key.
   *
   * @param isAnimated
   * Default: true
   * Optional boolean parameter that when it's set to "true", it will enable animation in dialog box.
   */


  aria.Dialog = function (dialogId, focusAfterClosed, focusFirst, hasOverlayMask) {
    var isCloseOnEsc = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;
    var isAnimated = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : true;
    this.dialogNode = document.getElementById(dialogId);

    if (null === this.dialogNode) {
      throw new Error('No element found with id="' + dialogId + '".');
    }

    var validRoles = ['dialog', 'alertdialog'];
    var isDialog = (this.dialogNode.getAttribute('role') || '').trim().split(/\s+/g).some(function (token) {
      return validRoles.some(function (role) {
        return token === role;
      });
    });

    if (!isDialog) {
      throw new Error('Dialog() requires a DOM element with ARIA role of dialog or alertdialog.');
    }

    this.isCloseOnEsc = isCloseOnEsc; // Trigger the 'open' event at the beginning of the opening process.
    // After validating the modal's attributes.

    var openEvent = new Event('open');
    this.dialogNode.dispatchEvent(openEvent); // Wrap in an individual backdrop element if one doesn't exist
    // Native <dialog> elements use the ::backdrop pseudo-element, which
    // works similarly.

    var backdropClass = 'sui-modal';

    if (this.dialogNode.parentNode.classList.contains(backdropClass)) {
      this.backdropNode = this.dialogNode.parentNode;
    } else {
      this.backdropNode = document.createElement('div');
      this.backdropNode.className = backdropClass;
      this.backdropNode.setAttribute('data-markup', 'new');
      this.dialogNode.parentNode.insertBefore(this.backdropNode, this.dialogNodev);
      this.backdropNode.appendChild(this.dialogNode);
    }

    this.backdropNode.classList.add('sui-active'); // Disable scroll on the body element

    document.body.parentNode.classList.add(aria.Utils.dialogOpenClass);

    if ('string' === typeof focusAfterClosed) {
      this.focusAfterClosed = document.getElementById(focusAfterClosed);
    } else if ('object' === _typeof(focusAfterClosed)) {
      this.focusAfterClosed = focusAfterClosed;
    } else {
      throw new Error('the focusAfterClosed parameter is required for the aria.Dialog constructor.');
    }

    if ('string' === typeof focusFirst) {
      this.focusFirst = document.getElementById(focusFirst);
    } else if ('object' === _typeof(focusFirst)) {
      this.focusFirst = focusFirst;
    } else {
      this.focusFirst = null;
    } // Bracket the dialog node with two invisible, focusable nodes.
    // While this dialog is open, we use these to make sure that focus never
    // leaves the document even if dialogNode is the first or last node.


    var preDiv = document.createElement('div');
    this.preNode = this.dialogNode.parentNode.insertBefore(preDiv, this.dialogNode);
    this.preNode.tabIndex = 0;

    if ('boolean' === typeof hasOverlayMask && true === hasOverlayMask) {
      this.preNode.classList.add('sui-modal-overlay');

      this.preNode.onclick = function () {
        aria.getCurrentDialog().close();
      };
    }

    var postDiv = document.createElement('div');
    this.postNode = this.dialogNode.parentNode.insertBefore(postDiv, this.dialogNode.nextSibling);
    this.postNode.tabIndex = 0; // If this modal is opening on top of one that is already open,
    // get rid of the document focus listener of the open dialog.

    if (0 < aria.OpenDialogList.length) {
      aria.getCurrentDialog().removeListeners();
    }

    this.addListeners();
    aria.OpenDialogList.push(this); // If isAnimated is set true then modal box will animate.

    if (isAnimated) {
      this.dialogNode.classList.add('sui-content-fade-in'); // make visible

      this.dialogNode.classList.remove('sui-content-fade-out');
    } else {
      this.dialogNode.classList.remove('sui-content-fade-in');
      this.dialogNode.classList.remove('sui-content-fade-out');
    }

    if (this.focusFirst) {
      this.focusFirst.focus();
    } else {
      aria.Utils.focusFirstDescendant(this.dialogNode);
    }

    this.lastFocus = document.activeElement; // Trigger the 'afteropen' event at the end of the opening process.

    var afterOpenEvent = new Event('afterOpen');
    this.dialogNode.dispatchEvent(afterOpenEvent);
  }; // end Dialog constructor.

  /**
   * @desc Hides the current top dialog, removes listeners of the top dialog,
   * restore listeners of a parent dialog if one was open under the one that
   * just closed, and sets focus on the element specified for focusAfterClosed.
   *
   * @param isAnimated
   * Default: true
   * Optional boolean parameter that when it's set to "true", it will enable animation in dialog box.
   */


  aria.Dialog.prototype.close = function () {
    var isAnimated = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
    var self = this; // Trigger the 'close' event at the beginning of the closing process.

    var closeEvent = new Event('close');
    this.dialogNode.dispatchEvent(closeEvent);
    aria.OpenDialogList.pop();
    this.removeListeners();
    this.preNode.parentNode.removeChild(this.preNode);
    this.postNode.parentNode.removeChild(this.postNode); // If isAnimated is set true then modal box will animate.

    if (isAnimated) {
      this.dialogNode.classList.add('sui-content-fade-out');
      this.dialogNode.classList.remove('sui-content-fade-in');
    } else {
      this.dialogNode.classList.remove('sui-content-fade-in');
      this.dialogNode.classList.remove('sui-content-fade-out');
    }

    this.focusAfterClosed.focus();
    setTimeout(function () {
      self.backdropNode.classList.remove('sui-active');
    }, 300);
    setTimeout(function () {
      var slides = self.dialogNode.querySelectorAll('.sui-modal-slide');

      if (0 < slides.length) {
        // Hide all slides.
        for (var i = 0; i < slides.length; i++) {
          slides[i].setAttribute('disabled', true);
          slides[i].classList.remove('sui-loaded');
          slides[i].classList.remove('sui-active');
          slides[i].setAttribute('tabindex', '-1');
          slides[i].setAttribute('aria-hidden', true);
        } // Change modal size.


        if (slides[0].hasAttribute('data-modal-size')) {
          var newDialogSize = slides[0].getAttribute('data-modal-size');

          switch (newDialogSize) {
            case 'sm':
            case 'small':
              newDialogSize = 'sm';
              break;

            case 'md':
            case 'med':
            case 'medium':
              newDialogSize = 'md';
              break;

            case 'lg':
            case 'large':
              newDialogSize = 'lg';
              break;

            case 'xl':
            case 'extralarge':
            case 'extraLarge':
            case 'extra-large':
              newDialogSize = 'xl';
              break;

            default:
              newDialogSize = undefined;
          }

          if (undefined !== newDialogSize) {
            // Remove others sizes from dialog to prevent any conflicts with styles.
            self.dialogNode.parentNode.classList.remove('sui-modal-sm');
            self.dialogNode.parentNode.classList.remove('sui-modal-md');
            self.dialogNode.parentNode.classList.remove('sui-modal-lg');
            self.dialogNode.parentNode.classList.remove('sui-modal-xl'); // Apply the new size to dialog.

            self.dialogNode.parentNode.classList.add('sui-modal-' + newDialogSize);
          }
        } // Show first slide.


        slides[0].classList.add('sui-active');
        slides[0].classList.add('sui-loaded');
        slides[0].removeAttribute('disabled');
        slides[0].removeAttribute('tabindex');
        slides[0].removeAttribute('aria-hidden'); // Change modal label.

        if (slides[0].hasAttribute('data-modal-labelledby')) {
          var newDialogLabel, getDialogLabel;
          newDialogLabel = '';
          getDialogLabel = slides[0].getAttribute('data-modal-labelledby');

          if ('' !== getDialogLabel || undefined !== getDialogLabel) {
            newDialogLabel = getDialogLabel;
          }

          self.dialogNode.setAttribute('aria-labelledby', newDialogLabel);
        } // Change modal description.


        if (slides[0].hasAttribute('data-modal-describedby')) {
          var newDialogDesc, getDialogDesc;
          newDialogDesc = '';
          getDialogDesc = slides[0].getAttribute('data-modal-describedby');

          if ('' !== getDialogDesc || undefined !== getDialogDesc) {
            newDialogDesc = getDialogDesc;
          }

          self.dialogNode.setAttribute('aria-describedby', newDialogDesc);
        }
      }
    }, 350); // If a dialog was open underneath this one, restore its listeners.

    if (0 < aria.OpenDialogList.length) {
      aria.getCurrentDialog().addListeners();
    } else {
      document.body.parentNode.classList.remove(aria.Utils.dialogOpenClass);
    } // Trigger the 'afterclose' event at the end of the closing process.


    var afterCloseEvent = new Event('afterClose');
    this.dialogNode.dispatchEvent(afterCloseEvent);
  }; // end close.

  /**
   * @desc Hides the current dialog and replaces it with another.
   *
   * @param newDialogId
   * ID of the dialog that will replace the currently open top dialog.
   *
   * @param newFocusAfterClosed
   * Optional ID or DOM node specifying where to place focus when the new dialog closes.
   * If not specified, focus will be placed on the element specified by the dialog being replaced.
   *
   * @param newFocusFirst
   * Optional ID or DOM node specifying where to place focus in the new dialog when it opens.
   * If not specified, the first focusable element will receive focus.
   *
   * @param hasOverlayMask
   * Optional boolean parameter that when is set to "true" will enable a clickable overlay
   * mask to the new opened dialog. This mask will fire close dialog function when you click it.
   *
   * @param isCloseOnEsc
   * Default: true
   * Optional boolean parameter that when it's set to "true", it will enable closing the
   * dialog with the Esc key.
   *
   * @param isAnimated
   * Default: true
   * Optional boolean parameter that when it's set to "true", it will enable animation in dialog box.
   */


  aria.Dialog.prototype.replace = function (newDialogId, newFocusAfterClosed, newFocusFirst, hasOverlayMask) {
    var isCloseOnEsc = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;
    var isAnimated = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : true;
    var self = this;
    aria.OpenDialogList.pop();
    this.removeListeners();
    aria.Utils.remove(this.preNode);
    aria.Utils.remove(this.postNode); // If isAnimated is set true then modal box will animate.

    if (isAnimated) {
      this.dialogNode.classList.add('sui-content-fade-in'); // make visible

      this.dialogNode.classList.remove('sui-content-fade-out');
    } else {
      this.dialogNode.classList.remove('sui-content-fade-in');
      this.dialogNode.classList.remove('sui-content-fade-out');
    }

    this.backdropNode.classList.remove('sui-active');
    setTimeout(function () {
      var slides = self.dialogNode.querySelectorAll('.sui-modal-slide');

      if (0 < slides.length) {
        // Hide all slides.
        for (var i = 0; i < slides.length; i++) {
          slides[i].setAttribute('disabled', true);
          slides[i].classList.remove('sui-loaded');
          slides[i].classList.remove('sui-active');
          slides[i].setAttribute('tabindex', '-1');
          slides[i].setAttribute('aria-hidden', true);
        } // Change modal size.


        if (slides[0].hasAttribute('data-modal-size')) {
          var newDialogSize = slides[0].getAttribute('data-modal-size');

          switch (newDialogSize) {
            case 'sm':
            case 'small':
              newDialogSize = 'sm';
              break;

            case 'md':
            case 'med':
            case 'medium':
              newDialogSize = 'md';
              break;

            case 'lg':
            case 'large':
              newDialogSize = 'lg';
              break;

            case 'xl':
            case 'extralarge':
            case 'extraLarge':
            case 'extra-large':
              newDialogSize = 'xl';
              break;

            default:
              newDialogSize = undefined;
          }

          if (undefined !== newDialogSize) {
            // Remove others sizes from dialog to prevent any conflicts with styles.
            self.dialogNode.parentNode.classList.remove('sui-modal-sm');
            self.dialogNode.parentNode.classList.remove('sui-modal-md');
            self.dialogNode.parentNode.classList.remove('sui-modal-lg');
            self.dialogNode.parentNode.classList.remove('sui-modal-xl'); // Apply the new size to dialog.

            self.dialogNode.parentNode.classList.add('sui-modal-' + newDialogSize);
          }
        } // Show first slide.


        slides[0].classList.add('sui-active');
        slides[0].classList.add('sui-loaded');
        slides[0].removeAttribute('disabled');
        slides[0].removeAttribute('tabindex');
        slides[0].removeAttribute('aria-hidden'); // Change modal label.

        if (slides[0].hasAttribute('data-modal-labelledby')) {
          var newDialogLabel, getDialogLabel;
          newDialogLabel = '';
          getDialogLabel = slides[0].getAttribute('data-modal-labelledby');

          if ('' !== getDialogLabel || undefined !== getDialogLabel) {
            newDialogLabel = getDialogLabel;
          }

          self.dialogNode.setAttribute('aria-labelledby', newDialogLabel);
        } // Change modal description.


        if (slides[0].hasAttribute('data-modal-describedby')) {
          var newDialogDesc, getDialogDesc;
          newDialogDesc = '';
          getDialogDesc = slides[0].getAttribute('data-modal-describedby');

          if ('' !== getDialogDesc || undefined !== getDialogDesc) {
            newDialogDesc = getDialogDesc;
          }

          self.dialogNode.setAttribute('aria-describedby', newDialogDesc);
        }
      }
    }, 350);
    var focusAfterClosed = newFocusAfterClosed || this.focusAfterClosed;
    var dialog = new aria.Dialog(newDialogId, focusAfterClosed, newFocusFirst, hasOverlayMask, isCloseOnEsc, isAnimated);
  }; // end replace

  /**
   * @desc Uses the same dialog to display different content that will slide to show.
   *
   * @param newSlideId
   * ID of the slide that will replace the currently active slide content.
   *
   * @param newSlideFocus
   * Optional ID or DOM node specifying where to place focus in the new slide when it shows.
   * If not specified, the first focusable element will receive focus.
   *
   * @param newSlideEntrance
   * Determine if the new slide will show up from "left" or "right" of the screen.
   * If not specified, the slide entrance animation will be "fade in".
   */


  aria.Dialog.prototype.slide = function (newSlideId, newSlideFocus, newSlideEntrance) {
    var animation = 'sui-fadein',
        currentDialog = aria.getCurrentDialog(),
        getAllSlides = this.dialogNode.querySelectorAll('.sui-modal-slide'),
        getNewSlide = document.getElementById(newSlideId);

    switch (newSlideEntrance) {
      case 'back':
      case 'left':
        animation = 'sui-fadein-left';
        break;

      case 'next':
      case 'right':
        animation = 'sui-fadein-right';
        break;

      default:
        animation = 'sui-fadein';
        break;
    } // Hide all slides.


    for (var i = 0; i < getAllSlides.length; i++) {
      getAllSlides[i].setAttribute('disabled', true);
      getAllSlides[i].classList.remove('sui-loaded');
      getAllSlides[i].classList.remove('sui-active');
      getAllSlides[i].setAttribute('tabindex', '-1');
      getAllSlides[i].setAttribute('aria-hidden', true);
    } // Change modal size.


    if (getNewSlide.hasAttribute('data-modal-size')) {
      var newDialogSize = getNewSlide.getAttribute('data-modal-size');

      switch (newDialogSize) {
        case 'sm':
        case 'small':
          newDialogSize = 'sm';
          break;

        case 'md':
        case 'med':
        case 'medium':
          newDialogSize = 'md';
          break;

        case 'lg':
        case 'large':
          newDialogSize = 'lg';
          break;

        case 'xl':
        case 'extralarge':
        case 'extraLarge':
        case 'extra-large':
          newDialogSize = 'xl';
          break;

        default:
          newDialogSize = undefined;
      }

      if (undefined !== newDialogSize) {
        // Remove others sizes from dialog to prevent any conflicts with styles.
        this.dialogNode.parentNode.classList.remove('sui-modal-sm');
        this.dialogNode.parentNode.classList.remove('sui-modal-md');
        this.dialogNode.parentNode.classList.remove('sui-modal-lg');
        this.dialogNode.parentNode.classList.remove('sui-modal-xl'); // Apply the new size to dialog.

        this.dialogNode.parentNode.classList.add('sui-modal-' + newDialogSize);
      }
    } // Change modal label.


    if (getNewSlide.hasAttribute('data-modal-labelledby')) {
      var newDialogLabel, getDialogLabel;
      newDialogLabel = '';
      getDialogLabel = getNewSlide.getAttribute('data-modal-labelledby');

      if ('' !== getDialogLabel || undefined !== getDialogLabel) {
        newDialogLabel = getDialogLabel;
      }

      this.dialogNode.setAttribute('aria-labelledby', newDialogLabel);
    } // Change modal description.


    if (getNewSlide.hasAttribute('data-modal-describedby')) {
      var newDialogDesc, getDialogDesc;
      newDialogDesc = '';
      getDialogDesc = getNewSlide.getAttribute('data-modal-describedby');

      if ('' !== getDialogDesc || undefined !== getDialogDesc) {
        newDialogDesc = getDialogDesc;
      }

      this.dialogNode.setAttribute('aria-describedby', newDialogDesc);
    } // Show new slide.


    getNewSlide.classList.add('sui-active');
    getNewSlide.classList.add(animation);
    getNewSlide.removeAttribute('tabindex');
    getNewSlide.removeAttribute('aria-hidden');
    setTimeout(function () {
      getNewSlide.classList.add('sui-loaded');
      getNewSlide.classList.remove(animation);
      getNewSlide.removeAttribute('disabled');
    }, 600);

    if ('string' === typeof newSlideFocus) {
      this.newSlideFocus = document.getElementById(newSlideFocus);
    } else if ('object' === _typeof(newSlideFocus)) {
      this.newSlideFocus = newSlideFocus;
    } else {
      this.newSlideFocus = null;
    }

    if (this.newSlideFocus) {
      this.newSlideFocus.focus();
    } else {
      aria.Utils.focusFirstDescendant(this.dialogNode);
    }
  }; // end slide.


  aria.Dialog.prototype.addListeners = function () {
    document.addEventListener('focus', this.trapFocus, true);

    if (this.isCloseOnEsc) {
      this.dialogNode.addEventListener('keyup', aria.handleEscape);
    }
  }; // end addListeners.


  aria.Dialog.prototype.removeListeners = function () {
    document.removeEventListener('focus', this.trapFocus, true);
  }; // end removeListeners.


  aria.Dialog.prototype.trapFocus = function (event) {
    if (aria.Utils.IgnoreUtilFocusChanges) {
      return;
    }

    var currentDialog = aria.getCurrentDialog();

    if (currentDialog.dialogNode.contains(event.target)) {
      currentDialog.lastFocus = event.target;
    } else {
      aria.Utils.focusFirstDescendant(currentDialog.dialogNode);

      if (currentDialog.lastFocus == document.activeElement) {
        aria.Utils.focusLastDescendant(currentDialog.dialogNode);
      }

      currentDialog.lastFocus = document.activeElement;
    }
  }; // end trapFocus.


  SUI.openModal = function (dialogId, focusAfterClosed, focusFirst, dialogOverlay) {
    var isCloseOnEsc = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;
    var isAnimated = arguments.length > 5 ? arguments[5] : undefined;
    var dialog = new aria.Dialog(dialogId, focusAfterClosed, focusFirst, dialogOverlay, isCloseOnEsc, isAnimated);
  }; // end openModal.


  SUI.closeModal = function (isAnimated) {
    var topDialog = aria.getCurrentDialog();
    topDialog.close(isAnimated);
  }; // end closeDialog.


  SUI.replaceModal = function (newDialogId, newFocusAfterClosed, newFocusFirst, hasOverlayMask) {
    var isCloseOnEsc = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;
    var isAnimated = arguments.length > 5 ? arguments[5] : undefined;
    var topDialog = aria.getCurrentDialog();
    /**
     * BUG #1:
     * When validating document.activeElement it always returns "false" but
     * even when "false" on Chrome function is fired correctly while on Firefox
     * and Safari this validation prevents function to be fired on click.
     *
     * if ( topDialog.dialogNode.contains( document.activeElement ) ) { ... }
     */

    topDialog.replace(newDialogId, newFocusAfterClosed, newFocusFirst, hasOverlayMask, isCloseOnEsc, isAnimated);
  }; // end replaceModal.


  SUI.slideModal = function (newSlideId, newSlideFocus, newSlideEntrance) {
    var topDialog = aria.getCurrentDialog();
    topDialog.slide(newSlideId, newSlideFocus, newSlideEntrance);
  }; // end slideModal.

})();

(function ($) {
  // Enable strict mode.
  'use strict';

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.modalDialog = function () {
    function init() {
      var button, buttonOpen, buttonClose, buttonReplace, buttonSlide, overlayMask, modalId, slideId, closeFocus, newFocus, animation, isAnimated;
      buttonOpen = $('[data-modal-open]');
      buttonClose = $('[data-modal-close]');
      buttonReplace = $('[data-modal-replace]');
      buttonSlide = $('[data-modal-slide]');
      overlayMask = $('.sui-modal-overlay');
      buttonOpen.on('click', function (e) {
        button = $(this);
        modalId = button.attr('data-modal-open');
        closeFocus = button.attr('data-modal-close-focus');
        newFocus = button.attr('data-modal-open-focus');
        overlayMask = button.attr('data-modal-mask');
        isAnimated = button.attr('data-modal-animated');
        var isCloseOnEsc = 'false' === button.attr('data-esc-close') ? false : true;

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) === _typeof(closeFocus) || false === closeFocus || '' === closeFocus) {
          closeFocus = this;
        }

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) === _typeof(newFocus) || false === newFocus || '' === newFocus) {
          newFocus = undefined;
        }

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) !== _typeof(overlayMask) && false !== overlayMask && 'true' === overlayMask) {
          overlayMask = true;
        } else {
          overlayMask = false;
        }

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) !== _typeof(isAnimated) && false !== isAnimated && 'false' === isAnimated) {
          isAnimated = false;
        } else {
          isAnimated = true;
        }

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) !== _typeof(modalId) && false !== modalId && '' !== modalId) {
          SUI.openModal(modalId, closeFocus, newFocus, overlayMask, isCloseOnEsc, isAnimated);
        }

        e.preventDefault();
      });
      buttonReplace.on('click', function (e) {
        button = $(this);
        modalId = button.attr('data-modal-replace');
        closeFocus = button.attr('data-modal-close-focus');
        newFocus = button.attr('data-modal-open-focus');
        overlayMask = button.attr('data-modal-replace-mask');
        var isCloseOnEsc = 'false' === button.attr('data-esc-close') ? false : true;

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) === _typeof(closeFocus) || false === closeFocus || '' === closeFocus) {
          closeFocus = undefined;
        }

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) === _typeof(newFocus) || false === newFocus || '' === newFocus) {
          newFocus = undefined;
        }

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) !== _typeof(overlayMask) && false !== overlayMask && 'true' === overlayMask) {
          overlayMask = true;
        } else {
          overlayMask = false;
        }

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) !== _typeof(modalId) && false !== modalId && '' !== modalId) {
          SUI.replaceModal(modalId, closeFocus, newFocus, overlayMask, isCloseOnEsc, isAnimated);
        }

        e.preventDefault();
      });
      buttonSlide.on('click', function (e) {
        button = $(this);
        slideId = button.attr('data-modal-slide');
        newFocus = button.attr('data-modal-slide-focus');
        animation = button.attr('data-modal-slide-intro');

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) === _typeof(newFocus) || false === newFocus || '' === newFocus) {
          newFocus = undefined;
        }

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) === _typeof(animation) || false === animation || '' === animation) {
          animation = '';
        }

        if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) !== _typeof(slideId) && false !== slideId && '' !== slideId) {
          SUI.slideModal(slideId, newFocus, animation);
        }

        e.preventDefault();
      });
      buttonClose.on('click', function (e) {
        SUI.closeModal(isAnimated);
        e.preventDefault();
      });
    }

    init();
    return this;
  };

  SUI.modalDialog();
})(jQuery);;
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exists.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.multistrings = function () {
    function buildWrapper(textarea, uniqid) {
      var parent = textarea.parent(),
          label = parent.find('> .sui-label'),
          description = parent.find('> .sui-description');
      /**
       * Build main wrapper for the whole multistring element.
       */

      parent.wrap('<div class="sui-multistrings-wrap"></div>');
      /**
       * Build ARIA-ready element.
       */
      // Hide field.

      parent.addClass('sui-multistrings-aria').removeClass('sui-form-field');
      /**
       * Build visual-ready element.
       */
      // Build a new field.

      $('<div class="sui-form-field sui-multistrings" tabindex="-1" aria-hidden="true" />').insertAfter(parent);
      var newParent = parent.next('.sui-multistrings');

      if (label.length) {
        newParent.append(label.clone());

        if ('' !== newParent.find('.sui-label').attr('for')) {
          newParent.find('.sui-label').attr('for', newParent.find('.sui-label').attr('for') + '-input-multistrings');
        }

        if ('' !== newParent.find('.sui-label').attr('id')) {
          newParent.find('.sui-label').attr('id', newParent.find('.sui-label').attr('id') + '-input-multistrings');
        }
      }

      newParent.append('<ul class="sui-multistrings-list" />');

      if (description.length) {
        newParent.append(description.clone());
        var $childDescription = newParent.find('.sui-description');

        if ('' !== $childDescription.attr('id')) {
          var newId = $childDescription.attr('id') + '-input-multistrings';
          $childDescription.attr('id', newId);
        }
      }
    }

    function bindFocus($mainWrapper) {
      var $listWrapper = $mainWrapper.find('.sui-multistrings');
      $listWrapper.on('click', function (e) {
        var $this = $(e.target);

        if ('sui-multistrings-list' !== $this.attr('class')) {
          return;
        }

        $listWrapper.find('.sui-multistrings-input input').trigger('focus');
      });
      var $input = $listWrapper.find('.sui-multistrings-input input'),
          $textarea = $mainWrapper.find('textarea'),
          $stringList = $mainWrapper.find('.sui-multistrings-list');

      var addSuiFocus = function addSuiFocus($element) {
        $element.on('focus', function () {
          $stringList.addClass('sui-focus');
          $element.off('blur').on('blur', function () {
            $stringList.removeClass('sui-focus');
          });
        });
      };

      addSuiFocus($input);
      addSuiFocus($textarea);
    }

    function buildInput(textarea, uniqid) {
      var html = '',
          placeholder = '',
          ariaLabel = '',
          ariaDescription = '';
      var dataPlaceholder = textarea.attr('placeholder');
      var dataLabel = textarea.attr('data-field-label');

      if ('undefined' !== typeof dataPlaceholder && '' !== dataPlaceholder) {
        placeholder = ' placeholder="' + dataPlaceholder + '"';
      }

      if ('undefined' !== typeof dataLabel && '' !== dataLabel) {
        ariaLabel = ' aria-labelledby="' + uniqid + '-label"';
        textarea.attr('aria-labelledby', uniqid + '-label');
      } else {
        if (textarea.closest('.sui-form-field').find('.sui-label').length) {
          ariaLabel = ' aria-labelledby="' + uniqid + '-label"';
        }

        textarea.attr('aria-labelledby', uniqid + '-label');
      }

      if ('undefined' !== typeof dataLabel && '' !== dataLabel) {
        ariaDescription = ' aria-describedby="' + uniqid + '-description"';
      } else {
        if (textarea.closest('.sui-form-field').find('.sui-label').length) {
          ariaDescription = ' aria-ariaDescription="' + uniqid + '-description"';
        }
      }

      html += '<li class="sui-multistrings-input">';
      html += '<input type="text" autocomplete="off"' + placeholder + ' id="' + uniqid + '"' + ariaLabel + ariaDescription + ' aria-autocomplete="none" />';
      html += '</li>';
      return html;
    }

    function buildItem(itemName) {
      var html = '';
      html += '<li title="' + itemName + '">';
      html += '<span class="sui-icon-page sui-sm" aria-hidden="true"></span>';
      html += itemName;
      html += '<button class="sui-button-close">';
      html += '<span class="sui-icon-close" aria-hidden="true"></span>';
      html += '<span class="sui-screen-reader-text">Delete</span>';
      html += '</button>';
      html += '</li>';
      return html;
    }

    function bindRemoveTag($mainWrapper) {
      var $removeButtons = $mainWrapper.find('.sui-multistrings-list .sui-button-close');
      $removeButtons.off('click').on('click', removeTag);
    }

    function insertStringOnLoad(textarea, uniqid, disallowedCharsArray) {
      var html = '',
          $mainWrapper = textarea.closest('.sui-multistrings-wrap'),
          $entriesList = $mainWrapper.find('.sui-multistrings-list'),
          forbiddenRemoved = cleanTextarea(textarea.val(), disallowedCharsArray, true); // Split lines for inserting the tags and cleaning the new textarea value.

      var splitStrings = forbiddenRemoved.split(/[\r\n]/gm),
          cleanStringsArray = []; // Insert the tags and add clean values to the cleanStringsArray.

      for (var i = 0; i < splitStrings.length; i++) {
        var stringLine = splitStrings[i].trim();

        if (0 === stringLine.length) {
          continue;
        }

        html += buildItem(stringLine);
        cleanStringsArray.push(stringLine);
      } // Clean-up textarea value with the cleanStringsArray joined by newlines.


      var newTextareaValue = cleanStringsArray.join('\n');
      textarea.val(newTextareaValue); // Build input to insert strings.

      html += buildInput(textarea, uniqid);
      $entriesList.append(html);
      bindRemoveTag($mainWrapper);
    }

    function getDisallowedChars($mainWrapper) {
      var $textarea = $mainWrapper.find('textarea.sui-multistrings'),
          disallowedCharsArray = [];
      var customDisallowedKeys = $textarea.data('disallowedKeys');

      if (customDisallowedKeys) {
        if ('number' === typeof customDisallowedKeys) {
          customDisallowedKeys = customDisallowedKeys.toString();
        } // Make an array from the user defined keys.


        var customKeysArray = customDisallowedKeys.split(',');

        var _iterator = _createForOfIteratorHelper(customKeysArray),
            _step;

        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var key = _step.value;
            // Convert to integer.
            var intKey = parseInt(key, 10); // And filter out any NaN.

            if (!isNaN(intKey)) {
              // Convert ascii code to character.
              disallowedCharsArray.push(String.fromCharCode(intKey));
            }
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      }

      return disallowedCharsArray;
    }

    function getRegexPatternForDisallowedChars(disallowedCharsArray) {
      // Regex for removing the disallowed keys from the inserted strings.
      var escapeRegExp = function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      },
          disallowedPattern = escapeRegExp(disallowedCharsArray.join(''));

      return disallowedPattern;
    }

    function handleInsertTags($mainWrapper, disallowedCharsArray) {
      var $tagInput = $mainWrapper.find('.sui-multistrings-input input'),
          $textarea = $mainWrapper.find('textarea.sui-multistrings'),
          disallowedString = getRegexPatternForDisallowedChars(disallowedCharsArray),
          regex = new RegExp("[\r\n".concat(disallowedString, "]"), 'gm'); // Sanitize the values on keydown.

      $tagInput.on('keydown', function (e) {
        // Do nothing if the key is from the disallowed ones.
        if (disallowedCharsArray.includes(e.key)) {
          e.preventDefault();
          return;
        }

        var input = $(this),
            oldValue = $textarea.val(),
            newValue = input.val(); // Get rid of new lines, commas, and any chars passed by the admin from the newly entered value.

        var newTrim = newValue.replace(regex, ''),
            isEnter = 13 === e.keyCode;

        if (isEnter) {
          e.preventDefault();
          e.stopPropagation();
        } // If there's no value to add, don't insert any new value.


        if (0 !== newTrim.length && 0 !== newTrim.trim().length) {
          if (isEnter) {
            var newTextareaValue = oldValue.length ? "".concat(oldValue, "\n").concat(newTrim) : newTrim; // Print new value on textarea.

            $textarea.val(newTextareaValue); // Print new value on the list.

            var html = buildItem(newTrim);
            $(html).insertBefore($mainWrapper.find('.sui-multistrings-input')); // Clear input value.

            input.val(''); // Bid the event to remove the tags.

            bindRemoveTag($mainWrapper);
          } else {
            input.val(newTrim);
          }
        } else {
          input.val('');
        }
      });
    }

    function handleTextareaChange($mainWrapper, disallowedCharsArray) {
      var textarea = $mainWrapper.find('textarea.sui-multistrings');
      var oldValue = textarea.val(),
          isTabTrapped = true; // Keep tab trapped when focusing on the textarea.

      textarea.on('focus', function () {
        return isTabTrapped = true;
      });
      textarea.on('keydown', function (e) {
        // Do nothing if the key is from the disallowed ones.
        if (disallowedCharsArray.includes(e.key)) {
          e.preventDefault();
          return;
        } // If it's tab...


        if (9 === e.keyCode) {
          // Add a new line if it's trapped.
          if (isTabTrapped) {
            e.preventDefault();
            var start = this.selectionStart,
                end = this.selectionEnd; // Insert a new line where the caret is.

            $(this).val($(this).val().substring(0, start) + '\n' + $(this).val().substring(end)); // Put caret at right position again.

            this.selectionStart = start + 1;
            this.selectionEnd = this.selectionStart;
          } // Release the tab.

        } else if (27 === e.keyCode) {
          isTabTrapped = false;
        }
      }).on('keyup change', function (e) {
        var currentValue = textarea.val(); // Nothing has changed, do nothing.

        if (currentValue === oldValue) {
          return;
        } // Clear up the content.


        var cleanedCurrentValue = cleanTextarea(currentValue, disallowedCharsArray); // Set the current value as the old one for future iterations.

        textarea.val(cleanedCurrentValue);
        oldValue = cleanedCurrentValue;
        var textboxValuesArray = cleanedCurrentValue.split(/[\r\n]+/gm),
            tags = $mainWrapper.find('.sui-multistrings-list li:not(.sui-multistrings-input)'),
            tagsTitles = [];

        var _iterator2 = _createForOfIteratorHelper(tags),
            _step2;

        try {
          for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
            var tag = _step2.value;
            tagsTitles.push($(tag).attr('title'));
          }
        } catch (err) {
          _iterator2.e(err);
        } finally {
          _iterator2.f();
        }

        var areEqual = compareArrays(textboxValuesArray, tagsTitles); // The existing elements changed, update the existing tags.

        if (!areEqual) {
          $mainWrapper.find('.sui-multistrings-list li:not(.sui-multistrings-input)').remove();

          var _iterator3 = _createForOfIteratorHelper(textboxValuesArray),
              _step3;

          try {
            for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
              var value = _step3.value;
              value = value.trim();

              if (value.length) {
                // Print new value on the list.
                var html = buildItem(value);
                $(html).insertBefore($mainWrapper.find('.sui-multistrings-input'));
              }
            } // Bind the event to remove the tags.

          } catch (err) {
            _iterator3.e(err);
          } finally {
            _iterator3.f();
          }

          bindRemoveTag($mainWrapper);
        }
      });
    }

    function compareArrays(firstArray, secondArray) {
      if (!Array.isArray(firstArray) || !Array.isArray(secondArray)) {
        return false;
      }

      if (firstArray.length !== secondArray.length) {
        return false;
      }

      return firstArray.every(function (value, index) {
        return value === secondArray[index];
      });
    }

    function cleanTextarea(string, disallowedCharsArray) {
      var isLoad = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
      var disallowedString = getRegexPatternForDisallowedChars(disallowedCharsArray),
          regex = new RegExp("[".concat(disallowedString, "]+|((\\r\\n|\\n|\\r)$)|^\\s*$"), 'gm');
      var clearedString = string.replace(regex, '');

      if (!isLoad) {
        // Avoid removing the last newline if it existed.
        var endsInNewline = string.match(/\n$/);

        if (endsInNewline) {
          clearedString += '\n';
        }
      }

      return clearedString;
    }

    function removeTag(e) {
      var $removeButton = $(e.currentTarget),
          $tag = $removeButton.closest('li');
      var $hiddenTextarea = $removeButton.closest('.sui-multistrings-wrap').find('textarea.sui-multistrings'),
          textareaValue = $hiddenTextarea.val(),
          removedTag = $tag.attr('title'),
          escapedRemovedTag = removedTag.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'),
          regex = new RegExp("^".concat(escapedRemovedTag, "\\s|^").concat(escapedRemovedTag, "$"), 'm'),
          newTextareaValue = textareaValue.replace(regex, ''); // Remove the string from the hidden textarea.

      $hiddenTextarea.val(newTextareaValue);
      $hiddenTextarea.trigger('change'); // Remove the tag the close button belongs to.

      $tag.remove();
    }

    function init() {
      var multistrings = $('.sui-multistrings');

      if (0 !== multistrings.length) {
        multistrings.each(function () {
          multistrings = $(this);
          var uniqueId = '';
          var hasUniqueId = 'undefined' !== typeof multistrings.attr('id') && '' !== multistrings.attr('id');
          var isTextarea = multistrings.is('textarea');
          var isWrapped = multistrings.parent().hasClass('sui-form-field');

          if (!hasUniqueId) {
            throw new Error('Multistrings field require an ID attribute.');
          } else {
            uniqueId = multistrings.attr('id') + '-strings';
          }

          if (!isTextarea) {
            throw new Error('Multistrings field with id="' + multistrings.attr('id') + '" needs to be "textarea".');
          }

          if (!isWrapped) {
            throw new Error('Multistrings field needs to be wrapped inside "sui-form-field" div.');
          }

          buildWrapper(multistrings, uniqueId);
          var $mainWrapper = multistrings.closest('.sui-multistrings-wrap'),
              disallowedCharsArray = getDisallowedChars($mainWrapper);
          insertStringOnLoad(multistrings, uniqueId, disallowedCharsArray);
          handleInsertTags($mainWrapper, disallowedCharsArray);
          handleTextareaChange($mainWrapper, disallowedCharsArray);
          bindFocus($mainWrapper);
        });
      }
    }

    init();
    return this;
  };

  SUI.multistrings();
})(jQuery);;
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it does not exist.

  var _this = this;

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }
  /**
   * @desc Notifications function to show when alert.
   *
   * Assumptions: The element serving as the alert container is present in the
   * DOM and hidden. The alert container has role='alert'.
   *
   * @param noticeId
   * The ID of the element serving as the alert container.
   *
   * @param noticeMessage
   * The content to be printed when the alert shows up. It accepts HTML.
   *
   * @param noticeOptions
   * An object with different paramethers to modify the alert appearance.
   */


  SUI.openNotice = function (noticeId, noticeMessage, noticeOptions) {
    // Get notification node by ID.
    var noticeNode = $('#' + noticeId);
    var nodeWrapper = noticeNode.parent(); // Check if element ID exists.

    if (null === _typeof(noticeNode) || 'undefined' === typeof noticeNode) {
      throw new Error('No element found with id="' + noticeId + '".');
    } // Check if element has correct attribute.


    if ('alert' !== noticeNode.attr('role')) {
      throw new Error('Notice requires a DOM element with ARIA role of alert.');
    } // Check if notice message is empty.


    if (null === _typeof(noticeMessage) || 'undefined' === typeof noticeMessage || '' === noticeMessage) {
      throw new Error('Notice requires a message to print.');
    }

    var utils = utils || {};
    /**
     * @desc Allowed types for notification.
     */

    utils.allowedNotices = ['info', 'blue', 'green', 'success', 'yellow', 'warning', 'red', 'error', 'purple', 'upsell'];
    /**
     * @desc Verify if property is an array.
     */

    utils.isObject = function (obj) {
      if ((null !== obj || 'undefined' !== obj) && $.isPlainObject(obj)) {
        return true;
      }

      return false;
    };
    /**
     * @desc Deep merge two objects.
     * Watch out for infinite recursion on circular references.
     */


    utils.deepMerge = function (target) {
      for (var _len = arguments.length, sources = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        sources[_key - 1] = arguments[_key];
      }

      if (!sources.length) {
        return target;
      }

      var source = sources.shift();

      if (utils.isObject(target) && utils.isObject(source)) {
        for (var key in source) {
          if (utils.isObject(source[key])) {
            if (!target[key]) {
              Object.assign(target, _defineProperty({}, key, {}));
            }

            utils.deepMerge(target[key], source[key]);
          } else {
            Object.assign(target, _defineProperty({}, key, source[key]));
          }
        }
      }

      return utils.deepMerge.apply(utils, [target].concat(sources));
    };
    /**
     * @desc Declare default styling options for notifications.
     */


    utils.setProperties = function () {
      var incomingOptions = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      utils.options = [];
      var defaults = {
        type: 'default',
        icon: 'info',
        dismiss: {
          show: false,
          label: 'Close this notice',
          tooltip: ''
        },
        autoclose: {
          show: true,
          timeout: 3000
        }
      };
      utils.options[0] = utils.deepMerge(defaults, incomingOptions);
    };

    utils.setProperties(noticeOptions);
    /**
     * @desc Build notice dismiss.
     */

    utils.buildDismiss = function () {
      var html = '';
      var dismiss = utils.options[0].dismiss;

      if (true === dismiss.show) {
        html = document.createElement('div');
        html.className = 'sui-notice-actions';
        var innerHTML = '';

        if ('' !== dismiss.tooltip) {
          if (nodeWrapper.hasClass('sui-floating-notices')) {
            innerHTML += '<div class="sui-tooltip sui-tooltip-bottom" data-tooltip="' + dismiss.tooltip + '">';
          } else {
            innerHTML += '<div class="sui-tooltip" data-tooltip="' + dismiss.tooltip + '">';
          }
        }

        innerHTML += '<button class="sui-button-icon">';
        innerHTML += '<span class="sui-icon-check" aria-hidden="true"></span>';

        if ('' !== dismiss.label) {
          innerHTML += '<span class="sui-screen-reader-text">' + dismiss.label + '</span>';
        }

        innerHTML += '</button>';

        if ('' !== dismiss.tooltip) {
          innerHTML += '</div>';
        }

        html.innerHTML = innerHTML;
      }

      return html;
    };
    /**
     * @desc Build notice icon.
     */


    utils.buildIcon = function () {
      var html = '';
      var icon = utils.options[0].icon;

      if ('' !== icon || 'undefined' !== typeof icon || null !== _typeof(icon)) {
        html = document.createElement('span');
        html.className += 'sui-notice-icon sui-icon-' + icon + ' sui-md';
        html.setAttribute('aria-hidden', true);

        if ('loader' === icon) {
          html.classList.add('sui-loading');
        }
      }

      return html;
    };
    /**
     * @desc Build notice message.
     */


    utils.buildMessage = function () {
      var html = document.createElement('div');
      html.className = 'sui-notice-message';
      html.innerHTML = noticeMessage;
      html.prepend(utils.buildIcon());
      return html;
    };
    /**
     * @desc Build notice markup.
     */


    utils.buildNotice = function () {
      var html = document.createElement('div');
      html.className = 'sui-notice-content';
      html.append(utils.buildMessage(), utils.buildDismiss());
      return html;
    };
    /**
     * @desc Show notification message.
     */


    utils.showNotice = function (animation) {
      var timeout = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 300;
      var type = utils.options[0].type;
      var dismiss = utils.options[0].dismiss;
      var autoclose = utils.options[0].autoclose; // Add active class.

      noticeNode.addClass('sui-active'); // Check for allowed notification types.

      $.each(utils.allowedNotices, function (key, value) {
        if (value === type) {
          noticeNode.addClass('sui-notice-' + value);
        }
      }); // Remove tabindex.

      noticeNode.removeAttr('tabindex'); // Print notification message.

      noticeNode.html(utils.buildNotice()); // Show animation.

      if ('slide' === animation) {
        noticeNode.slideDown(timeout, function () {
          // Check if dismiss button enabled.
          if (true === dismiss.show) {
            // Focus dismiss button.
            noticeNode.find('.sui-notice-actions button').trigger('focus'); // Dismiss button.

            noticeNode.find('.sui-notice-actions button').on('click', function () {
              SUI.closeNotice(noticeId);
            });
          } else {
            // Check if notice auto-closes.
            if (true === autoclose.show) {
              setTimeout(function () {
                return SUI.closeNotice(noticeId);
              }, parseInt(autoclose.timeout));
            }
          }
        });
      } else if ('fade' === animation) {
        noticeNode.fadeIn(timeout, function () {
          // Check if dismiss button enabled.
          if (true === dismiss.show) {
            // Focus dismiss button.
            noticeNode.find('.sui-notice-actions button').trigger('focus'); // Dismiss button.

            noticeNode.find('.sui-notice-actions button').on('click', function () {
              SUI.closeNotice(noticeId);
            });
          } else {
            // Check if notice auto-closes.
            if (true === autoclose.show) {
              setTimeout(function () {
                return SUI.closeNotice(noticeId);
              }, parseInt(autoclose.timeout));
            }
          }
        });
      } else {
        noticeNode.show(timeout, function () {
          // Check if dismiss button enabled.
          if (true === dismiss.show) {
            // Focus dismiss button.
            noticeNode.find('.sui-notice-actions button').trigger('focus'); // Dismiss button.

            noticeNode.find('.sui-notice-actions button').on('click', function () {
              SUI.closeNotice(noticeId);
            });
          } else {
            // Check if notice auto-closes.
            if (true === autoclose.show) {
              setTimeout(function () {
                return SUI.closeNotice(noticeId);
              }, parseInt(autoclose.timeout));
            }
          }
        });
      }
    };
    /**
     * @desc Open notification message.
     */


    utils.openNotice = function (animation) {
      var timeout = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 300;

      if (noticeNode.hasClass('sui-active')) {
        if ('slide' === animation) {
          noticeNode.slideUp(timeout, function () {
            utils.showNotice('slide', timeout);
          });
        } else if ('fade' === animation) {
          noticeNode.fadeOut(timeout, function () {
            utils.showNotice('fade', timeout);
          });
        } else {
          noticeNode.hide(timeout, function () {
            utils.showNotice(null, timeout);
          });
        }
      } else {
        // Show notice.
        utils.showNotice(animation, timeout);
      }
    };
    /**
     * @desc Initialize function.
     */


    var init = function init() {
      /**
       * When notice should float, it needs to be wrapped inside:
       * <div class="sui-floating-notices"></div>
       *
       * IMPORTANT: This wrapper goes before "sui-wrap" closing tag
       * and after modals markup.
       */
      if (nodeWrapper.hasClass('sui-floating-notices')) {
        utils.openNotice('slide');
      } else {
        utils.openNotice('fade');
      }
    };

    init();
    return _this;
  };
  /**
   * @desc Close and clear the alert.
   *
   * Assumptions: The element that will trigger this function is part of alert content.
   *
   * @param noticeId
   * The ID of the element serving as the alert container.
   *
   */


  SUI.closeNotice = function (noticeId) {
    // Get notification node by ID.
    var noticeNode = $('#' + noticeId);
    var nodeWrapper = noticeNode.parent(); // Check if element ID exists.

    if (null === _typeof(noticeNode) || 'undefined' === typeof noticeNode) {
      throw new Error('No element found with id="' + noticeId + '".');
    }

    var utils = utils || {};
    /**
     * @desc Allowed types for notification.
     */

    utils.allowedNotices = ['info', 'blue', 'green', 'success', 'yellow', 'warning', 'red', 'error', 'purple', 'upsell'];
    /**
     * @desc Destroy notification.
     */

    utils.hideNotice = function () {
      // Remove active class.
      noticeNode.removeClass('sui-active'); // Remove styling classes.

      $.each(utils.allowedNotices, function (key, value) {
        noticeNode.removeClass('sui-notice-' + value);
      }); // Prevent TAB key from accessing the element.

      noticeNode.attr('tabindex', '-1'); // Remove all content from notification.

      noticeNode.empty();
    };
    /**
     * @desc Close notification message.
     */


    utils.closeNotice = function (animation) {
      var timeout = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 300;

      // Close animation.
      if ('slide' === animation) {
        noticeNode.slideUp(timeout, function () {
          return utils.hideNotice();
        });
      } else if ('fade' === animation) {
        noticeNode.fadeOut(timeout, function () {
          return utils.hideNotice();
        });
      } else {
        noticeNode.hide(timeout, function () {
          return utils.hideNotice();
        });
      }
    };
    /**
     * @desc Initialize function.
     */


    var init = function init() {
      /**
       * When notice should float, it needs to be wrapped inside:
       * <div class="sui-floating-notices"></div>
       *
       * IMPORTANT: This wrapper goes before "sui-wrap" closing tag
       * and after modals markup.
       */
      if (nodeWrapper.hasClass('sui-floating-notices')) {
        utils.closeNotice('slide');
      } else {
        utils.closeNotice('fade');
      }
    };

    init();
    return _this;
  };
  /**
   * @desc Trigger open and close alert notification functions through element attributes.
   *
   * Assumptions: Elements in charge of triggering the actions will be a button or a non-hyperlink element.
   *
   */


  SUI.notice = function () {
    var notice = notice || {};
    notice.Utils = notice.Utils || {};
    /**
     * @desc Click an element to open a notification.
     */

    notice.Utils.Open = function (element) {
      element.on('click', function () {
        self = $(this); // Define main variables for open function.

        var noticeId = self.attr('data-notice-open');
        var noticeMessage = '';
        var noticeOptions = {}; // Define index to use on for loops.

        var i; // Define maximum number of paragraphs.

        var numbLines = 4; // Check if `data-notice-message` exists.

        if (self.is('[data-notice-message]') && '' !== self.attr('data-notice-message')) {
          noticeMessage += self.attr('data-notice-message'); // If `data-notice-message` doesn't exists, look for `data-notice-paragraph-[i]` attributes.
        } else {
          for (i = 0; i < numbLines; i++) {
            var index = i + 1;
            var paragraph = 'data-notice-paragraph-' + index;

            if (self.is('[' + paragraph + ']') && '' !== self.attr(paragraph)) {
              noticeMessage += '<p>' + self.attr(paragraph) + '</p>';
            }
          }
        } // Check if `data-notice-type` exists.


        if (self.is('[data-notice-type]') && '' !== self.attr('data-notice-dismiss-type')) {
          noticeOptions.type = self.attr('data-notice-type');
        } // Check if `data-notice-icon` exists.


        if (self.is('[data-notice-icon]')) {
          noticeOptions.icon = self.attr('data-notice-icon');
        } // Check if `data-notice-dismiss` exists.


        if (self.is('[data-notice-dismiss]')) {
          noticeOptions.dismiss = {};

          if ('true' === self.attr('data-notice-dismiss')) {
            noticeOptions.dismiss.show = true;
          } else if ('false' === self.attr('data-notice-dismiss')) {
            noticeOptions.dismiss.show = false;
          }
        } // Check if `data-notice-dismiss-label` exists.


        if (self.is('[data-notice-dismiss-label]') && '' !== self.attr('data-notice-dismiss-label')) {
          noticeOptions.dismiss.label = self.attr('data-notice-dismiss-label');
        } // Check if `data-notice-dismiss-label` exists.


        if (self.is('[data-notice-dismiss-tooltip]') && '' !== self.attr('data-notice-dismiss-tooltip')) {
          noticeOptions.dismiss.tooltip = self.attr('data-notice-dismiss-tooltip');
        } // Check if `data-notice-autoclose` exists.


        if (self.is('[data-notice-autoclose]')) {
          noticeOptions.autoclose = {};

          if ('true' === self.attr('data-notice-autoclose')) {
            noticeOptions.autoclose.show = true;
          } else if ('false' === self.attr('data-notice-autoclose')) {
            noticeOptions.autoclose.show = false;
          }
        } // Check if `data-notice-autoclose-timeout` exists.


        if (self.is('[data-notice-autoclose-timeout]')) {
          noticeOptions.autoclose = noticeOptions.autoclose || {};
          noticeOptions.autoclose.timeout = parseInt(self.attr('data-notice-autoclose-timeout'));
        }

        SUI.openNotice(noticeId, noticeMessage, noticeOptions);
      });
    };
    /**
     * @desc Close a notification.
     */


    notice.Utils.Close = function (element) {
      element.on('click', function () {
        self = $(this);
        var noticeId;

        if (self.is('[data-notice-close]')) {
          noticeId = self.closest('.sui-notice').attr('id');

          if ('' !== self.attr('[data-notice-close]')) {
            noticeId = self.attr('data-notice-close');
          }

          SUI.closeNotice(noticeId);
        }
      });
    };

    var init = function init() {
      // Open a notification.
      var btnOpen = $('[data-notice-open]');
      notice.Utils.Open(btnOpen); // Close a notification.

      var btnClose = $('[data-notice-close]');
      notice.Utils.Close(btnClose);
    };

    init();
    return _this;
  };

  SUI.notice();
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.showHidePassword = function () {
    $('.sui-2-12-13 .sui-form-field').each(function () {
      var $this = $(this);

      if (0 !== $this.find('input[type="password"]').length) {
        $this.find('[class*="sui-button"], .sui-password-toggle').off('click.toggle-password').on('click.toggle-password', function () {
          var $button = $(this),
              $input = $button.parent().find('input'),
              $icon = $button.find('> span');
          $button.parent().toggleClass('sui-password-visible');
          $button.find('.sui-password-text').toggleClass('sui-hidden');

          if ($button.parent().hasClass('sui-password-visible')) {
            $input.prop('type', 'text');
            $icon.removeClass('sui-icon-eye').addClass('sui-icon-eye-hide');
          } else {
            $input.prop('type', 'password');
            $icon.removeClass('sui-icon-eye-hide').addClass('sui-icon-eye');
          }
        });
      }
    });
  };

  SUI.showHidePassword();
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.reviews = function (review, reviews, rating) {
    if (reviews <= 0) {
      return;
    }

    function init() {
      var stars = Math.round(rating),
          starsBlock = review.find('.sui-reviews__stars')[0],
          i;

      for (i = 0; i < stars; i++) {
        starsBlock.innerHTML += '<span class="sui-icon-star" aria-hidden="true"></span> ';
      }

      review.find('.sui-reviews-rating').replaceWith(rating);
      review.find('.sui-reviews-customer-count').replaceWith(reviews);
    }

    init();
    return this;
  }; // Update the reviews with the live stats.


  $('.sui-2-12-13 .sui-reviews').each(function () {
    var review = $(this);
    $.ajax({
      url: "https://api.reviews.co.uk/merchant/reviews?store=wpmudev-org",
      success: function success(data) {
        SUI.reviews(review, data.stats.reviews, data.stats.average_rating);
      }
    });
  });
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.loadCircleScore = function (el) {
    var dial = $(el).find('svg circle:last-child'),
        score = $(el).data('score'),
        radius = 42,
        circumference = 2 * Math.PI * radius,
        dashLength = circumference / 100 * score,
        gapLength = dashLength * 100 - score,
        svg = '<svg viewbox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">\n' + '<circle stroke-width="16" cx="50" cy="50" r="42" />\n' + '<circle stroke-width="16" cx="50" cy="50" r="42" stroke-dasharray="0,' + gapLength + '" />\n' + '</svg>\n' + '<span class="sui-circle-score-label" aria-hidden="true">' + score + '</span>\n' + '<span class="sui-screen-reader-text" tabindex="0">Score ' + score + ' out of 100</span>'; // Add svg to score element, add loaded class, & change stroke-dasharray to represent target score/percentage.

    $(el).prepend(svg).addClass('loaded').find('circle:last-child').css('animation', 'sui' + score + ' 3s forwards');
  };

  $('.sui-2-12-13 .sui-circle-score').each(function () {
    SUI.loadCircleScore(this);
  });
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

/*!
 * Select2 4.1.0-rc.0
 * https://select2.github.io
 *
 * Released under the MIT license
 * https://github.com/select2/select2/blob/master/LICENSE.md
 *
 * Modified logic/function,etc besides formatting should be marked with //SUI-SELECT2
 * For easy debugging process or update upstream of select
 */
;

(function (factory) {
  // Disable AMD and module exports. @edited
  if (false && typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module.
    define(['jquery'], factory);
  } else if (false && (typeof module === "undefined" ? "undefined" : _typeof(module)) === 'object' && module.exports) {
    // Node/CommonJS
    module.exports = function (root, jQuery) {
      if (jQuery === undefined) {
        // require('jQuery') returns a factory that requires window to
        // build a jQuery instance, we normalize how we use modules
        // that require this pattern but the window provided is a noop
        // if it's defined (how jquery works)
        if (typeof window !== 'undefined') {
          jQuery = require('jquery');
        } else {
          jQuery = require('jquery')(root);
        }
      }

      factory(jQuery);
      return jQuery;
    };
  } else {
    // Browser globals
    factory(jQuery);
  }
})(function (jQuery) {
  // This is needed so we can catch the AMD loader configuration and use it
  // The inner file should be wrapped (by `banner.start.js`) in a function that
  // returns the AMD loader references.
  var S2 = function () {
    // Restore the Select2 AMD loader so it can be used
    // Needed mostly in the language files, where the loader is not inserted
    if (jQuery && jQuery.fn && jQuery.fn.select2 && jQuery.fn.select2.amd) {
      var S2 = jQuery.fn.select2.amd;
    }

    var S2;

    (function () {
      if (!S2 || !S2.requirejs) {
        if (!S2) {
          S2 = {};
        } else {
          require = S2;
        }
        /**
         * @license almond 0.3.3 Copyright jQuery Foundation and other contributors.
         * Released under MIT license, http://github.com/requirejs/almond/LICENSE
         */
        //Going sloppy to avoid 'use strict' string cost, but strict practices should
        //be followed.

        /*global setTimeout: false */


        var requirejs, require, define;

        (function (undef) {
          var main,
              _req,
              makeMap,
              handlers,
              defined = {},
              waiting = {},
              config = {},
              defining = {},
              hasOwn = Object.prototype.hasOwnProperty,
              aps = [].slice,
              jsSuffixRegExp = /\.js$/;

          function hasProp(obj, prop) {
            return hasOwn.call(obj, prop);
          }
          /**
           * Given a relative module name, like ./something, normalize it to
           * a real name that can be mapped to a path.
           * @param {String} name the relative name
           * @param {String} baseName a real name that the name arg is relative
           * to.
           * @returns {String} normalized name
           */


          function normalize(name, baseName) {
            var nameParts,
                nameSegment,
                mapValue,
                foundMap,
                lastIndex,
                foundI,
                foundStarMap,
                starI,
                i,
                j,
                part,
                normalizedBaseParts,
                baseParts = baseName && baseName.split("/"),
                map = config.map,
                starMap = map && map['*'] || {}; //Adjust any relative paths.

            if (name) {
              name = name.split('/');
              lastIndex = name.length - 1; // If wanting node ID compatibility, strip .js from end
              // of IDs. Have to do this here, and not in nameToUrl
              // because node allows either .js or non .js to map
              // to same file.

              if (config.nodeIdCompat && jsSuffixRegExp.test(name[lastIndex])) {
                name[lastIndex] = name[lastIndex].replace(jsSuffixRegExp, '');
              } // Starts with a '.' so need the baseName


              if (name[0].charAt(0) === '.' && baseParts) {
                //Convert baseName to array, and lop off the last part,
                //so that . matches that 'directory' and not name of the baseName's
                //module. For instance, baseName of 'one/two/three', maps to
                //'one/two/three.js', but we want the directory, 'one/two' for
                //this normalization.
                normalizedBaseParts = baseParts.slice(0, baseParts.length - 1);
                name = normalizedBaseParts.concat(name);
              } //start trimDots


              for (i = 0; i < name.length; i++) {
                part = name[i];

                if (part === '.') {
                  name.splice(i, 1);
                  i -= 1;
                } else if (part === '..') {
                  // If at the start, or previous value is still ..,
                  // keep them so that when converted to a path it may
                  // still work when converted to a path, even though
                  // as an ID it is less than ideal. In larger point
                  // releases, may be better to just kick out an error.
                  if (i === 0 || i === 1 && name[2] === '..' || name[i - 1] === '..') {
                    continue;
                  } else if (i > 0) {
                    name.splice(i - 1, 2);
                    i -= 2;
                  }
                }
              } //end trimDots


              name = name.join('/');
            } //Apply map config if available.


            if ((baseParts || starMap) && map) {
              nameParts = name.split('/');

              for (i = nameParts.length; i > 0; i -= 1) {
                nameSegment = nameParts.slice(0, i).join("/");

                if (baseParts) {
                  //Find the longest baseName segment match in the config.
                  //So, do joins on the biggest to smallest lengths of baseParts.
                  for (j = baseParts.length; j > 0; j -= 1) {
                    mapValue = map[baseParts.slice(0, j).join('/')]; //baseName segment has config, find if it has one for
                    //this name.

                    if (mapValue) {
                      mapValue = mapValue[nameSegment];

                      if (mapValue) {
                        //Match, update name to the new value.
                        foundMap = mapValue;
                        foundI = i;
                        break;
                      }
                    }
                  }
                }

                if (foundMap) {
                  break;
                } //Check for a star map match, but just hold on to it,
                //if there is a shorter segment match later in a matching
                //config, then favor over this star map.


                if (!foundStarMap && starMap && starMap[nameSegment]) {
                  foundStarMap = starMap[nameSegment];
                  starI = i;
                }
              }

              if (!foundMap && foundStarMap) {
                foundMap = foundStarMap;
                foundI = starI;
              }

              if (foundMap) {
                nameParts.splice(0, foundI, foundMap);
                name = nameParts.join('/');
              }
            }

            return name;
          }

          function makeRequire(relName, forceSync) {
            return function () {
              //A version of a require function that passes a moduleName
              //value for items that may need to
              //look up paths relative to the moduleName
              var args = aps.call(arguments, 0); //If first arg is not require('string'), and there is only
              //one arg, it is the array form without a callback. Insert
              //a null so that the following concat is correct.

              if (typeof args[0] !== 'string' && args.length === 1) {
                args.push(null);
              }

              return _req.apply(undef, args.concat([relName, forceSync]));
            };
          }

          function makeNormalize(relName) {
            return function (name) {
              return normalize(name, relName);
            };
          }

          function makeLoad(depName) {
            return function (value) {
              defined[depName] = value;
            };
          }

          function callDep(name) {
            if (hasProp(waiting, name)) {
              var args = waiting[name];
              delete waiting[name];
              defining[name] = true;
              main.apply(undef, args);
            }

            if (!hasProp(defined, name) && !hasProp(defining, name)) {
              throw new Error('No ' + name);
            }

            return defined[name];
          } //Turns a plugin!resource to [plugin, resource]
          //with the plugin being undefined if the name
          //did not have a plugin prefix.


          function splitPrefix(name) {
            var prefix,
                index = name ? name.indexOf('!') : -1;

            if (index > -1) {
              prefix = name.substring(0, index);
              name = name.substring(index + 1, name.length);
            }

            return [prefix, name];
          } //Creates a parts array for a relName where first part is plugin ID,
          //second part is resource ID. Assumes relName has already been normalized.


          function makeRelParts(relName) {
            return relName ? splitPrefix(relName) : [];
          }
          /**
           * Makes a name map, normalizing the name, and using a plugin
           * for normalization if necessary. Grabs a ref to plugin
           * too, as an optimization.
           */


          makeMap = function makeMap(name, relParts) {
            var plugin,
                parts = splitPrefix(name),
                prefix = parts[0],
                relResourceName = relParts[1];
            name = parts[1];

            if (prefix) {
              prefix = normalize(prefix, relResourceName);
              plugin = callDep(prefix);
            } //Normalize according


            if (prefix) {
              if (plugin && plugin.normalize) {
                name = plugin.normalize(name, makeNormalize(relResourceName));
              } else {
                name = normalize(name, relResourceName);
              }
            } else {
              name = normalize(name, relResourceName);
              parts = splitPrefix(name);
              prefix = parts[0];
              name = parts[1];

              if (prefix) {
                plugin = callDep(prefix);
              }
            } //Using ridiculous property names for space reasons


            return {
              f: prefix ? prefix + '!' + name : name,
              //fullName
              n: name,
              pr: prefix,
              p: plugin
            };
          };

          function makeConfig(name) {
            return function () {
              return config && config.config && config.config[name] || {};
            };
          }

          handlers = {
            require: function require(name) {
              return makeRequire(name);
            },
            exports: function exports(name) {
              var e = defined[name];

              if (typeof e !== 'undefined') {
                return e;
              } else {
                return defined[name] = {};
              }
            },
            module: function module(name) {
              return {
                id: name,
                uri: '',
                exports: defined[name],
                config: makeConfig(name)
              };
            }
          };

          main = function main(name, deps, callback, relName) {
            var cjsModule,
                depName,
                ret,
                map,
                i,
                relParts,
                args = [],
                callbackType = _typeof(callback),
                usingExports; //Use name if no relName


            relName = relName || name;
            relParts = makeRelParts(relName); //Call the callback to define the module, if necessary.

            if (callbackType === 'undefined' || callbackType === 'function') {
              //Pull out the defined dependencies and pass the ordered
              //values to the callback.
              //Default to [require, exports, module] if no deps
              deps = !deps.length && callback.length ? ['require', 'exports', 'module'] : deps;

              for (i = 0; i < deps.length; i += 1) {
                map = makeMap(deps[i], relParts);
                depName = map.f; //Fast path CommonJS standard dependencies.

                if (depName === "require") {
                  args[i] = handlers.require(name);
                } else if (depName === "exports") {
                  //CommonJS module spec 1.1
                  args[i] = handlers.exports(name);
                  usingExports = true;
                } else if (depName === "module") {
                  //CommonJS module spec 1.1
                  cjsModule = args[i] = handlers.module(name);
                } else if (hasProp(defined, depName) || hasProp(waiting, depName) || hasProp(defining, depName)) {
                  args[i] = callDep(depName);
                } else if (map.p) {
                  map.p.load(map.n, makeRequire(relName, true), makeLoad(depName), {});
                  args[i] = defined[depName];
                } else {
                  throw new Error(name + ' missing ' + depName);
                }
              }

              ret = callback ? callback.apply(defined[name], args) : undefined;

              if (name) {
                //If setting exports via "module" is in play,
                //favor that over return value and exports. After that,
                //favor a non-undefined return value over exports use.
                if (cjsModule && cjsModule.exports !== undef && cjsModule.exports !== defined[name]) {
                  defined[name] = cjsModule.exports;
                } else if (ret !== undef || !usingExports) {
                  //Use the return value from the function.
                  defined[name] = ret;
                }
              }
            } else if (name) {
              //May just be an object definition for the module. Only
              //worry about defining if have a module name.
              defined[name] = callback;
            }
          };

          requirejs = require = _req = function req(deps, callback, relName, forceSync, alt) {
            if (typeof deps === "string") {
              if (handlers[deps]) {
                //callback in this case is really relName
                return handlers[deps](callback);
              } //Just return the module wanted. In this scenario, the
              //deps arg is the module name, and second arg (if passed)
              //is just the relName.
              //Normalize module name, if it contains . or ..


              return callDep(makeMap(deps, makeRelParts(callback)).f);
            } else if (!deps.splice) {
              //deps is a config object, not an array.
              config = deps;

              if (config.deps) {
                _req(config.deps, config.callback);
              }

              if (!callback) {
                return;
              }

              if (callback.splice) {
                //callback is an array, which means it is a dependency list.
                //Adjust args if there are dependencies
                deps = callback;
                callback = relName;
                relName = null;
              } else {
                deps = undef;
              }
            } //Support require(['a'])


            callback = callback || function () {}; //If relName is a function, it is an errback handler,
            //so remove it.


            if (typeof relName === 'function') {
              relName = forceSync;
              forceSync = alt;
            } //Simulate async callback;


            if (forceSync) {
              main(undef, deps, callback, relName);
            } else {
              //Using a non-zero value because of concern for what old browsers
              //do, and latest browsers "upgrade" to 4 if lower value is used:
              //http://www.whatwg.org/specs/web-apps/current-work/multipage/timers.html#dom-windowtimers-settimeout:
              //If want a value immediately, use require('id') instead -- something
              //that works in almond on the global level, but not guaranteed and
              //unlikely to work in other AMD implementations.
              setTimeout(function () {
                main(undef, deps, callback, relName);
              }, 4);
            }

            return _req;
          };
          /**
           * Just drops the config on the floor, but returns req in case
           * the config return value is used.
           */


          _req.config = function (cfg) {
            return _req(cfg);
          };
          /**
           * Expose module registry for debugging and tooling
           */


          requirejs._defined = defined;

          define = function define(name, deps, callback) {
            if (typeof name !== 'string') {
              throw new Error('See almond README: incorrect module build, no module name');
            } //This module may not have dependencies


            if (!deps.splice) {
              //deps is not an array, so probably means
              //an object literal or factory function for
              //the value. Adjust args.
              callback = deps;
              deps = [];
            }

            if (!hasProp(defined, name) && !hasProp(waiting, name)) {
              waiting[name] = [name, deps, callback];
            }
          };

          define.amd = {
            jQuery: true
          };
        })();

        S2.requirejs = requirejs;
        S2.require = require;
        S2.define = define;
      }
    })();

    S2.define("almond", function () {});
    /* global jQuery:false, $:false */

    S2.define('jquery', [], function () {
      var _$ = jQuery || $;

      if (_$ == null && console && console.error) {
        console.error('Select2: An instance of jQuery or a jQuery-compatible library was not ' + 'found. Make sure that you are including jQuery before Select2 on your ' + 'web page.');
      }

      return _$;
    });
    S2.define('select2/utils', ['jquery'], function ($) {
      var Utils = {};

      Utils.Extend = function (ChildClass, SuperClass) {
        var __hasProp = {}.hasOwnProperty;

        function BaseConstructor() {
          this.constructor = ChildClass;
        }

        for (var key in SuperClass) {
          if (__hasProp.call(SuperClass, key)) {
            ChildClass[key] = SuperClass[key];
          }
        }

        BaseConstructor.prototype = SuperClass.prototype;
        ChildClass.prototype = new BaseConstructor();
        ChildClass.__super__ = SuperClass.prototype;
        return ChildClass;
      };

      function getMethods(theClass) {
        var proto = theClass.prototype;
        var methods = [];

        for (var methodName in proto) {
          var m = proto[methodName];

          if (typeof m !== 'function') {
            continue;
          }

          if (methodName === 'constructor') {
            continue;
          }

          methods.push(methodName);
        }

        return methods;
      }

      Utils.Decorate = function (SuperClass, DecoratorClass) {
        var decoratedMethods = getMethods(DecoratorClass);
        var superMethods = getMethods(SuperClass);

        function DecoratedClass() {
          var unshift = Array.prototype.unshift;
          var argCount = DecoratorClass.prototype.constructor.length;
          var calledConstructor = SuperClass.prototype.constructor;

          if (argCount > 0) {
            unshift.call(arguments, SuperClass.prototype.constructor);
            calledConstructor = DecoratorClass.prototype.constructor;
          }

          calledConstructor.apply(this, arguments);
        }

        DecoratorClass.displayName = SuperClass.displayName;

        function ctr() {
          this.constructor = DecoratedClass;
        }

        DecoratedClass.prototype = new ctr();

        for (var m = 0; m < superMethods.length; m++) {
          var superMethod = superMethods[m];
          DecoratedClass.prototype[superMethod] = SuperClass.prototype[superMethod];
        }

        var calledMethod = function calledMethod(methodName) {
          // Stub out the original method if it's not decorating an actual method
          var originalMethod = function originalMethod() {};

          if (methodName in DecoratedClass.prototype) {
            originalMethod = DecoratedClass.prototype[methodName];
          }

          var decoratedMethod = DecoratorClass.prototype[methodName];
          return function () {
            var unshift = Array.prototype.unshift;
            unshift.call(arguments, originalMethod);
            return decoratedMethod.apply(this, arguments);
          };
        };

        for (var d = 0; d < decoratedMethods.length; d++) {
          var decoratedMethod = decoratedMethods[d];
          DecoratedClass.prototype[decoratedMethod] = calledMethod(decoratedMethod);
        }

        return DecoratedClass;
      };

      var Observable = function Observable() {
        this.listeners = {};
      };

      Observable.prototype.on = function (event, callback) {
        this.listeners = this.listeners || {};

        if (event in this.listeners) {
          this.listeners[event].push(callback);
        } else {
          this.listeners[event] = [callback];
        }
      };

      Observable.prototype.trigger = function (event) {
        var slice = Array.prototype.slice;
        var params = slice.call(arguments, 1);
        this.listeners = this.listeners || {}; // Params should always come in as an array

        if (params == null) {
          params = [];
        } // If there are no arguments to the event, use a temporary object


        if (params.length === 0) {
          params.push({});
        } // Set the `_type` of the first object to the event


        params[0]._type = event;

        if (event in this.listeners) {
          this.invoke(this.listeners[event], slice.call(arguments, 1));
        }

        if ('*' in this.listeners) {
          this.invoke(this.listeners['*'], arguments);
        }
      };

      Observable.prototype.invoke = function (listeners, params) {
        for (var i = 0, len = listeners.length; i < len; i++) {
          listeners[i].apply(this, params);
        }
      };

      Utils.Observable = Observable;

      Utils.generateChars = function (length) {
        var chars = '';

        for (var i = 0; i < length; i++) {
          var randomChar = Math.floor(Math.random() * 36);
          chars += randomChar.toString(36);
        }

        return chars;
      };

      Utils.bind = function (func, context) {
        return function () {
          func.apply(context, arguments);
        };
      };

      Utils._convertData = function (data) {
        for (var originalKey in data) {
          var keys = originalKey.split('-');
          var dataLevel = data;

          if (keys.length === 1) {
            continue;
          }

          for (var k = 0; k < keys.length; k++) {
            var key = keys[k]; // Lowercase the first letter
            // By default, dash-separated becomes camelCase

            key = key.substring(0, 1).toLowerCase() + key.substring(1);

            if (!(key in dataLevel)) {
              dataLevel[key] = {};
            }

            if (k == keys.length - 1) {
              dataLevel[key] = data[originalKey];
            }

            dataLevel = dataLevel[key];
          }

          delete data[originalKey];
        }

        return data;
      };

      Utils.hasScroll = function (index, el) {
        // Adapted from the function created by @ShadowScripter
        // and adapted by @BillBarry on the Stack Exchange Code Review website.
        // The original code can be found at
        // http://codereview.stackexchange.com/q/13338
        // and was designed to be used with the Sizzle selector engine.
        var $el = $(el);
        var overflowX = el.style.overflowX;
        var overflowY = el.style.overflowY; //Check both x and y declarations

        if (overflowX === overflowY && (overflowY === 'hidden' || overflowY === 'visible')) {
          return false;
        }

        if (overflowX === 'scroll' || overflowY === 'scroll') {
          return true;
        }

        return $el.innerHeight() < el.scrollHeight || $el.innerWidth() < el.scrollWidth;
      };

      Utils.escapeMarkup = function (markup) {
        var replaceMap = {
          '\\': '&#92;',
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          '\'': '&#39;',
          '/': '&#47;'
        }; // Do not try to escape the markup if it's not a string

        if (typeof markup !== 'string') {
          return markup;
        }

        return String(markup).replace(/[&<>"'\/\\]/g, function (match) {
          return replaceMap[match];
        });
      }; // Cache objects in Utils.__cache instead of $.data (see #4346)


      Utils.__cache = {};
      var id = 0;

      Utils.GetUniqueElementId = function (element) {
        // Get a unique element Id. If element has no id,
        // creates a new unique number, stores it in the id
        // attribute and returns the new id with a prefix.
        // If an id already exists, it simply returns it with a prefix.
        var select2Id = element.getAttribute('data-select2-id');

        if (select2Id != null) {
          return select2Id;
        } // If element has id, use it.


        if (element.id) {
          select2Id = 'select2-data-' + element.id;
        } else {
          select2Id = 'select2-data-' + (++id).toString() + '-' + Utils.generateChars(4);
        }

        element.setAttribute('data-select2-id', select2Id);
        return select2Id;
      };

      Utils.StoreData = function (element, name, value) {
        // Stores an item in the cache for a specified element.
        // name is the cache key.
        var id = Utils.GetUniqueElementId(element);

        if (!Utils.__cache[id]) {
          Utils.__cache[id] = {};
        }

        Utils.__cache[id][name] = value;
      };

      Utils.GetData = function (element, name) {
        // Retrieves a value from the cache by its key (name)
        // name is optional. If no name specified, return
        // all cache items for the specified element.
        // and for a specified element.
        var id = Utils.GetUniqueElementId(element);

        if (name) {
          if (Utils.__cache[id]) {
            if (Utils.__cache[id][name] != null) {
              return Utils.__cache[id][name];
            }

            return $(element).data(name); // Fallback to HTML5 data attribs.
          }

          return $(element).data(name); // Fallback to HTML5 data attribs.
        } else {
          return Utils.__cache[id];
        }
      };

      Utils.RemoveData = function (element) {
        // Removes all cached items for a specified element.
        var id = Utils.GetUniqueElementId(element);

        if (Utils.__cache[id] != null) {
          delete Utils.__cache[id];
        }

        element.removeAttribute('data-select2-id');
      };

      Utils.copyNonInternalCssClasses = function (dest, src) {
        var classes;
        var destinationClasses = dest.getAttribute('class').trim().split(/\s+/);
        destinationClasses = destinationClasses.filter(function (clazz) {
          // Save all Select2 classes
          return clazz.indexOf('select2-') === 0;
        });
        var sourceClasses = src.getAttribute('class').trim().split(/\s+/);
        sourceClasses = sourceClasses.filter(function (clazz) {
          // Only copy non-Select2 classes
          return clazz.indexOf('select2-') !== 0;
        });
        var replacements = destinationClasses.concat(sourceClasses);
        dest.setAttribute('class', replacements.join(' '));
      };

      return Utils;
    });
    S2.define('select2/results', ['jquery', './utils'], function ($, Utils) {
      function Results($element, options, dataAdapter) {
        this.$element = $element;
        this.data = dataAdapter;
        this.options = options;

        Results.__super__.constructor.call(this);
      }

      Utils.Extend(Results, Utils.Observable);

      Results.prototype.render = function () {
        var $results = $('<ul class="select2-results__options" role="listbox"></ul>');

        if (this.options.get('multiple')) {
          $results.attr('aria-multiselectable', 'true');
        }

        this.$results = $results;
        return $results;
      };

      Results.prototype.clear = function () {
        this.$results.empty();
      };

      Results.prototype.displayMessage = function (params) {
        var escapeMarkup = this.options.get('escapeMarkup');
        this.clear();
        this.hideLoading();
        var $message = $('<li role="alert" aria-live="assertive"' + ' class="select2-results__option"></li>');
        var message = this.options.get('translations').get(params.message);
        $message.append(escapeMarkup(message(params.args)));
        $message[0].className += ' select2-results__message';
        this.$results.append($message);
      };

      Results.prototype.hideMessages = function () {
        this.$results.find('.select2-results__message').remove();
      };

      Results.prototype.append = function (data) {
        this.hideLoading();
        var $options = [];

        if (data.results == null || data.results.length === 0) {
          if (this.$results.children().length === 0) {
            this.trigger('results:message', {
              message: 'noResults'
            });
          }

          return;
        }

        data.results = this.sort(data.results);

        for (var d = 0; d < data.results.length; d++) {
          var item = data.results[d];
          var $option = this.option(item);
          $options.push($option);
        }

        this.$results.append($options);
      };

      Results.prototype.position = function ($results, $dropdown) {
        var $resultsContainer = $dropdown.find('.select2-results');
        $resultsContainer.append($results);
      };

      Results.prototype.sort = function (data) {
        var sorter = this.options.get('sorter');
        return sorter(data);
      };

      Results.prototype.highlightFirstItem = function () {
        var $options = this.$results.find('.select2-results__option--selectable');
        var $selected = $options.filter('.select2-results__option--selected'); // Check if there are any selected options

        if ($selected.length > 0) {
          // If there are selected options, highlight the first
          $selected.first().trigger('mouseenter');
        } else {
          // If there are no selected options, highlight the first option
          // in the dropdown
          $options.first().trigger('mouseenter');
        }

        this.ensureHighlightVisible();
      };

      Results.prototype.setClasses = function () {
        var self = this;
        this.data.current(function (selected) {
          var selectedIds = selected.map(function (s) {
            return s.id.toString();
          });
          var $options = self.$results.find('.select2-results__option--selectable');
          $options.each(function () {
            var $option = $(this);
            var item = Utils.GetData(this, 'data'); // id needs to be converted to a string when comparing

            var id = '' + item.id;

            if (item.element != null && item.element.selected || item.element == null && selectedIds.indexOf(id) > -1) {
              this.classList.add('select2-results__option--selected');
              $option.attr('aria-selected', 'true');
            } else {
              this.classList.remove('select2-results__option--selected');
              $option.attr('aria-selected', 'false');
            }
          });
        });
      };

      Results.prototype.showLoading = function (params) {
        this.hideLoading();
        var loadingMore = this.options.get('translations').get('searching');
        var loading = {
          disabled: true,
          loading: true,
          text: loadingMore(params)
        };
        var $loading = this.option(loading);
        $loading.className += ' loading-results';
        this.$results.prepend($loading);
      };

      Results.prototype.hideLoading = function () {
        this.$results.find('.loading-results').remove();
      };

      Results.prototype.option = function (data) {
        var option = document.createElement('li');
        option.classList.add('select2-results__option');
        option.classList.add('select2-results__option--selectable');
        var attrs = {
          'role': 'option'
        };
        var matches = window.Element.prototype.matches || window.Element.prototype.msMatchesSelector || window.Element.prototype.webkitMatchesSelector;

        if (data.element != null && matches.call(data.element, ':disabled') || data.element == null && data.disabled) {
          attrs['aria-disabled'] = 'true';
          option.classList.remove('select2-results__option--selectable');
          option.classList.add('select2-results__option--disabled');
        }

        if (data.id == null) {
          option.classList.remove('select2-results__option--selectable');
        }

        if (data._resultId != null) {
          option.id = data._resultId;
        }

        if (data.title) {
          option.title = data.title;
        }

        if (data.children) {
          attrs.role = 'group';
          attrs['aria-label'] = data.text;
          option.classList.remove('select2-results__option--selectable');
          option.classList.add('select2-results__option--group');
        }

        for (var attr in attrs) {
          var val = attrs[attr];
          option.setAttribute(attr, val);
        }

        if (data.children) {
          var $option = $(option);
          var label = document.createElement('strong');
          label.className = 'select2-results__group';
          this.template(data, label);
          var $children = [];

          for (var c = 0; c < data.children.length; c++) {
            var child = data.children[c];
            var $child = this.option(child);
            $children.push($child);
          }

          var $childrenContainer = $('<ul></ul>', {
            'class': 'select2-results__options select2-results__options--nested',
            'role': 'none'
          });
          $childrenContainer.append($children);
          $option.append(label);
          $option.append($childrenContainer);
        } else {
          this.template(data, option);
        }

        Utils.StoreData(option, 'data', data);
        return option;
      };

      Results.prototype.bind = function (container, $container) {
        var self = this;
        var id = container.id + '-results';
        this.$results.attr('id', id);
        container.on('results:all', function (params) {
          self.clear();
          self.append(params.data);

          if (container.isOpen()) {
            self.setClasses();
            self.highlightFirstItem();
          }
        });
        container.on('results:append', function (params) {
          self.append(params.data);

          if (container.isOpen()) {
            self.setClasses();
          }
        });
        container.on('query', function (params) {
          self.hideMessages();
          self.showLoading(params);
        });
        container.on('select', function () {
          if (!container.isOpen()) {
            return;
          }

          self.setClasses();

          if (self.options.get('scrollAfterSelect')) {
            self.highlightFirstItem();
          }
        });
        container.on('unselect', function () {
          if (!container.isOpen()) {
            return;
          }

          self.setClasses();

          if (self.options.get('scrollAfterSelect')) {
            self.highlightFirstItem();
          }
        });
        container.on('open', function () {
          // When the dropdown is open, aria-expended="true"
          self.$results.attr('aria-expanded', 'true');
          self.$results.attr('aria-hidden', 'false');
          self.setClasses();
          self.ensureHighlightVisible();
        });
        container.on('close', function () {
          // When the dropdown is closed, aria-expended="false"
          self.$results.attr('aria-expanded', 'false');
          self.$results.attr('aria-hidden', 'true');
          self.$results.removeAttr('aria-activedescendant');
        });
        container.on('results:toggle', function () {
          var $highlighted = self.getHighlightedResults();

          if ($highlighted.length === 0) {
            return;
          }

          $highlighted.trigger('mouseup');
        });
        container.on('results:select', function () {
          var $highlighted = self.getHighlightedResults();

          if ($highlighted.length === 0) {
            return;
          }

          var data = Utils.GetData($highlighted[0], 'data');

          if ($highlighted.hasClass('select2-results__option--selected')) {
            self.trigger('close', {});
          } else {
            self.trigger('select', {
              data: data
            });
          }
        });
        container.on('results:previous', function () {
          var $highlighted = self.getHighlightedResults();
          var $options = self.$results.find('.select2-results__option--selectable');
          var currentIndex = $options.index($highlighted); // If we are already at the top, don't move further
          // If no options, currentIndex will be -1

          if (currentIndex <= 0) {
            return;
          }

          var nextIndex = currentIndex - 1; // If none are highlighted, highlight the first

          if ($highlighted.length === 0) {
            nextIndex = 0;
          }

          var $next = $options.eq(nextIndex);
          $next.trigger('mouseenter');
          var currentOffset = self.$results.offset().top;
          var nextTop = $next.offset().top;
          var nextOffset = self.$results.scrollTop() + (nextTop - currentOffset);

          if (nextIndex === 0) {
            self.$results.scrollTop(0);
          } else if (nextTop - currentOffset < 0) {
            self.$results.scrollTop(nextOffset);
          }
        });
        container.on('results:next', function () {
          var $highlighted = self.getHighlightedResults();
          var $options = self.$results.find('.select2-results__option--selectable');
          var currentIndex = $options.index($highlighted);
          var nextIndex = currentIndex + 1; // If we are at the last option, stay there

          if (nextIndex >= $options.length) {
            return;
          }

          var $next = $options.eq(nextIndex);
          $next.trigger('mouseenter');
          var currentOffset = self.$results.offset().top + self.$results.outerHeight(false);
          var nextBottom = $next.offset().top + $next.outerHeight(false);
          var nextOffset = self.$results.scrollTop() + nextBottom - currentOffset;

          if (nextIndex === 0) {
            self.$results.scrollTop(0);
          } else if (nextBottom > currentOffset) {
            self.$results.scrollTop(nextOffset);
          }
        });
        container.on('results:focus', function (params) {
          params.element[0].classList.add('select2-results__option--highlighted');
          params.element[0].setAttribute('aria-selected', 'true');
        });
        container.on('results:message', function (params) {
          self.displayMessage(params);
        });

        if ($.fn.mousewheel) {
          this.$results.on('mousewheel', function (e) {
            var top = self.$results.scrollTop();
            var bottom = self.$results.get(0).scrollHeight - top + e.deltaY;
            var isAtTop = e.deltaY > 0 && top - e.deltaY <= 0;
            var isAtBottom = e.deltaY < 0 && bottom <= self.$results.height();

            if (isAtTop) {
              self.$results.scrollTop(0);
              e.preventDefault();
              e.stopPropagation();
            } else if (isAtBottom) {
              self.$results.scrollTop(self.$results.get(0).scrollHeight - self.$results.height());
              e.preventDefault();
              e.stopPropagation();
            }
          });
        }

        this.$results.on('mouseup', '.select2-results__option--selectable', function (evt) {
          var $this = $(this);
          var data = Utils.GetData(this, 'data');

          if ($this.hasClass('select2-results__option--selected')) {
            if (self.options.get('multiple')) {
              self.trigger('unselect', {
                originalEvent: evt,
                data: data
              });
            } else {
              self.trigger('close', {});
            }

            return;
          }

          self.trigger('select', {
            originalEvent: evt,
            data: data
          });
        });
        this.$results.on('mouseenter', '.select2-results__option--selectable', function (evt) {
          var data = Utils.GetData(this, 'data');
          self.getHighlightedResults().removeClass('select2-results__option--highlighted').attr('aria-selected', 'false');
          self.trigger('results:focus', {
            data: data,
            element: $(this)
          });
        });
      };

      Results.prototype.getHighlightedResults = function () {
        var $highlighted = this.$results.find('.select2-results__option--highlighted');
        return $highlighted;
      };

      Results.prototype.destroy = function () {
        this.$results.remove();
      };

      Results.prototype.ensureHighlightVisible = function () {
        var $highlighted = this.getHighlightedResults();

        if ($highlighted.length === 0) {
          return;
        }

        var $options = this.$results.find('.select2-results__option--selectable');
        var currentIndex = $options.index($highlighted);
        var currentOffset = this.$results.offset().top;
        var nextTop = $highlighted.offset().top;
        var nextOffset = this.$results.scrollTop() + (nextTop - currentOffset);
        var offsetDelta = nextTop - currentOffset;
        nextOffset -= $highlighted.outerHeight(false) * 2;

        if (currentIndex <= 2) {
          this.$results.scrollTop(0);
        } else if (offsetDelta > this.$results.outerHeight() || offsetDelta < 0) {
          this.$results.scrollTop(nextOffset);
        }
      };

      Results.prototype.template = function (result, container) {
        var template = this.options.get('templateResult');
        var escapeMarkup = this.options.get('escapeMarkup');
        var content = template(result, container);

        if (content == null) {
          container.style.display = 'none';
        } else if (typeof content === 'string') {
          container.innerHTML = escapeMarkup(content);
        } else {
          $(container).append(content);
        }
      };

      return Results;
    });
    S2.define('select2/keys', [], function () {
      var KEYS = {
        BACKSPACE: 8,
        TAB: 9,
        ENTER: 13,
        SHIFT: 16,
        CTRL: 17,
        ALT: 18,
        ESC: 27,
        SPACE: 32,
        PAGE_UP: 33,
        PAGE_DOWN: 34,
        END: 35,
        HOME: 36,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        DELETE: 46
      };
      return KEYS;
    });
    S2.define('select2/selection/base', ['jquery', '../utils', '../keys'], function ($, Utils, KEYS) {
      function BaseSelection($element, options) {
        this.$element = $element;
        this.options = options;

        BaseSelection.__super__.constructor.call(this);
      }

      Utils.Extend(BaseSelection, Utils.Observable);

      BaseSelection.prototype.render = function () {
        var $selection = $('<span class="select2-selection" role="combobox" ' + ' aria-haspopup="true" aria-expanded="false">' + '</span>');
        this._tabindex = 0;

        if (Utils.GetData(this.$element[0], 'old-tabindex') != null) {
          this._tabindex = Utils.GetData(this.$element[0], 'old-tabindex');
        } else if (this.$element.attr('tabindex') != null) {
          this._tabindex = this.$element.attr('tabindex');
        }

        $selection.attr('title', this.$element.attr('title'));
        $selection.attr('tabindex', this._tabindex);
        $selection.attr('aria-disabled', 'false');
        this.$selection = $selection;
        return $selection;
      };

      BaseSelection.prototype.bind = function (container, $container) {
        var self = this;
        var resultsId = container.id + '-results';
        this.container = container;
        this.$selection.on('focus', function (evt) {
          self.trigger('focus', evt);
        });
        this.$selection.on('blur', function (evt) {
          self._handleBlur(evt);
        });
        this.$selection.on('keydown', function (evt) {
          self.trigger('keypress', evt);

          if (evt.which === KEYS.SPACE) {
            evt.preventDefault();
          }
        });
        container.on('results:focus', function (params) {
          self.$selection.attr('aria-activedescendant', params.data._resultId);
        });
        container.on('selection:update', function (params) {
          self.update(params.data);
        });
        container.on('open', function () {
          // When the dropdown is open, aria-expanded="true"
          self.$selection.attr('aria-expanded', 'true');
          self.$selection.attr('aria-owns', resultsId);

          self._attachCloseHandler(container);
        });
        container.on('close', function () {
          // When the dropdown is closed, aria-expanded="false"
          self.$selection.attr('aria-expanded', 'false');
          self.$selection.removeAttr('aria-activedescendant');
          self.$selection.removeAttr('aria-owns');
          self.$selection.trigger('focus');

          self._detachCloseHandler(container);
        });
        container.on('enable', function () {
          self.$selection.attr('tabindex', self._tabindex);
          self.$selection.attr('aria-disabled', 'false');
        });
        container.on('disable', function () {
          self.$selection.attr('tabindex', '-1');
          self.$selection.attr('aria-disabled', 'true');
        });
      };

      BaseSelection.prototype._handleBlur = function (evt) {
        var self = this; // This needs to be delayed as the active element is the body when the tab
        // key is pressed, possibly along with others.

        window.setTimeout(function () {
          // Don't trigger `blur` if the focus is still in the selection
          if (document.activeElement == self.$selection[0] || $.contains(self.$selection[0], document.activeElement)) {
            return;
          }

          self.trigger('blur', evt);
        }, 1);
      };

      BaseSelection.prototype._attachCloseHandler = function (container) {
        $(document.body).on('mousedown.select2.' + container.id, function (e) {
          var $target = $(e.target);
          var $select = $target.closest('.select2');
          var $all = $('.select2.select2-container--open');
          $all.each(function () {
            if (this == $select[0]) {
              return;
            }

            var $element = Utils.GetData(this, 'element'); // Renamed function. @edited
            // old: $element.select2('close');

            $element.SUIselect2('close');
          });
        });
      };

      BaseSelection.prototype._detachCloseHandler = function (container) {
        $(document.body).off('mousedown.select2.' + container.id);
      };

      BaseSelection.prototype.position = function ($selection, $container) {
        var $selectionContainer = $container.find('.selection');
        $selectionContainer.append($selection);
      };

      BaseSelection.prototype.destroy = function () {
        this._detachCloseHandler(this.container);
      };

      BaseSelection.prototype.update = function (data) {
        throw new Error('The `update` method must be defined in child classes.');
      };
      /**
       * Helper method to abstract the "enabled" (not "disabled") state of this
       * object.
       *
       * @return {true} if the instance is not disabled.
       * @return {false} if the instance is disabled.
       */


      BaseSelection.prototype.isEnabled = function () {
        return !this.isDisabled();
      };
      /**
       * Helper method to abstract the "disabled" state of this object.
       *
       * @return {true} if the disabled option is true.
       * @return {false} if the disabled option is false.
       */


      BaseSelection.prototype.isDisabled = function () {
        return this.options.get('disabled');
      };

      return BaseSelection;
    });
    S2.define('select2/selection/single', ['jquery', './base', '../utils', '../keys'], function ($, BaseSelection, Utils, KEYS) {
      function SingleSelection() {
        SingleSelection.__super__.constructor.apply(this, arguments);
      }

      Utils.Extend(SingleSelection, BaseSelection);

      SingleSelection.prototype.render = function () {
        var $selection = SingleSelection.__super__.render.call(this);

        $selection[0].classList.add('select2-selection--single'); // Assign SUI icon to select button. @edited

        $selection.html('<span class="select2-selection__rendered"></span>' + '<span class="select2-selection__arrow" role="presentation">' + '<span class="sui-icon-chevron-down sui-sm" aria-hidden="true"></span>' + '</span>');
        return $selection;
      };

      SingleSelection.prototype.bind = function (container, $container) {
        var self = this;

        SingleSelection.__super__.bind.apply(this, arguments);

        var id = container.id + '-container';
        this.$selection.find('.select2-selection__rendered').attr('id', id).attr('role', 'textbox').attr('aria-readonly', 'true');
        this.$selection.attr('aria-labelledby', id);
        this.$selection.attr('aria-controls', id);
        this.$selection.on('mousedown', function (evt) {
          // Only respond to left clicks
          if (evt.which !== 1) {
            return;
          }

          self.trigger('toggle', {
            originalEvent: evt
          });
        });
        this.$selection.on('focus', function (evt) {// User focuses on the container
        });
        this.$selection.on('blur', function (evt) {// User exits the container
        });
        container.on('focus', function (evt) {
          if (!container.isOpen()) {
            self.$selection.trigger('focus');
          }
        });
      };

      SingleSelection.prototype.clear = function () {
        var $rendered = this.$selection.find('.select2-selection__rendered');
        $rendered.empty();
        $rendered.removeAttr('title'); // clear tooltip on empty
      };

      SingleSelection.prototype.display = function (data, container) {
        var template = this.options.get('templateSelection');
        var escapeMarkup = this.options.get('escapeMarkup');
        return escapeMarkup(template(data, container));
      };

      SingleSelection.prototype.selectionContainer = function () {
        return $('<span></span>');
      };

      SingleSelection.prototype.update = function (data) {
        // Add icon when variables is empty. @edited
        if (data.length === 0) {
          this.clear();

          if ('vars' === this.options.get('theme')) {
            this.$selection.find('.select2-selection__rendered').html('<span class="sui-icon-plus-circle sui-md" aria-hidden="true"></span>');
          }

          return;
        }

        var selection = data[0];
        var $rendered = this.$selection.find('.select2-selection__rendered');
        var formatted = this.display(selection, $rendered);
        $rendered.empty().append(formatted);
        var title = selection.title || selection.text;

        if (title) {
          $rendered.attr('title', title);
        } else {
          $rendered.removeAttr('title');
        }
      };

      return SingleSelection;
    });
    S2.define('select2/selection/multiple', ['jquery', './base', '../utils'], function ($, BaseSelection, Utils) {
      function MultipleSelection($element, options) {
        MultipleSelection.__super__.constructor.apply(this, arguments);
      }

      Utils.Extend(MultipleSelection, BaseSelection);

      MultipleSelection.prototype.render = function () {
        var $selection = MultipleSelection.__super__.render.call(this);

        $selection[0].classList.add('select2-selection--multiple');
        $selection.html('<ul class="select2-selection__rendered"></ul>');
        return $selection;
      };

      MultipleSelection.prototype.bind = function (container, $container) {
        var self = this;

        MultipleSelection.__super__.bind.apply(this, arguments);

        var id = container.id + '-container';
        this.$selection.find('.select2-selection__rendered').attr('id', id);
        this.$selection.on('click', function (evt) {
          self.trigger('toggle', {
            originalEvent: evt
          });
        });
        this.$selection.on('click', '.sui-button-icon', function (evt) {
          // Ignore the event if it is disabled
          if (self.isDisabled()) {
            return;
          }

          var $remove = $(this);
          var $selection = $remove.parent();
          var data = Utils.GetData($selection[0], 'data');
          self.trigger('unselect', {
            originalEvent: evt,
            data: data
          });
        });
        this.$selection.on('keydown', '.sui-button-icon', function (evt) {
          // Ignore the event if it is disabled
          if (self.isDisabled()) {
            return;
          }

          evt.stopPropagation();
        });
      };

      MultipleSelection.prototype.clear = function () {
        var $rendered = this.$selection.find('.select2-selection__rendered');
        $rendered.empty();
        $rendered.removeAttr('title');
        $rendered.removeClass('has-option-selected');
      };

      MultipleSelection.prototype.display = function (data, container) {
        var template = this.options.get('templateSelection');
        var escapeMarkup = this.options.get('escapeMarkup');
        return escapeMarkup(template(data, container));
      };

      MultipleSelection.prototype.selectionContainer = function () {
        var $container = $('<li class="select2-selection__choice">' + '<span class="select2-selection__choice__display"></span>' + '<button type="button" class="sui-button-icon" ' + 'tabindex="-1">' + '<span class="sui-icon-close sui-sm" aria-hidden="true"></span>' + '</button>' + '</li>');
        return $container;
      };

      MultipleSelection.prototype.update = function (data) {
        this.clear();

        if (data.length === 0) {
          return;
        }

        var $selections = [];
        var selectionIdPrefix = this.$selection.find('.select2-selection__rendered').attr('id') + '-choice-';

        for (var d = 0; d < data.length; d++) {
          var selection = data[d];
          var $selection = this.selectionContainer();
          var formatted = this.display(selection, $selection);
          var selectionId = selectionIdPrefix + Utils.generateChars(4) + '-';

          if (selection.id) {
            selectionId += selection.id;
          } else {
            selectionId += Utils.generateChars(4);
          }

          $selection.find('.select2-selection__choice__display').append(formatted).attr('id', selectionId);
          var title = selection.title || selection.text;

          if (title) {
            $selection.attr('title', title);
          }

          var removeItem = this.options.get('translations').get('removeItem');
          var $remove = $selection.find('.sui-button-icon');
          $remove.attr('title', removeItem());
          $remove.attr('aria-label', removeItem());
          $remove.attr('aria-describedby', selectionId);
          Utils.StoreData($selection[0], 'data', selection);
          $selections.push($selection);
        }

        var $rendered = this.$selection.find('.select2-selection__rendered');
        $rendered.append($selections).addClass('has-option-selected');
      };

      return MultipleSelection;
    });
    S2.define('select2/selection/placeholder', [], function () {
      function Placeholder(decorated, $element, options) {
        this.placeholder = this.normalizePlaceholder(options.get('placeholder'));
        decorated.call(this, $element, options);
      }

      Placeholder.prototype.normalizePlaceholder = function (_, placeholder) {
        if (typeof placeholder === 'string') {
          placeholder = {
            id: '',
            text: placeholder
          };
        }

        return placeholder;
      };

      Placeholder.prototype.createPlaceholder = function (decorated, placeholder) {
        var $placeholder = this.selectionContainer();
        $placeholder.html(this.display(placeholder));
        $placeholder[0].classList.add('select2-selection__placeholder');
        $placeholder[0].classList.remove('select2-selection__choice');
        var placeholderTitle = placeholder.title || placeholder.text || $placeholder.text();
        this.$selection.find('.select2-selection__rendered').attr('title', placeholderTitle);
        return $placeholder;
      };

      Placeholder.prototype.update = function (decorated, data) {
        var singlePlaceholder = data.length == 1 && data[0].id != this.placeholder.id;
        var multipleSelections = data.length > 1;

        if (multipleSelections || singlePlaceholder) {
          return decorated.call(this, data);
        }

        this.clear();
        var $placeholder = this.createPlaceholder(this.placeholder);
        this.$selection.find('.select2-selection__rendered').append($placeholder);
      };

      return Placeholder;
    });
    S2.define('select2/selection/allowClear', ['jquery', '../keys', '../utils'], function ($, KEYS, Utils) {
      function AllowClear() {}

      AllowClear.prototype.bind = function (decorated, container, $container) {
        var self = this;
        decorated.call(this, container, $container);

        if (this.placeholder == null) {
          if (this.options.get('debug') && window.console && console.error) {
            console.error('Select2: The `allowClear` option should be used in combination ' + 'with the `placeholder` option.');
          }
        }

        this.$selection.on('mousedown', '.select2-selection__clear', function (evt) {
          self._handleClear(evt);
        });
        container.on('keypress', function (evt) {
          self._handleKeyboardClear(evt, container);
        });
      };

      AllowClear.prototype._handleClear = function (_, evt) {
        // Ignore the event if it is disabled
        if (this.isDisabled()) {
          return;
        }

        var $clear = this.$selection.find('.select2-selection__clear'); // Ignore the event if nothing has been selected

        if ($clear.length === 0) {
          return;
        }

        evt.stopPropagation();
        var data = Utils.GetData($clear[0], 'data');
        var previousVal = this.$element.val();
        this.$element.val(this.placeholder.id);
        var unselectData = {
          data: data
        };
        this.trigger('clear', unselectData);

        if (unselectData.prevented) {
          this.$element.val(previousVal);
          return;
        }

        for (var d = 0; d < data.length; d++) {
          unselectData = {
            data: data[d]
          }; // Trigger the `unselect` event, so people can prevent it from being
          // cleared.

          this.trigger('unselect', unselectData); // If the event was prevented, don't clear it out.

          if (unselectData.prevented) {
            this.$element.val(previousVal);
            return;
          }
        }

        this.$element.trigger('input').trigger('change');
        this.trigger('toggle', {});
      };

      AllowClear.prototype._handleKeyboardClear = function (_, evt, container) {
        if (container.isOpen()) {
          return;
        }

        if (evt.which == KEYS.DELETE || evt.which == KEYS.BACKSPACE) {
          this._handleClear(evt);
        }
      };

      AllowClear.prototype.update = function (decorated, data) {
        decorated.call(this, data);
        this.$selection.find('.select2-selection__clear').remove();
        this.$selection[0].classList.remove('select2-selection--clearable');

        if (this.$selection.find('.select2-selection__placeholder').length > 0 || data.length === 0) {
          return;
        }

        var selectionId = this.$selection.find('.select2-selection__rendered').attr('id');
        var removeAll = this.options.get('translations').get('removeAllItems');
        var $remove = $('<button type="button" class="select2-selection__clear" tabindex="-1">' + '<span aria-hidden="true">&times;</span>' + '</button>');
        $remove.attr('title', removeAll());
        $remove.attr('aria-label', removeAll());
        $remove.attr('aria-describedby', selectionId);
        Utils.StoreData($remove[0], 'data', data);
        this.$selection.prepend($remove);
        this.$selection[0].classList.add('select2-selection--clearable');
      };

      return AllowClear;
    });
    S2.define('select2/selection/search', ['jquery', '../utils', '../keys'], function ($, Utils, KEYS) {
      function Search(decorated, $element, options) {
        decorated.call(this, $element, options);
      }

      Search.prototype.render = function (decorated) {
        var searchLabel = this.options.get('translations').get('search');
        var $search = $('<span class="select2-search select2-search--inline">' + '<textarea class="select2-search__field"' + ' type="search" tabindex="-1"' + ' autocorrect="off" autocapitalize="none"' + ' spellcheck="false" role="searchbox" aria-autocomplete="list" >' + '</textarea>' + '</span>');
        this.$searchContainer = $search;
        this.$search = $search.find('textarea');
        this.$search.prop('autocomplete', this.options.get('autocomplete'));
        this.$search.attr('aria-label', searchLabel());
        var $rendered = decorated.call(this);

        this._transferTabIndex();

        $rendered.append(this.$searchContainer);
        return $rendered;
      };

      Search.prototype.bind = function (decorated, container, $container) {
        var self = this;
        var resultsId = container.id + '-results';
        var selectionId = container.id + '-container';
        decorated.call(this, container, $container);
        self.$search.attr('aria-describedby', selectionId);
        container.on('open', function () {
          self.$search.attr('aria-controls', resultsId);
          self.$search.trigger('focus');
        });
        container.on('close', function () {
          self.$search.val('');
          self.resizeSearch();
          self.$search.removeAttr('aria-controls');
          self.$search.removeAttr('aria-activedescendant');
          self.$search.trigger('focus');
        });
        container.on('enable', function () {
          self.$search.prop('disabled', false);

          self._transferTabIndex();
        });
        container.on('disable', function () {
          self.$search.prop('disabled', true);
        });
        container.on('focus', function (evt) {
          self.$search.trigger('focus');
        });
        container.on('results:focus', function (params) {
          if (params.data._resultId) {
            self.$search.attr('aria-activedescendant', params.data._resultId);
          } else {
            self.$search.removeAttr('aria-activedescendant');
          }
        });
        this.$selection.on('focusin', '.select2-search--inline', function (evt) {
          self.trigger('focus', evt);
        });
        this.$selection.on('focusout', '.select2-search--inline', function (evt) {
          self._handleBlur(evt);
        });
        this.$selection.on('keydown', '.select2-search--inline', function (evt) {
          evt.stopPropagation();
          self.trigger('keypress', evt);
          self._keyUpPrevented = evt.isDefaultPrevented();
          var key = evt.which;

          if (key === KEYS.BACKSPACE && self.$search.val() === '') {
            var $previousChoice = self.$selection.find('.select2-selection__choice').last();

            if ($previousChoice.length > 0) {
              var item = Utils.GetData($previousChoice[0], 'data');
              self.searchRemoveChoice(item);
              evt.preventDefault();
            }
          }
        });
        this.$selection.on('click', '.select2-search--inline', function (evt) {
          if (self.$search.val()) {
            evt.stopPropagation();
          }
        }); // Try to detect the IE version should the `documentMode` property that
        // is stored on the document. This is only implemented in IE and is
        // slightly cleaner than doing a user agent check.
        // This property is not available in Edge, but Edge also doesn't have
        // this bug.

        var msie = document.documentMode;
        var disableInputEvents = msie && msie <= 11; // Workaround for browsers which do not support the `input` event
        // This will prevent double-triggering of events for browsers which support
        // both the `keyup` and `input` events.

        this.$selection.on('input.searchcheck', '.select2-search--inline', function (evt) {
          // IE will trigger the `input` event when a placeholder is used on a
          // search box. To get around this issue, we are forced to ignore all
          // `input` events in IE and keep using `keyup`.
          if (disableInputEvents) {
            self.$selection.off('input.search input.searchcheck');
            return;
          } // Unbind the duplicated `keyup` event


          self.$selection.off('keyup.search');
        });
        this.$selection.on('keyup.search input.search', '.select2-search--inline', function (evt) {
          // IE will trigger the `input` event when a placeholder is used on a
          // search box. To get around this issue, we are forced to ignore all
          // `input` events in IE and keep using `keyup`.
          if (disableInputEvents && evt.type === 'input') {
            self.$selection.off('input.search input.searchcheck');
            return;
          }

          var key = evt.which; // We can freely ignore events from modifier keys

          if (key == KEYS.SHIFT || key == KEYS.CTRL || key == KEYS.ALT) {
            return;
          } // Tabbing will be handled during the `keydown` phase


          if (key == KEYS.TAB) {
            return;
          }

          self.handleSearch(evt);
        });
      };
      /**
       * This method will transfer the tabindex attribute from the rendered
       * selection to the search box. This allows for the search box to be used as
       * the primary focus instead of the selection container.
       *
       * @private
       */


      Search.prototype._transferTabIndex = function (decorated) {
        this.$search.attr('tabindex', this.$selection.attr('tabindex'));
        this.$selection.attr('tabindex', '-1');
      };

      Search.prototype.createPlaceholder = function (decorated, placeholder) {
        this.$search.attr('placeholder', placeholder.text);
      };

      Search.prototype.update = function (decorated, data) {
        var searchHadFocus = this.$search[0] == document.activeElement;
        this.$search.attr('placeholder', '');
        decorated.call(this, data);
        this.resizeSearch();

        if (searchHadFocus) {
          this.$search.trigger('focus');
        }
      };

      Search.prototype.handleSearch = function () {
        this.resizeSearch();

        if (!this._keyUpPrevented) {
          var input = this.$search.val();
          this.trigger('query', {
            term: input
          });
        }

        this._keyUpPrevented = false;
      };

      Search.prototype.searchRemoveChoice = function (decorated, item) {
        this.trigger('unselect', {
          data: item
        });
        this.$search.val(item.text);
        this.handleSearch();
      };

      Search.prototype.resizeSearch = function () {
        this.$search.css('width', '25px');
        var width = '100%';

        if (this.$search.attr('placeholder') === '') {
          var minimumWidth = this.$search.val().length + 1;
          width = minimumWidth * 0.75 + 'em';
        }

        this.$search.css('width', width);
      };

      return Search;
    });
    S2.define('select2/selection/selectionCss', ['../utils'], function (Utils) {
      function SelectionCSS() {}

      SelectionCSS.prototype.render = function (decorated) {
        var $selection = decorated.call(this);
        var selectionCssClass = this.options.get('selectionCssClass') || '';

        if (selectionCssClass.indexOf(':all:') !== -1) {
          selectionCssClass = selectionCssClass.replace(':all:', '');
          Utils.copyNonInternalCssClasses($selection[0], this.$element[0]);
        }

        $selection.addClass(selectionCssClass);
        return $selection;
      };

      return SelectionCSS;
    });
    S2.define('select2/selection/eventRelay', ['jquery'], function ($) {
      function EventRelay() {}

      EventRelay.prototype.bind = function (decorated, container, $container) {
        var self = this;
        var relayEvents = ['open', 'opening', 'close', 'closing', 'select', 'selecting', 'unselect', 'unselecting', 'clear', 'clearing'];
        var preventableEvents = ['opening', 'closing', 'selecting', 'unselecting', 'clearing'];
        decorated.call(this, container, $container);
        container.on('*', function (name, params) {
          // Ignore events that should not be relayed
          if (relayEvents.indexOf(name) === -1) {
            return;
          } // The parameters should always be an object


          params = params || {}; // Generate the jQuery event for the Select2 event

          var evt = $.Event('select2:' + name, {
            params: params
          });
          self.$element.trigger(evt); // Only handle preventable events if it was one

          if (preventableEvents.indexOf(name) === -1) {
            return;
          }

          params.prevented = evt.isDefaultPrevented();
        });
      };

      return EventRelay;
    });
    S2.define('select2/translation', ['jquery', 'require'], function ($, require) {
      function Translation(dict) {
        this.dict = dict || {};
      }

      Translation.prototype.all = function () {
        return this.dict;
      };

      Translation.prototype.get = function (key) {
        return this.dict[key];
      };

      Translation.prototype.extend = function (translation) {
        this.dict = $.extend({}, translation.all(), this.dict);
      }; // Static functions


      Translation._cache = {};

      Translation.loadPath = function (path) {
        if (!(path in Translation._cache)) {
          var translations = require(path);

          Translation._cache[path] = translations;
        }

        return new Translation(Translation._cache[path]);
      };

      return Translation;
    });
    S2.define('select2/diacritics', [], function () {
      var diacritics = {
        "\u24B6": 'A',
        "\uFF21": 'A',
        "\xC0": 'A',
        "\xC1": 'A',
        "\xC2": 'A',
        "\u1EA6": 'A',
        "\u1EA4": 'A',
        "\u1EAA": 'A',
        "\u1EA8": 'A',
        "\xC3": 'A',
        "\u0100": 'A',
        "\u0102": 'A',
        "\u1EB0": 'A',
        "\u1EAE": 'A',
        "\u1EB4": 'A',
        "\u1EB2": 'A',
        "\u0226": 'A',
        "\u01E0": 'A',
        "\xC4": 'A',
        "\u01DE": 'A',
        "\u1EA2": 'A',
        "\xC5": 'A',
        "\u01FA": 'A',
        "\u01CD": 'A',
        "\u0200": 'A',
        "\u0202": 'A',
        "\u1EA0": 'A',
        "\u1EAC": 'A',
        "\u1EB6": 'A',
        "\u1E00": 'A',
        "\u0104": 'A',
        "\u023A": 'A',
        "\u2C6F": 'A',
        "\uA732": 'AA',
        "\xC6": 'AE',
        "\u01FC": 'AE',
        "\u01E2": 'AE',
        "\uA734": 'AO',
        "\uA736": 'AU',
        "\uA738": 'AV',
        "\uA73A": 'AV',
        "\uA73C": 'AY',
        "\u24B7": 'B',
        "\uFF22": 'B',
        "\u1E02": 'B',
        "\u1E04": 'B',
        "\u1E06": 'B',
        "\u0243": 'B',
        "\u0182": 'B',
        "\u0181": 'B',
        "\u24B8": 'C',
        "\uFF23": 'C',
        "\u0106": 'C',
        "\u0108": 'C',
        "\u010A": 'C',
        "\u010C": 'C',
        "\xC7": 'C',
        "\u1E08": 'C',
        "\u0187": 'C',
        "\u023B": 'C',
        "\uA73E": 'C',
        "\u24B9": 'D',
        "\uFF24": 'D',
        "\u1E0A": 'D',
        "\u010E": 'D',
        "\u1E0C": 'D',
        "\u1E10": 'D',
        "\u1E12": 'D',
        "\u1E0E": 'D',
        "\u0110": 'D',
        "\u018B": 'D',
        "\u018A": 'D',
        "\u0189": 'D',
        "\uA779": 'D',
        "\u01F1": 'DZ',
        "\u01C4": 'DZ',
        "\u01F2": 'Dz',
        "\u01C5": 'Dz',
        "\u24BA": 'E',
        "\uFF25": 'E',
        "\xC8": 'E',
        "\xC9": 'E',
        "\xCA": 'E',
        "\u1EC0": 'E',
        "\u1EBE": 'E',
        "\u1EC4": 'E',
        "\u1EC2": 'E',
        "\u1EBC": 'E',
        "\u0112": 'E',
        "\u1E14": 'E',
        "\u1E16": 'E',
        "\u0114": 'E',
        "\u0116": 'E',
        "\xCB": 'E',
        "\u1EBA": 'E',
        "\u011A": 'E',
        "\u0204": 'E',
        "\u0206": 'E',
        "\u1EB8": 'E',
        "\u1EC6": 'E',
        "\u0228": 'E',
        "\u1E1C": 'E',
        "\u0118": 'E',
        "\u1E18": 'E',
        "\u1E1A": 'E',
        "\u0190": 'E',
        "\u018E": 'E',
        "\u24BB": 'F',
        "\uFF26": 'F',
        "\u1E1E": 'F',
        "\u0191": 'F',
        "\uA77B": 'F',
        "\u24BC": 'G',
        "\uFF27": 'G',
        "\u01F4": 'G',
        "\u011C": 'G',
        "\u1E20": 'G',
        "\u011E": 'G',
        "\u0120": 'G',
        "\u01E6": 'G',
        "\u0122": 'G',
        "\u01E4": 'G',
        "\u0193": 'G',
        "\uA7A0": 'G',
        "\uA77D": 'G',
        "\uA77E": 'G',
        "\u24BD": 'H',
        "\uFF28": 'H',
        "\u0124": 'H',
        "\u1E22": 'H',
        "\u1E26": 'H',
        "\u021E": 'H',
        "\u1E24": 'H',
        "\u1E28": 'H',
        "\u1E2A": 'H',
        "\u0126": 'H',
        "\u2C67": 'H',
        "\u2C75": 'H',
        "\uA78D": 'H',
        "\u24BE": 'I',
        "\uFF29": 'I',
        "\xCC": 'I',
        "\xCD": 'I',
        "\xCE": 'I',
        "\u0128": 'I',
        "\u012A": 'I',
        "\u012C": 'I',
        "\u0130": 'I',
        "\xCF": 'I',
        "\u1E2E": 'I',
        "\u1EC8": 'I',
        "\u01CF": 'I',
        "\u0208": 'I',
        "\u020A": 'I',
        "\u1ECA": 'I',
        "\u012E": 'I',
        "\u1E2C": 'I',
        "\u0197": 'I',
        "\u24BF": 'J',
        "\uFF2A": 'J',
        "\u0134": 'J',
        "\u0248": 'J',
        "\u24C0": 'K',
        "\uFF2B": 'K',
        "\u1E30": 'K',
        "\u01E8": 'K',
        "\u1E32": 'K',
        "\u0136": 'K',
        "\u1E34": 'K',
        "\u0198": 'K',
        "\u2C69": 'K',
        "\uA740": 'K',
        "\uA742": 'K',
        "\uA744": 'K',
        "\uA7A2": 'K',
        "\u24C1": 'L',
        "\uFF2C": 'L',
        "\u013F": 'L',
        "\u0139": 'L',
        "\u013D": 'L',
        "\u1E36": 'L',
        "\u1E38": 'L',
        "\u013B": 'L',
        "\u1E3C": 'L',
        "\u1E3A": 'L',
        "\u0141": 'L',
        "\u023D": 'L',
        "\u2C62": 'L',
        "\u2C60": 'L',
        "\uA748": 'L',
        "\uA746": 'L',
        "\uA780": 'L',
        "\u01C7": 'LJ',
        "\u01C8": 'Lj',
        "\u24C2": 'M',
        "\uFF2D": 'M',
        "\u1E3E": 'M',
        "\u1E40": 'M',
        "\u1E42": 'M',
        "\u2C6E": 'M',
        "\u019C": 'M',
        "\u24C3": 'N',
        "\uFF2E": 'N',
        "\u01F8": 'N',
        "\u0143": 'N',
        "\xD1": 'N',
        "\u1E44": 'N',
        "\u0147": 'N',
        "\u1E46": 'N',
        "\u0145": 'N',
        "\u1E4A": 'N',
        "\u1E48": 'N',
        "\u0220": 'N',
        "\u019D": 'N',
        "\uA790": 'N',
        "\uA7A4": 'N',
        "\u01CA": 'NJ',
        "\u01CB": 'Nj',
        "\u24C4": 'O',
        "\uFF2F": 'O',
        "\xD2": 'O',
        "\xD3": 'O',
        "\xD4": 'O',
        "\u1ED2": 'O',
        "\u1ED0": 'O',
        "\u1ED6": 'O',
        "\u1ED4": 'O',
        "\xD5": 'O',
        "\u1E4C": 'O',
        "\u022C": 'O',
        "\u1E4E": 'O',
        "\u014C": 'O',
        "\u1E50": 'O',
        "\u1E52": 'O',
        "\u014E": 'O',
        "\u022E": 'O',
        "\u0230": 'O',
        "\xD6": 'O',
        "\u022A": 'O',
        "\u1ECE": 'O',
        "\u0150": 'O',
        "\u01D1": 'O',
        "\u020C": 'O',
        "\u020E": 'O',
        "\u01A0": 'O',
        "\u1EDC": 'O',
        "\u1EDA": 'O',
        "\u1EE0": 'O',
        "\u1EDE": 'O',
        "\u1EE2": 'O',
        "\u1ECC": 'O',
        "\u1ED8": 'O',
        "\u01EA": 'O',
        "\u01EC": 'O',
        "\xD8": 'O',
        "\u01FE": 'O',
        "\u0186": 'O',
        "\u019F": 'O',
        "\uA74A": 'O',
        "\uA74C": 'O',
        "\u0152": 'OE',
        "\u01A2": 'OI',
        "\uA74E": 'OO',
        "\u0222": 'OU',
        "\u24C5": 'P',
        "\uFF30": 'P',
        "\u1E54": 'P',
        "\u1E56": 'P',
        "\u01A4": 'P',
        "\u2C63": 'P',
        "\uA750": 'P',
        "\uA752": 'P',
        "\uA754": 'P',
        "\u24C6": 'Q',
        "\uFF31": 'Q',
        "\uA756": 'Q',
        "\uA758": 'Q',
        "\u024A": 'Q',
        "\u24C7": 'R',
        "\uFF32": 'R',
        "\u0154": 'R',
        "\u1E58": 'R',
        "\u0158": 'R',
        "\u0210": 'R',
        "\u0212": 'R',
        "\u1E5A": 'R',
        "\u1E5C": 'R',
        "\u0156": 'R',
        "\u1E5E": 'R',
        "\u024C": 'R',
        "\u2C64": 'R',
        "\uA75A": 'R',
        "\uA7A6": 'R',
        "\uA782": 'R',
        "\u24C8": 'S',
        "\uFF33": 'S',
        "\u1E9E": 'S',
        "\u015A": 'S',
        "\u1E64": 'S',
        "\u015C": 'S',
        "\u1E60": 'S',
        "\u0160": 'S',
        "\u1E66": 'S',
        "\u1E62": 'S',
        "\u1E68": 'S',
        "\u0218": 'S',
        "\u015E": 'S',
        "\u2C7E": 'S',
        "\uA7A8": 'S',
        "\uA784": 'S',
        "\u24C9": 'T',
        "\uFF34": 'T',
        "\u1E6A": 'T',
        "\u0164": 'T',
        "\u1E6C": 'T',
        "\u021A": 'T',
        "\u0162": 'T',
        "\u1E70": 'T',
        "\u1E6E": 'T',
        "\u0166": 'T',
        "\u01AC": 'T',
        "\u01AE": 'T',
        "\u023E": 'T',
        "\uA786": 'T',
        "\uA728": 'TZ',
        "\u24CA": 'U',
        "\uFF35": 'U',
        "\xD9": 'U',
        "\xDA": 'U',
        "\xDB": 'U',
        "\u0168": 'U',
        "\u1E78": 'U',
        "\u016A": 'U',
        "\u1E7A": 'U',
        "\u016C": 'U',
        "\xDC": 'U',
        "\u01DB": 'U',
        "\u01D7": 'U',
        "\u01D5": 'U',
        "\u01D9": 'U',
        "\u1EE6": 'U',
        "\u016E": 'U',
        "\u0170": 'U',
        "\u01D3": 'U',
        "\u0214": 'U',
        "\u0216": 'U',
        "\u01AF": 'U',
        "\u1EEA": 'U',
        "\u1EE8": 'U',
        "\u1EEE": 'U',
        "\u1EEC": 'U',
        "\u1EF0": 'U',
        "\u1EE4": 'U',
        "\u1E72": 'U',
        "\u0172": 'U',
        "\u1E76": 'U',
        "\u1E74": 'U',
        "\u0244": 'U',
        "\u24CB": 'V',
        "\uFF36": 'V',
        "\u1E7C": 'V',
        "\u1E7E": 'V',
        "\u01B2": 'V',
        "\uA75E": 'V',
        "\u0245": 'V',
        "\uA760": 'VY',
        "\u24CC": 'W',
        "\uFF37": 'W',
        "\u1E80": 'W',
        "\u1E82": 'W',
        "\u0174": 'W',
        "\u1E86": 'W',
        "\u1E84": 'W',
        "\u1E88": 'W',
        "\u2C72": 'W',
        "\u24CD": 'X',
        "\uFF38": 'X',
        "\u1E8A": 'X',
        "\u1E8C": 'X',
        "\u24CE": 'Y',
        "\uFF39": 'Y',
        "\u1EF2": 'Y',
        "\xDD": 'Y',
        "\u0176": 'Y',
        "\u1EF8": 'Y',
        "\u0232": 'Y',
        "\u1E8E": 'Y',
        "\u0178": 'Y',
        "\u1EF6": 'Y',
        "\u1EF4": 'Y',
        "\u01B3": 'Y',
        "\u024E": 'Y',
        "\u1EFE": 'Y',
        "\u24CF": 'Z',
        "\uFF3A": 'Z',
        "\u0179": 'Z',
        "\u1E90": 'Z',
        "\u017B": 'Z',
        "\u017D": 'Z',
        "\u1E92": 'Z',
        "\u1E94": 'Z',
        "\u01B5": 'Z',
        "\u0224": 'Z',
        "\u2C7F": 'Z',
        "\u2C6B": 'Z',
        "\uA762": 'Z',
        "\u24D0": 'a',
        "\uFF41": 'a',
        "\u1E9A": 'a',
        "\xE0": 'a',
        "\xE1": 'a',
        "\xE2": 'a',
        "\u1EA7": 'a',
        "\u1EA5": 'a',
        "\u1EAB": 'a',
        "\u1EA9": 'a',
        "\xE3": 'a',
        "\u0101": 'a',
        "\u0103": 'a',
        "\u1EB1": 'a',
        "\u1EAF": 'a',
        "\u1EB5": 'a',
        "\u1EB3": 'a',
        "\u0227": 'a',
        "\u01E1": 'a',
        "\xE4": 'a',
        "\u01DF": 'a',
        "\u1EA3": 'a',
        "\xE5": 'a',
        "\u01FB": 'a',
        "\u01CE": 'a',
        "\u0201": 'a',
        "\u0203": 'a',
        "\u1EA1": 'a',
        "\u1EAD": 'a',
        "\u1EB7": 'a',
        "\u1E01": 'a',
        "\u0105": 'a',
        "\u2C65": 'a',
        "\u0250": 'a',
        "\uA733": 'aa',
        "\xE6": 'ae',
        "\u01FD": 'ae',
        "\u01E3": 'ae',
        "\uA735": 'ao',
        "\uA737": 'au',
        "\uA739": 'av',
        "\uA73B": 'av',
        "\uA73D": 'ay',
        "\u24D1": 'b',
        "\uFF42": 'b',
        "\u1E03": 'b',
        "\u1E05": 'b',
        "\u1E07": 'b',
        "\u0180": 'b',
        "\u0183": 'b',
        "\u0253": 'b',
        "\u24D2": 'c',
        "\uFF43": 'c',
        "\u0107": 'c',
        "\u0109": 'c',
        "\u010B": 'c',
        "\u010D": 'c',
        "\xE7": 'c',
        "\u1E09": 'c',
        "\u0188": 'c',
        "\u023C": 'c',
        "\uA73F": 'c',
        "\u2184": 'c',
        "\u24D3": 'd',
        "\uFF44": 'd',
        "\u1E0B": 'd',
        "\u010F": 'd',
        "\u1E0D": 'd',
        "\u1E11": 'd',
        "\u1E13": 'd',
        "\u1E0F": 'd',
        "\u0111": 'd',
        "\u018C": 'd',
        "\u0256": 'd',
        "\u0257": 'd',
        "\uA77A": 'd',
        "\u01F3": 'dz',
        "\u01C6": 'dz',
        "\u24D4": 'e',
        "\uFF45": 'e',
        "\xE8": 'e',
        "\xE9": 'e',
        "\xEA": 'e',
        "\u1EC1": 'e',
        "\u1EBF": 'e',
        "\u1EC5": 'e',
        "\u1EC3": 'e',
        "\u1EBD": 'e',
        "\u0113": 'e',
        "\u1E15": 'e',
        "\u1E17": 'e',
        "\u0115": 'e',
        "\u0117": 'e',
        "\xEB": 'e',
        "\u1EBB": 'e',
        "\u011B": 'e',
        "\u0205": 'e',
        "\u0207": 'e',
        "\u1EB9": 'e',
        "\u1EC7": 'e',
        "\u0229": 'e',
        "\u1E1D": 'e',
        "\u0119": 'e',
        "\u1E19": 'e',
        "\u1E1B": 'e',
        "\u0247": 'e',
        "\u025B": 'e',
        "\u01DD": 'e',
        "\u24D5": 'f',
        "\uFF46": 'f',
        "\u1E1F": 'f',
        "\u0192": 'f',
        "\uA77C": 'f',
        "\u24D6": 'g',
        "\uFF47": 'g',
        "\u01F5": 'g',
        "\u011D": 'g',
        "\u1E21": 'g',
        "\u011F": 'g',
        "\u0121": 'g',
        "\u01E7": 'g',
        "\u0123": 'g',
        "\u01E5": 'g',
        "\u0260": 'g',
        "\uA7A1": 'g',
        "\u1D79": 'g',
        "\uA77F": 'g',
        "\u24D7": 'h',
        "\uFF48": 'h',
        "\u0125": 'h',
        "\u1E23": 'h',
        "\u1E27": 'h',
        "\u021F": 'h',
        "\u1E25": 'h',
        "\u1E29": 'h',
        "\u1E2B": 'h',
        "\u1E96": 'h',
        "\u0127": 'h',
        "\u2C68": 'h',
        "\u2C76": 'h',
        "\u0265": 'h',
        "\u0195": 'hv',
        "\u24D8": 'i',
        "\uFF49": 'i',
        "\xEC": 'i',
        "\xED": 'i',
        "\xEE": 'i',
        "\u0129": 'i',
        "\u012B": 'i',
        "\u012D": 'i',
        "\xEF": 'i',
        "\u1E2F": 'i',
        "\u1EC9": 'i',
        "\u01D0": 'i',
        "\u0209": 'i',
        "\u020B": 'i',
        "\u1ECB": 'i',
        "\u012F": 'i',
        "\u1E2D": 'i',
        "\u0268": 'i',
        "\u0131": 'i',
        "\u24D9": 'j',
        "\uFF4A": 'j',
        "\u0135": 'j',
        "\u01F0": 'j',
        "\u0249": 'j',
        "\u24DA": 'k',
        "\uFF4B": 'k',
        "\u1E31": 'k',
        "\u01E9": 'k',
        "\u1E33": 'k',
        "\u0137": 'k',
        "\u1E35": 'k',
        "\u0199": 'k',
        "\u2C6A": 'k',
        "\uA741": 'k',
        "\uA743": 'k',
        "\uA745": 'k',
        "\uA7A3": 'k',
        "\u24DB": 'l',
        "\uFF4C": 'l',
        "\u0140": 'l',
        "\u013A": 'l',
        "\u013E": 'l',
        "\u1E37": 'l',
        "\u1E39": 'l',
        "\u013C": 'l',
        "\u1E3D": 'l',
        "\u1E3B": 'l',
        "\u017F": 'l',
        "\u0142": 'l',
        "\u019A": 'l',
        "\u026B": 'l',
        "\u2C61": 'l',
        "\uA749": 'l',
        "\uA781": 'l',
        "\uA747": 'l',
        "\u01C9": 'lj',
        "\u24DC": 'm',
        "\uFF4D": 'm',
        "\u1E3F": 'm',
        "\u1E41": 'm',
        "\u1E43": 'm',
        "\u0271": 'm',
        "\u026F": 'm',
        "\u24DD": 'n',
        "\uFF4E": 'n',
        "\u01F9": 'n',
        "\u0144": 'n',
        "\xF1": 'n',
        "\u1E45": 'n',
        "\u0148": 'n',
        "\u1E47": 'n',
        "\u0146": 'n',
        "\u1E4B": 'n',
        "\u1E49": 'n',
        "\u019E": 'n',
        "\u0272": 'n',
        "\u0149": 'n',
        "\uA791": 'n',
        "\uA7A5": 'n',
        "\u01CC": 'nj',
        "\u24DE": 'o',
        "\uFF4F": 'o',
        "\xF2": 'o',
        "\xF3": 'o',
        "\xF4": 'o',
        "\u1ED3": 'o',
        "\u1ED1": 'o',
        "\u1ED7": 'o',
        "\u1ED5": 'o',
        "\xF5": 'o',
        "\u1E4D": 'o',
        "\u022D": 'o',
        "\u1E4F": 'o',
        "\u014D": 'o',
        "\u1E51": 'o',
        "\u1E53": 'o',
        "\u014F": 'o',
        "\u022F": 'o',
        "\u0231": 'o',
        "\xF6": 'o',
        "\u022B": 'o',
        "\u1ECF": 'o',
        "\u0151": 'o',
        "\u01D2": 'o',
        "\u020D": 'o',
        "\u020F": 'o',
        "\u01A1": 'o',
        "\u1EDD": 'o',
        "\u1EDB": 'o',
        "\u1EE1": 'o',
        "\u1EDF": 'o',
        "\u1EE3": 'o',
        "\u1ECD": 'o',
        "\u1ED9": 'o',
        "\u01EB": 'o',
        "\u01ED": 'o',
        "\xF8": 'o',
        "\u01FF": 'o',
        "\u0254": 'o',
        "\uA74B": 'o',
        "\uA74D": 'o',
        "\u0275": 'o',
        "\u0153": 'oe',
        "\u01A3": 'oi',
        "\u0223": 'ou',
        "\uA74F": 'oo',
        "\u24DF": 'p',
        "\uFF50": 'p',
        "\u1E55": 'p',
        "\u1E57": 'p',
        "\u01A5": 'p',
        "\u1D7D": 'p',
        "\uA751": 'p',
        "\uA753": 'p',
        "\uA755": 'p',
        "\u24E0": 'q',
        "\uFF51": 'q',
        "\u024B": 'q',
        "\uA757": 'q',
        "\uA759": 'q',
        "\u24E1": 'r',
        "\uFF52": 'r',
        "\u0155": 'r',
        "\u1E59": 'r',
        "\u0159": 'r',
        "\u0211": 'r',
        "\u0213": 'r',
        "\u1E5B": 'r',
        "\u1E5D": 'r',
        "\u0157": 'r',
        "\u1E5F": 'r',
        "\u024D": 'r',
        "\u027D": 'r',
        "\uA75B": 'r',
        "\uA7A7": 'r',
        "\uA783": 'r',
        "\u24E2": 's',
        "\uFF53": 's',
        "\xDF": 's',
        "\u015B": 's',
        "\u1E65": 's',
        "\u015D": 's',
        "\u1E61": 's',
        "\u0161": 's',
        "\u1E67": 's',
        "\u1E63": 's',
        "\u1E69": 's',
        "\u0219": 's',
        "\u015F": 's',
        "\u023F": 's',
        "\uA7A9": 's',
        "\uA785": 's',
        "\u1E9B": 's',
        "\u24E3": 't',
        "\uFF54": 't',
        "\u1E6B": 't',
        "\u1E97": 't',
        "\u0165": 't',
        "\u1E6D": 't',
        "\u021B": 't',
        "\u0163": 't',
        "\u1E71": 't',
        "\u1E6F": 't',
        "\u0167": 't',
        "\u01AD": 't',
        "\u0288": 't',
        "\u2C66": 't',
        "\uA787": 't',
        "\uA729": 'tz',
        "\u24E4": 'u',
        "\uFF55": 'u',
        "\xF9": 'u',
        "\xFA": 'u',
        "\xFB": 'u',
        "\u0169": 'u',
        "\u1E79": 'u',
        "\u016B": 'u',
        "\u1E7B": 'u',
        "\u016D": 'u',
        "\xFC": 'u',
        "\u01DC": 'u',
        "\u01D8": 'u',
        "\u01D6": 'u',
        "\u01DA": 'u',
        "\u1EE7": 'u',
        "\u016F": 'u',
        "\u0171": 'u',
        "\u01D4": 'u',
        "\u0215": 'u',
        "\u0217": 'u',
        "\u01B0": 'u',
        "\u1EEB": 'u',
        "\u1EE9": 'u',
        "\u1EEF": 'u',
        "\u1EED": 'u',
        "\u1EF1": 'u',
        "\u1EE5": 'u',
        "\u1E73": 'u',
        "\u0173": 'u',
        "\u1E77": 'u',
        "\u1E75": 'u',
        "\u0289": 'u',
        "\u24E5": 'v',
        "\uFF56": 'v',
        "\u1E7D": 'v',
        "\u1E7F": 'v',
        "\u028B": 'v',
        "\uA75F": 'v',
        "\u028C": 'v',
        "\uA761": 'vy',
        "\u24E6": 'w',
        "\uFF57": 'w',
        "\u1E81": 'w',
        "\u1E83": 'w',
        "\u0175": 'w',
        "\u1E87": 'w',
        "\u1E85": 'w',
        "\u1E98": 'w',
        "\u1E89": 'w',
        "\u2C73": 'w',
        "\u24E7": 'x',
        "\uFF58": 'x',
        "\u1E8B": 'x',
        "\u1E8D": 'x',
        "\u24E8": 'y',
        "\uFF59": 'y',
        "\u1EF3": 'y',
        "\xFD": 'y',
        "\u0177": 'y',
        "\u1EF9": 'y',
        "\u0233": 'y',
        "\u1E8F": 'y',
        "\xFF": 'y',
        "\u1EF7": 'y',
        "\u1E99": 'y',
        "\u1EF5": 'y',
        "\u01B4": 'y',
        "\u024F": 'y',
        "\u1EFF": 'y',
        "\u24E9": 'z',
        "\uFF5A": 'z',
        "\u017A": 'z',
        "\u1E91": 'z',
        "\u017C": 'z',
        "\u017E": 'z',
        "\u1E93": 'z',
        "\u1E95": 'z',
        "\u01B6": 'z',
        "\u0225": 'z',
        "\u0240": 'z',
        "\u2C6C": 'z',
        "\uA763": 'z',
        "\u0386": "\u0391",
        "\u0388": "\u0395",
        "\u0389": "\u0397",
        "\u038A": "\u0399",
        "\u03AA": "\u0399",
        "\u038C": "\u039F",
        "\u038E": "\u03A5",
        "\u03AB": "\u03A5",
        "\u038F": "\u03A9",
        "\u03AC": "\u03B1",
        "\u03AD": "\u03B5",
        "\u03AE": "\u03B7",
        "\u03AF": "\u03B9",
        "\u03CA": "\u03B9",
        "\u0390": "\u03B9",
        "\u03CC": "\u03BF",
        "\u03CD": "\u03C5",
        "\u03CB": "\u03C5",
        "\u03B0": "\u03C5",
        "\u03CE": "\u03C9",
        "\u03C2": "\u03C3",
        "\u2019": '\''
      };
      return diacritics;
    });
    S2.define('select2/data/base', ['../utils'], function (Utils) {
      function BaseAdapter($element, options) {
        BaseAdapter.__super__.constructor.call(this);
      }

      Utils.Extend(BaseAdapter, Utils.Observable);

      BaseAdapter.prototype.current = function (callback) {
        throw new Error('The `current` method must be defined in child classes.');
      };

      BaseAdapter.prototype.query = function (params, callback) {
        throw new Error('The `query` method must be defined in child classes.');
      };

      BaseAdapter.prototype.bind = function (container, $container) {// Can be implemented in subclasses
      };

      BaseAdapter.prototype.destroy = function () {// Can be implemented in subclasses
      };

      BaseAdapter.prototype.generateResultId = function (container, data) {
        var id = container.id + '-result-';
        id += Utils.generateChars(4);

        if (data.id != null) {
          id += '-' + data.id.toString();
        } else {
          id += '-' + Utils.generateChars(4);
        }

        return id;
      };

      return BaseAdapter;
    });
    S2.define('select2/data/select', ['./base', '../utils', 'jquery'], function (BaseAdapter, Utils, $) {
      function SelectAdapter($element, options) {
        this.$element = $element;
        this.options = options;

        SelectAdapter.__super__.constructor.call(this);
      }

      Utils.Extend(SelectAdapter, BaseAdapter);

      SelectAdapter.prototype.current = function (callback) {
        var self = this;
        var data = Array.prototype.map.call(this.$element[0].querySelectorAll(':checked'), function (selectedElement) {
          return self.item($(selectedElement));
        });
        callback(data);
      };

      SelectAdapter.prototype.select = function (data) {
        var self = this;
        data.selected = true; // If data.element is a DOM node, use it instead

        if (data.element != null && data.element.tagName.toLowerCase() === 'option') {
          data.element.selected = true;
          this.$element.trigger('input').trigger('change');
          return;
        }

        if (this.$element.prop('multiple')) {
          this.current(function (currentData) {
            var val = [];
            data = [data];
            data.push.apply(data, currentData);

            for (var d = 0; d < data.length; d++) {
              var id = data[d].id;

              if (val.indexOf(id) === -1) {
                val.push(id);
              }
            }

            self.$element.val(val);
            self.$element.trigger('input').trigger('change');
          });
        } else {
          var val = data.id;
          this.$element.val(val);
          this.$element.trigger('input').trigger('change');
        }
      };

      SelectAdapter.prototype.unselect = function (data) {
        var self = this;

        if (!this.$element.prop('multiple')) {
          return;
        }

        data.selected = false;

        if (data.element != null && data.element.tagName.toLowerCase() === 'option') {
          data.element.selected = false;
          this.$element.trigger('input').trigger('change');
          return;
        }

        this.current(function (currentData) {
          var val = [];

          for (var d = 0; d < currentData.length; d++) {
            var id = currentData[d].id;

            if (id !== data.id && val.indexOf(id) === -1) {
              val.push(id);
            }
          }

          self.$element.val(val);
          self.$element.trigger('input').trigger('change');
        });
      };

      SelectAdapter.prototype.bind = function (container, $container) {
        var self = this;
        this.container = container;
        container.on('select', function (params) {
          self.select(params.data);
        });
        container.on('unselect', function (params) {
          self.unselect(params.data);
        });
      };

      SelectAdapter.prototype.destroy = function () {
        // Remove anything added to child elements
        this.$element.find('*').each(function () {
          // Remove any custom data set by Select2
          Utils.RemoveData(this);
        });
      };

      SelectAdapter.prototype.query = function (params, callback) {
        var data = [];
        var self = this;
        var $options = this.$element.children();
        $options.each(function () {
          if (this.tagName.toLowerCase() !== 'option' && this.tagName.toLowerCase() !== 'optgroup') {
            return;
          }

          var $option = $(this);
          var option = self.item($option);
          var matches = self.matches(params, option);

          if (matches !== null) {
            data.push(matches);
          }
        });
        callback({
          results: data
        });
      };

      SelectAdapter.prototype.addOptions = function ($options) {
        this.$element.append($options);
      };

      SelectAdapter.prototype.option = function (data) {
        var option;

        if (data.children) {
          option = document.createElement('optgroup');
          option.label = data.text;
        } else {
          option = document.createElement('option');

          if (option.textContent !== undefined) {
            option.textContent = data.text;
          } else {
            option.innerText = data.text;
          }
        }

        if (data.id !== undefined) {
          option.value = data.id;
        }

        if (data.disabled) {
          option.disabled = true;
        }

        if (data.selected) {
          option.selected = true;
        }

        if (data.title) {
          option.title = data.title;
        }

        var normalizedData = this._normalizeItem(data);

        normalizedData.element = option; // Override the option's data with the combined data

        Utils.StoreData(option, 'data', normalizedData);
        return $(option);
      };

      SelectAdapter.prototype.item = function ($option) {
        var data = {};
        data = Utils.GetData($option[0], 'data');

        if (data != null) {
          return data;
        }

        var option = $option[0];

        if (option.tagName.toLowerCase() === 'option') {
          data = {
            id: $option.val(),
            text: $option.text(),
            disabled: $option.prop('disabled'),
            selected: $option.prop('selected'),
            title: $option.prop('title')
          };
        } else if (option.tagName.toLowerCase() === 'optgroup') {
          data = {
            text: $option.prop('label'),
            children: [],
            title: $option.prop('title')
          };
          var $children = $option.children('option');
          var children = [];

          for (var c = 0; c < $children.length; c++) {
            var $child = $($children[c]);
            var child = this.item($child);
            children.push(child);
          }

          data.children = children;
        }

        data = this._normalizeItem(data);
        data.element = $option[0];
        Utils.StoreData($option[0], 'data', data);
        return data;
      };

      SelectAdapter.prototype._normalizeItem = function (item) {
        if (item !== Object(item)) {
          item = {
            id: item,
            text: item
          };
        }

        item = $.extend({}, {
          text: ''
        }, item);
        var defaults = {
          selected: false,
          disabled: false
        };

        if (item.id != null) {
          item.id = item.id.toString();
        }

        if (item.text != null) {
          item.text = item.text.toString();
        }

        if (item._resultId == null && item.id && this.container != null) {
          item._resultId = this.generateResultId(this.container, item);
        }

        return $.extend({}, defaults, item);
      };

      SelectAdapter.prototype.matches = function (params, data) {
        var matcher = this.options.get('matcher');
        return matcher(params, data);
      };

      return SelectAdapter;
    });
    S2.define('select2/data/array', ['./select', '../utils', 'jquery'], function (SelectAdapter, Utils, $) {
      function ArrayAdapter($element, options) {
        this._dataToConvert = options.get('data') || [];

        ArrayAdapter.__super__.constructor.call(this, $element, options);
      }

      Utils.Extend(ArrayAdapter, SelectAdapter);

      ArrayAdapter.prototype.bind = function (container, $container) {
        ArrayAdapter.__super__.bind.call(this, container, $container);

        this.addOptions(this.convertToOptions(this._dataToConvert));
      };

      ArrayAdapter.prototype.select = function (data) {
        var $option = this.$element.find('option').filter(function (i, elm) {
          return elm.value == data.id.toString();
        });

        if ($option.length === 0) {
          $option = this.option(data);
          this.addOptions($option);
        }

        ArrayAdapter.__super__.select.call(this, data);
      };

      ArrayAdapter.prototype.convertToOptions = function (data) {
        var self = this;
        var $existing = this.$element.find('option');
        var existingIds = $existing.map(function () {
          return self.item($(this)).id;
        }).get();
        var $options = []; // Filter out all items except for the one passed in the argument

        function onlyItem(item) {
          return function () {
            return $(this).val() == item.id;
          };
        }

        for (var d = 0; d < data.length; d++) {
          var item = this._normalizeItem(data[d]); // Skip items which were pre-loaded, only merge the data


          if (existingIds.indexOf(item.id) >= 0) {
            var $existingOption = $existing.filter(onlyItem(item));
            var existingData = this.item($existingOption);
            var newData = $.extend(true, {}, item, existingData);
            var $newOption = this.option(newData);
            $existingOption.replaceWith($newOption);
            continue;
          }

          var $option = this.option(item);

          if (item.children) {
            var $children = this.convertToOptions(item.children);
            $option.append($children);
          }

          $options.push($option);
        }

        return $options;
      };

      return ArrayAdapter;
    });
    S2.define('select2/data/ajax', ['./array', '../utils', 'jquery'], function (ArrayAdapter, Utils, $) {
      function AjaxAdapter($element, options) {
        this.ajaxOptions = this._applyDefaults(options.get('ajax'));

        if (this.ajaxOptions.processResults != null) {
          this.processResults = this.ajaxOptions.processResults;
        }

        AjaxAdapter.__super__.constructor.call(this, $element, options);
      }

      Utils.Extend(AjaxAdapter, ArrayAdapter);

      AjaxAdapter.prototype._applyDefaults = function (options) {
        var defaults = {
          data: function data(params) {
            return $.extend({}, params, {
              q: params.term
            });
          },
          transport: function transport(params, success, failure) {
            var $request = $.ajax(params);
            $request.then(success);
            $request.fail(failure);
            return $request;
          }
        };
        return $.extend({}, defaults, options, true);
      };

      AjaxAdapter.prototype.processResults = function (results) {
        return results;
      };

      AjaxAdapter.prototype.query = function (params, callback) {
        var matches = [];
        var self = this;

        if (this._request != null) {
          // JSONP requests cannot always be aborted
          if (typeof this._request.abort === 'function') {
            this._request.abort();
          }

          this._request = null;
        }

        var options = $.extend({
          type: 'GET'
        }, this.ajaxOptions);

        if (typeof options.url === 'function') {
          options.url = options.url.call(this.$element, params);
        }

        if (typeof options.data === 'function') {
          options.data = options.data.call(this.$element, params);
        }

        function request() {
          var $request = options.transport(options, function (data) {
            var results = self.processResults(data, params);

            if (self.options.get('debug') && window.console && console.error) {
              // Check to make sure that the response included a `results` key.
              if (!results || !results.results || !Array.isArray(results.results)) {
                console.error('Select2: The AJAX results did not return an array in the ' + '`results` key of the response.');
              }
            }

            callback(results);
          }, function () {
            // Attempt to detect if a request was aborted
            // Only works if the transport exposes a status property
            if ('status' in $request && ($request.status === 0 || $request.status === '0')) {
              return;
            }

            self.trigger('results:message', {
              message: 'errorLoading'
            });
          });
          self._request = $request;
        }

        if (this.ajaxOptions.delay && params.term != null) {
          if (this._queryTimeout) {
            window.clearTimeout(this._queryTimeout);
          }

          this._queryTimeout = window.setTimeout(request, this.ajaxOptions.delay);
        } else {
          request();
        }
      };

      return AjaxAdapter;
    });
    S2.define('select2/data/tags', ['jquery'], function ($) {
      function Tags(decorated, $element, options) {
        var tags = options.get('tags');
        var createTag = options.get('createTag');

        if (createTag !== undefined) {
          this.createTag = createTag;
        }

        var insertTag = options.get('insertTag');

        if (insertTag !== undefined) {
          this.insertTag = insertTag;
        }

        decorated.call(this, $element, options);

        if (Array.isArray(tags)) {
          for (var t = 0; t < tags.length; t++) {
            var tag = tags[t];

            var item = this._normalizeItem(tag);

            var $option = this.option(item);
            this.$element.append($option);
          }
        }
      }

      Tags.prototype.query = function (decorated, params, callback) {
        var self = this;

        this._removeOldTags();

        if (params.term == null || params.page != null) {
          decorated.call(this, params, callback);
          return;
        }

        function wrapper(obj, child) {
          var data = obj.results;

          for (var i = 0; i < data.length; i++) {
            var option = data[i];
            var checkChildren = option.children != null && !wrapper({
              results: option.children
            }, true);
            var optionText = (option.text || '').toUpperCase();
            var paramsTerm = (params.term || '').toUpperCase();
            var checkText = optionText === paramsTerm;

            if (checkText || checkChildren) {
              if (child) {
                return false;
              }

              obj.data = data;
              callback(obj);
              return;
            }
          }

          if (child) {
            return true;
          }

          var tag = self.createTag(params);

          if (tag != null) {
            var $option = self.option(tag);
            $option.attr('data-select2-tag', 'true');
            self.addOptions([$option]);
            self.insertTag(data, tag);
          }

          obj.results = data;
          callback(obj);
        }

        decorated.call(this, params, wrapper);
      };

      Tags.prototype.createTag = function (decorated, params) {
        if (params.term == null) {
          return null;
        }

        var term = params.term.trim();

        if (term === '') {
          return null;
        }

        return {
          id: term,
          text: term
        };
      };

      Tags.prototype.insertTag = function (_, data, tag) {
        data.unshift(tag);
      };

      Tags.prototype._removeOldTags = function (_) {
        var $options = this.$element.find('option[data-select2-tag]');
        $options.each(function () {
          if (this.selected) {
            return;
          }

          $(this).remove();
        });
      };

      return Tags;
    });
    S2.define('select2/data/tokenizer', ['jquery'], function ($) {
      function Tokenizer(decorated, $element, options) {
        var tokenizer = options.get('tokenizer');

        if (tokenizer !== undefined) {
          this.tokenizer = tokenizer;
        }

        decorated.call(this, $element, options);
      }

      Tokenizer.prototype.bind = function (decorated, container, $container) {
        decorated.call(this, container, $container);
        this.$search = container.dropdown.$search || container.selection.$search || $container.find('.select2-search__field');
      };

      Tokenizer.prototype.query = function (decorated, params, callback) {
        var self = this;

        function createAndSelect(data) {
          // Normalize the data object so we can use it for checks
          var item = self._normalizeItem(data); // Check if the data object already exists as a tag
          // Select it if it doesn't


          var $existingOptions = self.$element.find('option').filter(function () {
            return $(this).val() === item.id;
          }); // If an existing option wasn't found for it, create the option

          if (!$existingOptions.length) {
            var $option = self.option(item);
            $option.attr('data-select2-tag', true);

            self._removeOldTags();

            self.addOptions([$option]);
          } // Select the item, now that we know there is an option for it


          select(item);
        }

        function select(data) {
          self.trigger('select', {
            data: data
          });
        }

        params.term = params.term || '';
        var tokenData = this.tokenizer(params, this.options, createAndSelect);

        if (tokenData.term !== params.term) {
          // Replace the search term if we have the search box
          if (this.$search.length) {
            this.$search.val(tokenData.term);
            this.$search.trigger('focus');
          }

          params.term = tokenData.term;
        }

        decorated.call(this, params, callback);
      };

      Tokenizer.prototype.tokenizer = function (_, params, options, callback) {
        var separators = options.get('tokenSeparators') || [];
        var term = params.term;
        var i = 0;

        var createTag = this.createTag || function (params) {
          return {
            id: params.term,
            text: params.term
          };
        };

        while (i < term.length) {
          var termChar = term[i];

          if (separators.indexOf(termChar) === -1) {
            i++;
            continue;
          }

          var part = term.substr(0, i);
          var partParams = $.extend({}, params, {
            term: part
          });
          var data = createTag(partParams);

          if (data == null) {
            i++;
            continue;
          }

          callback(data); // Reset the term to not include the tokenized portion

          term = term.substr(i + 1) || '';
          i = 0;
        }

        return {
          term: term
        };
      };

      return Tokenizer;
    });
    S2.define('select2/data/minimumInputLength', [], function () {
      function MinimumInputLength(decorated, $e, options) {
        this.minimumInputLength = options.get('minimumInputLength');
        decorated.call(this, $e, options);
      }

      MinimumInputLength.prototype.query = function (decorated, params, callback) {
        params.term = params.term || '';

        if (params.term.length < this.minimumInputLength) {
          this.trigger('results:message', {
            message: 'inputTooShort',
            args: {
              minimum: this.minimumInputLength,
              input: params.term,
              params: params
            }
          });
          return;
        }

        decorated.call(this, params, callback);
      };

      return MinimumInputLength;
    });
    S2.define('select2/data/maximumInputLength', [], function () {
      function MaximumInputLength(decorated, $e, options) {
        this.maximumInputLength = options.get('maximumInputLength');
        decorated.call(this, $e, options);
      }

      MaximumInputLength.prototype.query = function (decorated, params, callback) {
        params.term = params.term || '';

        if (this.maximumInputLength > 0 && params.term.length > this.maximumInputLength) {
          this.trigger('results:message', {
            message: 'inputTooLong',
            args: {
              maximum: this.maximumInputLength,
              input: params.term,
              params: params
            }
          });
          return;
        }

        decorated.call(this, params, callback);
      };

      return MaximumInputLength;
    });
    S2.define('select2/data/maximumSelectionLength', [], function () {
      function MaximumSelectionLength(decorated, $e, options) {
        this.maximumSelectionLength = options.get('maximumSelectionLength');
        decorated.call(this, $e, options);
      }

      MaximumSelectionLength.prototype.bind = function (decorated, container, $container) {
        var self = this;
        decorated.call(this, container, $container);
        container.on('select', function () {
          self._checkIfMaximumSelected();
        });
      };

      MaximumSelectionLength.prototype.query = function (decorated, params, callback) {
        var self = this;

        this._checkIfMaximumSelected(function () {
          decorated.call(self, params, callback);
        });
      };

      MaximumSelectionLength.prototype._checkIfMaximumSelected = function (_, successCallback) {
        var self = this;
        this.current(function (currentData) {
          var count = currentData != null ? currentData.length : 0;

          if (self.maximumSelectionLength > 0 && count >= self.maximumSelectionLength) {
            self.trigger('results:message', {
              message: 'maximumSelected',
              args: {
                maximum: self.maximumSelectionLength
              }
            });
            return;
          }

          if (successCallback) {
            successCallback();
          }
        });
      };

      return MaximumSelectionLength;
    });
    S2.define('select2/dropdown', ['jquery', './utils'], function ($, Utils) {
      function Dropdown($element, options) {
        this.$element = $element;
        this.options = options;

        Dropdown.__super__.constructor.call(this);
      }

      Utils.Extend(Dropdown, Utils.Observable);

      Dropdown.prototype.render = function () {
        // Change dropdown classname and markup. @edited
        var $dropdown = $('<span class="sui-select-dropdown">' + '<span class="select2-results"></span>' + '</span>');
        $dropdown.attr('dir', this.options.get('dir'));
        this.$dropdown = $dropdown;
        return $dropdown;
      };

      Dropdown.prototype.bind = function () {// Should be implemented in subclasses
      };

      Dropdown.prototype.position = function ($dropdown, $container) {// Should be implemented in subclasses
      };

      Dropdown.prototype.destroy = function () {
        // Remove the dropdown from the DOM
        this.$dropdown.remove();
      };

      return Dropdown;
    });
    S2.define('select2/dropdown/search', ['jquery'], function ($) {
      function Search() {}

      Search.prototype.render = function (decorated) {
        var $rendered = decorated.call(this);
        var searchLabel = this.options.get('translations').get('search');
        var $search = $('<span class="select2-search select2-search--dropdown">' + '<input class="select2-search__field" type="search" tabindex="-1"' + ' autocorrect="off" autocapitalize="none"' + ' spellcheck="false" role="searchbox" aria-autocomplete="list" />' + '</span>');
        this.$searchContainer = $search;
        this.$search = $search.find('input');
        this.$search.prop('autocomplete', this.options.get('autocomplete'));
        this.$search.attr('aria-label', searchLabel());
        $rendered.prepend($search);
        return $rendered;
      };

      Search.prototype.bind = function (decorated, container, $container) {
        var self = this;
        var resultsId = container.id + '-results';
        decorated.call(this, container, $container);
        this.$search.on('keydown', function (evt) {
          self.trigger('keypress', evt);
          self._keyUpPrevented = evt.isDefaultPrevented();
        }); // Workaround for browsers which do not support the `input` event
        // This will prevent double-triggering of events for browsers which support
        // both the `keyup` and `input` events.

        this.$search.on('input', function (evt) {
          // Unbind the duplicated `keyup` event
          $(this).off('keyup');
        });
        this.$search.on('keyup input', function (evt) {
          self.handleSearch(evt);
        });
        container.on('open', function () {
          self.$search.attr('tabindex', 0);
          self.$search.attr('aria-controls', resultsId);
          self.$search.trigger('focus');
          window.setTimeout(function () {
            self.$search.trigger('focus');
          }, 0);
        });
        container.on('close', function () {
          self.$search.attr('tabindex', -1);
          self.$search.removeAttr('aria-controls');
          self.$search.removeAttr('aria-activedescendant');
          self.$search.val('');
          self.$search.trigger('blur');
        });
        container.on('focus', function () {
          if (!container.isOpen()) {
            self.$search.trigger('focus');
          }
        });
        container.on('results:all', function (params) {
          if (params.query.term == null || params.query.term === '') {
            var showSearch = self.showSearch(params);

            if (showSearch) {
              self.$searchContainer[0].classList.remove('select2-search--hide');
            } else {
              self.$searchContainer[0].classList.add('select2-search--hide');
            }
          }
        });
        container.on('results:focus', function (params) {
          if (params.data._resultId) {
            self.$search.attr('aria-activedescendant', params.data._resultId);
          } else {
            self.$search.removeAttr('aria-activedescendant');
          }
        });
      };

      Search.prototype.handleSearch = function (evt) {
        if (!this._keyUpPrevented) {
          var input = this.$search.val();
          this.trigger('query', {
            term: input
          });
        }

        this._keyUpPrevented = false;
      };

      Search.prototype.showSearch = function (_, params) {
        return true;
      };

      return Search;
    });
    S2.define('select2/dropdown/hidePlaceholder', [], function () {
      function HidePlaceholder(decorated, $element, options, dataAdapter) {
        this.placeholder = this.normalizePlaceholder(options.get('placeholder'));
        decorated.call(this, $element, options, dataAdapter);
      }

      HidePlaceholder.prototype.append = function (decorated, data) {
        data.results = this.removePlaceholder(data.results);
        decorated.call(this, data);
      };

      HidePlaceholder.prototype.normalizePlaceholder = function (_, placeholder) {
        if (typeof placeholder === 'string') {
          placeholder = {
            id: '',
            text: placeholder
          };
        }

        return placeholder;
      };

      HidePlaceholder.prototype.removePlaceholder = function (_, data) {
        var modifiedData = data.slice(0);

        for (var d = data.length - 1; d >= 0; d--) {
          var item = data[d];

          if (this.placeholder.id === item.id) {
            modifiedData.splice(d, 1);
          }
        }

        return modifiedData;
      };

      return HidePlaceholder;
    });
    S2.define('select2/dropdown/infiniteScroll', ['jquery'], function ($) {
      function InfiniteScroll(decorated, $element, options, dataAdapter) {
        this.lastParams = {};
        decorated.call(this, $element, options, dataAdapter);
        this.$loadingMore = this.createLoadingMore();
        this.loading = false;
      }

      InfiniteScroll.prototype.append = function (decorated, data) {
        this.$loadingMore.remove();
        this.loading = false;
        decorated.call(this, data);

        if (this.showLoadingMore(data)) {
          this.$results.append(this.$loadingMore);
          this.loadMoreIfNeeded();
        }
      };

      InfiniteScroll.prototype.bind = function (decorated, container, $container) {
        var self = this;
        decorated.call(this, container, $container);
        container.on('query', function (params) {
          self.lastParams = params;
          self.loading = true;
        });
        container.on('query:append', function (params) {
          self.lastParams = params;
          self.loading = true;
        });
        this.$results.on('scroll', this.loadMoreIfNeeded.bind(this));
      };

      InfiniteScroll.prototype.loadMoreIfNeeded = function () {
        var isLoadMoreVisible = $.contains(document.documentElement, this.$loadingMore[0]);

        if (this.loading || !isLoadMoreVisible) {
          return;
        }

        var currentOffset = this.$results.offset().top + this.$results.outerHeight(false);
        var loadingMoreOffset = this.$loadingMore.offset().top + this.$loadingMore.outerHeight(false);

        if (currentOffset + 50 >= loadingMoreOffset) {
          this.loadMore();
        }
      };

      InfiniteScroll.prototype.loadMore = function () {
        this.loading = true;
        var params = $.extend({}, {
          page: 1
        }, this.lastParams);
        params.page++;
        this.trigger('query:append', params);
      };

      InfiniteScroll.prototype.showLoadingMore = function (_, data) {
        return data.pagination && data.pagination.more;
      };

      InfiniteScroll.prototype.createLoadingMore = function () {
        var $option = $('<li ' + 'class="select2-results__option select2-results__option--load-more"' + 'role="option" aria-disabled="true"></li>');
        var message = this.options.get('translations').get('loadingMore');
        $option.html(message(this.lastParams));
        return $option;
      };

      return InfiniteScroll;
    });
    S2.define('select2/dropdown/attachBody', ['jquery', '../utils'], function ($, Utils) {
      function AttachBody(decorated, $element, options) {
        this.$dropdownParent = $(options.get('dropdownParent') || document.body);
        decorated.call(this, $element, options);
      }

      AttachBody.prototype.bind = function (decorated, container, $container) {
        var self = this;
        decorated.call(this, container, $container);
        container.on('open', function () {
          self._showDropdown();

          self._attachPositioningHandler(container); // Must bind after the results handlers to ensure correct sizing


          self._bindContainerResultHandlers(container);
        });
        container.on('close', function () {
          self._hideDropdown();

          self._detachPositioningHandler(container);
        });
        this.$dropdownContainer.on('mousedown', function (evt) {
          evt.stopPropagation();
        });
      };

      AttachBody.prototype.destroy = function (decorated) {
        decorated.call(this);
        this.$dropdownContainer.remove();
      };

      AttachBody.prototype.position = function (decorated, $dropdown, $container) {
        // Clone all of the container classes
        $dropdown.attr('class', $container.attr('class')); // Custom SUIselect dropdown. @edited

        $dropdown.removeClass('select2');
        $dropdown.addClass('sui-select-dropdown-container--open');
        $dropdown[0].classList.remove('select2');
        $dropdown[0].classList.add('select2-container--open');
        $dropdown.css({
          position: 'absolute',
          top: -999999
        });
        this.$container = $container;
      };

      AttachBody.prototype.render = function (decorated) {
        var $container = $('<span></span>');
        var $dropdown = decorated.call(this);
        $container.append($dropdown);
        this.$dropdownContainer = $container;
        return $container;
      };

      AttachBody.prototype._hideDropdown = function (decorated) {
        this.$dropdownContainer.detach();
      };

      AttachBody.prototype._bindContainerResultHandlers = function (decorated, container) {
        // These should only be bound once
        if (this._containerResultsHandlersBound) {
          return;
        }

        var self = this;
        container.on('results:all', function () {
          self._positionDropdown();

          self._resizeDropdown();
        });
        container.on('results:append', function () {
          self._positionDropdown();

          self._resizeDropdown();
        });
        container.on('results:message', function () {
          self._positionDropdown();

          self._resizeDropdown();
        });
        container.on('select', function () {
          self._positionDropdown();

          self._resizeDropdown();
        });
        container.on('unselect', function () {
          self._positionDropdown();

          self._resizeDropdown();
        });
        this._containerResultsHandlersBound = true;
      };

      AttachBody.prototype._attachPositioningHandler = function (decorated, container) {
        var self = this;
        var scrollEvent = 'scroll.select2.' + container.id;
        var resizeEvent = 'resize.select2.' + container.id;
        var orientationEvent = 'orientationchange.select2.' + container.id;
        var $watchers = this.$container.parents().filter(Utils.hasScroll);
        $watchers.each(function () {
          Utils.StoreData(this, 'select2-scroll-position', {
            x: $(this).scrollLeft(),
            y: $(this).scrollTop()
          });
        });
        $watchers.on(scrollEvent, function (ev) {
          var position = Utils.GetData(this, 'select2-scroll-position');
          $(this).scrollTop(position.y);
        });
        $(window).on(scrollEvent + ' ' + resizeEvent + ' ' + orientationEvent, function (e) {
          self._positionDropdown();

          self._resizeDropdown();
        });
      };

      AttachBody.prototype._detachPositioningHandler = function (decorated, container) {
        var scrollEvent = 'scroll.select2.' + container.id;
        var resizeEvent = 'resize.select2.' + container.id;
        var orientationEvent = 'orientationchange.select2.' + container.id;
        var $watchers = this.$container.parents().filter(Utils.hasScroll);
        $watchers.off(scrollEvent);
        $(window).off(scrollEvent + ' ' + resizeEvent + ' ' + orientationEvent);
      };

      AttachBody.prototype._positionDropdown = function () {
        var $window = $(window); // Custom SUIselect dropdown. @edited

        var isCurrentlyAbove = this.$dropdown[0].classList.contains('sui-select-dropdown--above');
        var isCurrentlyBelow = this.$dropdown[0].classList.contains('sui-select-dropdown--below');
        var newDirection = null;
        var offset = this.$container.offset();
        offset.bottom = offset.top + this.$container.outerHeight(false);
        var container = {
          height: this.$container.outerHeight(false)
        };
        container.top = offset.top;
        container.bottom = offset.top + container.height;
        var dropdown = {
          height: this.$dropdown.outerHeight(false)
        };
        var viewport = {
          top: $window.scrollTop(),
          bottom: $window.scrollTop() + $window.height()
        };
        var enoughRoomAbove = viewport.top < offset.top - dropdown.height;
        var enoughRoomBelow = viewport.bottom > offset.bottom + dropdown.height;
        var css = {
          left: offset.left,
          top: container.bottom
        }; // Determine what the parent element is to use for calculating the offset

        var $offsetParent = this.$dropdownParent; // For statically positioned elements, we need to get the element
        // that is determining the offset

        if ($offsetParent.css('position') === 'static') {
          $offsetParent = $offsetParent.offsetParent();
        }

        var parentOffset = {
          top: 0,
          left: 0
        };

        if ($.contains(document.body, $offsetParent[0]) || $offsetParent[0].isConnected) {
          parentOffset = $offsetParent.offset();
        }

        css.top -= parentOffset.top;
        css.left -= parentOffset.left;

        if (!isCurrentlyAbove && !isCurrentlyBelow) {
          newDirection = 'below';
        }

        if (!enoughRoomBelow && enoughRoomAbove && !isCurrentlyAbove) {
          newDirection = 'above';
        } else if (!enoughRoomAbove && enoughRoomBelow && isCurrentlyAbove) {
          newDirection = 'below';
        }

        if (newDirection == 'above' || isCurrentlyAbove && newDirection !== 'below') {
          css.top = container.top - parentOffset.top - dropdown.height;
        } // Custom SUIselect dropdown. @edited


        if (newDirection != null) {
          this.$dropdown[0].classList.remove('sui-select-dropdown--below');
          this.$dropdown[0].classList.remove('sui-select-dropdown--above');
          this.$dropdown[0].classList.add('sui-select-dropdown--' + newDirection);
          this.$container[0].classList.remove('sui-select-container--below');
          this.$container[0].classList.remove('sui-select-container--above');
          this.$container[0].classList.add('sui-select-dropdown-container--' + newDirection);
        }

        this.$dropdownContainer.css(css);
      };

      AttachBody.prototype._resizeDropdown = function () {
        var css = {
          width: this.$container.outerWidth(false) + 'px'
        };

        if (this.options.get('dropdownAutoWidth')) {
          css.minWidth = css.width;
          css.position = 'relative';
          css.width = 'auto';
        }

        this.$dropdown.css(css);
      };

      AttachBody.prototype._showDropdown = function (decorated) {
        this.$dropdownContainer.appendTo(this.$dropdownParent);

        this._positionDropdown();

        this._resizeDropdown();
      };

      return AttachBody;
    });
    S2.define('select2/dropdown/minimumResultsForSearch', [], function () {
      function countResults(data) {
        var count = 0;

        for (var d = 0; d < data.length; d++) {
          var item = data[d];

          if (item.children) {
            count += countResults(item.children);
          } else {
            count++;
          }
        }

        return count;
      }

      function MinimumResultsForSearch(decorated, $element, options, dataAdapter) {
        this.minimumResultsForSearch = options.get('minimumResultsForSearch');

        if (this.minimumResultsForSearch < 0) {
          this.minimumResultsForSearch = Infinity;
        }

        decorated.call(this, $element, options, dataAdapter);
      }

      MinimumResultsForSearch.prototype.showSearch = function (decorated, params) {
        if (countResults(params.data.results) < this.minimumResultsForSearch) {
          return false;
        }

        return decorated.call(this, params);
      };

      return MinimumResultsForSearch;
    });
    S2.define('select2/dropdown/selectOnClose', ['../utils'], function (Utils) {
      function SelectOnClose() {}

      SelectOnClose.prototype.bind = function (decorated, container, $container) {
        var self = this;
        decorated.call(this, container, $container);
        container.on('close', function (params) {
          self._handleSelectOnClose(params);
        });
      };

      SelectOnClose.prototype._handleSelectOnClose = function (_, params) {
        if (params && params.originalSelect2Event != null) {
          var event = params.originalSelect2Event; // Don't select an item if the close event was triggered from a select or
          // unselect event

          if (event._type === 'select' || event._type === 'unselect') {
            return;
          }
        }

        var $highlightedResults = this.getHighlightedResults(); // Only select highlighted results

        if ($highlightedResults.length < 1) {
          return;
        }

        var data = Utils.GetData($highlightedResults[0], 'data'); // Don't re-select already selected resulte

        if (data.element != null && data.element.selected || data.element == null && data.selected) {
          return;
        }

        this.trigger('select', {
          data: data
        });
      };

      return SelectOnClose;
    });
    S2.define('select2/dropdown/closeOnSelect', [], function () {
      function CloseOnSelect() {}

      CloseOnSelect.prototype.bind = function (decorated, container, $container) {
        var self = this;
        decorated.call(this, container, $container);
        container.on('select', function (evt) {
          self._selectTriggered(evt);
        });
        container.on('unselect', function (evt) {
          self._selectTriggered(evt);
        });
      };

      CloseOnSelect.prototype._selectTriggered = function (_, evt) {
        var originalEvent = evt.originalEvent; // Don't close if the control key is being held

        if (originalEvent && (originalEvent.ctrlKey || originalEvent.metaKey)) {
          return;
        }

        this.trigger('close', {
          originalEvent: originalEvent,
          originalSelect2Event: evt
        });
      };

      return CloseOnSelect;
    });
    S2.define('select2/dropdown/dropdownCss', ['../utils'], function (Utils) {
      function DropdownCSS() {}

      DropdownCSS.prototype.render = function (decorated) {
        var $dropdown = decorated.call(this);
        var dropdownCssClass = this.options.get('dropdownCssClass') || '';

        if (dropdownCssClass.indexOf(':all:') !== -1) {
          dropdownCssClass = dropdownCssClass.replace(':all:', '');
          Utils.copyNonInternalCssClasses($dropdown[0], this.$element[0]);
        }

        $dropdown.addClass('sui-select-dropdown'); // FIX: Make sure "sui-select-dropdown" main class does not get erased. @edited

        $dropdown.addClass(dropdownCssClass);
        return $dropdown;
      };

      return DropdownCSS;
    });
    S2.define('select2/dropdown/tagsSearchHighlight', ['../utils'], function (Utils) {
      function TagsSearchHighlight() {}

      TagsSearchHighlight.prototype.highlightFirstItem = function (decorated) {
        var $options = this.$results.find('.select2-results__option--selectable' + ':not(.select2-results__option--selected)');

        if ($options.length > 0) {
          var $firstOption = $options.first();
          var data = Utils.GetData($firstOption[0], 'data');
          var firstElement = data.element;

          if (firstElement && firstElement.getAttribute) {
            if (firstElement.getAttribute('data-select2-tag') === 'true') {
              $firstOption.trigger('mouseenter');
              return;
            }
          }
        }

        decorated.call(this);
      };

      return TagsSearchHighlight;
    });
    S2.define('select2/i18n/en', [], function () {
      // English
      return {
        errorLoading: function errorLoading() {
          return 'The results could not be loaded.';
        },
        inputTooLong: function inputTooLong(args) {
          var overChars = args.input.length - args.maximum;
          var message = 'Please delete ' + overChars + ' character';

          if (overChars != 1) {
            message += 's';
          }

          return message;
        },
        inputTooShort: function inputTooShort(args) {
          var remainingChars = args.minimum - args.input.length;
          var message = 'Please enter ' + remainingChars + ' or more characters';
          return message;
        },
        loadingMore: function loadingMore() {
          return 'Loading more results…';
        },
        maximumSelected: function maximumSelected(args) {
          var message = 'You can only select ' + args.maximum + ' item';

          if (args.maximum != 1) {
            message += 's';
          }

          return message;
        },
        noResults: function noResults() {
          return 'No results found';
        },
        searching: function searching() {
          return 'Searching…';
        },
        removeAllItems: function removeAllItems() {
          return 'Remove all items';
        },
        removeItem: function removeItem() {
          return 'Remove item';
        },
        search: function search() {
          return 'Search';
        }
      };
    });
    S2.define('select2/defaults', ['jquery', './results', './selection/single', './selection/multiple', './selection/placeholder', './selection/allowClear', './selection/search', './selection/selectionCss', './selection/eventRelay', './utils', './translation', './diacritics', './data/select', './data/array', './data/ajax', './data/tags', './data/tokenizer', './data/minimumInputLength', './data/maximumInputLength', './data/maximumSelectionLength', './dropdown', './dropdown/search', './dropdown/hidePlaceholder', './dropdown/infiniteScroll', './dropdown/attachBody', './dropdown/minimumResultsForSearch', './dropdown/selectOnClose', './dropdown/closeOnSelect', './dropdown/dropdownCss', './dropdown/tagsSearchHighlight', './i18n/en'], function ($, ResultsList, SingleSelection, MultipleSelection, Placeholder, AllowClear, SelectionSearch, SelectionCSS, EventRelay, Utils, Translation, DIACRITICS, SelectData, ArrayData, AjaxData, Tags, Tokenizer, MinimumInputLength, MaximumInputLength, MaximumSelectionLength, Dropdown, DropdownSearch, HidePlaceholder, InfiniteScroll, AttachBody, MinimumResultsForSearch, SelectOnClose, CloseOnSelect, DropdownCSS, TagsSearchHighlight, EnglishTranslation) {
      function Defaults() {
        this.reset();
      }

      Defaults.prototype.apply = function (options) {
        options = $.extend(true, {}, this.defaults, options);

        if (options.dataAdapter == null) {
          if (options.ajax != null) {
            options.dataAdapter = AjaxData;
          } else if (options.data != null) {
            options.dataAdapter = ArrayData;
          } else {
            options.dataAdapter = SelectData;
          }

          if (options.minimumInputLength > 0) {
            options.dataAdapter = Utils.Decorate(options.dataAdapter, MinimumInputLength);
          }

          if (options.maximumInputLength > 0) {
            options.dataAdapter = Utils.Decorate(options.dataAdapter, MaximumInputLength);
          }

          if (options.maximumSelectionLength > 0) {
            options.dataAdapter = Utils.Decorate(options.dataAdapter, MaximumSelectionLength);
          }

          if (options.tags) {
            options.dataAdapter = Utils.Decorate(options.dataAdapter, Tags);
          }

          if (options.tokenSeparators != null || options.tokenizer != null) {
            options.dataAdapter = Utils.Decorate(options.dataAdapter, Tokenizer);
          }
        }

        if (options.resultsAdapter == null) {
          options.resultsAdapter = ResultsList;

          if (options.ajax != null) {
            options.resultsAdapter = Utils.Decorate(options.resultsAdapter, InfiniteScroll);
          }

          if (options.placeholder != null) {
            options.resultsAdapter = Utils.Decorate(options.resultsAdapter, HidePlaceholder);
          }

          if (options.selectOnClose) {
            options.resultsAdapter = Utils.Decorate(options.resultsAdapter, SelectOnClose);
          }

          if (options.tags) {
            options.resultsAdapter = Utils.Decorate(options.resultsAdapter, TagsSearchHighlight);
          }
        }

        if (options.dropdownAdapter == null) {
          if (options.multiple) {
            options.dropdownAdapter = Dropdown;
          } else {
            var SearchableDropdown = Utils.Decorate(Dropdown, DropdownSearch);
            options.dropdownAdapter = SearchableDropdown;
          }

          if (options.minimumResultsForSearch !== 0) {
            options.dropdownAdapter = Utils.Decorate(options.dropdownAdapter, MinimumResultsForSearch);
          }

          if (options.closeOnSelect) {
            options.dropdownAdapter = Utils.Decorate(options.dropdownAdapter, CloseOnSelect);
          }

          if (options.dropdownCssClass != null) {
            options.dropdownAdapter = Utils.Decorate(options.dropdownAdapter, DropdownCSS);
          }

          options.dropdownAdapter = Utils.Decorate(options.dropdownAdapter, AttachBody);
        }

        if (options.selectionAdapter == null) {
          if (options.multiple) {
            options.selectionAdapter = MultipleSelection;
          } else {
            options.selectionAdapter = SingleSelection;
          } // Add the placeholder mixin if a placeholder was specified


          if (options.placeholder != null) {
            options.selectionAdapter = Utils.Decorate(options.selectionAdapter, Placeholder);
          }

          if (options.allowClear) {
            options.selectionAdapter = Utils.Decorate(options.selectionAdapter, AllowClear);
          }

          if (options.multiple) {
            options.selectionAdapter = Utils.Decorate(options.selectionAdapter, SelectionSearch);
          }

          if (options.selectionCssClass != null) {
            options.selectionAdapter = Utils.Decorate(options.selectionAdapter, SelectionCSS);
          }

          options.selectionAdapter = Utils.Decorate(options.selectionAdapter, EventRelay);
        } // If the defaults were not previously applied from an element, it is
        // possible for the language option to have not been resolved


        options.language = this._resolveLanguage(options.language); // Always fall back to English since it will always be complete

        options.language.push('en');
        var uniqueLanguages = [];

        for (var l = 0; l < options.language.length; l++) {
          var language = options.language[l];

          if (uniqueLanguages.indexOf(language) === -1) {
            uniqueLanguages.push(language);
          }
        }

        options.language = uniqueLanguages;
        options.translations = this._processTranslations(options.language, options.debug);
        return options;
      };

      Defaults.prototype.reset = function () {
        function stripDiacritics(text) {
          // Used 'uni range + named function' from http://jsperf.com/diacritics/18
          function match(a) {
            return DIACRITICS[a] || a;
          }

          return text.replace(/[^\u0000-\u007E]/g, match);
        }

        function matcher(params, data) {
          // Always return the object if there is nothing to compare
          if (params.term == null || params.term.trim() === '') {
            return data;
          } // Do a recursive check for options with children


          if (data.children && data.children.length > 0) {
            // Clone the data object if there are children
            // This is required as we modify the object to remove any non-matches
            var match = $.extend(true, {}, data); // Check each child of the option

            for (var c = data.children.length - 1; c >= 0; c--) {
              var child = data.children[c];
              var matches = matcher(params, child); // If there wasn't a match, remove the object in the array

              if (matches == null) {
                match.children.splice(c, 1);
              }
            } // If any children matched, return the new object


            if (match.children.length > 0) {
              return match;
            } // If there were no matching children, check just the plain object


            return matcher(params, match);
          }

          var original = stripDiacritics(data.text).toUpperCase();
          var term = stripDiacritics(params.term).toUpperCase(); // Check if the text contains the term

          if (original.indexOf(term) > -1) {
            return data;
          } // If it doesn't contain the term, don't return anything


          return null;
        }

        this.defaults = {
          amdLanguageBase: './i18n/',
          autocomplete: 'off',
          closeOnSelect: true,
          debug: false,
          dropdownAutoWidth: false,
          escapeMarkup: Utils.escapeMarkup,
          language: {},
          matcher: matcher,
          minimumInputLength: 0,
          maximumInputLength: 0,
          maximumSelectionLength: 0,
          minimumResultsForSearch: 0,
          selectOnClose: false,
          scrollAfterSelect: false,
          sorter: function sorter(data) {
            return data;
          },
          templateResult: function templateResult(result) {
            return result.text;
          },
          templateSelection: function templateSelection(selection) {
            return selection.text;
          },
          theme: 'default',
          width: 'resolve'
        };
      };

      Defaults.prototype.applyFromElement = function (options, $element) {
        var optionLanguage = options.language;
        var defaultLanguage = this.defaults.language;
        var elementLanguage = $element.prop('lang');
        var parentLanguage = $element.closest('[lang]').prop('lang');
        var languages = Array.prototype.concat.call(this._resolveLanguage(elementLanguage), this._resolveLanguage(optionLanguage), this._resolveLanguage(defaultLanguage), this._resolveLanguage(parentLanguage));
        options.language = languages;
        return options;
      };

      Defaults.prototype._resolveLanguage = function (language) {
        if (!language) {
          return [];
        }

        if ($.isEmptyObject(language)) {
          return [];
        }

        if ($.isPlainObject(language)) {
          return [language];
        }

        var languages;

        if (!Array.isArray(language)) {
          languages = [language];
        } else {
          languages = language;
        }

        var resolvedLanguages = [];

        for (var l = 0; l < languages.length; l++) {
          resolvedLanguages.push(languages[l]);

          if (typeof languages[l] === 'string' && languages[l].indexOf('-') > 0) {
            // Extract the region information if it is included
            var languageParts = languages[l].split('-');
            var baseLanguage = languageParts[0];
            resolvedLanguages.push(baseLanguage);
          }
        }

        return resolvedLanguages;
      };

      Defaults.prototype._processTranslations = function (languages, debug) {
        var translations = new Translation();

        for (var l = 0; l < languages.length; l++) {
          var languageData = new Translation();
          var language = languages[l];

          if (typeof language === 'string') {
            try {
              // Try to load it with the original name
              languageData = Translation.loadPath(language);
            } catch (e) {
              try {
                // If we couldn't load it, check if it wasn't the full path
                language = this.defaults.amdLanguageBase + language;
                languageData = Translation.loadPath(language);
              } catch (ex) {
                // The translation could not be loaded at all. Sometimes this is
                // because of a configuration problem, other times this can be
                // because of how Select2 helps load all possible translation files
                if (debug && window.console && console.warn) {
                  console.warn('Select2: The language file for "' + language + '" could ' + 'not be automatically loaded. A fallback will be used instead.');
                }
              }
            }
          } else if ($.isPlainObject(language)) {
            languageData = new Translation(language);
          } else {
            languageData = language;
          }

          translations.extend(languageData);
        }

        return translations;
      };

      Defaults.prototype.set = function (key, value) {
        var camelKey = $.camelCase(key);
        var data = {};
        data[camelKey] = value;

        var convertedData = Utils._convertData(data);

        $.extend(true, this.defaults, convertedData);
      };

      var defaults = new Defaults();
      return defaults;
    });
    S2.define('select2/options', ['jquery', './defaults', './utils'], function ($, Defaults, Utils) {
      function Options(options, $element) {
        this.options = options;

        if ($element != null) {
          this.fromElement($element);
        }

        if ($element != null) {
          this.options = Defaults.applyFromElement(this.options, $element);
        }

        this.options = Defaults.apply(this.options);
      }

      Options.prototype.fromElement = function ($e) {
        var excludedData = ['select2'];

        if (this.options.multiple == null) {
          this.options.multiple = $e.prop('multiple');
        }

        if (this.options.disabled == null) {
          this.options.disabled = $e.prop('disabled');
        }

        if (this.options.autocomplete == null && $e.prop('autocomplete')) {
          this.options.autocomplete = $e.prop('autocomplete');
        }

        if (this.options.dir == null) {
          if ($e.prop('dir')) {
            this.options.dir = $e.prop('dir');
          } else if ($e.closest('[dir]').prop('dir')) {
            this.options.dir = $e.closest('[dir]').prop('dir');
          } else {
            this.options.dir = 'ltr';
          }
        }

        $e.prop('disabled', this.options.disabled);
        $e.prop('multiple', this.options.multiple);

        if (Utils.GetData($e[0], 'select2Tags')) {
          if (this.options.debug && window.console && console.warn) {
            console.warn('Select2: The `data-select2-tags` attribute has been changed to ' + 'use the `data-data` and `data-tags="true"` attributes and will be ' + 'removed in future versions of Select2.');
          }

          Utils.StoreData($e[0], 'data', Utils.GetData($e[0], 'select2Tags'));
          Utils.StoreData($e[0], 'tags', true);
        }

        if (Utils.GetData($e[0], 'ajaxUrl')) {
          if (this.options.debug && window.console && console.warn) {
            console.warn('Select2: The `data-ajax-url` attribute has been changed to ' + '`data-ajax--url` and support for the old attribute will be removed' + ' in future versions of Select2.');
          }

          $e.attr('ajax--url', Utils.GetData($e[0], 'ajaxUrl'));
          Utils.StoreData($e[0], 'ajax-Url', Utils.GetData($e[0], 'ajaxUrl'));
        }

        var dataset = {};

        function upperCaseLetter(_, letter) {
          return letter.toUpperCase();
        } // Pre-load all of the attributes which are prefixed with `data-`


        for (var attr = 0; attr < $e[0].attributes.length; attr++) {
          var attributeName = $e[0].attributes[attr].name;
          var prefix = 'data-';

          if (attributeName.substr(0, prefix.length) == prefix) {
            // Get the contents of the attribute after `data-`
            var dataName = attributeName.substring(prefix.length); // Get the data contents from the consistent source
            // This is more than likely the jQuery data helper

            var dataValue = Utils.GetData($e[0], dataName); // camelCase the attribute name to match the spec

            var camelDataName = dataName.replace(/-([a-z])/g, upperCaseLetter); // Store the data attribute contents into the dataset since

            dataset[camelDataName] = dataValue;
          }
        } // Prefer the element's `dataset` attribute if it exists
        // jQuery 1.x does not correctly handle data attributes with multiple dashes


        if ($.fn.jquery && $.fn.jquery.substr(0, 2) == '1.' && $e[0].dataset) {
          dataset = $.extend(true, {}, $e[0].dataset, dataset);
        } // Prefer our internal data cache if it exists


        var data = $.extend(true, {}, Utils.GetData($e[0]), dataset);
        data = Utils._convertData(data);

        for (var key in data) {
          if (excludedData.indexOf(key) > -1) {
            continue;
          }

          if ($.isPlainObject(this.options[key])) {
            $.extend(this.options[key], data[key]);
          } else {
            this.options[key] = data[key];
          }
        }

        return this;
      };

      Options.prototype.get = function (key) {
        return this.options[key];
      };

      Options.prototype.set = function (key, val) {
        this.options[key] = val;
      };

      return Options;
    });
    S2.define('select2/core', ['jquery', './options', './utils', './keys'], function ($, Options, Utils, KEYS) {
      var Select2 = function Select2($element, options) {
        if (Utils.GetData($element[0], 'select2') != null) {
          Utils.GetData($element[0], 'select2').destroy();
        }

        this.$element = $element;
        this.id = this._generateId($element);
        options = options || {};
        this.options = new Options(options, $element);

        Select2.__super__.constructor.call(this); // Set up the tabindex


        var tabindex = $element.attr('tabindex') || 0;
        Utils.StoreData($element[0], 'old-tabindex', tabindex);
        $element.attr('tabindex', '-1'); // Set up containers and adapters

        var DataAdapter = this.options.get('dataAdapter');
        this.dataAdapter = new DataAdapter($element, this.options);
        var $container = this.render();

        this._placeContainer($container);

        var SelectionAdapter = this.options.get('selectionAdapter');
        this.selection = new SelectionAdapter($element, this.options);
        this.$selection = this.selection.render();
        this.selection.position(this.$selection, $container);
        var DropdownAdapter = this.options.get('dropdownAdapter');
        this.dropdown = new DropdownAdapter($element, this.options);
        this.$dropdown = this.dropdown.render();
        this.dropdown.position(this.$dropdown, $container);
        var ResultsAdapter = this.options.get('resultsAdapter');
        this.results = new ResultsAdapter($element, this.options, this.dataAdapter);
        this.$results = this.results.render();
        this.results.position(this.$results, this.$dropdown); // Bind events

        var self = this; // Bind the container to all of the adapters

        this._bindAdapters(); // Register any DOM event handlers


        this._registerDomEvents(); // Register any internal event handlers


        this._registerDataEvents();

        this._registerSelectionEvents();

        this._registerDropdownEvents();

        this._registerResultsEvents();

        this._registerEvents(); // Set the initial state


        this.dataAdapter.current(function (initialData) {
          self.trigger('selection:update', {
            data: initialData
          });
        }); // Hide the original select

        $element[0].classList.add('select2-hidden-accessible');
        $element.attr('aria-hidden', 'true'); // Hide the original select with SUI. @edited

        $element.addClass('sui-screen-reader-text'); // Synchronize any monitored attributes

        this._syncAttributes();

        Utils.StoreData($element[0], 'select2', this); // Ensure backwards compatibility with $element.data('select2').

        $element.data('select2', this);
      };

      Utils.Extend(Select2, Utils.Observable);

      Select2.prototype._generateId = function ($element) {
        var id = '';

        if ($element.attr('id') != null) {
          id = $element.attr('id');
        } else if ($element.attr('name') != null) {
          id = $element.attr('name') + '-' + Utils.generateChars(2);
        } else {
          id = Utils.generateChars(4);
        }

        id = id.replace(/(:|\.|\[|\]|,)/g, '');
        id = 'select2-' + id;
        return id;
      };

      Select2.prototype._placeContainer = function ($container) {
        $container.insertAfter(this.$element);

        var width = this._resolveWidth(this.$element, this.options.get('width'));

        if (width != null) {
          $container.css('width', width);
        }
      };

      Select2.prototype._resolveWidth = function ($element, method) {
        var WIDTH = /^width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i;

        if (method == 'resolve') {
          var styleWidth = this._resolveWidth($element, 'style');

          if (styleWidth != null) {
            return styleWidth;
          }

          return this._resolveWidth($element, 'element');
        }

        if (method == 'element') {
          var elementWidth = $element.outerWidth(false);

          if (elementWidth <= 0) {
            return 'auto';
          }

          return elementWidth + 'px';
        }

        if (method == 'style') {
          var style = $element.attr('style');

          if (typeof style !== 'string') {
            return null;
          }

          var attrs = style.split(';');

          for (var i = 0, l = attrs.length; i < l; i = i + 1) {
            var attr = attrs[i].replace(/\s/g, '');
            var matches = attr.match(WIDTH);

            if (matches !== null && matches.length >= 1) {
              return matches[1];
            }
          }

          return null;
        }

        if (method == 'computedstyle') {
          var computedStyle = window.getComputedStyle($element[0]);
          return computedStyle.width;
        }

        return method;
      };

      Select2.prototype._bindAdapters = function () {
        this.dataAdapter.bind(this, this.$container);
        this.selection.bind(this, this.$container);
        this.dropdown.bind(this, this.$container);
        this.results.bind(this, this.$container);
      };

      Select2.prototype._registerDomEvents = function () {
        var self = this;
        this.$element.on('change.select2', function () {
          self.dataAdapter.current(function (data) {
            self.trigger('selection:update', {
              data: data
            });
          });
        });
        this.$element.on('focus.select2', function (evt) {
          self.trigger('focus', evt);
        });
        this._syncA = Utils.bind(this._syncAttributes, this);
        this._syncS = Utils.bind(this._syncSubtree, this);
        this._observer = new window.MutationObserver(function (mutations) {
          self._syncA();

          self._syncS(mutations);
        });

        this._observer.observe(this.$element[0], {
          attributes: true,
          childList: true,
          subtree: false
        });
      };

      Select2.prototype._registerDataEvents = function () {
        var self = this;
        this.dataAdapter.on('*', function (name, params) {
          self.trigger(name, params);
        });
      };

      Select2.prototype._registerSelectionEvents = function () {
        var self = this;
        var nonRelayEvents = ['toggle', 'focus'];
        this.selection.on('toggle', function () {
          self.toggleDropdown();
        });
        this.selection.on('focus', function (params) {
          self.focus(params);
        });
        this.selection.on('*', function (name, params) {
          if (nonRelayEvents.indexOf(name) !== -1) {
            return;
          }

          self.trigger(name, params);
        });
      };

      Select2.prototype._registerDropdownEvents = function () {
        var self = this;
        this.dropdown.on('*', function (name, params) {
          self.trigger(name, params);
        });
      };

      Select2.prototype._registerResultsEvents = function () {
        var self = this;
        this.results.on('*', function (name, params) {
          self.trigger(name, params);
        });
      };

      Select2.prototype._registerEvents = function () {
        var self = this;
        this.on('open', function () {
          self.$container[0].classList.add('select2-container--open');
        });
        this.on('close', function () {
          self.$container[0].classList.remove('select2-container--open');
        });
        this.on('enable', function () {
          self.$container[0].classList.remove('select2-container--disabled');
        });
        this.on('disable', function () {
          self.$container[0].classList.add('select2-container--disabled');
        });
        this.on('blur', function () {
          self.$container[0].classList.remove('select2-container--focus');
        });
        this.on('query', function (params) {
          if (!self.isOpen()) {
            self.trigger('open', {});
          }

          this.dataAdapter.query(params, function (data) {
            self.trigger('results:all', {
              data: data,
              query: params
            });
          });
        });
        this.on('query:append', function (params) {
          this.dataAdapter.query(params, function (data) {
            self.trigger('results:append', {
              data: data,
              query: params
            });
          });
        });
        this.on('keypress', function (evt) {
          var key = evt.which;

          if (self.isOpen()) {
            if (key === KEYS.ENTER) {
              self.trigger('results:select');
              evt.preventDefault();
            } else if (key === KEYS.SPACE && evt.ctrlKey) {
              self.trigger('results:toggle');
              evt.preventDefault();
            } else if (key === KEYS.UP) {
              self.trigger('results:previous');
              evt.preventDefault();
            } else if (key === KEYS.DOWN) {
              self.trigger('results:next');
              evt.preventDefault();
            } else if (key === KEYS.ESC || key === KEYS.TAB) {
              self.close();
              evt.preventDefault();
            }
          } else {
            // Added the functionality to change option on press of up and down arrow. @edited
            if (key === KEYS.ENTER || key === KEYS.SPACE || (key === KEYS.DOWN || key === KEYS.UP) && evt.altKey) {
              self.open();
              evt.preventDefault();
            } else if (key === KEYS.DOWN) {
              if (undefined != this.$element.find('option:selected').next().val()) {
                this.$element.val(this.$element.find('option:selected').next().val());
                this.$element.trigger('change');
              }

              evt.preventDefault();
            } else if (key === KEYS.UP) {
              if (undefined != this.$element.find('option:selected').prev().val()) {
                this.$element.val(this.$element.find('option:selected').prev().val());
                this.$element.trigger('change');
              }

              evt.preventDefault();
            } // Added the functionality to select option based on key press. @edited
            else {
              var selectedValue = this.$element.find('option:selected').text();
              var keyPressed = String.fromCharCode(key).toLowerCase();
              var values = this.$element.find('option').filter(function () {
                var _$$text;

                return (_$$text = $(this).text()) === null || _$$text === void 0 ? void 0 : _$$text.toLowerCase().startsWith(keyPressed);
              });
              var arrLength = values.length - 1;
              var elemVal = selectedValue;
              values.each(function (index) {
                console.log(selectedValue);

                if (selectedValue !== '' && selectedValue[0].toLowerCase() === keyPressed) {
                  if ($(this).text() === selectedValue && index !== arrLength) {
                    elemVal = $(values[index + 1]).val();
                    return false;
                  }

                  return;
                }

                elemVal = $(this).val();
                return false;
              });
              elemVal !== selectedValue && (self.$element.val(elemVal), self.$element.trigger('change'));
            }
          }
        });
      };

      Select2.prototype._syncAttributes = function () {
        this.options.set('disabled', this.$element.prop('disabled'));

        if (this.isDisabled()) {
          if (this.isOpen()) {
            this.close();
          }

          this.trigger('disable', {});
        } else {
          this.trigger('enable', {});
        }
      };

      Select2.prototype._isChangeMutation = function (mutations) {
        var self = this;

        if (mutations.addedNodes && mutations.addedNodes.length > 0) {
          for (var n = 0; n < mutations.addedNodes.length; n++) {
            var node = mutations.addedNodes[n];

            if (node.selected) {
              return true;
            }
          }
        } else if (mutations.removedNodes && mutations.removedNodes.length > 0) {
          return true;
        } else if (Array.isArray(mutations)) {
          return mutations.some(function (mutation) {
            return self._isChangeMutation(mutation);
          });
        }

        return false;
      };

      Select2.prototype._syncSubtree = function (mutations) {
        var changed = this._isChangeMutation(mutations);

        var self = this; // Only re-pull the data if we think there is a change

        if (changed) {
          this.dataAdapter.current(function (currentData) {
            self.trigger('selection:update', {
              data: currentData
            });
          });
        }
      };
      /**
       * Override the trigger method to automatically trigger pre-events when
       * there are events that can be prevented.
       */


      Select2.prototype.trigger = function (name, args) {
        var actualTrigger = Select2.__super__.trigger;
        var preTriggerMap = {
          'open': 'opening',
          'close': 'closing',
          'select': 'selecting',
          'unselect': 'unselecting',
          'clear': 'clearing'
        };

        if (args === undefined) {
          args = {};
        }

        if (name in preTriggerMap) {
          var preTriggerName = preTriggerMap[name];
          var preTriggerArgs = {
            prevented: false,
            name: name,
            args: args
          };
          actualTrigger.call(this, preTriggerName, preTriggerArgs);

          if (preTriggerArgs.prevented) {
            args.prevented = true;
            return;
          }
        }

        actualTrigger.call(this, name, args);
      };

      Select2.prototype.toggleDropdown = function () {
        if (this.isDisabled()) {
          return;
        }

        if (this.isOpen()) {
          this.close();
        } else {
          this.open();
        }
      };

      Select2.prototype.open = function () {
        if (this.isOpen()) {
          return;
        }

        if (this.isDisabled()) {
          return;
        }

        this.trigger('query', {});
      };

      Select2.prototype.close = function (evt) {
        if (!this.isOpen()) {
          return;
        }

        this.trigger('close', {
          originalEvent: evt
        });
      };
      /**
       * Helper method to abstract the "enabled" (not "disabled") state of this
       * object.
       *
       * @return {true} if the instance is not disabled.
       * @return {false} if the instance is disabled.
       */


      Select2.prototype.isEnabled = function () {
        return !this.isDisabled();
      };
      /**
       * Helper method to abstract the "disabled" state of this object.
       *
       * @return {true} if the disabled option is true.
       * @return {false} if the disabled option is false.
       */


      Select2.prototype.isDisabled = function () {
        return this.options.get('disabled');
      };

      Select2.prototype.isOpen = function () {
        return this.$container[0].classList.contains('select2-container--open');
      };

      Select2.prototype.hasFocus = function () {
        return this.$container[0].classList.contains('select2-container--focus');
      };

      Select2.prototype.focus = function (data) {
        // No need to re-trigger focus events if we are already focused
        if (this.hasFocus()) {
          return;
        }

        this.$container[0].classList.add('select2-container--focus');
        this.trigger('focus', {});
      };

      Select2.prototype.enable = function (args) {
        if (this.options.get('debug') && window.console && console.warn) {
          console.warn('Select2: The `select2("enable")` method has been deprecated and will' + ' be removed in later Select2 versions. Use $element.prop("disabled")' + ' instead.');
        }

        if (args == null || args.length === 0) {
          args = [true];
        }

        var disabled = !args[0];
        this.$element.prop('disabled', disabled);
      };

      Select2.prototype.data = function () {
        if (this.options.get('debug') && arguments.length > 0 && window.console && console.warn) {
          console.warn('Select2: Data can no longer be set using `select2("data")`. You ' + 'should consider setting the value instead using `$element.val()`.');
        }

        var data = [];
        this.dataAdapter.current(function (currentData) {
          data = currentData;
        });
        return data;
      };

      Select2.prototype.val = function (args) {
        if (this.options.get('debug') && window.console && console.warn) {
          console.warn('Select2: The `select2("val")` method has been deprecated and will be' + ' removed in later Select2 versions. Use $element.val() instead.');
        }

        if (args == null || args.length === 0) {
          return this.$element.val();
        }

        var newVal = args[0];

        if (Array.isArray(newVal)) {
          newVal = newVal.map(function (obj) {
            return obj.toString();
          });
        }

        this.$element.val(newVal).trigger('input').trigger('change');
      };

      Select2.prototype.destroy = function () {
        Utils.RemoveData(this.$container[0]);
        this.$container.remove();

        this._observer.disconnect();

        this._observer = null;
        this._syncA = null;
        this._syncS = null;
        this.$element.off('.select2');
        this.$element.attr('tabindex', Utils.GetData(this.$element[0], 'old-tabindex')); // Remove SUI screen reader class. @edited

        this.$element.removeClass('sui-screen-reader-text');
        this.$element[0].classList.remove('select2-hidden-accessible');
        this.$element.attr('aria-hidden', 'false');
        Utils.RemoveData(this.$element[0]);
        this.$element.removeData('select2');
        this.dataAdapter.destroy();
        this.selection.destroy();
        this.dropdown.destroy();
        this.results.destroy();
        this.dataAdapter = null;
        this.selection = null;
        this.dropdown = null;
        this.results = null;
      };

      Select2.prototype.render = function () {
        var $container = $('<span class="select2 select2-container">' + '<span class="selection"></span>' + '<span class="dropdown-wrapper" aria-hidden="true"></span>' + '</span>');
        $container.attr('dir', this.options.get('dir'));
        this.$container = $container; // Add SUIselect class to select main div. @edited

        this.$container[0].classList.add('sui-select'); // Additional class for themes. @edited

        if ('default' !== this.options.get('theme')) {
          this.$container[0].classList.add('sui-select-theme--' + this.options.get('theme'));
        }

        Utils.StoreData($container[0], 'element', this.$element);
        return $container;
      };

      return Select2;
    });
    S2.define('jquery-mousewheel', ['jquery'], function ($) {
      // Used to shim jQuery.mousewheel for non-full builds.
      return $;
    });
    /**
     * Rebranding select2 to SUIselect2
     * It does avoid conflicts with other(s) that include select2 manually
     * @edited
     */

    S2.define('sui.select2', ['jquery', 'jquery-mousewheel', './select2/core', './select2/defaults', './select2/utils'], function ($, _, Select2, Defaults, Utils) {
      // Rename function. @edited
      if ($.fn.SUIselect2 == null) {
        // All methods that should return the element
        var thisMethods = ['open', 'close', 'destroy']; // Rename function. @edited

        $.fn.SUIselect2 = function (options) {
          options = options || {};

          if (_typeof(options) === 'object') {
            this.each(function () {
              var instanceOptions = $.extend(true, {}, options);
              var instance = new Select2($(this), instanceOptions);
            });
            return this;
          } else if (typeof options === 'string') {
            var ret;
            var args = Array.prototype.slice.call(arguments, 1);
            this.each(function () {
              var instance = Utils.GetData(this, 'select2');

              if (instance == null && window.console && console.error) {
                // Rename function on error message. @edited
                console.error('The SUIselect2(\'' + options + '\') method was called on an ' + 'element that is not using Select2.');
              }

              ret = instance[options].apply(instance, args);
            }); // Check if we should be returning `this`

            if (thisMethods.indexOf(options) > -1) {
              return this;
            }

            return ret;
          } else {
            // Rename function on error message. @edited
            throw new Error('Invalid arguments for SUIselect2: ' + options);
          }
        };
      } // Rename function. @edited


      if ($.fn.SUIselect2.defaults == null) {
        $.fn.SUIselect2.defaults = Defaults; // Rename function. @edited
      }

      return Select2;
    }); // Return the AMD loader configuration so it can be used outside of this file

    return {
      define: S2.define,
      require: S2.require
    };
  }(); // Autoload the jQuery bindings
  // We know that all of the modules exist above this, so we're safe


  var select2 = S2.require('sui.select2'); // Rename function. @edited
  // Hold the AMD module references on the jQuery function that was just loaded
  // This allows Select2 to use the internal loader outside of this file, such
  // as in the language files.
  // jQuery.fn.select2.amd = S2;
  // Return the Select2 instance for anyone who is importing it.


  return select2;
});;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

;

(function ($) {
  // Define global SUI object if it doesn't exist.
  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.select = {};

  SUI.select.formatIcon = function (data, container) {
    var markup;
    var label = data.text;
    var icon = $(data.element).attr('data-icon');

    if (!data.id) {
      return label; // optgroup.
    }

    if ('undefined' !== typeof icon) {
      markup = '<span class="sui-icon-' + icon.toLowerCase() + '" aria-hidden="true"></span> ' + label;
    } else {
      markup = label;
    }

    return markup;
  };

  SUI.select.formatIconSelection = function (data, container) {
    var markup;
    var label = data.text;
    var icon = $(data.element).attr('data-icon');

    if ('undefined' !== typeof icon) {
      markup = '<span class="sui-icon-' + icon.toLowerCase() + '" aria-hidden="true"></span> ' + label;
    } else {
      markup = label;
    }

    return markup;
  };

  SUI.select.formatColor = function (data, container) {
    var markup, border;
    var label = data.text;
    var color = $(data.element).attr('data-color');

    if (!data.id) {
      return label; // optgroup.
    }

    if ('undefined' !== typeof color) {
      switch (color) {
        case '#FFF':
        case 'white':
        case '#FFFFFF':
          border = '#000';
          break;

        case '#FAFAFA':
        case '#F8F8F8':
        case '#F2F2F2':
          border = '#333';
          break;

        default:
          border = color;
          break;
      }

      markup = '<span class="sui-color" style="border-color: ' + border + '; background-color: ' + color + ';" aria-hidden="true"></span> ' + label;
    } else {
      markup = label;
    }

    return markup;
  };

  SUI.select.formatColorSelection = function (data, container) {
    var markup;
    var label = data.text;
    var color = $(data.element).attr('data-color');

    if ('undefined' !== typeof color) {
      switch (color) {
        case '#FFF':
        case 'white':
        case '#FFFFFF':
          border = '#000';
          break;

        case '#FAFAFA':
        case '#F8F8F8':
        case '#F2F2F2':
          border = '#333';
          break;

        default:
          border = color;
          break;
      }

      markup = '<span class="sui-color" style="border-color: ' + border + '; background-color: ' + color + ';" aria-hidden="true"></span> ' + label;
    } else {
      markup = label;
    }

    return markup;
  };

  SUI.select.formatVars = function (data, container) {
    var markup;
    var label = data.text;
    var content = $(data.element).val();

    if (!data.id) {
      return label; // optgroup.
    }

    if ('undefined' !== typeof content) {
      markup = '<span class="sui-variable-name">' + label + '</span><span class="sui-variable-value">' + content + '</span> ';
    } else {
      markup = label;
    }

    return markup;
  };

  SUI.select.formatVarsSelection = function (data, container) {
    var markup;
    var label = data.text;
    markup = '<span class="sui-icon-plus-circle sui-md" aria-hidden="true"></span>';
    markup += '<span class="sui-screen-reader-text">' + label + '</span>';
    return markup;
  };

  SUI.select.init = function (select) {
    var getParent = select.closest('.sui-modal-content'),
        getParentId = getParent.attr('id'),
        selectParent = getParent.length ? $('#' + getParentId) : $('.sui-2-12-13'),
        hasSearch = 'true' === select.attr('data-search') ? 0 : -1,
        isSmall = select.hasClass('sui-select-sm') ? 'sui-select-dropdown-sm' : '';
    select.SUIselect2({
      dropdownParent: selectParent,
      minimumResultsForSearch: hasSearch,
      dropdownCssClass: isSmall
    });
  };

  SUI.select.initIcon = function (select) {
    var getParent = select.closest('.sui-modal-content'),
        getParentId = getParent.attr('id'),
        selectParent = getParent.length ? $('#' + getParentId) : $('.sui-2-12-13'),
        hasSearch = 'true' === select.attr('data-search') ? 0 : -1,
        isSmall = select.hasClass('sui-select-sm') ? 'sui-select-dropdown-sm' : '';
    select.SUIselect2({
      dropdownParent: selectParent,
      templateResult: SUI.select.formatIcon,
      templateSelection: SUI.select.formatIconSelection,
      escapeMarkup: function escapeMarkup(markup) {
        return markup;
      },
      minimumResultsForSearch: hasSearch,
      dropdownCssClass: isSmall
    });
  };

  SUI.select.initColor = function (select) {
    var getParent = select.closest('.sui-modal-content'),
        getParentId = getParent.attr('id'),
        selectParent = getParent.length ? $('#' + getParentId) : $('.sui-2-12-13'),
        hasSearch = 'true' === select.attr('data-search') ? 0 : -1,
        isSmall = select.hasClass('sui-select-sm') ? 'sui-select-dropdown-sm' : '';
    select.SUIselect2({
      dropdownParent: selectParent,
      templateResult: SUI.select.formatColor,
      templateSelection: SUI.select.formatColorSelection,
      escapeMarkup: function escapeMarkup(markup) {
        return markup;
      },
      minimumResultsForSearch: hasSearch,
      dropdownCssClass: isSmall
    });
  };

  SUI.select.initSearch = function (select) {
    var getParent = select.closest('.sui-modal-content'),
        getParentId = getParent.attr('id'),
        selectParent = getParent.length ? $('#' + getParentId) : $('.sui-2-12-13'),
        isSmall = select.hasClass('sui-select-sm') ? 'sui-select-dropdown-sm' : '';
    select.SUIselect2({
      dropdownParent: selectParent,
      minimumInputLength: 2,
      maximumSelectionLength: 1,
      dropdownCssClass: isSmall
    });
  };

  SUI.select.initVars = function (select) {
    var getParent = select.closest('.sui-modal-content'),
        getParentId = getParent.attr('id'),
        selectParent = getParent.length ? $('#' + getParentId) : $('.sui-2-12-13'),
        hasSearch = 'true' === select.attr('data-search') ? 0 : -1;
    select.SUIselect2({
      theme: 'vars',
      dropdownParent: selectParent,
      templateResult: SUI.select.formatVars,
      templateSelection: SUI.select.formatVarsSelection,
      escapeMarkup: function escapeMarkup(markup) {
        return markup;
      },
      minimumResultsForSearch: hasSearch
    }).on('select2:close', function () {
      $(this).val(null);
    });
    select.val(null);
  };

  $('.sui-select').each(function () {
    var select = $(this);

    if ('icon' === select.data('theme')) {
      SUI.select.initIcon(select);
    } else if ('color' === select.data('theme')) {
      SUI.select.initColor(select);
    } else if ('search' === select.data('theme')) {
      SUI.select.initSearch(select);
    } else {
      SUI.select.init(select);
    }
  });
  $('.sui-variables').each(function () {
    var select = $(this);
    SUI.select.initVars(select);
  });
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode
  'use strict'; // Define global SUI object if it doesn't exist

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.sideTabs = function (element) {
    var $this = $(element),
        $label = $this.parent('label'),
        $data = $this.data('tab-menu'),
        $wrapper = $this.closest('.sui-side-tabs'),
        $alllabels = $wrapper.find('>.sui-tabs-menu .sui-tab-item'),
        $allinputs = $alllabels.find('input'),
        newContent;
    $this.on('click', function (e) {
      $alllabels.removeClass('active');
      $allinputs.attr('checked', false);
      $allinputs.attr('aria-selected', false);
      $wrapper.find('> .sui-tabs-content > div[data-tab-content]').removeClass('active');
      $label.addClass('active');
      $this.attr('checked', true);
      $this.attr('aria-selected', true);
      newContent = $wrapper.find('.sui-tabs-content div[data-tab-content="' + $data + '"]');

      if (newContent.length) {
        newContent.addClass('active');
      }
    });
  };

  $('.sui-2-12-13 .sui-side-tabs label.sui-tab-item input').each(function () {
    SUI.sideTabs(this);
  });
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.floatInput = function () {
    $('body').ready(function () {
      var $moduleName = $('.sui-sidenav .sui-with-floating-input'),
          $pageHeader = $('.sui-header-inline'),
          $pageTitle = $pageHeader.find('.sui-header-title');
      var $titleWidth = $pageTitle.width(),
          $navWidth = $pageHeader.next().find('.sui-sidenav').width();

      if ($titleWidth > $navWidth) {
        $moduleName.each(function () {
          $(this).css({
            'left': $titleWidth + 20 + 'px'
          });
        });
      }
    });
  };

  SUI.floatInput();
})(jQuery);;
(function ($) {
  // Enable strict mode.
  'use strict';

  var _$stickies = [].slice.call(document.querySelectorAll('.sui-box-sticky'));

  _$stickies.forEach(function (_$sticky) {
    if (CSS.supports && CSS.supports('position', 'sticky')) {
      if (null !== _$sticky.offsetParent) {
        apply_sticky_class(_$sticky);
      }

      window.addEventListener('scroll', function () {
        if (null !== _$sticky.offsetParent) {
          apply_sticky_class(_$sticky);
        }
      });
    }
  });

  function apply_sticky_class(_$sticky) {
    var currentOffset = _$sticky.getBoundingClientRect().top;

    var stickyOffset = parseInt(getComputedStyle(_$sticky).top.replace('px', ''));
    var isStuck = currentOffset <= stickyOffset;

    if (isStuck) {
      _$sticky.classList.add('sui-is-sticky');
    } else {
      _$sticky.classList.remove('sui-is-sticky');
    }
  }
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.suiTabs = function (config) {
    var data;
    var types = ['tab', 'pane'];
    var type;
    var groups = [];
    var activeGroups = [];
    var activeChildren = [];
    var activeItems = [];
    var indexGroup;
    var indexItem;
    var memory = [];

    function init(options) {
      var groupIndex;
      var tabItems;
      var itemIndex;
      var hashId;
      data = options;
      setDefaults();
      groups.tab = document.querySelectorAll(data.tabGroup);
      groups.pane = document.querySelectorAll(data.paneGroup);

      for (groupIndex = 0; groupIndex < groups.tab.length; groupIndex++) {
        tabItems = groups.tab[groupIndex].children;

        for (itemIndex = 0; itemIndex < tabItems.length; itemIndex++) {
          tabItems[itemIndex].addEventListener('click', onClick.bind(this, groupIndex, itemIndex), false);
          indexGroup = groupIndex;
          indexItem = itemIndex;

          if (window.location.hash) {
            hashId = window.location.hash.replace(/[^\w-_]/g, '');

            if (hashId === tabItems[itemIndex].id) {
              setNodes(groupIndex, itemIndex);
            }
          }
        }
      }
    }

    function onClick(groupIndex, itemIndex) {
      setNodes(groupIndex, itemIndex);
      setCallback();
    }

    function setNodes(groupIndex, itemIndex) {
      var i;
      indexGroup = groupIndex;
      indexItem = itemIndex;

      for (i = 0; i < types.length; i++) {
        type = types[i];
        setActiveGroup();
        setActiveChildren();
        setActiveItems();
        putActiveClass();
      }

      memory[groupIndex] = [];
      memory[groupIndex][itemIndex] = true;
    }

    function putActiveClass() {
      var i;

      for (i = 0; i < activeChildren[type].length; i++) {
        activeChildren[type][i].classList.remove(data[type + 'Active']);
      }

      activeItems[type].classList.add(data[type + 'Active']);
    }

    function setDefaults() {
      var i;

      for (i = 0; i < types.length; i++) {
        type = types[i];
        setOption(type + 'Group', '[data-' + type + 's]');
        setOption(type + 'Active', 'active');
      }
    }

    function setOption(key, value) {
      data = data || [];
      data[key] = data[key] || value;
    }

    function setActiveGroup() {
      activeGroups[type] = groups[type][indexGroup];
    }

    function setActiveChildren() {
      activeChildren[type] = activeGroups[type].children;
    }

    function setActiveItems() {
      activeItems[type] = activeChildren[type][indexItem];
    }

    function setCallback() {
      if ('function' === typeof data.callback) {
        data.callback(activeItems.tab, activeItems.pane);
      }
    }

    init(config);
    return;
  };

  SUI.tabsOverflow = function ($el) {
    var tabs = $el.closest('.sui-tabs').find('[data-tabs], [role="tablist"]'),
        leftButton = $el.find('.sui-tabs-navigation--left'),
        rightButton = $el.find('.sui-tabs-navigation--right');

    function overflowing() {
      if (tabs[0].scrollWidth > tabs.width()) {
        if (0 === tabs.scrollLeft()) {
          leftButton.addClass('sui-tabs-navigation--hidden');
        } else {
          leftButton.removeClass('sui-tabs-navigation--hidden');
        }

        reachedEnd(0);
        return true;
      } else {
        leftButton.addClass('sui-tabs-navigation--hidden');
        rightButton.addClass('sui-tabs-navigation--hidden');
        return false;
      }
    }

    overflowing();

    function reachedEnd(offset) {
      var newScrollLeft, width, scrollWidth;
      newScrollLeft = tabs.scrollLeft() + offset;
      width = tabs.outerWidth();
      scrollWidth = tabs.get(0).scrollWidth;

      if (scrollWidth - newScrollLeft <= width) {
        rightButton.addClass('sui-tabs-navigation--hidden');
      } else {
        rightButton.removeClass('sui-tabs-navigation--hidden');
      }
    }

    leftButton.on('click', function () {
      rightButton.removeClass('sui-tabs-navigation--hidden');

      if (0 >= tabs.scrollLeft() - 150) {
        leftButton.addClass('sui-tabs-navigation--hidden');
      }

      tabs.animate({
        scrollLeft: '-=150'
      }, 400, function () {});
      return false;
    });
    rightButton.on('click', function () {
      leftButton.removeClass('sui-tabs-navigation--hidden');
      reachedEnd(150);
      tabs.animate({
        scrollLeft: '+=150'
      }, 400, function () {});
      return false;
    });
    $(window).on('resize', function () {
      overflowing();
    });
    tabs.on('scroll', function () {
      overflowing();
    });
  };

  SUI.tabs = function (config) {
    var tablist = $('.sui-tabs > div[role="tablist"]');
    var data = config; // For easy reference.

    var keys = {
      end: 35,
      home: 36,
      left: 37,
      up: 38,
      right: 39,
      down: 40,
      "delete": 46,
      enter: 13,
      space: 32
    }; // Add or substract depending on key pressed.

    var direction = {
      37: -1,
      38: -1,
      39: 1,
      40: 1
    }; // Prevent function from running if tablist does not exist.

    if (!tablist.length) {
      return;
    } // Deactivate all tabs and tab panels.


    function deactivateTabs(tabs, panels, inputs) {
      tabs.removeClass('active');
      tabs.attr('tabindex', '-1');
      tabs.attr('aria-selected', false);
      inputs.prop('checked', false);
      panels.removeClass('active');
      panels.prop('hidden', true);
    } // Activate current tab panel.


    function activateTab(tab) {
      var tabs = $(tab).closest('[role="tablist"]').find('[role="tab"]'),
          inputs = $(tab).closest('[role="tablist"]').find('input[type="radio"]'),
          panels = $(tab).closest('.sui-tabs').find('> .sui-tabs-content > [role="tabpanel"]'),
          controls = $(tab).attr('aria-controls'),
          input = $(tab).next('input[type="radio"]'),
          panel = $('#' + controls);
      deactivateTabs(tabs, panels, inputs);
      $(tab).addClass('active');
      $(tab).removeAttr('tabindex');
      $(tab).attr('aria-selected', true);
      input.prop('checked', true);
      panel.addClass('active');
      panel.prop('hidden', false);
    } // When a "tablist" aria-orientation is set to vertical,
    // only up and down arrow should function.
    // In all other cases only left and right should function.


    function determineOrientation(event, index, tablist) {
      var key = event.keyCode || event.which,
          vertical = 'vertical' === $(tablist).attr('aria-orientation'),
          proceed = false; // Check if aria orientation is set to vertical.

      if (vertical) {
        if (keys.up === key || keys.down === key) {
          event.preventDefault();
          proceed = true;
        }
      } else {
        if (keys.left === key || keys.right === key) {
          proceed = true;
        }
      }

      if (true === proceed) {
        switchTabOnArrowPress(event, index);
      }
    } // Either focus the next, previous, first, or last tab
    // depending on key pressed.


    function switchTabOnArrowPress(event, index) {
      var pressed, target, tabs;
      pressed = event.keyCode || event.which;

      if (direction[pressed]) {
        target = event.target;
        tabs = $(target).closest('[role="tablist"]').find('> [role="tab"]');

        if (undefined !== index) {
          if (tabs[index + direction[pressed]]) {
            tabs[index + direction[pressed]].focus();
          } else if (keys.left === pressed || keys.up === pressed) {
            tabs[tabs.length - 1].focus();
          } else if (keys.right === pressed || keys.down === pressed) {
            tabs[0].focus();
          }
        }
      }
    } // Callback function.


    function setCallback(currentTab) {
      var tab = $(currentTab),
          controls = tab.attr('aria-controls'),
          panel = $('#' + controls);

      if ('function' === typeof data.callback) {
        data.callback(tab, panel);
      }
    } // When a tab is clicked, activateTab is fired to activate it.


    function clickEventListener(event) {
      var tab = event.target;
      activateTab(tab);

      if (undefined !== data && 'undefined' !== data) {
        setCallback(tab);
      }

      event.preventDefault();
      event.stopPropagation();
    }

    function keydownEventListener(event, index, tablist) {
      var key = event.keyCode || event.which;

      switch (key) {
        case keys.end:
        case keys.home:
          event.preventDefault();
          break;
        // Up and down are in keydown
        // because we need to prevent page scroll.

        case keys.up:
        case keys.down:
          determineOrientation(event, index, tablist);
          break;
      }
    }

    function keyupEventListener(event, index, tablist) {
      var key = event.keyCode || event.which;

      switch (key) {
        case keys.left:
        case keys.right:
          determineOrientation(event, index, tablist);
          break;

        case keys.enter:
        case keys.space:
          activateTab(event);
          break;
      }
    }

    function init() {
      var tabgroup = tablist.closest('.sui-tabs'); // Run the function for each group of tabs to prevent conflicts
      // when having child tabs.

      tabgroup.each(function () {
        var tabs, index;
        tabgroup = $(this);
        tablist = tabgroup.find('> [role="tablist"]');
        tabs = tablist.find('> [role="tab"]'); // Trigger events on click.

        tabs.on('click', function (e) {
          clickEventListener(e); // Trigger events when pressing key.
        }).on('keydown', function (e) {
          index = $(this).index();
          keydownEventListener(e, index, tablist); // Trigger events when releasing key.
        }).on('keyup', function (e) {
          index = $(this).index();
          keyupEventListener(e, index, tablist);
        });
      });
    }

    init();
    return this;
  };

  if (0 !== $('.sui-2-12-13 .sui-tabs').length) {
    // Support tabs new markup.
    SUI.tabs(); // Support legacy tabs.

    SUI.suiTabs();
    $('.sui-2-12-13 .sui-tabs-navigation').each(function () {
      SUI.tabsOverflow($(this));
    });
  }
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.treeOnLoad = function (element) {
    var tree = $(element),
        leaf = tree.find('li[role="treeitem"]'),
        branch = leaf.find('> ul[role="group"]'); // Hide sub-groups

    branch.slideUp();
    leaf.each(function () {
      var leaf = $(this),
          openLeaf = leaf.attr('aria-expanded'),
          checkLeaf = leaf.attr('aria-selected'),
          node = leaf.find('> .sui-tree-node'),
          checkbox = node.find('> .sui-node-checkbox'),
          button = node.find('> span[role="button"], > button'),
          icon = node.find('> span[aria-hidden]'),
          branch = leaf.find('> ul[role="group"]'),
          innerLeaf = branch.find('> li[role="treeitem"]'),
          innerCheck = innerLeaf.find('> .sui-tree-node > .sui-node-checkbox'); // FIX: Remove unnecessary elements for leafs

      if (('selector' === tree.data('tree') || 'selector' === tree.attr('data-tree')) && 0 !== icon.length) {
        button.remove();
      }

      if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) !== _typeof(openLeaf) && false !== openLeaf) {
        // Open sub-groups
        if ('true' === openLeaf) {
          branch.slideDown();
        }
      } else {
        if (0 !== branch.length) {
          leaf.attr('aria-expanded', 'false');
        } else {
          // FIX: Remove unnecessary elements for leafs
          if (0 !== button.length) {
            button.remove();
          }
        }
      }

      if ((typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) !== _typeof(checkLeaf) && false !== checkLeaf) {
        // Checked leafs
        if ('true' === checkLeaf && 0 < branch.length) {
          innerLeaf.attr('aria-selected', 'true');

          if (0 !== checkbox.length && checkbox.is('label')) {
            checkbox.find('input').prop('checked', true);
          }

          if (0 !== innerCheck.length && innerCheck.is('label')) {
            innerCheck.find('input').prop('checked', true);
          }
        }
      } else {
        // Unchecked leafs
        leaf.attr('aria-selected', 'false');

        if (0 !== checkbox.length && checkbox.is('label')) {
          checkbox.find('input').prop('checked', false);
        }
      }
    });
  };

  SUI.treeButton = function (element) {
    var button = $(element);
    button.on('click', function (e) {
      var button = $(this),
          leaf = button.closest('li[role="treeitem"]'),
          branch = leaf.find('> ul[role="group"]');

      if (0 !== branch.length) {
        branch.slideToggle(250);

        if ('true' === leaf.attr('aria-expanded')) {
          leaf.attr('aria-expanded', 'false');
        } else {
          leaf.attr('aria-expanded', 'true');
        }
      }

      e.preventDefault();
    });
  };

  SUI.treeCheckbox = function (element) {
    var checkbox = $(element);
    checkbox.on('click', function () {
      var checkbox = $(this),
          leaf = checkbox.closest('li[role="treeitem"]'),
          branches = leaf.find('ul[role="group"]'),
          leafs = branches.find('> li[role="treeitem"]'),
          checks = leafs.find('> .sui-tree-node > .sui-node-checkbox input'),
          topBranch = leaf.parent('ul'),
          topLeaf = topBranch.parent('li');
      var countIndex = 0,
          countTopBranches = topLeaf.parents('ul').length - 1;

      if ('true' === leaf.attr('aria-selected')) {
        // Unselect current leaf
        leaf.attr('aria-selected', 'false'); // Unselect current checkbox

        if (checkbox.is('input')) {
          checkbox.prop('checked', false);
        } // Unselect child leafs


        if (0 !== branches.length) {
          leafs.attr('aria-selected', 'false');
        } // Unselect child checkboxes


        if (0 !== checks.length) {
          checks.prop('checked', false);
        } // Unselect branch(es) when not all leafs are selected


        if (leaf.parent().is('ul') && 'group' === leaf.parent().attr('role')) {
          leaf.parents('ul').each(function () {
            var branch = $(this),
                leaf = branch.parent('li'),
                check = leaf.find('> .sui-tree-node > .sui-node-checkbox input');

            if ('treeitem' === leaf.attr('role')) {
              leaf.attr('aria-selected', 'false');

              if (0 !== check.length) {
                check.prop('checked', false);
              }
            }
          });
        }
      } else {
        // Select current leaf
        leaf.attr('aria-selected', 'true'); // Select current checkbox

        if (checkbox.is('input')) {
          checkbox.prop('checked', true);
        } // Select child leafs


        if (0 !== branches.length) {
          leafs.attr('aria-selected', 'true');
        } // Select child checkboxes


        if (0 !== checks.length) {
          checks.prop('checked', true);
        } // Select top branch(es) when all leafs are selected


        if (0 === topLeaf.find('li[aria-selected="false"]').length) {
          topLeaf.attr('aria-selected', 'true');

          for (countIndex = 0; countTopBranches >= countIndex; countIndex++) {
            topLeaf.parent('ul').eq(countIndex).each(function () {
              var branch = $(this),
                  leafFalse = branch.find('> li[aria-selected="false"]');

              if (0 === leafFalse.length) {
                branch.parent('li').attr('aria-selected', 'true');
                branch.parent('li').find('> .sui-tree-node > .sui-node-checkbox input').prop('checked', true);
              }
            });
          }
        }
      }
    });
  };

  SUI.treeForm = function (element) {
    var button = $(element);

    if ('add' === button.attr('data-button')) {
      button.on('click', function () {
        var button = $(this),
            leaf = button.closest('li[role="treeitem"]'),
            node = leaf.find('> .sui-tree-node'),
            expand = node.find('span[data-button="expander"]'),
            branch = leaf.find('> ul[role="group"]'),
            content = branch.find('> span[role="contentinfo"]');

        if (0 !== content.length) {
          // Hide button
          button.hide();
          button.removeAttr('tabindex');
          button.attr('aria-hidden', 'true'); // Show content

          content.addClass('sui-show');
          content.removeAttr('aria-hidden'); // FIX: Open tree if it's closed

          if ('true' !== leaf.attr('aria-expanded')) {
            expand.trigger('click');
          } // Focus content


          content.trigger('focus');
          content.attr('tabindex', '-1');
        }
      });
    }

    if ('remove' === button.attr('data-button')) {
      button.on('click', function () {
        var button = $(this),
            content = button.closest('span[role="contentinfo"]'),
            leaf = content.closest('li[role="treeitem"]'),
            node = leaf.find('> .sui-tree-node'),
            btnAdd = node.find('> span[data-button="add"]'); // Hide content

        content.removeClass('sui-show');
        content.removeAttr('tabindex');
        content.attr('aria-hidden', 'true'); // Show button

        btnAdd.show();
        btnAdd.removeAttr('aria-hidden');
        btnAdd.trigger('focus');
        btnAdd.attr('tabindex', '-1');
      });
    }
  };

  SUI.suiTree = function (element, dynamic) {
    var tree = $(element);

    if (!tree.hasClass('sui-tree') || (typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) === tree.attr('data-tree')) {
      return;
    }

    function button() {
      var leaf = tree.find('li[role="treeitem"]'),
          node = leaf.find('> .sui-tree-node'),
          button = node.find('> [data-button="expander"]'),
          label = node.find('> span.sui-node-text');
      button.each(function () {
        var button = $(this);
        SUI.treeButton(button);
      });
      label.each(function () {
        var label = $(this);
        SUI.treeButton(label);
      });
    }

    function checkbox() {
      var leaf = tree.find('li[role="treeitem"]'),
          node = leaf.find('> .sui-tree-node'),
          checkbox = node.find('> .sui-node-checkbox');
      checkbox.each(function () {
        var checkbox = $(this).is('label') ? $(this).find('input') : $(this);
        SUI.treeCheckbox(checkbox);
      });
    }

    function add() {
      var leaf = tree.find('li[role="treeitem"]'),
          node = leaf.find('> .sui-tree-node'),
          button = node.find('> [data-button="add"]');
      button.each(function () {
        var button = $(this);
        SUI.treeForm(button);
      });
    }

    function remove() {
      var button = tree.find('[data-button="remove"]');
      button.each(function () {
        var button = $(this);
        SUI.treeForm(button);
      });
    }

    function init() {
      if ('selector' === tree.data('tree') || 'directory' === tree.data('tree') || 'selector' === tree.attr('data-tree') || 'directory' === tree.atrr('data-tree')) {
        // Initial setup
        SUI.treeOnLoad(tree); // Expand action

        button(); // Select action

        checkbox(); // Add folder action

        if (true === dynamic || 'true' === dynamic) {
          add();
          remove();
        }
      } // TEST: Verify if input is checked on load
      // if ( 'selector' === tree.data( 'tree' ) ) {
      //
      // 	if ( 0 !== tree.find( 'input' ).length ) {
      //
      // 		tree.find( 'input' ).each( function() {
      //
      // 			console.log( '#' + $( this ).attr( 'id' ) + ': ' + $( this ).prop( 'checked' ) );
      //
      // 			// Output:
      // 			// #input-id: value
      //
      // 		});
      // 	}
      // }

    }

    init();
    return this;
  };

  if (0 !== $('.sui-2-12-13 .sui-tree').length) {
    $('.sui-2-12-13 .sui-tree').each(function () {
      SUI.suiTree($(this), true);
    });
  }
})(jQuery);;
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

(function ($) {
  // Enable strict mode.
  'use strict'; // Define global SUI object if it doesn't exist.

  if ('object' !== _typeof(window.SUI)) {
    window.SUI = {};
  }

  SUI.upload = function () {
    $('.sui-2-12-13 .sui-upload-group input[type="file"]').on('change', function (e) {
      var file = $(this)[0].files[0],
          message = $(this).find('~ .sui-upload-message');

      if (file) {
        message.text(file.name);
      }
    }); // check whether element exist then execute js

    if ($('.sui-2-12-13 .sui-file-upload').length) {
      // This will trigger on file change. 
      $('.sui-2-12-13 .sui-file-browser input[type="file"]').on('change', function () {
        var parent = $(this).parent();
        var filename = $(this).val();
        var imageContainer = parent.find('.sui-upload-image');

        if (filename) {
          var lastIndex = filename.lastIndexOf("\\");

          if (lastIndex >= 0) {
            filename = filename.substring(lastIndex + 1); // To show uploaded file preview.

            if (imageContainer.length) {
              var reader = new FileReader();
              var imagePreview = imageContainer.find('.sui-image-preview');

              reader.onload = function (e) {
                imagePreview.attr('style', 'background-image: url(' + e.target.result + ' );');
              };

              reader.readAsDataURL($(this)[0].files[0]);
            }

            parent.find('.sui-upload-file > span').text(filename);
            parent.addClass('sui-has_file');
          }
        } else {
          if (imageContainer.length) {
            var imagePreview = imageContainer.find('.sui-image-preview');
            imagePreview.attr('style', 'background-image: url();');
          }

          parent.find('.sui-upload-file > span').text('');
          parent.removeClass('sui-has_file');
        }
      }); // This will trigger on click of upload button

      $('.sui-2-12-13 .sui-file-browser .sui-upload-button').on('click', function () {
        selectFile($(this));
      }); // This will trigger when user wants to remove the selected upload file

      $('.sui-2-12-13 .sui-file-upload [aria-label="Remove file"]').on('click', function () {
        removeFile($(this));
      }); // This will trigger reupload of file

      $('.sui-2-12-13 .sui-file-browser .sui-upload-image').on('click', function () {
        selectFile($(this));
      }); // upload drag and drop functionality

      var isAdvancedUpload = function () {
        var div = document.createElement('div');
        return ('draggable' in div || 'ondragstart' in div && 'ondrop' in div) && 'FormData' in window && 'FileReader' in window;
      }();

      var uploadArea = $('.sui-2-12-13 .sui-upload-button');

      if (isAdvancedUpload) {
        var droppedFiles = false;
        uploadArea.on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
          e.preventDefault();
          e.stopPropagation();
        }).on('dragover dragenter', function () {
          uploadArea.addClass('sui-is-dragover');
        }).on('dragleave dragend drop', function () {
          uploadArea.removeClass('sui-is-dragover');
        }).on('drop', function (e) {
          droppedFiles = e.originalEvent.dataTransfer.files;
          uploadedFile($(this), droppedFiles[0], droppedFiles[0].name);
        });
      } // function to set uploaded file


      var uploadedFile = function uploadedFile(element, file, filename) {
        var parent = element.closest('.sui-upload');
        var imageContainer = parent.find('.sui-upload-image');

        if (filename) {
          if (imageContainer.length) {
            var reader = new FileReader();
            var imagePreview = imageContainer.find('.sui-image-preview');

            reader.onload = function (e) {
              imagePreview.attr('style', 'background-image: url(' + e.target.result + ' );');
            };

            reader.readAsDataURL(file);
          }

          parent.find('.sui-upload-file > span').text(filename);
          parent.addClass('sui-has_file');
        }
      }; // function to open browser file explorer for selecting file


      var selectFile = function selectFile(element) {
        var parent = element.closest('.sui-upload');
        var file = parent.find('input[type="file"]');
        file.trigger('click');
      }; // function to remove file


      var removeFile = function removeFile(element) {
        var parent = element.closest('.sui-upload');
        var file = parent.find('input[type="file"]');
        file.val('').change();
      };
    }
  };

  SUI.upload();
})(jQuery);