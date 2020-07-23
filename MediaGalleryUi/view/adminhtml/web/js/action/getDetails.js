/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function (imageDetailsUrl, imageIds) {
            var message;

            return $.ajax({
                type: 'GET',
                url: imageDetailsUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'ids': imageIds
                },
                context: this,

                /**
                 * Extract the message and reject
                 *
                 * @param {Object} response
                 */
                error: function (response) {

                    if (typeof response.responseJSON === 'undefined' ||
                        typeof response.responseJSON.message === 'undefined'
                    ) {
                        message = $t('Could not retrieve image details.');
                    } else {
                        message = response.responseJSON.message;
                    }

                    return message;
                }
            });
        };
});
