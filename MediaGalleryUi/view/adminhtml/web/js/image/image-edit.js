/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'uiLayout',
    'Magento_Ui/js/lib/key-codes',
    'Magento_MediaGalleryUi/js/action/getDetails'
], function ($, _, Component, layout, keyCodes, getDetails) {
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
                mediaGridMessages: '${ $.mediaGridMessages }',
                select: '${ $.name }_select'
            },
            viewConfig: [
                {
                    component: 'Magento_MediaGalleryUi/js/image/edit/keyword-ui-select',
                    name: '${ $.name }_select'
                }
            ],
            exports: {
                image: '${ $.name }_select:image'
            }
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super().initView();

            return this;
        },

        /**
         * Initialize child components
         *
         * @returns {Object}
         */
        initView: function () {
            layout(this.viewConfig);

            return this;
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
                getDetails(this.imageEditDetailsUrl, [imageId]).then(function (imageDetails) {
                    this.images[imageId] = imageDetails[imageId];
                    this.image(this.images[imageId]);
                    this.openEditImageDetailsModal();
                    this.getKeywordsOp();
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

        getKeywordsOp: function() {
            this.select().getKeywordsOp();
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
        },

        /**
         * Handle Enter key event to save image details
         *
         * @param {Object} data
         * @param {jQuery.Event} event
         * @returns {Boolean}
         */
        handleEnterKey: function (data, event) {
            var modalElement = $(this.modalSelector),
                key = keyCodes[event.keyCode];

            if (key === 'enterKey') {
                event.preventDefault();
                modalElement.find('.page-action-buttons button.save').click();
            }

            return true;
        }
    });
});
