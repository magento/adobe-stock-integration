/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiElement',
    'Magento_MediaGalleryUi/js/action/deleteImageWithDetailConfirmation',
    'Magento_MediaGalleryUi/js/grid/columns/image/insertImageAction',
    'Magento_MediaGalleryUi/js/action/saveDetails'
], function ($, _, Element, deleteImageWithDetailConfirmation, addSelected, saveDetails) {
    'use strict';

    return Element.extend({
        defaults: {
            modalSelector: '',
            modalWindowSelector: '',
            mediaGalleryImageDetailsName: 'mediaGalleryImageDetails',
            mediaGalleryEditDetailsName: 'mediaGalleryEditDetails',
            template: 'Magento_MediaGalleryUi/image/actions',
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
            var imageDetailsUrl = this.mediaGalleryImageDetails().imageDetailsUrl,
                deleteImageUrl = this.imageModel().deleteImageUrl;

            deleteImageWithDetailConfirmation.deleteImageAction(
                [this.imageModel().getSelected().id],
                imageDetailsUrl,
                deleteImageUrl
            );
        },

        /**
         * Save image details action
         */
        saveImageDetailsAction: function () {
            var saveDetailsUrl = this.mediaGalleryEditDetails().saveDetailsUrl,
                modalElement = $(this.modalSelector);

            saveDetails(
                saveDetailsUrl,
                modalElement.find('#image-edit-details-form')
            ).then(function () {
                this.closeModal();
                this.imageModel().reloadGrid();
            }.bind(this));

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
