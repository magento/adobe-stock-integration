/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiElement',
    'jquery',
    'mage/translate',
    'Magento_AdobeStockAdminUi/js/config',
    'Magento_AdobeStockAdminUi/js/user-quota',
], function (Element, $, $t, config, userQuota) {
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

            config.downloadPreviewUrl = this.downloadPreviewUrl;
            config.licenseAndDownloadUrl = this.licenseAndDownloadUrl;
            config.quotaUrl = this.quotaUrl;
            config.relatedImagesUrl = this.relatedImagesUrl;

            userQuota.images(this.userQuota.images);
            userQuota.credits(this.userQuota.credits);

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
