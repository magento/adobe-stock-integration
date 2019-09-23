/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'jquery',
    'Magento_Ui/js/grid/columns/column',
], function (_, $, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            previewImageSelector: '[data-image-preview]',
            visibility: [],
            height: 0,
            lastOpenedImage: null,
            imports: {
                records: '${ $.provider }:data.items'
            }
        },

        /**
         * Next image preview
         *
         * @param record
         */
        next: function (record){
            var recordToShow = this.records[record._rowIndex + 1];
            recordToShow.rowNumber = record.lastInRow ? record.rowNumber + 1 : record.rowNumber;
            this.show(recordToShow);
        },

        /**
         * Previous image preview
         *
         * @param record
         */
        prev: function (record){
            var recordToShow = this.records[record._rowIndex - 1];
            recordToShow.rowNumber = record.firstInRow ? record.rowNumber - 1 : record.rowNumber;
            this.show(recordToShow);
        },

        /**
         * Set selected row id
         *
         * @param {Number} rowId
         * @private
         */
        _selectRow: function (rowId){
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

            if (record.rowNumber) {
                this._selectRow(record.rowNumber);
            }

            visibility[record._rowIndex] = true;

            this.visibility(visibility);

            img = $(this.previewImageSelector + ' img');
            if(img.get(0).complete) {
                this._updateHeight();
            } else {
                img.load(this._updateHeight.bind(this));
            }
            this.lastOpenedImage = record;
        },

        /**
         * @private
         */
        _updateHeight: function (){
            this.height($(this.previewImageSelector).height() + 'px');
            this.visibility(this.visibility());
            this.scrollToPreview();
        },

        /**
         * Close image preview
         */
        hide: function () {
            var visibility = this.visibility();

            this.lastOpenedImage = null;
            visibility.fill(false);
            this.visibility(visibility);
            this.height(0);
            this._selectRow(null);
        }
    });
});
