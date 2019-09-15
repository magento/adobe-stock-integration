/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'jquery',
    'Magento_AdobeIms/js/action/authorization',
    'Magento_AdobeIms/js/config',
    'Magento_AdobeIms/js/user'
], function (Component, $, login, config, user) {
    'use strict';

    return Component.extend({

        defaults: {
            adobeStockModalSelector: '#adobe-stock-images-search-modal',
            profileUrl: 'adobe_ims/user/profile',
            loginUrl: 'https://ims-na1.adobelogin.com/ims/authorize',
            logoutUrl: 'adobe_ims/user/logout',
            userName: '',
            userEmail: '',
            isAuthorized: false
        },

        user: user,
        login: login,

        initialize: function () {
            this._super();

            config.profileUrl = this.profileUrl;
            config.loginUrl = this.loginUrl;
            config.logoutUrl = this.logoutUrl;

            user.isAuthorized.subscribe(function () {
                if (user.isAuthorized() && user.name() === '') {
                    this.loadUserProfile();
                }
            }.bind(this));

            user.name(this.userName);
            user.email(this.userEmail);
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
                url: config.profileUrl,
                data: {form_key: window.FORM_KEY},
                dataType: 'json',
                async: false,
                context: this,
                success: function (response) {
                    user.name(response.result.name);
                    user.email(response.result.email);
                },
                error: function (response) {
                    return response.message;
                }
            });
        },

        /**
         * Logout from adobe account
         */
        logout: function () {
            $(this.adobeStockModalSelector).trigger('processStart');
            $.ajax({
                type: 'POST',
                url: config.logoutUrl,
                data: {form_key: window.FORM_KEY},
                dataType: 'json',
                async: false,
                context: this,
                success: function ()  {
                    $(this.adobeStockModalSelector).trigger('processStop');
                    user.isAuthorized(false);
                }.bind(this),
                error: function (response) {
                    $(this.adobeStockModalSelector).trigger('processStop');
                    return response.message;
                }.bind(this)
            });
        }
    });
});
