/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        /**
         * Returns url to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getUrl: function (record) {
            return record.url;
        },

        /**
         * Returns id to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Number}
         */
        getId: function (record) {
            return record.id;
        },

        /**
         * Returns container styles to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Object}
         */
        getStyles: function (record) {
            if (record.styles) {
                return record.styles;
            }
            return {};
        }
    });
});
