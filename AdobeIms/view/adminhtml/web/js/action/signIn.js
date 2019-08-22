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
                    successCode: 'success',
                    errorCode: 'error'
                }
            },
        },

        initialize: function () {
            this._super();
        },

        /**
         * Authorization process.
         */
        execute: function () {
            authorizationAction(this.authConfig)
                .then(
                    function (authConfig) {
                        this.authConfig = _.extend(this.defaults.authConfig, authConfig);

                        console.log('success');
                    }.bind(this)
                )
                .catch(
                    function (error) {
                        console.log('error');
                    }.bind(this)
                )
                .finally((function () {
                    console.log('finally');
                }).bind(this));
        },
    });

});
