/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column',
    'uiLayout'
], function (Column, layout) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magento_MediaGalleryUi/grid/columns/image',
            deleteImageUrl: 'media_gallery/image/delete',
            selected: null,
            fields: {
                id: 'id',
                url: 'url'
            },
            modules: {
                actions: '${ $.name }_actions'
            },
            viewConfig: [
                {
                    component: 'Magento_MediaGalleryUi/js/grid/columns/image/actions',
                    name: '${ $.name }_actions',
                    providerName: '${ $.provider }',
                    imageModelName: '${ $.name }',
                    messagesName: '${ $.messagesName }'
                }
            ]
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super();
            this.initView();

            return this;
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
         * Check if the record is currently selected
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Object}
         */
        isSelected: function (record) {
            return this.selected() === this.getId(record);
        },

        /**
         * Set the record as selected
         */
        select: function (record) {
            this.isSelected(record) ? this.selected(null) : this.selected(this.getId(record));
        },

        /**
         * Initialize child components
         *
         * @returns {Object}
         */
        initView: function () {
            layout(this.viewConfig);
        }
    });
});
