/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (imageDetailsUrl, imageId) {
        return new window.Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: imageDetailsUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'id': imageId
                },
                context: this,

                /**
                 * Success handler for deleting image
                 *
                 * @param {Object} response
                 */
                success: function (response) {
                    if (response.success) {
                        resolve(response.imageDetails);

                        return;
                    }

                    reject(response.message);
                },

                /**
                 * Error handler for deleting image
                 *
                 * @param {Object} response
                 */
                error: function (response) {
                    var message;

                    if (typeof response.responseJSON === 'undefined' ||
                        typeof response.responseJSON.message === 'undefined'
                    ) {
                        message = $.mage.__('Could not retrieve image details.');
                    } else {
                        message = response.responseJSON.message;
                    }
                    reject(message);
                }
            });
        });
    }
});
