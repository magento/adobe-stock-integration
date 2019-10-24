/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column',
    'ko'
], function (Column, ko) {
    'use strict';

    return Column.extend({

        /**
         * Returns container styles to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Object}
         */
        getStyles: function (record) {
            var styles, overlayStyles, marginTop, marginLeft;
            if (!record.overlayStyles) {
                record.overlayStyles = ko.observable([]);
            }
            if (record.styles) {
                styles = record.styles();
                overlayStyles = record.overlayStyles();
                marginTop =  styles.height.split('px').map(function(pixel){
                    if (pixel) {
                        return pixel - 50;
                    }
                }).join('px');
                marginLeft =  styles.width.split('px').map(function(pixel){
                    if (pixel) {
                        return pixel - 80;
                    }
                }).join('px');
                overlayStyles['margin-top'] = marginTop;
                overlayStyles['margin-left'] = marginLeft;
                return overlayStyles;
            }

            return {};
        },
        /**
         * If overlay should be visible
         *
         * @param {Object} row
         * @returns {bool}
         */
        isVisible: function (row) {
            return !!row[this.index];
        },

        /**
         * Get overlay label
         *
         * @param {Object} row
         * @returns {String}
         */
        getLabel: function (row) {
            return row[this.index];
        }
    })
});
