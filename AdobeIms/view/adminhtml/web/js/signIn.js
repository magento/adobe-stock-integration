/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'jquery',
    'Magento_AdobeIms/js/action/authorization',
    'uiRegistry'
], function (Component, $, login, uiRegistry) {
    'use strict';

    return Component.extend({

        defaults: {
            profileUrl: 'adobe_ims/user/profile',
            logoutUrl: 'adobe_ims/user/logout',
            dataProvider: 'adobe_stock_images_listing.adobe_stock_images_listing_data_source',
            defaultProfileImage:
                'https://a5.behance.net/27000444e0c8b62c56deff3fc491e1a92d07f0cb/img/profile/no-image-276.png',
            user: {
                isAuthorized: false,
                name: '',
                email: '',
                image: ''
            },
            loginConfig: {
                url: 'https://ims-na1.adobelogin.com/ims/authorize',
                callbackParsingParams: {
                    regexpPattern: /auth\[code=(success|error);message=(.+)\]/,
                    codeIndex: 1,
                    messageIndex: 2,
                    nameIndex: 3,
                    successCode: 'success',
                    errorCode: 'error'
                },
                popupWindowParams: {
                    width: 500,
                    height: 600,
                    top: 100,
                    left: 300
                },
                popupWindowTimeout: 60000
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().observe(['user']);

            return this;
        },

        /**
         * Login to Adobe
         *
         * @return {window.Promise}
         */
        login: function () {
            var self = this; // TODO Please bind this properly
            return new window.Promise(function (resolve, reject) {
                if (self.user().isAuthorized) {
                    return resolve();
                }
                login(self.loginConfig)
                    .then(function (response) {
                        self.reloadGridData(self.dataProvider);
                        self.loadUserProfile();
                        resolve(response);
                    })
                    .catch(function (error) {
                        reject(error);
                    });
            });
        },

        /**
         * Retrieve data to authorized user.
         *
         * @return array
         */
        loadUserProfile: function () {
            $.ajax({
                type: 'POST',
                url: this.profileUrl,
                showLoader: true,
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
                    this.user({
                        isAuthorized: true,
                        name: response.result.name,
                        email: response.result.email,
                        image: response.result.image
                    });
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
         * Logout from adobe account
         */
        logout: function () {
            var self = this;
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
                    self.reloadGridData(self.dataProvider);
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
         * Reload Grid Data
         * @param {String} dataprovider
         */
        reloadGridData: function (dataprovider) {
            if (dataprovider) {
                var target = uiRegistry.get(dataprovider);
                if (target && typeof target === 'object') {
                    target.set('params.t ', Date.now());
                }
            }
        }
    });
});
