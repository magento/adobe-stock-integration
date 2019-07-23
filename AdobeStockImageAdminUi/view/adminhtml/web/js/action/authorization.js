/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([], function () {
    'use strict';
    var buildWindowParams,
        authorizationProcess;

    /**
     * Build window params
     *
     * @param {Object} windowParams
     * @returns {string}
     */
    buildWindowParams = function (windowParams) {
        var output = '',
            paramName,
            paramValue;

        for (paramName in windowParams) {
            paramValue = windowParams[paramName];
            output += paramName + '=' + paramValue;
        }

        return output;
    };

    /**
     * Authorization process
     *
     * @param {Object} authConfig
     * @return {Promise}
     */
    authorizationProcess = function (authConfig) {
        var authWindow;

        /**
         * If user have access tokens then reject authorization request
         */
        if (true === authConfig.isAuthorized) {
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
                stopWatcherId,
                startHandle,
                stopHandle;

            /**
             * Stop handle
             */
            stopHandle = function () {
                // Clear timers
                clearTimeout(stopWatcherId);
                clearInterval(watcherId);

                // Close window
                authWindow.close();

                reject(new Error('Time\'s up.'));
            };

            /**
             * Start handle
             */
            startHandle = function () {
                if (-1 !== (authWindow.origin || '').indexOf(window.location.host)) {
                    /**
                     * If within 10 seconds the result is not received, then reject the request.
                     */
                    stopWatcherId = setTimeout(stopHandle, 10000);

                    if (-1 !== authWindow.document.body.innerText.indexOf('123123123')) {
                        // Clear timers
                        clearTimeout(stopWatcherId);
                        clearInterval(watcherId);

                        // Close window
                        authWindow.close();

                        // Return new authorization configuration
                        resolve({isAuthorized: true});
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
