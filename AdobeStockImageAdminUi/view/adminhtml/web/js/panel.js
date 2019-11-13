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
            var imageIndex, startIndex, endIndex,
                imagePreviewSelector = '.masonry-image-preview',
                imageColumnSelector = '.masonry-image-column',
                adobeModalSelector = '.adobe-stock-modal';

            this._super();

            $(this.containerId).modal({
                type: 'slide',
                buttons: [],
                modalClass: 'adobe-stock-modal',
                title: $t('Adobe Stock')
            }).on('openModal', function () {
                this.masonry().setLayoutStylesWhenLoaded();
            }.bind(this)).applyBindings();

            $(document).on('keydown', function(e) {
                startIndex = 0;
                endIndex = $('.masonry-image-grid')[0].children.length - 1;

                if($(imagePreviewSelector).length > 0) {
                    imageIndex = $(imagePreviewSelector)
                        .parents(imageColumnSelector)
                        .data('repeatIndex');
                }

                if($(adobeModalSelector).hasClass('_show')) {
                    if(e.keyCode === 37 && imageIndex !== startIndex) {
                        $(imagePreviewSelector + ' .action-previous').click();
                    } else if (e.keyCode === 39 && imageIndex !== endIndex) {
                        $(imagePreviewSelector + ' .action-next').click();
                    }
                }
            });

            return this;
        }
    });
});
