/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function (createFolderUrl, paths) {
        var message,
              data = {
                paths: paths
            };

        return $.ajax({
            type: 'POST',
            url: createFolderUrl,
            dataType: 'json',
            showLoader: true,
            data: data,
            context: this
        });
    };
});
