/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery/validate',
    'mage/translate'
], function ($) {
    'use strict';

    $.validator.addMethod(
        'validate-image-name', function (value) {
            return /^[a-zA-Z0-9\-\_]+$/i.test(value);

        }, $.mage.__('Letters, numbers or dash only please'));
});
