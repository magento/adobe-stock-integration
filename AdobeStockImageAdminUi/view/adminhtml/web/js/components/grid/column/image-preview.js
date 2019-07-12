/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column',
    'underscore'
], function (Column, _) {
    'use strict';

    return Column.extend({
        defaults: {
            visibility: [],
            saveAvailable: true,
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'visibility'
                ]);

            return this;
        },

        /**
         * Returns url to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getUrl: function (record) {
            return record.preview_url;
        },

        /**
         * Returns title to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getTitle: function (record) {
            return record.title || 'Title';
        },

        /**
         * Returns author full name to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getAuthor: function (record) {
            return record.author || 'Author';
        },

        /**
         * Returns visibility for given record.
         *
         * @param {Object} record
         * @return {*|boolean}
         */
        isVisible (record) {
            return this.visibility()[record._rowIndex] || false;
        },

        /**
         * Next image preview
         *
         * @param record
         */
        next: function(record){
            this.show(record._rowIndex + 1);
        },

        /**
         * Previous image preview
         *
         * @param record
         */
        prev: function(record){
            this.show(record._rowIndex - 1);
        },

        /**
         * Show image preview
         *
         * @param {Object} record
         */
        show: function (record) {
            var visibility = this.visibility();
            if(~visibility.indexOf(true)) {// some other element is visible
                if(!Array.prototype.fill) {
                    visibility = _.times(visibility.length, _.constant(false));
                } else {
                    visibility.fill(false);
                }
            }
            var newIndex = this._isInt(record) ? record: record._rowIndex;
            visibility[newIndex] = true;
            this.visibility(visibility);
        },

        /**
         * Close image preview
         *
         * @param {Object} record
         */
        close: function (record) {
            var visibility = this.visibility();
            visibility[record._rowIndex] = false;
            this.visibility(visibility);
        },

        _isInt: function(value) {
            return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
        }
    });
});
