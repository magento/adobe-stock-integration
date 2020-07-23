/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/translate',
    'jquery/validate'
], function ($, $t) {
    'use strict';

    $.validator.addMethod(
        'validate-image-name', function (value) {
            return /^[a-zA-Z0-9\-\_]+$/i.test(value);

        }, $t('Please name the file using only letters, numbers, underscores and dashes'));
});
