/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (deleteFolderUrl, path) {
        var deferred = $.Deferred(),
            message;

        $.ajax({
            type: 'POST',
            url: deleteFolderUrl,
            dataType: 'json',
            showLoader: true,
            data: {
                path: path
            },
            context: this,

            /**
             * Resolve  if delete folder success, reject with response message othervise
             *
             * @param {Object} response
             */
            success: function (response) {
                if (response.success) {
                    deferred.resolve(response.message);

                    return;
                }

                deferred.reject(response.message);
            },

            /**
             * Extract the message and reject
             *
             * @param {Object} response
             */
            error: function (response) {

                if (typeof response.responseJSON === 'undefined' ||
                    typeof response.responseJSON.message === 'undefined'
                ) {
                    message = $.mage.__('Could not delete the directory.');
                } else {
                    message = response.responseJSON.message;
                }
                deferred.reject(message);
            }
        });

        return deferred.promise();
    };
});
