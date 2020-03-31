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
              image = this.mediaGalleryImageDetails().image();

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
        }
    });
});
