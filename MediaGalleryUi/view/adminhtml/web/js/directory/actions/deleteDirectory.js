/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function (deleteFolderUrl, path) {
        var message;

        return $.ajax({
            type: 'POST',
            url: deleteFolderUrl,
            dataType: 'json',
            showLoader: true,
            data: {
                path: path
            },
            context: this
        });
    };
});
