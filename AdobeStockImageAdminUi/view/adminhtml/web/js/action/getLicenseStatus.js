/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (requestUrl, ids) {
        var deferred = $.Deferred();

        $.ajax({
            type: 'GET',
            url: requestUrl + '?ids=' + ids.join(','),
            data: {
                'form_key': window.FORM_KEY
            },
            showLoader: true,
            dataType: 'json',

            /**
             * Resolve with the response result
             *
             * @param {Object} response
             */
            success: function (response) {
                deferred.resolve(response.result);
            },

            /**
             * Reject with the message from response
             *
             * @param {Object} response
             */
            error: function (response) {
                var message = response.message;

                if (response.status === 403) {
                    message = $.mage.__('Your admin role does not have permissions to license an image');
                }

                deferred.reject(message);
            }
        });

        return deferred.promise();
    };
});
