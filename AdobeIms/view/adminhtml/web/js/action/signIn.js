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
], function (ko, Component, $, authorizationAction, _) {
    'use strict';

    return Component.extend({

        defaults: {
            visibility: ko.observable(true),
            nameVisibility: ko.observable(false),
            displayName: ko.observable(),
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
                    regexpPattern: /auth\[code=(success|error);message=(.+);name=(.+)\]/,
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
            this.displayName(this.authConfig.displayName);
            return this;
        },

        /**
         * Check if user authorized, to show or hide sign in button.
         */
        checkAuthorize: function () {
                if (this.authConfig.isAuthorized) {
                    this.visibility(false);
                    this.nameVisibility(true);
                } else if (!this.authConfig.isAuthorized) {
                    this.visibility(true);
                    this.nameVisibility(false);
                }
        },

        /**
         * Authorization process.
         */
        execute: function () {
           return  authorizationAction(this.authConfig)
                .then(
                    function (authConfig) {
                        this.authConfig = _.extend(this.authConfig, authConfig);
                        this.displayName(authConfig.displayName);
                        this.checkAuthorize();
                        return this.authConfig.isAuthorized;
                    }.bind(this)
                ).catch(
                    function (error) {
                        return error;
                    }.bind(this)
                );
        },

    });

});
