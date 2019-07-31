/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([], function () {
    'use strict';

    /**
     * Build window params
     * @param {Object} windowParams
     * @returns {String}
     */
    function buildWindowParams (windowParams) {
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

    return function (authConfig) {
        var authWindow;

        /**
         * If user have access tokens then reject authorization request
         */
        if (authConfig.isAuthorized) {
            return new window.Promise(function (resolve, reject) {
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

        return new window.Promise(function (resolve, reject) {
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
            }

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
                    }, authConfig.stopHandleTimeout || 10000);

                    responseData = authWindow.document.body.innerText.match(authConfig.response.regexpPattern)

                    if (responseData) {
                        stopHandle();
                        if (responseData[authConfig.response.codeIndex] === authConfig.response.successCode) {
                            resolve({
                                isAuthorized: true,
                                lastAuthSuccessMessage: responseData[authConfig.response.messageIndex]
                            });
                        } else {
                            reject(new Error(responseData[authConfig.response.messageIndex]));
                        }
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
