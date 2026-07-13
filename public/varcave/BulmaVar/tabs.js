/**
 * Tabs Plugin for BulmaVar
 * Handles tab navigation and content switching
 * 
 * @requires BulmaVar Core (bulma-var.js)
 * @requires jQuery
 */

(function($) {
  'use strict';

  /**
   * Tabs Plugin Class
   */
  class Tabs {
    /**
     * Initialize tabs component
     * @param {jQuery} $container - jQuery object containing the tabs container
     * @param {string|null} initialTab - ID of the tab to display initially (without #)
     * @param {Object} options - Configuration options
     */
    constructor($container, initialTab = null, options = {}) {
      this.$container = $container;
      this.initialTab = initialTab;
      this.options = $.extend({
        onInit: null,        // Callback function after initialization
        onTabChange: null,   // Callback function when tab changes
        animationSpeed: 1000  // Fade animation speed in ms
      }, options);

      Logger.debug('BulmaVar.Tabs: Initializing tabs component');
      
      if (this.$container.length === 0) {
        Logger.debug('BulmaVar.Tabs: Container not found');
        return;
      }

      this.$tabLinks = this.$container.find('[data-tabs-target]');
      this.$tabContents = $();
      
      Logger.debug(`BulmaVar.Tabs: Found ${this.$tabLinks.length} tab links`);
      
      this.init();
    }

    /**
     * Initialize the tabs functionality
     */
    init() {
      // Collect all tab contents based on data-tab-target
      this.$tabLinks.each((index, link) => {
        const targetId = $(link).data('tabs-target');
        if (targetId) {
          const $content = $('#' + targetId);
          if ($content.length > 0) {
            this.$tabContents = this.$tabContents.add($content);
            Logger.debug(`BulmaVar.Tabs: Linked tab "${targetId}" to content`);
          } else {
            Logger.debug(`BulmaVar.Tabs: Warning - Content not found for tab "${targetId}"`);
          }
        }
      });

      // Attach click event handlers
      this.$tabLinks.on('click', (e) => {
        e.preventDefault();
        const $clickedLink = $(e.currentTarget);
        const targetId = $clickedLink.data('tabs-target');
        Logger.debug(`BulmaVar.Tabs: Tab clicked - "${targetId}"`);
        this.switchTab($clickedLink);
      });

      const hash = window.location.hash.replace('#tab=', '');
      Logger.debug('hash target: '+hash);
      if (hash) {
          this.goToTabById( hash );
          return //prevent code exec, #tab has precedence
      }

      $(window).on('hashchange', () =>{
        this.openTabFromHash();
      });

      // Determine which tab to activate initially
      if (this.initialTab) {
        // Activate specified tab
        const $initialLink = this.$tabLinks.filter(`[data-tabs-target="${this.initialTab}"]`);
        if ($initialLink.length > 0) {
          Logger.debug(`BulmaVar.Tabs: Activating initial tab "${this.initialTab}"`);
          this.switchTab($initialLink, true);
        } else {
          Logger.debug(`BulmaVar.Tabs: Initial tab "${this.initialTab}" not found, using first tab`);
          this.switchTab(this.$tabLinks.first(), true);
        }
      } else {
        // Check if there's already an active tab
        const $activeTab = this.$container.find('li.is-active [data-tabs-target]');
        if ($activeTab.length > 0) {
          Logger.debug('BulmaVar.Tabs: Found existing active tab');
          this.switchTab($activeTab, true);
        } else if (this.$tabLinks.length > 0) {
          Logger.debug('BulmaVar.Tabs: No active tab found, activating first tab');
          this.switchTab(this.$tabLinks.first(), true);
        }
      }

      // Execute initialization callback if provided
      if (typeof this.options.onInit === 'function') {
        Logger.debug('BulmaVar.Tabs: Executing onInit callback');
        this.options.onInit.call(this);
      }

      Logger.debug('BulmaVar.Tabs: Initialization complete');
    }

    /**
     * Switch to a specific tab
     * @param {jQuery} $selectedLink - The tab link to activate
     * @param {boolean} skipAnimation - Skip fade animation
     */
    switchTab($selectedLink, skipAnimation = false, animation = 'fade') {
      const targetId = $selectedLink.data('tabs-target');
      
      // Prevent re-clicking the same active tab
      if ($selectedLink.parent().hasClass('is-active')) {
        Logger.debug(`BulmaVar.Tabs: Tab "${targetId}" is already active, ignoring click`);
        return;
      }

      // Remove active class from all tabs
      this.$tabLinks.parent().removeClass('is-active');

      // Hide all tab contents
      if (skipAnimation) {
        this.$tabContents.hide();
      } else {
        this.$tabContents.hide();
        //this.$tabContents.fadeOut(this.options.animationSpeed);
      }

      // Activate selected tab
      $selectedLink.parent().addClass('is-active');

      // Show corresponding content
      const $targetContent = $('#' + targetId);
      if ($targetContent.length > 0) {
        if (skipAnimation) {
          $targetContent.show();
        } else {
          switch(animation) {
            case 'slide':
              $targetContent.show('slide', { direction: 'right' }, this.options.animationSpeed);
              break;
            
            case 'blind':
              $targetContent.show('blind', {}, this.options.animationSpeed);
              break;
            
            case 'drop':
              $targetContent.show('drop', { direction: 'down' }, this.options.animationSpeed);
              break;
            
            case 'clip':
              $targetContent.show('clip', {}, this.options.animationSpeed);
              break;
            
            case 'fade':
            case 'fadeOut':
            default:
              $targetContent.fadeIn(this.options.animationSpeed);
              break;
          }
        }
      }

      // Trigger custom event
      this.$container.trigger('bulmavar:tabChanged', {
        targetId: targetId,
        $link: $selectedLink,
        $content: $targetContent
      });

      Logger.debug(`BulmaVar.Tabs: Switched to tab "${targetId}"`);

      // Execute tab change callback if provided
      if (typeof this.options.onTabChange === 'function') {
        this.options.onTabChange.call(this, targetId, $targetContent);
      }
    }

    /**
     * Programmatically switch to a tab by index
     * @param {number} index - Zero-based index of the tab
     */
    goToTab(index) {
      const $link = this.$tabLinks.eq(index);
      if ($link.length > 0) {
        Logger.debug(`BulmaVar.Tabs: Going to tab index ${index}`);
        this.switchTab($link);
      } else {
        Logger.debug(`BulmaVar.Tabs: Tab index ${index} not found`);
      }
    }

    /**
     * Programmatically switch to a tab by ID
     * @param {string} targetId - The ID of the tab content (without #)
     */
    goToTabById(targetId) {
      const $link = this.$tabLinks.filter(`[data-tabs-target="${targetId}"]`);
      if ($link.length > 0) {
        Logger.debug(`BulmaVar.Tabs: Going to tab "${targetId}"`);
        this.switchTab($link);
      } else {
        Logger.debug(`BulmaVar.Tabs: Tab "${targetId}" not found`);
      }
    }

    /**
     * Get the currently active tab
     * @returns {jQuery} The active tab link
     */
    getActiveTab() {
      return this.$container.find('li.is-active [data-tabs-target]');
    }

    /**
     * Destroy the tabs instance
     */
    destroy() {
      Logger.debug('BulmaVar.Tabs: Destroying tabs instance');
      this.$tabLinks.off('click');
      this.$tabContents.show();
      this.$tabLinks.parent().removeClass('is-active');
    }

    /**
     * Change tab on hashchange the tab name (html id) must be preceded by #tab=
     * ie=http://url/page#tab=target-tab
     */
    openTabFromHash()
    {
      console.log('hash tab change request');
      const hash = window.location.hash;

      if (!hash.startsWith('#tab=')) {
        console.log('target tab id not found: ' + hash)
        return;
      }

      const tabId = hash.substring(5); // remove "#tab="

      console.log('target tabId='+tabId)
      //id without #
      this.goToTabById( tabId );
    }
       
  }

  // Register the Tabs plugin with BulmaVar
  if (typeof BulmaVar !== 'undefined') {
    BulmaVar.registerPlugin('Tabs', Tabs);
    Logger.debug('BulmaVar.Tabs: Plugin registered');
    console.log('BulmaVar.Tabs plugin loaded');
  } else {
    console.error('BulmaVar.Tabs: BulmaVar core not found. Load bulma-var.js first.');
  }

 
  

})(jQuery);