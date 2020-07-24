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
                    message = $t('Could not create the directory.');
                } else {
                    message = response.responseJSON.message;
                }

                return message;
            }
        });
    };
});
