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
            containerId: '#adobe-stock-images-search-modal',
            masonryComponentPath: 'adobe_stock_images_listing.adobe_stock_images_listing.adobe_stock_images_columns',
            dataSourcePath: 'adobe_stock_images_listing.adobe_stock_images_listing_data_source',
            modules: {
                masonry: '${ $.masonryComponentPath }',
                source: '${ $.dataSourcePath }'
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

            $(window).on('fileDeleted.mediabrowser', this.reloadGrid.bind(this));

            return this;
        },

        /**
         * Update listing data
         */
        reloadGrid: function () {
            this.source().set('params.t', Date.now());
        }
    });
});
