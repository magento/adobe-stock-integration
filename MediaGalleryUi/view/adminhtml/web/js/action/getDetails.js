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
                context: this
            });
        };
});
