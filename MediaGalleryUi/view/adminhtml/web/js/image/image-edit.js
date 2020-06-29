/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'Magento_MediaGalleryUi/js/action/getDetails'
], function ($, _, Component, getDetails) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_MediaGalleryUi/image/image-edit',
            modalSelector: '',
            imageEditDetailsUrl: '/media_gallery/image/details',
            saveDetailsUrl: '/media_gallery/image/saveDetails',
            images: [],
            image: null,
            modules: {
                mediaGridMessages: '${ $.mediaGridMessages }'
            }
        },

        /**
         * Init observable variables
         *
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'image'
                ]);

            return this;
        },

        /**
         * Get image details by ID
         *
         * @param {String} imageId
         */
        showEditDetailsPanel: function (imageId) {
            if (_.isUndefined(this.images[imageId])) {
                getDetails(this.imageEditDetailsUrl, imageId).then(function (imageDetails) {
                    this.images[imageId] = imageDetails;
                    this.image(this.images[imageId]);
                    this.openEditImageDetailsModal();
                }.bind(this)).fail(function (message) {
                    this.addMediaGridMessage('error', message);
                }.bind(this));

                return;
            }

            if (this.image() && this.image().id === imageId) {
                this.openEditImageDetailsModal();

                return;
            }

            this.image(this.images[imageId]);
            this.openEditImageDetailsModal();
        },

        /**
         * Open edit image details popup
         */
        openEditImageDetailsModal: function () {
            var modalElement = $(this.modalSelector);

            if (!modalElement.length || _.isUndefined(modalElement.modal)) {
                return;
            }
            modalElement.modal('openModal');
        },

        /**
         * Close image details popup
         */
        closeImageDetailsModal: function () {
            var modalElement = $(this.modalSelector);

            if (!modalElement.length || _.isUndefined(modalElement.modal)) {
                return;
            }

            modalElement.modal('closeModal');
        },

        /**
         * Add media grid message
         *
         * @param {String} code
         * @param {String} message
         */
        addMediaGridMessage: function (code, message) {
            this.mediaGridMessages().add(code, message);
            this.mediaGridMessages().scheduleCleanup();
        }
    });
});
