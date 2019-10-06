/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiElement',
    'jquery',
    'mage/translate',
    'Magento_AdobeStockImageAdminUi/js/config'
], function (Element, $, $t, config) {
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

            config.downloadPreviewUrl = this.downloadPreviewUrl;
            config.licenseAndDownloadUrl = this.licenseAndDownloadUrl;
            config.buyCreditsUrl = this.buyCreditsUrl;
            config.confirmationUrl = this.confirmationUrl;
            config.relatedImagesUrl = this.relatedImagesUrl;

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
