/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'uiComponent',
    'jquery'
], function (ko, Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_AdobeStockAdminUi/connection',
            defaultErrorMessage: 'Connection test failed.',
            emptyApiKeyErrorMessage: 'Please enter an API Key and then test the connection',
            apiKeyInputId: 'system_adobe_stock_integration_api_key',
            url: '',
            serverSuccess: false
        },
        success: ko.observable(false),
        message: ko.observable(''),
        visible: ko.observable(false),

        /**
         * @override
         */
        initialize: function () {
            this._super();
            this.messageClass = ko.computed(function () {
                return 'message-validation message message-' + (this.success() ? 'success' : 'error');
            }, this);

            if (!this.serverSuccess) {
                this.visible(true);
                this.message(this.defaultErrorMessage);
            }
        },

        /**
         * @param {String} success
         * @param {bool} message
         */
        showMessage: function (success, message) {
            this.message(message);
            this.success(success);
            this.visible(true);
        },

        /**
         * Send request to server to test connection to Adobe Stock API and display the result
         */
        testConnection: function () {
            var apiKey = document.getElementById(this.apiKeyInputId).value;

            this.visible(false);

            if (apiKey.length === 0) {
                this.showMessage(false, this.emptyApiKeyErrorMessage);
            } else {
                $.ajax({
                    type: 'POST',
                    url: this.url,
                    dataType: 'json',
                    data: {
                        'api_key': apiKey
                    },
                    success: $.proxy(function (response) {
                        this.showMessage(response.success === true, response.message);
                    }, this),
                    error: $.proxy(function () {
                        this.showMessage(false, this.defaultErrorMessage);
                    }, this)
                });
            }
        }
    });
});
