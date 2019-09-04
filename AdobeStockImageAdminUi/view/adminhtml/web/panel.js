/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiElement',
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal'
], function (Element, $, $t) {
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
                modalClass: 'adobe-stock-modal',
                title: $t('Adobe Stock')
            }).on('openModal', function () {
                this.setStyles();
            }.bind(this)).applyBindings();

            return this;
        },

        /**
         * Apply style after images rendered on page.
         */
        setStyles: function () {
            window.dispatchEvent(new Event('resize'));
            $(document).ajaxComplete(() => {
                setTimeout(() => {
                    this.masonry().setLayoutStyles();
                }, 200);
            });
        }
    });
});
