/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'uiLayout',
    'Magento_AdobeUi/js/components/grid/column/image-preview'
], function ($, layout, imagePreview) {
    'use strict';

    return imagePreview.extend({
        defaults: {
            downloadImagePreviewUrl: 'adobe_stock/preview/download',
            licenseAndDownloadUrl: 'adobe_stock/license/license',
            confirmationUrl: 'adobe_stock/license/confirmation',
            relatedImagesUrl: 'adobe_stock/preview/relatedimages',
            buyCreditsUrl: 'https://stock.adobe.com/',
            mediaGallerySelector: '.media-gallery-modal:has(#search_adobe_stock)',
            adobeStockModalSelector: '#adobe-stock-images-search-modal',
            modules: {
                keywords: '${ $.name }_keywords',
                related: '${ $.name }_related',
                actions: '${ $.name }_actions'
            },
            viewConfig: [
                {
                    component: 'Magento_AdobeStockImageAdminUi/js/components/grid/column/preview/keywords',
                    name: '${ $.name }_keywords'
                },
                {
                    component: 'Magento_AdobeStockImageAdminUi/js/components/grid/column/preview/related',
                    name: '${ $.name }_related'
                },
                {
                    component: 'Magento_AdobeStockImageAdminUi/js/components/grid/column/preview/actions',
                    name: '${ $.name }_actions'
                }
            ]
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super().initView();

            return this;
        },

        /**
         * Initialize child components
         *
         * @returns {Object}
         */
        initView: function () {
            layout(this.viewConfig);

            return this;
        },

        /**
         * Get previous button disabled
         *
         * @param {Object} record
         *
         * @return {Boolean}
         */
        getPreviousButtonDisabled: function (record) {
            return this.related().getPreviousButtonDisabled(record);
        },

        /**
         * Get next button disabled
         *
         * @param {Object} record
         *
         * @return {Boolean}
         */
        getNextButtonDisabled: function (record) {
            return this.related().getNextButtonDisabled(record);
        },

        /**
         * @inheritdoc
         */
        next: function (record) {
            if (this.related().selectedRelatedType()) {
                this.related().nextRelated(record);

                return;
            }
            this.keywords().hideAllKeywords();
            this._super(record);
        },

        /**
         * @inheritdoc
         */
        prev: function (record) {
            if (this.related().selectedRelatedType()) {
                this.related().prevRelated(record);

                return;
            }
            this.keywords().hideAllKeywords();
            this._super(record);
        },

        /**
         * @inheritdoc
         */
        show: function (record) {
            this.related().selectedRelatedType(null);
            this.related().initRecord(record);
            this.keywords().hideAllKeywords();
            this.displayedRecord(record);
            this._super(record);
            this.loadRelatedImages(record);
        },

        /**
         * Show related image data in the preview section
         *
         * @param {Object} record
         */
        showRelated: function (record) {
            this.keywords().hideAllKeywords();
            this.displayedRecord(record);
            this.updateHeight();
        },

        /**
         * Get image related image series.
         *
         * @param {Object} record
         */
        loadRelatedImages: function (record) {
            if (record.series && record.model &&
                record.series() && record.model() &&
                record.series().length && record.model().length) {
                return;
            }
            $.ajax({
                type: 'GET',
                url: this.relatedImagesUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'image_id': record.id,
                    'limit': 4
                }
            }).done(function (data) {
                record.series(data.result.same_series);
                record.model(data.result.same_model);
                this.updateHeight();
            }.bind(this));
        },

        /**
         * Returns attributes to display under the preview image
         *
         * @returns {*[]}
         */
        getDisplayAttributes: function () {
            return [
                {
                    name: 'Dimensions',
                    value: this.displayedRecord().width + ' x ' + this.displayedRecord().height + ' px'
                },
                {
                    name: 'File type',
                    value: this.displayedRecord().content_type.toUpperCase()
                },
                {
                    name: 'Category',
                    value: this.displayedRecord().category.name || 'None'
                },
                {
                    name: 'File #',
                    value: this.displayedRecord().id
                }
            ];
        }
    });
});
