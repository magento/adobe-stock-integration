/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'jquery',
    'Magento_AdobeIms/js/action/authorization',
    'Magento_AdobeIms/js/user',
    'Magento_AdobeStockAdminUi/js/user-quota',
    'Magento_AdobeStockAdminUi/js/config'
], function (Component, $, login, user, userQuota, stockConfig) {
    'use strict';

    return Component.extend({

        defaults: {
            adobeStockModalSelector: '#adobe-stock-images-search-modal',
            profileUrl: 'adobe_ims/user/profile',
            logoutUrl: 'adobe_ims/user/logout',
            userName: '',
            userEmail: '',
            image: '',
            isAuthorized: false,
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

        user: user,
        userQuota: userQuota,

        /**
         * Login to Adobe
         *
         * @return {window.Promise}
         */
        login: function () {
            var self = this; // TODO Please bind this properly

            return new window.Promise(function (resolve, reject) {
                if (user.isAuthorized()) {
                    reject(new Error('You are logged in.'))
                }
                login(self.loginConfig)
                    .then(function (response) {
                        user.isAuthorized(true);
                        self.loadUserProfile();
                        resolve(response);
                    })
                    .catch(function (error) {
                        reject(error);
                    });
            });
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            user.isAuthorized.subscribe(function () {
                if (user.isAuthorized() && user.name() === '') {
                    this.loadUserProfile();
                }
            }.bind(this));

            user.name(this.userName);
            user.email(this.userEmail);
            user.image(this.image);
            user.isAuthorized(this.isAuthorized === 'true');

            return this;
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
                data: {
                    form_key: window.FORM_KEY
                },
                dataType: 'json',
                async: false,
                context: this,
                success: function (response) {
                    user.name(response.result.name);
                    user.email(response.result.email);
                    user.image(response.result.image);
                    this.getUserQuota();
                },
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
                type: 'POST',
                url: stockConfig.quotaUrl,
                data: {
                    form_key: window.FORM_KEY
                },
                dataType: 'json',
                async: false,
                context: this,
                success: function (response) {
                    userQuota.images(response.result.images);
                    userQuota.credits(response.result.credits);
                }.bind(this),
                error: function (response) {
                    return response.message;
                }.bind(this)
            });
        },

        /**
         * Logout from adobe account
         */
        logout: function () {
            $(this.adobeStockModalSelector).trigger('processStart');
            $.ajax({
                type: 'POST',
                url: this.logoutUrl,
                data: {
                    form_key: window.FORM_KEY
                },
                dataType: 'json',
                async: false,
                context: this,
                success: function () {
                    $(this.adobeStockModalSelector).trigger('processStop');
                    user.isAuthorized(false);
                    user.name('');
                    user.email('');
                }.bind(this),
                error: function (response) {
                    $(this.adobeStockModalSelector).trigger('processStop');
                    return response.message;
                }.bind(this)
            });
        }
    });
});
