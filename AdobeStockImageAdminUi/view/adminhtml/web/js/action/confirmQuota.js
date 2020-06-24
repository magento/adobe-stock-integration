/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (requestUrl, adobeAssetId) {
        var deferred = $.Deferred();

        $.ajax({
            type: 'GET',
            url: requestUrl,
            dataType: 'json',
            data: {
                'media_id': adobeAssetId
            },
            showLoader: true,

            /**
             * Extract the data from the response and resolve
             *
             * @param {Object} response
             */
            success: function (response) {
                deferred.resolve({
                    canLicense: response.result.canLicense,
                    message: response.result.message
                });
            },

            /**
             * Extract the error message and reject
             *
             * @param {Object} response
             */
            error: function (response) {
                var message = response.JSON ? response.JSON.message
                    : $.mage.__('Could not fetch licensing information.');

                if (response.status === 403) {
                    message = $.mage.__('Your admin role does not have permissions to license an image');
                }

                deferred.reject(message);
            }
        });

        return deferred.promise();
    };
});
