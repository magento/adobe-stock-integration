// jscs:disable
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
// jscs:enable
define([
    'Magento_Ui/js/grid/columns/overlay',
    'jquery',
    'underscore'
], function (overlay, $, _) {
    'use strict';

    return overlay.extend({
        defaults: {
            // eslint-disable-next-line max-len
            provider: 'name = adobe_stock_images_listing.adobe_stock_images_listing_data_source, ns = adobe_stock_images_listing',
            loginProvider: 'name = adobe-login, ns = adobe-login',
            getImagesUrl: 'adobe_stock/license/getlist',
            licensed: {},
            modules: {
                login: '${ $.loginProvider }',
                masonry: '${ $.parentName }'
            },
            listens: {
                '${ $.provider }:data.items': 'updateLicensed',
                '${ $.loginProvider }:user': 'updateLicensed'
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
         * Set Licensed images data.
         */
        updateLicensed: function () {
            if (_.isUndefined(this.login()) || !this.login().user().isAuthorized) {
                this.licensed({});

                return;
            }

            $.ajax({
                type: 'GET',
                url: this.getImagesUrl + '?ids=' + this.getIds().join(','),
                data: {
                    'form_key': window.FORM_KEY
                },
                dataType: 'json',
                context: this,

                /**
                 * @param {Object} response
                 * @returns void
                 */
                success: function (response) {
                    this.licensed(response.result);
                },

                /**
                 * @param {Object} response
                 * @returns {String}
                 */
                error: function (response) {
                    return response.message;
                }
            });
        },

        /**
         * Get all ids from data provider
         *
         * @returns {Number[]}
         */
        getIds: function () {
            var ids = [];

            this.masonry().rows().forEach(function (record) {
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
            var height = record.styles().height.replace('px', '') - 50;

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
