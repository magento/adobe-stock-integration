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
            template: 'Magento_MediaGalleryUi/image/image-details',
            modalSelector: '',
            imageDetailsUrl: '/media_gallery/image/details',
            images: [],
            tagListLimit: 7,
            categoryContentType: 'Category',
            showAllTags: false,
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
                    'image',
                    'showAllTags'
                ]);

            return this;
        },

        /**
         * Show image details by ID
         *
         * @param {String} imageId
         */
        showImageDetailsById: function (imageId) {
            if (_.isUndefined(this.images[imageId])) {
                getDetails(this.imageDetailsUrl, [imageId]).then(function (imageDetails) {
                    this.images[imageId] = imageDetails[imageId];
                    this.image(this.images[imageId]);
                    this.openImageDetailsModal();
                }.bind(this)).fail(function (message) {
                    this.addMediaGridMessage('error', message);
                }.bind(this));

                return;
            }

            if (this.image() && this.image().id === imageId) {
                this.openImageDetailsModal();

                return;
            }

            this.image(this.images[imageId]);
            this.openImageDetailsModal();
        },

        /**
         * Open image details popup
         */
        openImageDetailsModal: function () {
            var modalElement = $(this.modalSelector);

            if (!modalElement.length || _.isUndefined(modalElement.modal)) {
                return;
            }

            this.showAllTags(false);
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
        },

        /**
         * Get tag text
         *
         * @param {String} tagText
         * @param {Number} tagIndex
         * @return {String}
         */
        getTagText: function (tagText, tagIndex) {
            return tagText + (this.image().tags.length - 1 === tagIndex ? '' : ',');
        },

        /**
         * Show all image tags
         */
        showMoreImageTags: function () {
            this.showAllTags(true);
        },

        /**
         * Get image details value
         *
         * @param {Object|String} value
         * @return {String}
         */
        getValueUnsanitizedHtml: function (value) {
            var usedIn = '';

            if (_.isObject(value)) {
                $.each(value, function (moduleName, count) {
                    usedIn += count + ' ' +
                        this.getEntityNameWithPrefix(moduleName, count) +
                        '</br>';
                }.bind(this));

                return usedIn;
            }

            return value;
        },

        /**
        * Return entity name based on used in count
        *
        * @param {String} entityName
        * @param {String} count
        */
        getEntityNameWithPrefix: function (entityName, count) {
            var name;

            if (count > 1) {
                if (entityName === this.categoryContentType) {
                    name = entityName.slice(0, -1) + 'ies';
                } else {
                    name = entityName + 's';
                }

                return name;
            }

            return entityName;
        },

        /**
         * Remove image details
         *
         * @param {String} id
         */
        removeCached: function (id) {
            delete this.images[id];
        }
    });
});
