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
            containerSelector: '.media-gallery-container',
            masonryComponentPath: 'media_gallery_listing.media_gallery_listing.media_gallery_columns',
            modules: {
                masonry: '${ $.masonryComponentPath }'
            }
        },

        /**
         * Init component
         *
         * @return {exports}
         */
        initialize: function () {
            this._super();

            $(this.containerSelector).applyBindings();
            this.masonry().setLayoutStylesWhenLoaded();

            return this;
        }
    });
});
