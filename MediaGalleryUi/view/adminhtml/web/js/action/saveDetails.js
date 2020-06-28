/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (saveImageDetailsUrl, formElement) {
        var deferred = $.Deferred(),
            message,
            formData = formElement.serialize();

        $.ajax({
            type: 'POST',
            url: saveImageDetailsUrl,
            dataType: 'json',
            showLoader: true,
            data: formData.concat(
                [{
                    name: 'isAjax',
                    value: true
                },
                {
                    name: 'form_key',
                    value: window.FORM_KEY
                }]
            ),

            /**
             * Resolve with image details if success, reject with response message otherwise
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
                    message = $.mage.__('Could not save image details.');
                } else {
                    message = response.responseJSON.message;
                }
                deferred.reject(message);
            }
        });

        return deferred.promise();
    };
});
