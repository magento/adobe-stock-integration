/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'uiLayout',
    'Magento_Ui/js/grid/columns/image-preview'
], function ($, layout, imagePreview) {
    'use strict';

    return imagePreview.extend({
        defaults: {
            downloadImagePreviewUrl: 'adobe_stock/preview/download',
            licenseAndDownloadUrl: 'adobe_stock/license/license',
            saveLicensedAndDownloadUrl: 'adobe_stock/license/saveLicensed',
            confirmationUrl: 'adobe_stock/license/confirmation',
            relatedImagesUrl: 'adobe_stock/preview/relatedimages',
            buyCreditsUrl: 'https://stock.adobe.com/',
            mediaGallerySelector: '.media-gallery-modal:has(#search_adobe_stock)',
            adobeStockModalSelector: '.adobe-search-images-modal',
            activeMediaGallerySelector: 'aside.modal-slide.adobe-stock-modal._show',
            bookmarksProvider: 'componentType = bookmark, ns = ${ $.ns }',
            modules: {
                keywords: '${ $.name }_keywords',
                related: '${ $.name }_related',
                actions: '${ $.name }_actions',
                bookmarks: '${ $.bookmarksProvider }'
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
                    name: '${ $.name }_actions',
                    provider: '${ $.provider }',
                    mediaGallery: '${ $.mediaGalleryComponent }',
                    mediaGalleryName: '${ $.mediaGalleryName }',
                    mediaGalleryProvider: '${ $.mediaGalleryProvider }',
                    mediaGallerySortBy: '${ $.mediaGallerySortBy }',
                    mediaGallerySearchInput: '${ $.mediaGallerySearchInput }',
                    mediaGalleryListingFilters: '${ $.mediaGalleryListingFilters }',
                    getMediaGalleryAsset: '${ $.getMediaGalleryAsset }',
                    imageEditDetailsUrl: '${ $.imageEditDetailsUrl }',
                    listingPaging: '${ $.listingPaging }'
                }
            ],
            listens: {
                '${ $.sortByComponentName }:applied': 'hide',
                '${ $.bookmarksProvider }:activeIndex': 'onActiveIndexChange',
                '${ $.bookmarksProvider }:current': 'onStateChange'
            }
        },

        /**
         * Listener of the activeIndex property.
         */
        onActiveIndexChange: function () {
            if (this.bookmarks().getActiveView().index === 'default') {
                this.hide();

                return;
            }
            this.subscribeImagePreview();
        },

        /**
         * Listener of the activeIndex property.
         * To open image preview with the correct image when switching to a saved view
         * from another page without reverting back to default view.
         */
        onStateChange: function () {
            if (this.bookmarks().getActiveView().index !== 'default') {
                this.subscribeImagePreview();
            }
        },

        /**
         * Subscribe image preview
         */
        subscribeImagePreview: function () {
            var subscription,
                rowIndex,
                record;

            subscription = this.masonry().rows.subscribe(function () {
                subscription.dispose();
                rowIndex = this.lastOpenedImage();

                if (rowIndex === false) {
                    return;
                }

                record = this.masonry().rows()[rowIndex];
                this.hide();
                this.show(record);
            }.bind(this));
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super().initView();
            $(window).on('fileDeleted.enhancedMediaGallery', this.reloadAdobeGrid.bind(this));
            $(window).on('folderDeleted.enhancedMediaGallery', this.reloadAdobeGrid.bind(this));

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
        cannotViewPrevious: function (record) {
            return this.related().cannotViewPrevious(record);
        },

        /**
         * Get next button disabled
         *
         * @param {Object} record
         *
         * @return {Boolean}
         */
        cannotViewNext: function (record) {
            return this.related().cannotViewNext(record);
        },

        /**
         * Return active adobe gallery selector.
         */
        getAdobeModal: function () {
            return $(this.activeMediaGallerySelector).find(this.adobeStockModalSelector);
        },

        /**
         * @inheritdoc
         */
        next: function (record) {
            if (this.related().selectedTab()) {
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
            if (this.related().selectedTab()) {
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
            this.related().selectedTab(null);
            this.keywords().hideAllKeywords();
            this.displayedRecord(record);
            this._super(record);
            this.related().loadRelatedImages(record);
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
         * Returns attributes to display under the preview image
         *
         * @returns {*[]}
         */
        getDisplayAttributes: function () {
            if (!this.displayedRecord()) {
                return [];
            }

            return [
                {
                    name: 'Dimensions',
                    value: this.displayedRecord().width + ' x ' + this.displayedRecord().height + ' px'
                },
                {
                    name: 'File type',
                    value: this.displayedRecord()['content_type'].toUpperCase()
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
        },

        /**
         * Reload Adobe grid after deleting image
         */
        reloadAdobeGrid: function () {
            this.actions().source().reload({
                refresh: true
            });
        }
    });
});
