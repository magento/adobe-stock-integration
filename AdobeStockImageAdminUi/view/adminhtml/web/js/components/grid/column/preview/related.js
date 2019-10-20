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
            // eslint-disable-next-line max-len
            previewProvider: 'name = adobe_stock_images_listing.adobe_stock_images_listing.adobe_stock_images_columns.preview, ns = ${ $.ns }',
            serieFilterValue: '',
            modelFilterValue: '',
            selectedRelatedType: null,
            statefull: {
                serieFilterValue: true,
                modelFilterValue: true
            },
            modules: {
                chips: '${ $.chipsProvider }',
                filterChips: '${ $.filterChipsProvider }',
                preview: '${ $.previewProvider }'
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
                    'selectedRelatedType'
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
         * Returns model to display under the image
         *
         * @param {Object} record
         * @returns {*[]}
         */
        getModel: function (record) {
            return record.model;
        },

        /**
         * Filter images from serie_id
         *
         * @param {Object} record
         */
        seeMoreFromSeries: function(record) {
            this.serieFilterValue(record.id);
            this.filterChips().set('applied', {'serie_id' : record.id.toString()});
        },

        /**
         * Filter images from serie_id
         *
         * @param {Object} record
         */
        seeMoreFromModel: function(record) {
            this.modelFilterValue(record.id);
            this.filterChips().set('applied', {'model_id' : record.id.toString()});
        },

        /**
         * Next related image preview
         *
         * @param {Object} record
         */
        nextRelated: function (record) {
            var relatedList = this.selectedRelatedType() === 'series' ? record.series() : record.model(),
                nextRelatedIndex = _.findLastIndex(relatedList, {id: this.preview().displayedRecord().id}) + 1,
                nextRelated = relatedList[nextRelatedIndex];

            if (typeof nextRelated === 'undefined') {
                return;
            }

            this.switchImagePreviewToRelatedImage(nextRelated, record);
        },

        /**
         * Previous related preview
         *
         * @param {Object} record
         */
        prevRelated: function (record) {
            var relatedList = this.selectedRelatedType() === 'series' ? record.series() : record.model(),
                prevRelatedIndex = _.findLastIndex(relatedList, {id: this.preview().displayedRecord().id}) - 1,
                prevRelated = relatedList[prevRelatedIndex];

            if (typeof prevRelated === 'undefined') {
                return;
            }

            this.switchImagePreviewToRelatedImage(prevRelated, record);
        },

        /**
         * Get previous button disabled
         *
         * @param {Object} record
         *
         * @return {Boolean}
         */
        getPreviousButtonDisabled: function (record) {
            var relatedList, prevRelatedIndex, prevRelated;

            if (!this.selectedRelatedType()) {
                return false;
            }
            relatedList = this.selectedRelatedType() === 'series' ? record.series() : record.model();
            prevRelatedIndex = _.findLastIndex(relatedList, {id: this.preview().displayedRecord().id}) - 1;
            prevRelated = relatedList[prevRelatedIndex];

            if (typeof prevRelated === 'undefined') {
                return true;
            }

            return false;
        },

        /**
         * Get next button disabled
         *
         * @param {Object} record
         *
         * @return {Boolean}
         */
        getNextButtonDisabled: function (record) {
            var relatedList, nextRelatedIndex, nextRelated;

            if (!this.selectedRelatedType()) {
                return false;
            }
            relatedList = this.selectedRelatedType() === 'series' ? record.series() : record.model();
            nextRelatedIndex = _.findLastIndex(relatedList, {id: this.preview().displayedRecord().id}) + 1;
            nextRelated = relatedList[nextRelatedIndex];

            if (typeof nextRelated === 'undefined') {
                return true;
            }

            return false;
        },

        /**
         * Switch image preview to related image
         *
         * @param {Object|null} relatedImage
         */
        switchImagePreviewToRelatedImage: function (relatedImage) {
            if (!relatedImage) {
                this.selectedRelatedType(null);

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
         * @param {Object} series
         * @param {Object} record
         */
        switchImagePreviewToSeriesImage: function (series, record) {
            this.selectedRelatedType('series');
            this.switchImagePreviewToRelatedImage(series, record);
        },

        /**
         * Switch image preview to model image
         *
         * @param {Object} model
         * @param {Object} record
         */
        switchImagePreviewToModelImage: function (model, record) {
            this.selectedRelatedType('model');
            this.switchImagePreviewToRelatedImage(model, record);
        }
    });
});
