/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_AdobeIms/js/signIn'
], function ($, signIn) {
    'use strict';

    return signIn.extend({

        defaults: {
            userQuota: {},
            quotaUrl: 'adobe_stock/license/quota'
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().observe(['userQuota']);

            return this;
        },

        /**
         * Retrieves full user quota.
         */
        getUserQuota: function () {
            $.ajax({
                type: 'POST',
                url: this.quotaUrl,
                data: {
                    form_key: window.FORM_KEY
                },
                dataType: 'json',
                context: this,
                success: function (response) {
                    this.userQuota(response.result)
                },
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
                type: 'POST',
                url: this.profileUrl,
                data: {
                    form_key: window.FORM_KEY
                },
                dataType: 'json',
                context: this,
                success: function (response) {
                    this.user({
                        isAuthorized: true,
                        name: response.result.name,
                        email: response.result.email,
                        image: response.result.image
                    });
                    this.getUserQuota();
                },
                error: function (response) {
                    return response.message;
                }
            });
        },
    });
});
