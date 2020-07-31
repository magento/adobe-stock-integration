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
            modalWindowSelector: '',
            imageDetailsUrl: '/media_gallery/image/details',
            images: [],
            tagListLimit: 7,
            categoryContentType: 'Category',
            showAllTags: false,
            image: null,
            usedInComponents : [],
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
         * Check if asset is used or not
         *
         * @param value
         */
        isUsedIn: function (value) {
            return _.isObject(value);
        },

        /**
         * Converting object into Array
         *
         * @param object
         */
        convertObjectToArray: function (object) {
            var usedIn = [];

            $.each(object, function (moduleName, count) {
                usedIn.push(count + ' ' + moduleName);
            });
            return usedIn;
        },

        /**
         * Return entity name based on used in count
         *
         * @param usedIn
         */
        getEntityNameWithPrefix: function (usedIn) {
            var count = usedIn.match(/\d+/g);
            var entityName =  usedIn.match(/[a-zA-Z]+/g);
            var name;

            if (count[0] > 1) {
                if (entityName[0] === this.categoryContentType) {
                    name = count[0] + ' ' + entityName[0].slice(0, -1) + 'ies';
                } else {
                    name = count[0] + ' ' + entityName[0] + 's';
                }

                return name;
            }

            return count[0] + ' ' +entityName[0];
        },

        /**
         * Check if details modal is active
         * @return {Boolean}
         */
        isActive: function () {
            return $(this.modalWindowSelector).hasClass('_show');
        },

        /**
         * Remove image details
         *
         * @param {String} id
         */
        removeCached: function (id) {
            delete this.images[id];
        },

        /**
         * Get filter url
         *
         * @param usedIn
         */
        getFilterUrl: function (usedIn) {
            var moduleName =  usedIn.match(/[a-zA-Z]+/g),
                url = '',
                self = this;

            _.each(this.usedInComponents, function (usedInComponent) {
                if (moduleName[0] === usedInComponent.name) {
                    url = usedInComponent.url + '?filters[asset_id]=' + self.image().id;
                }
            });

            return url;
        }
    });
});
