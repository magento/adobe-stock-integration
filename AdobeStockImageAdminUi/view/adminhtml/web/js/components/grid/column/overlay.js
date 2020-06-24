/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/columns/overlay',
    'Magento_AdobeStockImageAdminUi/js/action/getLicenseStatus'
], function ($, _, overlay, getLicenseStatus) {
    'use strict';

    return overlay.extend({
        defaults: {
            // eslint-disable-next-line max-len
            provider: 'name = adobe_stock_images_listing.adobe_stock_images_listing_data_source, ns = adobe_stock_images_listing',
            loginProvider: 'name = adobe-login, ns = adobe-login',
            getImagesUrl: 'adobe_stock/license/getlist',
            licensed: {},
            modules: {
                login: '${ $.loginProvider }'
            },
            listens: {
                '${ $.provider }:data.items': 'handleItemsUpdate',
                '${ $.loginProvider }:user': 'handleUserUpdate'
            },
            imports: {
                rows: '${ $.provider }:data.items'
            }
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'licensed'
                ]);

            return this;
        },

        /**
         * Updates the licensed data when items data is updated.
         *
         * @param {Array} items
         */
        handleItemsUpdate: function (items) {
            var ids = this.getIds(items);

            this.updateLicensed(ids);
        },

        /**
         * Updates the licensed data when user data is updated.
         */
        handleUserUpdate: function () {
            var ids = this.getIds(this.rows);

            this.updateLicensed(ids);
        },

        /**
         * Set Licensed images data.
         *
         * @param {Array} ids
         */
        updateLicensed: function (ids) {
            if (!this.isUserAuthorized() || ids.length === 0) {
                this.licensed({});

                return;
            }

            getLicenseStatus(this.getImagesUrl, ids).then(function (licensed) {
                this.licensed(licensed);
            }.bind(this));
        },

        /**
         * Checks if user is logged in and authorized
         *
         * @returns {Boolean}
         */
        isUserAuthorized: function () {
            return !_.isUndefined(this.login()) && this.login().user().isAuthorized;
        },

        /**
         * Get all ids from items array
         *
         * @param {Array} items
         * @returns {Number[]}
         */
        getIds: function (items) {
            var ids = [];

            items.forEach(function (record) {
                ids.push(record.id);
            });

            return ids;
        },

        /**
         * Returns top displacement of overlay according to image height
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Object}
         */
        getStyles: function (record) {
            var height = record.styles().height.replace('px', '') - 40;

            return {
                top: height + 'px'
            };
        },

        /**
         * If overlay should be visible
         *
         * @param {Object} row
         * @returns {Boolean}
         */
        isVisible: function (row) {
            return this.licensed()[row.id];
        },

        /**
         * Get overlay label
         *
         * @param {Object} row
         * @returns {String}
         */
        getLabel: function (row) {
            return this.licensed()[row.id] ? 'Licensed' : '';
        }
    });
});
