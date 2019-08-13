/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'jquery',
    'knockout',
    'Magento_Ui/js/grid/columns/column',
], function (_, $, ko, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            previewImageSelector: '[data-image-preview]',
            visibility: [],
            height: 0,
            lastOpenedImage: null,
        },

        /**
         * Next image preview
         *
         * @param record
         */
        next: function (record){
            this._selectRow(record.lastInRow ? record.currentRow + 1 : record.currentRow);
            this.show(record._rowIndex + 1);
        },

        /**
         * Previous image preview
         *
         * @param record
         */
        prev: function (record){
            this._selectRow(record.firstInRow ? record.currentRow - 1 : record.currentRow);
            this.show(record._rowIndex - 1);
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
         * @param {Object|Number} record
         */
        show: function (record) {
            var visibility = this.visibility(),
                img;

            this.lastOpenedImage = null;
            if(~visibility.indexOf(true)) {// hide any preview
                if(!Array.prototype.fill) {
                    visibility = _.times(visibility.length, _.constant(false));
                } else {
                    visibility.fill(false);
                }
            }
            if(this._isInt(record)) {
                visibility[record] = true;
            } else {
                this._selectRow(record.currentRow);
                visibility[record._rowIndex] = true;
            }
            this.visibility(visibility);

            img = $(this.previewImageSelector + ' img');
            if(img.get(0).complete) {
                this._updateHeight();
            } else {
                img.load(this._updateHeight.bind(this));
            }
            this.lastOpenedImage = this._isInt(record) ? record : record._rowIndex;
        },

        /**
         * @private
         */
        _updateHeight: function (){
            this.height($(this.previewImageSelector).height() + 'px');// set height
            this.visibility(this.visibility());// rerender
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
        },

        /**
         * Check if value is integer
         *
         * @param value
         * @returns {boolean}
         * @private
         */
        _isInt: function (value) {
            return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
        },

    });
});
