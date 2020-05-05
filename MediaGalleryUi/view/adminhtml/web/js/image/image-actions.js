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
            modalWindowSelector: '',
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
            $(window).on('fileDeleted.enhancedMediaGallery', this.closeViewDetailsModal.bind(this));

            return this;
        },

        /**
         * Close the images details modal
         */
        closeModal: function () {
            var modalElement = $(this.modalSelector),
                modalWindow = $(this.modalWindowSelector);

            if (!modalWindow.hasClass('_show') || !modalElement.length || _.isUndefined(modalElement.modal)) {
                return;
            }

            modalElement.modal('closeModal');
        },

        /**
         * Delete image action
         */
        deleteImageAction: function () {
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
                    storeId: this.imageModel().storeId
                }
            );
            this.closeModal();
        },

        /**
         * Close view details modal after confirm deleting image
         */
        closeViewDetailsModal: function () {
            this.closeModal();
        }
    });
});
