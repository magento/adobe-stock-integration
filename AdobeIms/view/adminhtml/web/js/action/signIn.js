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
            profileUrl: 'adobe_ims/user/profile',
            loginUrl: 'https://ims-na1.adobelogin.com/ims/authorize',
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
        }
    });

});
