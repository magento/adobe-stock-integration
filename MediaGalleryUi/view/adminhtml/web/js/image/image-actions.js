/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiElement',
    'mage/translate',
    'Magento_MediaGalleryUi/js/grid/columns/image/insertImageAction'
], function ($, _, Element, $t, addSelected) {
    'use strict';

    return Element.extend({
        defaults: {
            modalSelector: '',
            template: 'Magento_MediaGalleryUi/image/actions',
            mediaGalleryImageDetailsName: 'mediaGalleryImageDetails',
            imageModelName: 'media_gallery_listing.media_gallery_listing.media_gallery_columns.thumbnail_url',
            modules: {
                imageModel: '${ $.imageModelName }',
                mediaGalleryImageDetails: '${ $.mediaGalleryImageDetailsName }'
            }
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
         * Add Image
         */
        addImage: function () {
            addSelected.insertImage(
                this.imageModel().getSelected(),
                {
                    onInsertUrl: this.imageModel().onInsertUrl,
                    targetElementId: this.imageModel().targetElementId,
                    storeId: this.imageModel().storeId
                }
            );
            this.closeModal();
        }
    });
});
