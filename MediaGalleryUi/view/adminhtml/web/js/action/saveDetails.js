/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function (saveImageDetailsUrl, formElement) {
        var message,
              formData = formElement.serialize();

        return $.ajax({
            type: 'POST',
            url: saveImageDetailsUrl,
            dataType: 'json',
            showLoader: true,
            data: formData,

            /**
             * Extract the message and reject
             *
             * @param {Object} response
             */
            error: function (response) {

                if (typeof response.responseJSON === 'undefined' ||
                    typeof response.responseJSON.message === 'undefined'
                ) {
                    message = $t('Could not save image details.');
                } else {
                    message = response.responseJSON.message;
                }
            }
        });
    };
});
