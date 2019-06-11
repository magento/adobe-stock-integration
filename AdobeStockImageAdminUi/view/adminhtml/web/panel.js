/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal'
], function ($, $t) {
    'use strict';

    return function (config, element) {
        $(element).modal({
            type: 'slide',
            buttons: [],
            title: $t('Adobe Stock')
        }).applyBindings();
    };
});
