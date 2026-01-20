/**
 * Modal Plugin for BulmaVar
 * Handles modal display, content management, and user interactions
 * 
 * @requires BulmaVar Core (bulma-var.js)
 * @requires jQuery
 */

(function($) {
  'use strict';

  /**
   * Modal Plugin Class
   */
  class Modal {
    /**
     * Initialize modal component
     * @param {jQuery} $modal - jQuery object containing the modal element
     * @param {Object} options - Configuration options
     */
    constructor($modal, options = {}) {
      this.$modal = $modal;
      this.options = $.extend({
        closeOnBackground: true,  // Close modal when clicking on background
        closeOnEscape: true,      // Close modal when pressing ESC key
        onOpen: null,             // Callback function when modal opens
        onClose: null,            // Callback function when modal closes
        onConfirm: null,          // Callback function when confirm button is clicked
        animationSpeed: 200       // Animation speed in ms
      }, options);

      Logger.debug('BulmaVar.Modal: Initializing modal component');
      
      if (this.$modal.length === 0) {
        Logger.debug('BulmaVar.Modal: Modal element not found');
        return;
      }

      this.isOpen = false;
      this.$background = this.$modal.find('.modal-background');
      this.$closeButtons = this.$modal.find('.modal-card-head .delete, .modal-message-close');
      this.$cancelButton = this.$modal.find('#modal-message-button-cancel');
      
      this.init();
    }

    /**
     * Initialize the modal functionality
     */
    init() {
      // Attach close event on background click
      if (this.options.closeOnBackground) {
        this.$background.on('click', () => {
          Logger.debug('BulmaVar.Modal: Background clicked');
          this.close();
        });
      }

      // Attach close event on close buttons
      this.$closeButtons.on('click', (e) => {
        e.preventDefault();
        Logger.debug('BulmaVar.Modal: Close button clicked');
        this.close();
      });

      // Attach close event on cancel button
      this.$cancelButton.on('click', (e) => {
        e.preventDefault();
        Logger.debug('BulmaVar.Modal: Cancel button clicked');
        this.close();
      });

      // Attach ESC key event
      if (this.options.closeOnEscape) {
        $(document).on('keydown.bulmavar-modal-' + this.$modal.attr('id'), (e) => {
          if (e.key === 'Escape' && this.isOpen) {
            Logger.debug('BulmaVar.Modal: ESC key pressed');
            this.close();
          }
        });
      }

      Logger.debug('BulmaVar.Modal: Initialization complete');
    }

    /**
     * Open the modal
     * @param {Object} content - Optional content to set before opening
     *   {string} content.title - Modal title
     *   {string|jQuery} content.body - Modal body content (HTML string or jQuery object)
     *   {Array} content.buttons - Array of button configurations
     */
    open(content = null) {
      Logger.debug('BulmaVar.Modal: Opening modal');

      // Set content if provided
      if (content) {
        this.setContent(content);
      }

      // Add is-active class to display modal
      this.$modal.addClass('is-active');
      this.isOpen = true;

      // Prevent body scrolling
      $('html').addClass('is-clipped');

      // Trigger custom event
      this.$modal.trigger('bulmavar:modalOpened');

      // Execute callback if provided
      if (typeof this.options.onOpen === 'function') {
        Logger.debug('BulmaVar.Modal: Executing onOpen callback');
        this.options.onOpen.call(this);
      }

      Logger.debug('BulmaVar.Modal: Modal opened');
    }

    /**
     * Close the modal
     */
    close() {
      Logger.debug('BulmaVar.Modal: Closing modal');

      // Remove is-active class
      this.$modal.removeClass('is-active');
      this.isOpen = false;

      // Restore body scrolling
      $('html').removeClass('is-clipped');

      // Trigger custom event
      this.$modal.trigger('bulmavar:modalClosed');

      // Execute callback if provided
      if (typeof this.options.onClose === 'function') {
        Logger.debug('BulmaVar.Modal: Executing onClose callback');
        this.options.onClose.call(this);
      }

      Logger.debug('BulmaVar.Modal: Modal closed');
    }

    /**
     * Toggle modal open/close state
     */
    toggle() {
      if (this.isOpen) {
        this.close();
      } else {
        this.open();
      }
    }

    /**
     * Set modal content dynamically
     * @param {Object} content - Content configuration
     *   {string} content.title - Modal title
     *   {string|jQuery} content.body - Modal body content
     *   {Array} content.buttons - Array of button configurations
     *     Each button: {text: 'Button', class: 'is-primary', onClick: function}
     */
    setContent(content) {
      Logger.debug('BulmaVar.Modal: Setting content');

      // Set title
      if (content.title !== undefined) {
        this.$modal.find('#modal-message-title').html(content.title);
      }

      // Set body content
      if (content.body !== undefined) {
        const $body = this.$modal.find('#modal-message-body');
        if (typeof content.body === 'string') {
          $body.html(content.body);
        } else if (content.body instanceof jQuery) {
          $body.empty().append(content.body);
        }
      }

      // Set buttons
      if (content.buttons !== undefined && Array.isArray(content.buttons)) {
        const $buttonsContainer = this.$modal.find('#modal-message-buttons');
        $buttonsContainer.empty();

        content.buttons.forEach((btn) => {
          const $button = $('<button>')
            .addClass('button')
            .addClass(btn.class || '')
            .text(btn.text || 'Button');

          if (typeof btn.onClick === 'function') {
            $button.on('click', (e) => {
              e.preventDefault();
              btn.onClick.call(this, e);
            });
          }

          $buttonsContainer.append($button);
        });

        Logger.debug(`BulmaVar.Modal: Added ${content.buttons.length} buttons`);
      }
    }

    /**
     * Get current modal state
     * @returns {boolean} True if modal is open
     */
    isModalOpen() {
      return this.isOpen;
    }

    /**
     * Show a confirmation modal with custom buttons
     * @param {string} title - Modal title
     * @param {string} message - Modal message
     * @param {Function} onConfirm - Callback when confirmed
     * @param {Function} onCancel - Callback when cancelled (optional)
     */
    confirm(title, message, onConfirm, onCancel = null) {
      Logger.debug('BulmaVar.Modal: Showing confirmation dialog');

      this.setContent({
        title: title,
        body: `<p>${message}</p>`,
        buttons: [
          {
            text: 'Annuler',
            class: '',
            onClick: () => {
              this.close();
              if (typeof onCancel === 'function') {
                onCancel.call(this);
              }
            }
          },
          {
            text: 'Confirmer',
            class: 'is-primary',
            onClick: () => {
              if (typeof onConfirm === 'function') {
                onConfirm.call(this);
              }
              this.close();
            }
          }
        ]
      });

      this.open();
    }

    /**
     * Show an alert modal with single OK button
     * @param {string} title - Modal title
     * @param {string} message - Modal message
     * @param {Function} onClose - Callback when closed (optional)
     */
    alert(title, message, onClose = null) {
      Logger.debug('BulmaVar.Modal: Showing alert dialog');

      this.setContent({
        title: title,
        body: `<p>${message}</p>`,
        buttons: [
          {
            text: 'OK',
            class: 'is-primary',
            onClick: () => {
              this.close();
              if (typeof onClose === 'function') {
                onClose.call(this);
              }
            }
          }
        ]
      });

      this.open();
    }

    /**
     * Clear modal content (title, body, and buttons except cancel)
     * @param {boolean} keepCancel - Keep the cancel button (default: true)
     */
    clear(keepCancel = true) {
      Logger.debug('BulmaVar.Modal: Clearing modal content');

      // Clear title
      this.$modal.find('#modal-message-title').empty();

      // Clear body
      this.$modal.find('#modal-message-body').empty();

      // Clear buttons (except cancel if keepCancel is true)
      if (keepCancel) {
        this.$modal.find('#modal-message-buttons button:not(#modal-message-button-cancel)').remove();
        Logger.debug('BulmaVar.Modal: Content cleared, cancel button kept');
      } else {
        this.$modal.find('#modal-message-buttons').empty();
        Logger.debug('BulmaVar.Modal: All content cleared including cancel button');
      }
    }

    /**
     * Destroy the modal instance
     */
    destroy() {
      Logger.debug('BulmaVar.Modal: Destroying modal instance');
      
      // Remove event listeners
      this.$background.off('click');
      this.$closeButtons.off('click');
      this.$cancelButton.off('click');
      $(document).off('keydown.bulmavar-modal-' + this.$modal.attr('id'));

      // Close if open
      if (this.isOpen) {
        this.close();
      }
    }
  }

  // Register the Modal plugin with BulmaVar
  if (typeof BulmaVar !== 'undefined') {
    BulmaVar.registerPlugin('Modal', Modal);
    Logger.debug('BulmaVar.Modal: Plugin registered');
    console.log('BulmaVar.Modal plugin loaded');
  } else {
    console.error('BulmaVar.Modal: BulmaVar core not found. Load bulma-var.js first.');
  }

})(jQuery);

/**
 * USAGE INSTRUCTIONS:
 * 
 * 1. HTML Structure (add data-bulma="modal" for consistency):
 *    <div id="modal-message" class="modal" data-bulma="modal">
 *      <div class="modal-background"></div>
 *      <div class="modal-card">
 *        <header class="modal-card-head">
 *          <p class="modal-card-title" id="modal-message-title"></p>
 *          <button class="delete modal-message-close"></button>
 *        </header>
 *        <section id="modal-message-body" class="modal-card-body"></section>
 *        <footer class="modal-card-foot">
 *          <div id="modal-message-buttons" class="buttons">
 *            <button id="modal-message-button-cancel" class="button">Cancel</button>
 *          </div>
 *        </footer>
 *      </div>
 *    </div>
 * 
 * 2. Initialize:
 *    $('#modal-message').bulmaVar('Modal', 'init', {
 *      closeOnBackground: true,
 *      closeOnEscape: true,
 *      onOpen: function() { console.log('Modal opened!'); },
 *      onClose: function() { console.log('Modal closed!'); }
 *    });
 * 
 * 3. Open modal with content:
 *    $('#modal-message').bulmaVar('Modal', 'open', {
 *      title: 'My Title',
 *      body: '<p>Modal content here</p>',
 *      buttons: [
 *        { text: 'Cancel', class: '', onClick: function() { this.close(); } },
 *        { text: 'Save', class: 'is-primary', onClick: function() { console.log('Saved!'); this.close(); } }
 *      ]
 *    });
 * 
 * 4. Simple open/close:
 *    $('#modal-message').bulmaVar('Modal', 'open');
 *    $('#modal-message').bulmaVar('Modal', 'close');
 *    $('#modal-message').bulmaVar('Modal', 'toggle');
 * 
 * 5. Quick confirmation dialog:
 *    $('#modal-message').bulmaVar('Modal', 'confirm', 
 *      'Delete Item?',
 *      'Are you sure you want to delete this item?',
 *      function() { console.log('Confirmed!'); },
 *      function() { console.log('Cancelled!'); }
 *    );
 * 
 * 6. Quick alert dialog:
 *    $('#modal-message').bulmaVar('Modal', 'alert',
 *      'Success',
 *      'Your changes have been saved!',
 *      function() { console.log('Alert closed'); }
 *    );
 * 
 * 7. Listen to events:
 *    $('#modal-message').on('bulmavar:modalOpened', function() {
 *      console.log('Modal opened event');
 *    });
 *    $('#modal-message').on('bulmavar:modalClosed', function() {
 *      console.log('Modal closed event');
 *    });
 */