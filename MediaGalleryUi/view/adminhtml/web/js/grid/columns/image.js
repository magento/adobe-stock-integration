/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Ui/js/grid/columns/column',
    'uiLayout'
], function ($, Column, layout) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magento_MediaGalleryUi/grid/columns/image',
            deleteImageUrl: 'media_gallery/image/delete',
            addSelectedBtnSelector: '#add_selected',
            deleteSelectedBtnSelector: '#delete_selected',
            targetElementId: null,
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
         * @returns {Boolean}
         */
        isSelected: function (record) {
            if (this.selected() === null) {
                return false;
            }

            return this.getId(this.selected()) === this.getId(record);
        },

        /**
         * Set the record as selected
         */
        select: function (record) {
            this.isSelected(record) ? this.selected(null) : this.selected(record);
            this.toggleAddSelectedButton();
        },

        /**
         * Get the selected record
         * @returns {Object}
         */
        getSelected: function () {
            return this.selected();
        },

        /**
         * Initialize child components
         *
         * @returns {Object}
         */
        initView: function () {
            layout(this.viewConfig);

            return this;
        },

        /**
         * Toggle add selected button
         */
        toggleAddSelectedButton: function () {
            if (this.selected() === null) {
                $(this.addSelectedBtnSelector).addClass('no-display');
                $(this.deleteSelectedBtnSelector).addClass('no-display');
            } else {
                $(this.addSelectedBtnSelector).removeClass('no-display');
                $(this.deleteSelectedBtnSelector).removeClass('no-display');
            }
        }
    });
});
