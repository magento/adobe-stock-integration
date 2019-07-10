/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            visibility: []
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
         * Returns visibility for given record.
         *
         * @param {Object} record
         * @return {*|boolean}
         */
        isVisible (record) {
            return this.visibility()[record._rowIndex] || false;
        },

        /**
         * Show image preview
         *
         * @param {Number} rowIndex
         */
        show: function (rowIndex) {
            var visibility = this.visibility();
            visibility[rowIndex] = true;
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
        }
    });
});
