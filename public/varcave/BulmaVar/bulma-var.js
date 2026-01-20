/**
 * BulmaVar.js - Core library
 * A modular jQuery plugin library for Bulma CSS framework
 * 
 * @version 1.0.0
 * @requires jQuery
 */

(function($) {
  'use strict';

  /**
   * BulmaVar - Main namespace and plugin manager
   */
  window.BulmaVar = {
    plugins: {},
    version: '1.0.0',

    /**
     * Register a new plugin
     * @param {string} name - Plugin name (e.g., 'Tabs', 'Modal')
     * @param {Function} pluginClass - Plugin class constructor
     */
    registerPlugin: function(name, pluginClass) {
      if (this.plugins[name]) {
        Logger.debug(`BulmaVar: Plugin "${name}" is already registered, overwriting`);
      }
      this.plugins[name] = pluginClass;
      Logger.debug(`BulmaVar: Plugin "${name}" registered successfully`);
    },

    /**
     * Get a registered plugin
     * @param {string} name - Plugin name
     * @returns {Function|null} Plugin class or null if not found
     */
    getPlugin: function(name) {
      return this.plugins[name] || null;
    }
  };

  /**
   * jQuery plugin interface
   * Usage: $('[data-selector]').bulmaVar('PluginName', 'method', ...args)
   */
  $.fn.bulmaVar = function(pluginName, method, ...args) {
    return this.each(function() {
      const $element = $(this);
      
      // Check if plugin exists
      const PluginClass = BulmaVar.getPlugin(pluginName);
      if (!PluginClass) {
        Logger.debug(`BulmaVar: Plugin "${pluginName}" not found or not loaded`);
        console.error(`BulmaVar: Plugin "${pluginName}" not found. Make sure the plugin file is loaded.`);
        return;
      }

      // Get or create plugin instance
      const dataKey = 'bulmavar-' + pluginName.toLowerCase();
      let instance = $element.data(dataKey);
      
      if (method === 'init') {
        // Initialize new instance
        if (instance) {
          Logger.debug(`BulmaVar.${pluginName}: Instance already exists, destroying old one`);
          if (typeof instance.destroy === 'function') {
            instance.destroy();
          }
        }
        
        instance = new PluginClass($element, ...args);
        $element.data(dataKey, instance);
      } else if (instance && typeof instance[method] === 'function') {
        // Call method on existing instance
        return instance[method](...args);
      } else if (!instance) {
        Logger.debug(`BulmaVar.${pluginName}: Instance not initialized. Call init() first`);
        console.error(`BulmaVar.${pluginName}: Instance not initialized. Call .bulmaVar('${pluginName}', 'init') first`);
      } else {
        Logger.debug(`BulmaVar.${pluginName}: Method "${method}" not found`);
        console.error(`BulmaVar.${pluginName}: Method "${method}" not found`);
      }
    });
  };

  Logger.debug('BulmaVar: Core library loaded successfully');
  console.log('BulmaVar v' + BulmaVar.version + ' loaded');

})(jQuery);