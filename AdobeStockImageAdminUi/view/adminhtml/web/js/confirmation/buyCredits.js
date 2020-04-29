/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirm) {
    'use strict';

    return function (buyCreditsUrl, recordTitle, quotaMessage) {
        var confirmationContent = $.mage.__('License "' + recordTitle + '"'),
            content = '<p>' + confirmationContent + '</p><p><b>' + quotaMessage + '</b></p><br>';

        confirm({
            title: $.mage.__('License Adobe Stock Images?'),
            content: content,
            buttons: [{
                text: $.mage.__('Cancel'),
                class: 'action-secondary action-dismiss',

                /**
                 * Close modal
                 */
                click: function () {
                    this.closeModal();
                }
            },{
                text: $.mage.__('Buy Credits'),
                class: 'action-primary action-accept',

                /**
                 * Close modal
                 */
                click: function () {
                    window.open(buyCreditsUrl);
                    this.closeModal();
                }
            }]
        });
    };
});
