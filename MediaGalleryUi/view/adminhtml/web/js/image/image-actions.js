/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiElement',
    'Magento_MediaGalleryUi/js/action/deleteImage',
    'Magento_MediaGalleryUi/js/grid/columns/image/insertImageAction'
], function ($, _, Element, deleteImage, addSelected) {
    'use strict';

    return Element.extend({
        defaults: {
            modalSelector: '',
            template: 'Magento_MediaGalleryUi/image/actions',
            modules: {
                imageModel: '${ $.imageModelName }'
            }
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super();

            return this;
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
         */
        deleteImageAction: function () {
            this.closeModal();
            deleteImage.deleteImageAction(this.imageModel().getSelected(), this.imageModel().deleteImageUrl);
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
