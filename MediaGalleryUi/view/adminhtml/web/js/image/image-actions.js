/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiElement',
    'Magento_MediaGalleryUi/js/action/deleteImage',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, _, Element, deleteImage, confirmation, $t) {
    'use strict';

    return Element.extend({
        defaults: {
            modalSelector: '',
            template: 'Magento_MediaGalleryUi/image/actions',
            targetElementId: null,
            deleteImageUrl: null,
            modules: {
                mediaGalleryImageDetails: '${ $.mediaGalleryImageDetail }'
            }
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super();
            $(window).on('fileDeleted.enhancedMediaGallery', this.closeModal.bind(this));

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
            var record = this.getImageData();

            deleteImage.deleteImageAction(record, this.deleteImageUrl);
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
