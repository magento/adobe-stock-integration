/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([], function () {
    'use strict';
    /**
     * Consts of response
     *
     * RESPONSE_PATTERN - pattern of response
     * RESPONSE_CODE_INDEX index of response code
     * RESPONSE_MESSAGE_INDEX index of response message
     * RESPONSE_SUCCESS_CODE success code
     */
    var RESPONSE_PATTERN = /auth\[code=(success|error);message=(.+)\]/,
        RESPONSE_CODE_INDEX = 1,
        RESPONSE_MESSAGE_INDEX = 2,
        RESPONSE_SUCCESS_CODE = 'success';

    /**
     * Build window params
     *
     * @param {Object} windowParams
     * @returns {String}
     */
    function buildWindowParams (windowParams) {
        var output = '',
            coma = '',
            paramName,
            paramValue;

        for (paramName in windowParams) {
            paramValue = windowParams[paramName];
            output += coma + paramName + '=' + paramValue;
            coma = ',';
        }

        return output;
    };

    /**
     * Authorization process
     *
     * @param {Object} authConfig
     * @return {Promise}
     */
    function authorizationProcess (authConfig) {
        var authWindow;

        /**
         * If user have access tokens then reject authorization request
         */
        if (authConfig.isAuthorized) {
            return new Promise(function (resolve, reject) {
                reject(new Error('You are authorized.'));
            });
        }

        /**
         * Opens authorization window with special parameters
         */
        authWindow = window.adobeStockAuthWindow = window.open(
            authConfig.url,
            '',
            buildWindowParams(authConfig.windowParams || {width: 500, height: 300})
        );

        return new Promise(function (resolve, reject) {
            var watcherId,
                stopWatcherId;

            /**
             * Stop handle
             */
             function stopHandle () {
                // Clear timers
                clearTimeout(stopWatcherId);
                clearInterval(watcherId);

                // Close window
                authWindow.close();
            };

            /**
             * Start handle
             */
            function startHandle () {
                var responseData;
                if (-1 !== String(authWindow.origin).indexOf(window.location.host)) {
                    /**
                     * If within 10 seconds the result is not received, then reject the request
                     */
                    stopWatcherId = setTimeout(function () {
                        stopHandle();
                        reject(new Error('Time\'s up.'));
                    }, 10000);

                    if (responseData = authWindow.document.body.innerText.match(RESPONSE_PATTERN)) {
                        stopHandle();

                        if (responseData[RESPONSE_CODE_INDEX] === RESPONSE_SUCCESS_CODE) {
                            resolve({
                                isAuthorized: true,
                                lastAuthSuccessMessage: responseData[RESPONSE_MESSAGE_INDEX]
                            });
                        } else {
                            reject(new Error(responseData[RESPONSE_MESSAGE_INDEX]));
                        }
                    }
                }
            };

            /**
             * Watch a result 1 time per second
             */
            watcherId = setInterval(startHandle, 1000);
        });
    };

    return authorizationProcess;
});
