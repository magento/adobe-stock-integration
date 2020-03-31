/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiElement',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, _, Element, confirmation, $t) {
    'use strict';

    return Element.extend({
        defaults: {
            modalSelector: '',
            template: 'Magento_MediaGalleryUi/image/actions',
            targetElementId: null,
            modules: {
                mediaGalleryImageDetails: '${ $.mediaGalleryImageDetail }',
                imageActions: '${ $.imageActions }',
            },
        },

        /**
         * Close the images details modal
         */
        closeModal: function () {
            var modalElement = $(this.modalSelector);

            if (!modalElement.length || _.isUndefined(modalElement.modal)) {
                return;
            }

            modalElement.modal('closeModal');
        },

        /**
         * Delete image action
         *
         * @param {Object} record
         */
        deleteImageAction: function (record) {
            var baseContent = $.mage.__('Are you sure you want to delete "%s" image?'),
              title = record.title,
              cancelText = $.mage.__('Cancel'),
              deleteImageText = $.mage.__('Delete Image'),
              deleteImageCallback = this.deleteImage.bind(this),
              image = this.getImageData();

            confirmation({
                title: title,
                content: baseContent.replace('%s', image.title),
                buttons: [
                    {
                        text: cancelText,
                        class: 'action-secondary action-dismiss',

                        /**
                         * Close modal
                         */
                        click: function () {
                            this.closeModal();
                        }
                    },
                    {
                        text: deleteImageText,
                        class: 'action-primary action-accept',

                        /**
                         * Delete Image and close modal
                         */
                        click: function () {
                            deleteImageCallback(image);
                            this.closeModal();
                        }
                    }
                ]
            });
        },

        /**
         * Delete image
         *
         * @param {Object} record
         */
        deleteImage: function (record) {
            this.imageActions().deleteImage(record);
            this.closeModal();
        },

        /**
         * Add Image
         */
        addImage: function () {
            var targetElement = this.getTargetElement(),
              imageDetails = this.getImageData();

            if (!targetElement.length) {
                this.closeMediaGalleryGridModal();
                throw $t('Target element not found for content update');
            }

            targetElement.val(imageDetails['image_url'])
            .data('size', imageDetails.size)
            .data('mime-type', imageDetails['content_type'])
            .trigger('change');
            this.closeModal();
            this.closeMediaGalleryGridModal();
            targetElement.focus();
        },

        /**
         * Get image data
         *
         * @return {Object}
         */
        getImageData: function () {
            if (!this.mediaGalleryImageDetails) {
                return {};
            }

            return this.mediaGalleryImageDetails().getImageData();
        },

        /**
         * Close media gallery grid modal
         */
        closeMediaGalleryGridModal: function () {
            this.getMediaGalleryModal().closeDialog();
        },

        /**
         * Get target element
         *
         * @return {HTMLElement}
         */
        getTargetElement: function () {
            var targetElementSelector = '#{targetElementId}'.replace('{targetElementId}', this.targetElementId);

            return $(targetElementSelector);
        },

        /**
         * Get media gallery modal
         *
         * @return {Object}
         */
        getMediaGalleryModal: function () {
            return window.MediabrowserUtility;
        }
    });
});
