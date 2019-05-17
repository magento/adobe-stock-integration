/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

    return function (config, element) {
        $(element).modal({type: 'slide'}).applyBindings();
    }
});
