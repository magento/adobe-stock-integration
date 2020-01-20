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
            bodyTmpl: 'Magento_MediaGalleryUi/grid/columns/image',
            selected: null,
            fields: {
                id: 'id',
                url: 'url'
            }
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'selected'
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
            return record[this.fields.url];
        },

        /**
         * Returns id to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Number}
         */
        getId: function (record) {
            return record[this.fields.id];
        },

        /**
         * Returns class list to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Object}
         */
        isSelected: function (record) {
            return this.selected() === this.getId(record);
        },

        /**
         * Select image
         */
        select: function (record) {
            (this.isSelected(record)) ? this.selected(null) : this.selected(this.getId(record));
        }
    });
});
