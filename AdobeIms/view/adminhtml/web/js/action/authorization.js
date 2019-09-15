/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_AdobeIms/js/config',
    'Magento_AdobeIms/js/user'
], function (config, user) {
    'use strict';

    /**
     * Build window params
     * @param {Object} windowParams
     * @returns {String}
     */
    function buildWindowParams(windowParams) {
        var output = '',
            coma = '',
            paramName,
            paramValue;

        for (paramName in windowParams) {
            if (windowParams[paramName]) {
                paramValue = windowParams[paramName];
                output += coma + paramName + '=' + paramValue;
                coma = ',';
            }
        }

        return output;
    }

    return function () {
        var authWindow;

        /**
         * If user have access tokens then reject authorization request
         */
        if (user.isAuthorized()) {
            return new window.Promise(function (resolve, reject) {
                reject(new Error('You are authorized.'));
            });
        }

        /**
         * Opens authorization window with special parameters
         */
        authWindow = window.adobeStockAuthWindow = window.open(
            config.loginUrl,
            '',
            buildWindowParams(config.login.popupWindowParams || {width: 500, height: 300})
        );

        return new window.Promise(function (resolve, reject) {
            var watcherId,
                stopWatcherId;

            /**
             * Stop handle
             */
            function stopHandle() {
                // Clear timers
                clearTimeout(stopWatcherId);
                clearInterval(watcherId);

                // Close window
                authWindow.close();
            }

            /**
             * Start handle
             */
            function startHandle() {
                var responseData;

                if (-1 === String(authWindow.origin).indexOf(window.location.host)) {
                    return;
                }

                /**
                 * If within 10 seconds the result is not received, then reject the request
                 */
                stopWatcherId = setTimeout(function () {
                    stopHandle();
                    reject(new Error('Time\'s up.'));
                }, config.login.popupWindowTimeout || 10000);

                responseData = authWindow.document.body.innerText.match(
                    config.login.callbackParsingParams.regexpPattern
                );
                if (responseData) {
                    stopHandle();

                    if (responseData[config.login.callbackParsingParams.codeIndex] === config.login.callbackParsingParams.successCode) {
                        user.isAuthorized(true);
                        resolve({
                            isAuthorized: true,
                            lastAuthSuccessMessage: responseData[config.login.callbackParsingParams.messageIndex]
                        });
                    } else {
                        reject(new Error(responseData[config.login.callbackParsingParams.messageIndex]));
                    }
                }
            }

            /**
             * Watch a result 1 time per second
             */
            watcherId = setInterval(startHandle, 1000);
        });
    };
});
