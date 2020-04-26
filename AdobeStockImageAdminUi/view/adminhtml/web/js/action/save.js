/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (requestUrl, adobeAssetId, destinationPath) {
        return new window.Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                url: requestUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'media_id': adobeAssetId,
                    'destination_path': destinationPath
                },

                /**
                 * Success handler for Adobe Stock preview or licensed asset
                 * download
                 */
                success: function () {
                    resolve();
                },

                /**
                 * Error handler for Adobe Stock preview or licensed asset
                 * download
                 *
                 * @param {Object} response
                 */
                error: function (response) {
                    var message;

                    if (typeof response.responseJSON === 'undefined' ||
                        typeof response.responseJSON.message === 'undefined'
                    ) {
                        message = 'Could not save the asset!';
                    } else {
                        message = response.responseJSON.message;
                    }
                    reject(message);
                }
            });
        });
    }
});
