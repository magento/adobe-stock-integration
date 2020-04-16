/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'mage/url',
    'Magento_MediaGalleryUi/js/grid/messages',
    'Magento_Ui/js/modal/confirm'
], function ($, _, urlBuilder, messages, confirmation) {
    'use strict';

    return {

        /**
         * Delete image action
         *
         * @param {Object} record
         * @param {String} deleteUrl
         */
        deleteImageAction: function (record, deleteUrl) {
            var baseContent = $.mage.__('Are you sure you want to delete "%s" image?'),
                title = $.mage.__('Delete image'),
                cancelText = $.mage.__('Cancel'),
                deleteImageText = $.mage.__('Delete Image'),
                deleteImageCallback = this.deleteImage.bind(this);

            confirmation({
                title: title,
                modalClass: 'media-gallery-delete-image-action',
                content: baseContent.replace('%s', record.path),
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
                            deleteImageCallback(record, deleteUrl);
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
         * @param {String} deleteUrl
         */
        deleteImage: function (record, deleteUrl) {
            var recordId = record.id;

            $.ajax({
                type: 'POST',
                url: deleteUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'id': recordId
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
                        $(window).trigger('fileDeleted.enhancedMediaGallery', {
                            reload: false,
                            message: message,
                            code: 'error'
                        });

                        return;
                    }

                    message = message || $.mage.__('You have successfully removed the image.');
                    $(window).trigger('fileDeleted.enhancedMediaGallery', {
                        reload: true,
                        message: message,
                        code: 'success'
                    });
                },

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

                    $(window).trigger('fileDeleted.enhancedMediaGallery', {
                        reload: false,
                        message: message,
                        code: 'error'
                    });
                }
            });
        }
    };
});
