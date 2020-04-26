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
                 * @param {Object} response
                 * @returns void
                 */
                success: function (response) {
                    resolve(response.result);
                },

                /**
                 * @param {Object} response
                 * @returns {String}
                 */
                error: function (response) {
                    reject(response.message);
                }
            });
        });
    }
});
