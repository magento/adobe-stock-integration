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
            data: formData
        });
    };
});
