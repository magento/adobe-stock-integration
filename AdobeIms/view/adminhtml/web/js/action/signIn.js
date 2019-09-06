/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'uiComponent',
    'jquery',
    'Magento_AdobeIms/js/action/authorization',
    'underscore',
    'Magento_AdobeStockImageAdminUi/js/components/grid/column/image-preview'
], function (ko, Component, $, authorizationAction, _, imagePreview) {
    'use strict';

    return Component.extend({

        defaults: {
            isAuthorized: ko.observable(false),
            visibility: ko.observable(true),
            nameVisibility: ko.observable(false),
            displayName: ko.observable(),
            fullName: ko.observable(),
            email:  ko.observable(),
            imagesAvailable:  ko.observable(0),
            credits: ko.observable(0),
            getUserDataUrl: '',
            getSignOutUrl: '',
            userData: '',
            authConfig: {
                url: '',
                isAuthorized: false,
                stopHandleTimeout: 10000,
                windowParams: {
                    width: 500,
                    height: 600,
                    top: 100,
                    left: 300
                },
                response: {
                    regexpPattern: /auth\[code=(success|error);message=(.+)\]/,
                    codeIndex: 1,
                    messageIndex: 2,
                    nameIndex: 3,
                    successCode: 'success',
                    errorCode: 'error'
                }
            },
        },

        initialize: function () {
            this._super();
            this.observe([
                'visibility',
                'nameVisibility',
                'displayName'
            ]);
            this.checkAuthorize();
            this.displayName(this.userData['display_name']);
            imagePreview().isAuthorized.subscribe(function () {
                if (imagePreview().isAuthorized() === true) {
                    this.authConfig.isAuthorized = true;
                    this.getUserData();
                    this.checkAuthorize();
                }
            }.bind(this));
            return this;
        },

        /**
         * Check if user authorized, to show or hide sign in button.
         */
        checkAuthorize: function () {
            if (this.authConfig.isAuthorized) {
                this.visibility(false);
                this.nameVisibility(true);
                this.displayName(this.userData['display_name']);
                this.fullName(this.userData['full_name']);
                this.email(this.userData['email']);
                imagePreview().isAuthorized(true);
            } else if (!this.authConfig.isAuthorized) {
                this.isAuthorized(false);
                imagePreview().isAuthorized(false);
                this.visibility(true);
                this.nameVisibility(false);
            }
        },

        /**
         * Authorization process.
         */
        execute: function () {
            return authorizationAction(this.authConfig)
                .then(
                    function (authConfig) {
                        this.authConfig = _.extend(this.authConfig, authConfig);
                        this.checkAuthorize();
                        return this.authConfig.isAuthorized;
                    }.bind(this)
                ).catch(
                    function (error) {
                        return error;
                    }.bind(this)
                );
        },

        /**
         * Sign out user from adobeSDK
         */
        signOut: function () {
            $.ajax(
                {
                    type: 'POST',
                    url: this.getSignOutUrl,
                    data: {form_key: window.FORM_KEY},
                    dataType: 'json',
                    async: false,
                    context: this,
                    success: function  ()  {
                        this.authConfig.isAuthorized = false;
                        this.checkAuthorize();
                    },
                    error: function (response) {
                        return response.message;
                    }
                });

        },

        /**
         * Retrieve data to authorized user.
         *
         * @return array
         */
        getUserData: function () {
            $.ajax(
                {
                    type: 'POST',
                    url: this.getUserDataUrl,
                    data: {form_key: window.FORM_KEY},
                    dataType: 'json',
                    async: false,
                    context: this,
                    success: function  (response)  {
                        this.userData = response.result;
                    },
                    error: function (response) {
                        return response.message;
                    }
                });
        },

    });

});
