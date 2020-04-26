/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (requestUrl, ids) {
        return new window.Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: requestUrl + '?ids=' + ids.join(','),
                data: {
                    'form_key': window.FORM_KEY
                },
                dataType: 'json',

                /**
                 * Resolve with the response result
                 *
                 * @param {Object} response
                 */
                success: function (response) {
                    resolve(response.result);
                },

                /**
                 * Reject with the message from response
                 *
                 * @param {Object} response
                 */
                error: function (response) {
                    reject(response.message);
                }
            });
        });
    };
});
