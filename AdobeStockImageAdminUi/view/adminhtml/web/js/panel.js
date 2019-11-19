/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiElement',
    'jquery',
    'mage/translate'
], function (Element, $, $t) {
    'use strict';

    return Element.extend({
        defaults: {
            containerId: '',
            masonryComponentPath: '',
            modules: {
                masonry: '${$.masonryComponentPath}'
            }
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
                this.masonry().setLayoutStylesWhenLoaded();
            }.bind(this)).applyBindings();

            return this;
        }
    });
});
