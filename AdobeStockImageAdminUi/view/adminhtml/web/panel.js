/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiElement',
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'text!Magento_AdobeStockImageAdminUi/templates/modal/modal-slide.html'
], function (Element, $, $t, modal, slideTpl) {
    'use strict';

    return Element.extend({
        defaults: {
            containerId: '',
            masonryComponentPath: '',
            modules: {
                masonry: '${$.masonryComponentPath}'
            },
        },

        /**
         * Init component
         *
         * @return {exports}
         */
        initialize: function () {
            this._super();

            $(this.containerId).modal({
                type: 'slide',
                buttons: [],
                slideTpl: slideTpl,
                modalClass: 'adobe-stock-modal',
                signInText: $t('Sign in'),
                title: $t('Adobe Stock')
            }).on('openModal', function () {
                this.masonry().setLayoutStyles();
            }.bind(this)).applyBindings();

            return this;
        }
    });
});
