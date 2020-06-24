/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (requestUrl, adobeAssetId, destinationPath) {
        var deferred = $.Deferred();

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
             * Resolve on success
             */
            success: function () {
                deferred.resolve();
            },

            /**
             * Extract the error message and reject
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
                deferred.reject(message);
            }
        });

        return deferred.promise();
    };
});
