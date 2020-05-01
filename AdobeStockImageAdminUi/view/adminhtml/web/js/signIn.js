/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_AdobeIms/js/signIn',
    'Magento_AdobeIms/js/action/authorization',
    'Magento_Ui/js/modal/confirm'
], function ($, signIn, auth, confirm) {
    'use strict';

    return signIn.extend({

        defaults: {
            userQuota: {},
            // eslint-disable-next-line max-len
            dataProvider: 'name = adobe_stock_images_listing.adobe_stock_images_listing_data_source, ns = adobe_stock_images_listing',
            // eslint-disable-next-line max-len
            previewProvider: 'name = adobe_stock_images_listing.adobe_stock_images_listing.adobe_stock_images_columns.preview, ns = adobe_stock_images_listing',
            quotaUrl: 'adobe_stock/license/quota',
            modules: {
                source: '${ $.dataProvider }',
                preview: '${ $.previewProvider }'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().observe(['userQuota']);

            return this;
        },

        /**
         * Login to Adobe
         *
         * @return {*}
         */
        login: function () {
            var deferred = $.Deferred();

            if (this.user().isAuthorized) {
                return deferred.resolve();
            }
            auth(this.loginConfig)
                .then(function (response) {
                    this.loadUserProfile();
                    deferred.resolve(response);
                }.bind(this))
                .fail(function (error) {
                    deferred.reject(error);
                });

            return deferred.promise();
        },

        /**
         * Login action with popup on error..
         */
        loginClick: function () {
            this.login().fail(function (error) {
                this.showLoginErrorPopup(error);
            }.bind(this));
        },

        /**
         * Show popup that user failed to login.
         */
        showLoginErrorPopup: function (error) {
            confirm({
                title: $.mage.__('Couldn\'t log you in'),
                content: error,
                buttons: [{
                    text: $.mage.__('Ok'),
                    class: 'action-primary',
                    attr: {},

                    /**
                     * Close modal on button click
                     */
                    click: function (event) {
                        this.closeModal(event);
                    }
                }]
            });
        },

        /**
         * Logout from adobe account
         */
        logout: function () {
            $.ajax({
                type: 'POST',
                url: this.logoutUrl,
                data: {
                    'form_key': window.FORM_KEY
                },
                dataType: 'json',
                context: this,
                showLoader: true,
                success: function () {
                    this.user({
                        isAuthorized: false,
                        name: '',
                        email: '',
                        image: this.defaultProfileImage
                    });
                }.bind(this),

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
         * Retrieves full user quota.
         */
        getUserQuota: function () {
            $.ajax({
                type: 'GET',
                url: this.quotaUrl,
                dataType: 'json',
                context: this,

                /**
                 * @param {Object} response
                 * @returns void
                 */
                success: function (response) {
                    this.userQuota(response.result);
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
         * @inheritdoc
         */
        loadUserProfile: function () {
            $.ajax({
                type: 'GET',
                url: this.profileUrl,
                dataType: 'json',
                context: this,

                /**
                 * @param {Object} response
                 * @returns void
                 */
                success: function (response) {
                    this.user({
                        isAuthorized: true,
                        name: response.result.name,
                        email: response.result.email,
                        image: response.result.image
                    });
                    this.getUserQuota();
                },

                /**
                 * @param {Object} response
                 * @returns {String}
                 */
                error: function (response) {
                    return response.message;
                }
            });
        }
    });
});
