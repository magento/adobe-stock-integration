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
            selected: null,
            mediaGalleryDirectoryComponent: 'media_gallery_listing.media_gallery_listing.media_gallery_directories',
            fields: {
                id: 'id',
                url: 'url',
                alt: 'name'
            },
            modules: {
                actions: '${ $.name }_actions',
                provider: '${ $.provider }',
                messages: '${ $.messagesName }'
            },
            imports: {
                activeDirectory: '${ $.mediaGalleryDirectoryComponent }:activeNode'
            },
            listens: {
                activeDirectory: 'selectDirectoryHandle'
            },
            viewConfig: [
                {
                    component: 'Magento_MediaGalleryUi/js/grid/columns/image/actions',
                    name: '${ $.name }_actions',
                    imageModelName: '${ $.name }'
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
            $(window).on('fileDeleted.enhancedMediaGallery', this.reloadMediaGrid.bind(this));
            $(window).on('reload.MediaGallery', this.reloadGrid.bind(this));

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
         * Returns name to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getImageAlt: function (record) {
            return record[this.fields.alt];
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
         * Click on image
         *
         * @param {Object} record
         * @param {Boolean} collapsibleOpened
         */
        clickOnImage: function (record, collapsibleOpened) {
            if (!collapsibleOpened) {
                this.select(record);
            }
        },

        /**
         * Click on three-dots
         *
         * @param {Object} record
         * @param {Boolean} collapsibleOpened
         */
        clickOnThreeDots: function (record, collapsibleOpened) {
            if (!this.isSelected(record) || collapsibleOpened) {
                this.select(record);
            }
        },

        /**
         * Set the record as selected
         */
        select: function (record) {
            this.isSelected(record) ? this.selected(null) : this.selected(record);
            this.toggleAddSelectedButton();
        },

        /**
         * Deselect the record
         */
        deselectImage: function () {
            this.selected(null);
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
                this.hideAddSelectedAndDeleteButon();
            } else {
                $(this.addSelectedBtnSelector).removeClass('no-display');
                $(this.deleteSelectedBtnSelector).removeClass('no-display');
            }
        },

        /**
         * Hide add selected and Delete button
         */
        hideAddSelectedAndDeleteButon: function () {
            $(this.addSelectedBtnSelector).addClass('no-display');
            $(this.deleteSelectedBtnSelector).addClass('no-display');
        },

        /**
         * @param {jQuery.event} e
         * @param {Object} data
         */
        reloadMediaGrid: function (e, data) {
            if (data.reload) {
                this.reloadGrid();
            }

            if (data.message && data.code) {
                this.addMessage(data.code, data.message);
            }
            this.hideAddSelectedAndDeleteButon();
        },

        /**
         * Reload grid
         */
        reloadGrid: function () {
            var provider = this.provider(),
                dataStorage = provider.storage();

            dataStorage.clearRequests();
            provider.reload();
        },

        /**
         * Add message
         *
         * @param {String} code
         * @param {String} message
         */
        addMessage: function (code, message) {
            this.messages().add(code, message);
            this.messages().scheduleCleanup();
        },

        /**
         * Listener to select directory event
         *
         * @param {String} path
         */
        selectDirectoryHandle: function (path) {
            if (this.selected() && this.selected().directory !== path) {
                this.deselectImage();
            }
        }
    });
});
