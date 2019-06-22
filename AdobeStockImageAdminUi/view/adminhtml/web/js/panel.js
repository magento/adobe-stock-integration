/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/translate',
    'mageUtils',
    'Magento_Ui/js/modal/modal'
], function ($, $t, utils) {
    'use strict';

    return function (config) {
        new Ajax.Request(config.ajaxUrl, {
            parameters: {},
            onComplete: function (transport) {
                var html = utils.copy(transport.responseText);
                jQuery('<div id="' + config.modalWindowId + '">' + html + '</div>').modal({
                    type: 'slide',
                    buttons: [],
                    title: $t('Adobe Stock')
                }).applyBindings();
            }.bind(this)
        });
    }

});