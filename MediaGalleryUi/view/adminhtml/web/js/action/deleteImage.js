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

    return function (records, deleteUrl, confirmationContent) {
        var deferred = $.Deferred(),
               title = $.mage.__('Delete image'),
               cancelText = $.mage.__('Cancel'),
               deleteImageText = $.mage.__('Delete Image');

        /**
         * Send deletion request with redords ids
         *
         * @param {Array} images
         * @param {String} serviceUrl
         */
        function sendRequest(images, serviceUrl) {
            var recordIds = $.map(images, function (image) {
                return image.id;
            });

            $.ajax({
                type: 'POST',
                url: serviceUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'ids': recordIds
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
                        message = message || $.mage.__('There was an error on attempt to delete the images.');
                        $(window).trigger('fileDeleted.enhancedMediaGallery', {
                            reload: false,
                            message: message,
                            code: 'error'
                        });

                        deferred.reject(message);
                    }

                    message = message || $.mage.__('You have successfully removed the images.');
                    $(window).trigger('fileDeleted.enhancedMediaGallery', {
                        reload: true,
                        message: message,
                        code: 'success'
                    });
                    deferred.resolve(message);
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

        confirmation({
            title: title,
            modalClass: 'media-gallery-delete-image-action',
            content: confirmationContent,
            buttons: [
                {
                    text: cancelText,
                    class: 'action-secondary action-dismiss',

                    /**
                     * Close modal
                     */
                    click: function () {
                        this.closeModal();
                        deferred.resolve();
                    }
                },
                {
                    text: deleteImageText,
                    class: 'action-primary action-accept',

                    /**
                     * Delete Image and close modal
                     */
                    click: function () {
                        sendRequest(records, deleteUrl);
                        this.closeModal();
                    }
                }
            ]
        });

        return deferred.promise();
    };
});
