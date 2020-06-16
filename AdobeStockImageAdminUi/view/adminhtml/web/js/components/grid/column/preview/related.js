/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'underscore',
    'jquery'
], function (Component, _, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/grid/column/preview/related',
            filterChipsProvider: 'componentType = filters, ns = ${ $.ns }',
            filterBookmarksSelector: '.admin__data-grid-action-bookmarks',
            tabImagesLimit: 4,
            tabsContainerId: '#adobe-stock-tabs',
            serieFilterValue: '',
            modelFilterValue: '',
            selectedTab: null,
            loader: false,
            relatedImages: {
                series: {},
                model: {}
            },
            statefull: {
                serieFilterValue: true,
                modelFilterValue: true
            },
            modules: {
                chips: '${ $.chipsProvider }',
                filterChips: '${ $.filterChipsProvider }',
                preview: '${ $.parentName }.preview'
            },
            exports: {
                serieFilterValue: '${ $.provider }:params.filters.serie_id',
                modelFilterValue: '${ $.provider }:params.filters.model_id'
            }
        },

        /**
         * Initializes related component.
         */
        initialize: function () {
            this._super();

            this.filterChips().updateActive();

            return this;
        },

        /**
         * Disable keydown event for related content tabs
         */
        disableTabsKeyDownEvent: function () {
            if ($(this.tabsContainerId + ' li[role=tab]').length === 0) {
                setTimeout(function () {
                    this.disableTabsKeyDownEvent();
                }.bind(this), 100);
            } else {
                $(this.tabsContainerId + ' li[role=tab]').unbind('keydown');
            }
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'serieFilterValue',
                    'modelFilterValue',
                    'selectedTab',
                    'relatedImages',
                    'loader'
                ]);

            return this;
        },

        /**
         * Check if visible container
         *
         * @param {Object} record
         * @returns boolean
         */
        isVisible: function (record) {
            return this.showSeriesTab(record) && this.showModelTab(record);
        },

        /**
         * Get image related image series.s
         *
         * @param {Object} record
         */
        loadRelatedImages: function (record) {
            var series = this.getSeries(record),
                model = this.getModel(record);

            if (series && series.length ||
                model && model.length
            ) {
                return;
            }
            $.ajax({
                type: 'GET',
                url: this.preview().relatedImagesUrl,
                dataType: 'json',
                beforeSend: function () {
                    this.loader(true);
                }.bind(this),
                data: {
                    'image_id': record.id,
                    'limit': this.tabImagesLimit
                }
            }).done(function (data) {
                var relatedImages = this.relatedImages();

                this.loader(false);
                relatedImages.series[record.id] = data.result['same_series'];
                relatedImages.model[record.id] = data.result['same_model'];

                this.relatedImages(relatedImages);
                this.preview().updateHeight();

                /* Switch to the model tab if the series tab is hidden */
                if (relatedImages.series[record.id].length === 0 && relatedImages.model[record.id].length > 0) {
                    $('#adobe-stock-tabs').data().mageTabs.select(1);
                }
            }.bind(this));
        },

        /**
         * Returns true if the series tab should be show, false otherwise
         *
         * @param {Object} record
         * @returns boolean
         */
        showSeriesTab: function (record) {
            return typeof this.relatedImages().series[record.id] === 'undefined' ||
                this.relatedImages().series[record.id].length !== 0;
        },

        /**
         * Returns true if the model tab should be show, false otherwise
         *
         * @param {Object} record
         * @returns boolean
         */
        showModelTab: function (record) {
            return typeof this.relatedImages().model[record.id] === 'undefined' ||
                this.relatedImages().model[record.id].length !== 0;
        },

        /**
         * Returns series to display under the image
         *
         * @param {Object} record
         * @returns {*[]}
         */
        getSeries: function (record) {
            return this.relatedImages().series[record.id] || [];
        },

        /**
         * Check if the number of related series image is greater than 4 or not
         *
         * @param {Object} record
         * @returns boolean
         */
        canShowMoreSeriesImages: function (record) {
            return this.getSeries(record).length >= this.tabImagesLimit;
        },

        /**
         * Returns model to display under the image
         *
         * @param {Object} record
         * @returns {*[]}
         */
        getModel: function (record) {
            return this.relatedImages().model[record.id] || [];
        },

        /**
         * Check if the number of related model image is greater than 4 or not
         *
         * @param {Object} record
         * @returns boolean
         */
        canShowMoreModelImages: function (record) {
            return this.getModel(record).length >= this.tabImagesLimit;
        },

        /**
         * Filter images from serie_id
         *
         * @param {Object} record
         */
        seeMoreFromSeries: function (record) {
            if (this.isSerieFilterApplied(record)) {
                this.scrollToFilter();

                return;
            }
            this.serieFilterValue(record.id);
            this.applyFilter('serie_id', record.id.toString());
        },

        /**
         * Filter images from serie_id
         *
         * @param {Object} record
         */
        seeMoreFromModel: function (record) {
            if (this.isModelFilterApplied(record)) {
                this.scrollToFilter();

                return;
            }
            this.modelFilterValue(record.id);
            this.applyFilter('model_id', record.id.toString());
        },

        /**
         * Apply series or model id filter and scroll to top of the page
         *
         * @param {String} typeId
         * @param {String} recordId
         */
        applyFilter: function (typeId, recordId) {
            var data = {};

            data[typeId] = recordId;

            this.filterChips().clear();
            this.filterChips().setData(data, true);
            this.filterChips().apply();

            this.scrollToFilter();
        },

        /**
         * Checks if the filter is applied
         *
         * @param {Object} record
         * @returns {Boolean}
         */
        isSerieFilterApplied: function (record) {
            return this.filterChips().get('applied')['serie_id'] === record.id.toString();
        },

        /**
         * Checks if the filter is applied
         *
         * @param {Object} record
         * @returns {Boolean}
         */
        isModelFilterApplied: function (record) {
            return this.filterChips().get('applied')['model_id'] === record.id.toString();
        },

        /**
         * Scrolls user window to the filter bookmarks
         */
        scrollToFilter: function () {
            $(this.preview().adobeStockModalSelector + ' ' + this.filterBookmarksSelector).get(0).scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });
        },

        /**
         * Next related image preview
         *
         * @param {Object} record
         */
        nextRelated: function (record) {
            var relatedList = this.selectedTab() === 'series' ? this.getSeries(record) : this.getModel(record),
                nextRelatedIndex = _.findLastIndex(
                    relatedList,
                    {
                        id: this.preview().displayedRecord().id
                    }
                ) + 1,
                nextRelated = relatedList[nextRelatedIndex];

            if (typeof nextRelated === 'undefined') {
                return;
            }

            this.switchImagePreviewToRelatedImage(nextRelated);
        },

        /**
         * Previous related preview
         *
         * @param {Object} record
         */
        prevRelated: function (record) {
            var relatedList = this.selectedTab() === 'series' ? this.getSeries(record) : this.getModel(record),
                prevRelatedIndex = _.findLastIndex(
                    relatedList,
                    {
                        id: this.preview().displayedRecord().id
                    }
                ) - 1,
                prevRelated = relatedList[prevRelatedIndex];

            if (typeof prevRelated === 'undefined') {
                return;
            }

            this.switchImagePreviewToRelatedImage(prevRelated);
        },

        /**
         * Get previous button disabled
         *
         * @param {Object} record
         *
         * @return {Boolean}
         */
        cannotViewPrevious: function (record) {
            var relatedList, prevRelatedIndex, prevRelated;

            if (!this.selectedTab()) {
                return false;
            }
            relatedList = this.selectedTab() === 'series' ? this.getSeries(record) : this.getModel(record);
            prevRelatedIndex = _.findLastIndex(
                relatedList,
                {
                    id: this.preview().displayedRecord().id
                }
            ) - 1;
            prevRelated = relatedList[prevRelatedIndex];

            return typeof prevRelated === 'undefined';
        },

        /**
         * Get next button disabled
         *
         * @param {Object} record
         *
         * @return {Boolean}
         */
        cannotViewNext: function (record) {
            var relatedList, nextRelatedIndex, nextRelated;

            if (!this.selectedTab()) {
                return false;
            }
            relatedList = this.selectedTab() === 'series' ? this.getSeries(record) : this.getModel(record);
            nextRelatedIndex = _.findLastIndex(
                relatedList,
                {
                    id: this.preview().displayedRecord().id
                }
            ) + 1;
            nextRelated = relatedList[nextRelatedIndex];

            return typeof nextRelated === 'undefined';
        },

        /**
         * Switch image preview to related image
         *
         * @param {Object|null} relatedImage
         */
        switchImagePreviewToRelatedImage: function (relatedImage) {
            if (!relatedImage) {
                this.selectedTab(null);

                return;
            }

            if (this.preview().displayedRecord().id === relatedImage.id) {
                return;
            }

            this.preview().showRelated(relatedImage);
        },

        /**
         * Switch image preview to series image
         *
         * @param {Object} record
         */
        switchImagePreviewToSeriesImage: function (record) {
            this.selectedTab('series');
            this.switchImagePreviewToRelatedImage(record);
        },

        /**
         * Switch image preview to model image
         *
         * @param {Object} record
         */
        switchImagePreviewToModelImage: function (record) {
            this.selectedTab('model');
            this.switchImagePreviewToRelatedImage(record);
        }
    });
});
