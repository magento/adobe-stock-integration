/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'jquery',
    'Magento_Ui/js/grid/columns/column'
], function (_, $, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            previewImageSelector: '[data-image-preview]',
            visibility: [],
            height: 0,
            previewStyles: {},
            displayedRecord: {},
            lastOpenedImage: null,
            modules: {
                masonry: '${ $.parentName }',
                thumbnailComponent: '${ $.parentName }.thumbnail_url',
            },
            statefull: {
                visible: true,
                sorting: true,
                lastOpenedImage: true
            },
            listens: {
                '${ $.provider }:params.filters': 'hide',
                '${ $.provider }:params.search': 'hide'
            }
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'visibility',
                    'height',
                    'previewStyles',
                    'displayedRecord',
                    'lastOpenedImage'
                ]);
            this.height.subscribe(function () {
                this.thumbnailComponent().previewHeight(this.height());
            }, this);

            return this;
        },

        /**
         * Next image preview
         *
         * @param {Object} record
         */
        next: function (record) {
            var recordToShow = this.getRecord(record._rowIndex + 1);
            recordToShow.rowNumber = record.lastInRow ? record.rowNumber + 1 : record.rowNumber;
            this.show(recordToShow);
        },

        /**
         * Previous image preview
         *
         * @param {Object} record
         */
        prev: function (record) {
            var recordToShow = this.getRecord(record._rowIndex - 1);
            recordToShow.rowNumber = record.firstInRow ? record.rowNumber - 1 : record.rowNumber;
            this.show(recordToShow);
        },

        /**
         * Get record
         *
         * @param {Integer} recordIndex
         *
         * @return {Object}
         */
        getRecord: function (recordIndex) {
            return this.masonry().rows()[recordIndex];
        },

        /**
         * Set selected row id
         *
         * @param {Number} rowId
         * @private
         */
        _selectRow: function (rowId) {
            this.thumbnailComponent().previewRowId(rowId);
        },

        /**
         * Show image preview
         *
         * @param {Object} record
         */
        show: function (record) {
            var visibility = this.visibility(),
                img;

            this.hide();
            this.displayedRecord(record);

            if (record.rowNumber) {
                this._selectRow(record.rowNumber);
            }

            visibility[record._rowIndex] = true;

            this.visibility(visibility);

            img = $(this.previewImageSelector + ' img');

            if (img.get(0).complete) {
                setTimeout(function () {
                    this.updateHeight();
                    this.scrollToPreview();
                }.bind(this), 100);
            } else {
                img.load(function () {
                    this.updateHeight();
                    this.scrollToPreview();
                }.bind(this));
            }
            this.lastOpenedImage(record._rowIndex);
        },

        /**
         * Update image preview section height
         */
        updateHeight: function () {
            this.height($(this.previewImageSelector).height() + 'px');
            this.visibility(this.visibility());
        },

        /**
         * Close image preview
         */
        hide: function () {
            var visibility = this.visibility();

            this.lastOpenedImage(null);
            visibility.fill(false);
            this.visibility(visibility);
            this.height(0);
            this._selectRow(null);
        },

        /**
         * Returns visibility for given record.
         *
         * @param {Object} record
         * @return {*|boolean}
         */
        isVisible: function (record) {
            if (this.lastOpenedImage() === record._rowIndex
                && (
                    this.visibility()[record._rowIndex] === undefined
                    || this.visibility()[record._rowIndex] === false
                )
            ) {
                this.show(record);
            }
            return this.visibility()[record._rowIndex] || false;
        },

        /**
         * Get styles for preview
         *
         * @returns {Object}
         */
        getStyles: function () {
            this.previewStyles({
                'margin-top': '-' + this.height()
            });

            return this.previewStyles();
        },

        /**
         * Scroll to preview window
         */
        scrollToPreview: function () {
            $(this.previewImageSelector).get(0).scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });
        }
    });
});
