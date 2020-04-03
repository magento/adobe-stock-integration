/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiComponent',
    'Magento_MediaGalleryUi/js/action/deleteImage'
], function ($, _, Component, deleteImage) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_MediaGalleryUi/grid/columns/image/actions',
            mediaGalleryImageDetailsName: 'mediaGalleryImageDetails',
            actionsList: [
                {
                    name: 'image-details',
                    title: 'View Details',
                    handler: 'viewImageDetails'
                },
                {
                    name: 'delete',
                    title: 'Delete',
                    handler: 'deleteImageAction'
                }
            ],
            modules: {
                imageModel: '${ $.imageModelName }',
                mediaGalleryImageDetails: '${ $.mediaGalleryImageDetailsName }'
            }
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super();
            $(this.imageModel().addSelectedBtnSelector).click(function () {
                this.insertImage();
            }.bind(this));
            $(this.imageModel().deleteSelectedBtnSelector).click(function () {
                this.deleteImageAction(this.imageModel().selected());
            }.bind(this));

            return this;
        },

        /**
         * Insert selected image
         *
         * @returns {Boolean}
         */
        insertImage: function () {
            var record = this.imageModel().getSelected(),
                targetElement;

            if (record === null) {
                return false;
            }
            targetElement = this.getTargetElement();

            if (!targetElement.length) {
                window.MediabrowserUtility.closeDialog();
                throw 'Target element not found for content update';
            }

            targetElement.val(record['thumbnail_url'])
                .data('size', record.size)
                .data('mime-type', record['content_type'])
                .trigger('change');
            window.MediabrowserUtility.closeDialog();
            targetElement.focus();
        },

        /**
         * Delete image action
         *
         * @param {Object} record
         */
        deleteImageAction: function (record) {
            deleteImage.deleteImageAction(record, this.imageModel().deleteImageUrl);
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

        /**
         * Get target element
         *
         * @returns {*|n.fn.init|jQuery|HTMLElement}
         */
        getTargetElement: function () {
            return $('#' + this.imageModel().targetElementId);
        }
    });
});
