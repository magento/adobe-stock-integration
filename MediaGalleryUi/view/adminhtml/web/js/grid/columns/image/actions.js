/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiComponent',
    'Magento_MediaGalleryUi/js/action/deleteImageWithDetailConfirmation',
    'Magento_MediaGalleryUi/js/grid/columns/image/insertImageAction'
], function ($, _, Component, deleteImageWithDetailConfirmation, image) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_MediaGalleryUi/grid/columns/image/actions',
            mediaGalleryImageDetailsName: 'mediaGalleryImageDetails',
            mediaGalleryEditDetailsName: 'mediaGalleryEditDetails',
            actionsList: [
                {
                    name: 'image-details',
                    title: $.mage.__('View Details'),
                    handler: 'viewImageDetails'
                },
                {
                    name: 'edit',
                    title: $.mage.__('Edit'),
                    handler: 'editImageDetails'
                },
                {
                    name: 'delete',
                    title: $.mage.__('Delete'),
                    handler: 'deleteImageAction'
                }
            ],
            modules: {
                imageModel: '${ $.imageModelName }',
                mediaGalleryImageDetails: '${ $.mediaGalleryImageDetailsName }',
                mediaGalleryEditDetails: '${ $.mediaGalleryEditDetailsName }'
            }
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super();
            this.initEvents();

            return this;
        },

        /**
         * Initialize image action events
         */
        initEvents: function () {
            $(this.imageModel().addSelectedBtnSelector).click(function () {
                image.insertImage(
                    this.imageModel().getSelected(),
                    {
                        onInsertUrl: this.imageModel().onInsertUrl,
                        storeId: this.imageModel().storeId
                    }
                );
            }.bind(this));
            $(this.imageModel().deleteSelectedBtnSelector).click(function () {
                this.deleteImageAction(this.imageModel().selected());
            }.bind(this));

        },

        /**
         * Delete image action
         *
         * @param {Object} record
         */
        deleteImageAction: function (record) {
            var imageDetailsUrl = this.mediaGalleryImageDetails().imageDetailsUrl,
                deleteImageUrl = this.imageModel().deleteImageUrl;

            deleteImageWithDetailConfirmation.deleteImageAction(record, imageDetailsUrl, deleteImageUrl);
        },

        /**
         * View image details
         *
         * @param {Object} record
         */
        viewImageDetails: function (record) {
            var recordId = this.imageModel().getId(record);

            this.mediaGalleryImageDetails().showImageDetailsById(recordId);
        },

        editImageDetails: function (record) {
            //get record and identifies if it is from standalone media gallery or not
            var recordId = this.imageModel().getId(record);

            this.mediaGalleryEditDetails().showImageDetailsById(recordId);
        }
    });
});
