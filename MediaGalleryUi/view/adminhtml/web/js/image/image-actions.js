/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiElement',
    'Magento_Ui/js/modal/confirm'
], function ($, _, Element, confirmation) {
    'use strict';

    return Element.extend({
        defaults: {
            modalSelector: '',
            template: 'Magento_MediaGalleryUi/image/actions',
            deleteImageUrl: 'media_gallery/image/delete',
            mediaGalleryMessagesName: 'mediaGalleryImageDetailsMessages',
            mediaGalleryImageDetailsName: 'mediaGalleryImageDetails',
            imageActionName: 'media_gallery_listing.media_gallery_listing.media_gallery_columns.thumbnail_url_actions',
            modules: {
                mediaGridMessages: '${ $.mediaGalleryMessagesName }',
                mediaGalleryImageDetails: '${ $.mediaGalleryImageDetailsName }',
                imageAction: '${ $.imageActionName}'
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
              image = this.mediaGalleryImageDetails().image(),
              cancelText = $.mage.__('Cancel'),
              deleteImageText = $.mage.__('Delete Image'),
              deleteImageCallback = this.deleteImage.bind(this);

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
         * @param {Object} image
         */
        deleteImage: function (image) {
            $.ajax({
                type: 'POST',
                url: this.deleteImageUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'image_id': image.image_id
                },
                context: this,

                /**
                 * Success handler for deleting image
                 *
                 * @param {Object} response
                 */
                success: function (response) {
                    var message = !_.isUndefined(response.message) ? response.message : null;

                    if (!response.success) {
                        message = message || $.mage.__('There was an error on attempt to delete the image.');
                        this.addMessage('error', message);

                        return;
                    }

                    message = message || $.mage.__('You have successfully removed the image.');
                    this.imageAction().reloadGrid();
                    this.imageAction().addMessage('success', message);
                    this.closeModal();
                }.bind(this),

                /**
                 * Error handler for deleting image
                 *
                 * @param {Object} response
                 */
                error: function (response) {
                    var message;

                    if (typeof response.responseJSON === 'undefined' ||
                        typeof response.responseJSON.message === 'undefined'
                    ) {
                        message = 'There was an error on attempt to delete the image.';
                    } else {
                        message = response.responseJSON.message;
                    }

                    this.mediaGridMessages().add(code, message);
                    this.mediaGridMessages().scheduleCleanup();
                }.bind(this)
            });
        }
    });
});
