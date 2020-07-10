/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/ui-select',
    'jquery'
], function (uiSelect, $) {
    'use strict';

    return uiSelect.extend({
        /**
         * Get path to current option
         *
         * @param {Object} data - option data
         * @returns {String} path
         */
        getPath: function (data) {
            return data.path;
        },

        /**
         * Returns options from cache or send request
         *
         * @param {String} searchKey
         */
        loadOptions: function (searchKey) {
            var currentPage = searchKey === this.lastSearchKey ? this.lastSearchPage + 1 : 1,
                cachedSearchResult;

            this.renderPath = !!this.showPath;

            if (this.isSearchKeyCached(searchKey)) {
                cachedSearchResult = this.getCachedSearchResults(searchKey);
                this.cacheOptions.plain = cachedSearchResult.options;
                this.options(cachedSearchResult.options);
                this.afterLoadOptions(searchKey, cachedSearchResult.lastPage, cachedSearchResult.total);

                return;
            }

            this.processRequest(searchKey, currentPage);
        },

        /**
          * Remove element from selected array
          */
        removeSelected: function (value, data, event) {
            event ? event.stopPropagation() : false;
            this.value.remove(value);
            $.each(this.options(), function (key, option) {
                if (option.value === value) {
                    this.options.remove(option);
                    delete this.cachedSearchResults[value];
                }
            }.bind(this));
        }

    });
});
