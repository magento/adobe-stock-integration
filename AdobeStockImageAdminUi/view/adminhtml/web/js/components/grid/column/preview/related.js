/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'underscore',
    'jquery',
    'ko',
    'mage/backend/tabs'
], function (Component, _, $, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/grid/column/preview/related',
            filterChipsProvider: 'componentType = filters, ns = ${ $.ns }',
            serieFilterValue: '',
            modelFilterValue: '',
            relatedPreviewLimit: 4,
            selectedTab: null,
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
         * @param {Object} record
         */
        initRecord: function (record) {
            if (!record.model || !record.series) {
                record.series = ko.observable([]);
                record.model = ko.observable([]);
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
                    'selectedTab'
                ]);

            return this;
        },

        /**
         * Returns series to display under the image
         *
         * @param {Object} record
         * @returns {*[]}
         */
        getSeries: function (record) {
            return record.series;
        },

        /**
         * Check if the number of related series image is greater than 4 or not
         *
         * @param {Object} record
         * @returns boolean
         */
        canShowMoreSeriesImages: function (record) {
            return parseInt(record.series().length, 10) >= this.relatedPreviewLimit;
        },

        /**
         * Returns model to display under the image
         *
         * @param {Object} record
         * @returns {*[]}
         */
        getModel: function (record) {
            return record.model;
        },

        /**
         * Check if the number of related model image is greater than 4 or not
         *
         * @param {Object} record
         * @returns boolean
         */
        canShowMoreModelImages: function (record) {
            return parseInt(record.model().length, 10) >= this.relatedPreviewLimit;
        },

        /**
         * Filter images from serie_id
         *
         * @param {Object} record
         */
        seeMoreFromSeries: function (record) {
            this.serieFilterValue(record.id);
            this.filterChips().set(
                'applied',
                {
                    'serie_id': record.id.toString()
                }
            );
        },

        /**
         * Filter images from serie_id
         *
         * @param {Object} record
         */
        seeMoreFromModel: function (record) {
            this.modelFilterValue(record.id);
            this.filterChips().set(
                'applied',
                {
                    'model_id': record.id.toString()
                }
            );
        },

        /**
         * Next related image preview
         *
         * @param {Object} record
         */
        nextRelated: function (record) {
            var relatedList = this.selectedTab() === 'series' ? record.series() : record.model(),
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
            var relatedList = this.selectedTab() === 'series' ? record.series() : record.model(),
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
            relatedList = this.selectedTab() === 'series' ? record.series() : record.model();
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
            relatedList = this.selectedTab() === 'series' ? record.series() : record.model();
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
