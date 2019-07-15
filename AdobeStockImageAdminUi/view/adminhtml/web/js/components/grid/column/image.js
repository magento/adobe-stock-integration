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
            modules: {
                previewComponent: '${ $.parentName }.preview'
            },
            selectedRowId: null,
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'selectedRowId'
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
            return record.thumbnail_url;
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
            var styles = record.styles();

            // TODO: replace hardcoded value with preview container height
            styles['margin-bottom'] = this.selectedRowId() === record.currentRow ? '400px' : 0;
            record.styles(styles);

            if (record.styles()) {
                return record.styles;
            }
            return {};
        },

        /**
         * Returns class list to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Object}
         */
        getClasses: function (record) {
            if (record.css()) {
                return record.css;
            }
            return {};
        },

        /**
         * Expand image preview
         */
        expandPreview: function (record) {
            this.selectedRowId(record.currentRow);
            this.previewComponent().show(record);
        }
    });
});
