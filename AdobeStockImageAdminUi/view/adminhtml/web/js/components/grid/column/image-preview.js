/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column',
    'underscore',
    'jquery',
    'knockout'
], function (Column, _, $, ko) {
    'use strict';

    return Column.extend({
        defaults: {
            visibility: [],
            height: 0,
            saveAvailable: true,
            downloadImagePreviewUrl: Column.downloadImagePreviewUrl,
            modules: {
                thumbnailComponent: '${ $.parentName }.thumbnail_url'
            },
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'visibility',
                    'height'
                ]);

            this.height.subscribe(function(){
                this.thumbnailComponent().previewHeight(this.height());
            }, this);
            return this;
        },

        /**
         * Return id of the row.
         *
         * @param record
         * @returns {*}
         */
        getId: function (record) {
            return record.id;
        },

        /**
         * Returns url to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getUrl: function (record) {
            return record.preview_url;
        },

        /**
         * Returns title to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getTitle: function (record) {
            return record.title || 'Title';
        },

        /**
         * Returns author full name to given record.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getAuthor: function (record) {
            return record.author || 'Author';
        },

        /**
         * Returns visibility for given record.
         *
         * @param {Object} record
         * @return {*|boolean}
         */
        isVisible: function (record) {
            return this.visibility()[record._rowIndex] || false;
        },

        /**
         * Get styles for preview
         *
         * @param {Object} record
         * @returns {Object}
         */
        getStyles: function (record){
            if(!record.previewStyles) {
                record.previewStyles = ko.observable();
            }
            record.previewStyles({
                'margin-top': '-' + this.height()
            });
            return record.previewStyles;
        },

        /**
         * Next image preview
         *
         * @param record
         */
        next: function(record){
            this._selectRow(record.lastInRow ? record.currentRow + 1 : record.currentRow);
            this.show(record._rowIndex + 1);
        },

        /**
         * Previous image preview
         *
         * @param record
         */
        prev: function(record){
            this._selectRow(record.firstInRow ? record.currentRow - 1 : record.currentRow);
            this.show(record._rowIndex - 1);
        },

        /**
         * Set selected row id
         *
         * @param {Number} rowId
         * @param {Number} [height]
         * @private
         */
        _selectRow(rowId, height){
            this.thumbnailComponent().previewRowId(rowId);
        },

        /**
         * Show image preview
         *
         * @param {Object|Number} record
         */
        show: function (record) {
            var visibility = this.visibility();
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

            var $img = $('[data-image-preview] img');
            if($img.get(0).complete) {
                this._updateHeight();
            } else {
                $img.load(this._updateHeight.bind(this));
            }
        },

        /**
         *
         * @private
         */
        _updateHeight: function (){
            var $preview = $('[data-image-preview]');
            this.height($preview.height() + 'px');// set height
            this.visibility(this.visibility());// rerender
            $preview.get(0).scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"});// update scroll if needed
        },

        /**
         * Close image preview
         *
         * @param {Object} record
         */
        hide: function (record) {
            var visibility = this.visibility();
            visibility[record._rowIndex] = false;
            this.visibility(visibility);
            this.height(0);
            this._selectRow(null, 0);
        },

        /**
         * Check if value is integer
         *
         * @param value
         * @returns {boolean}
         * @private
         */
        _isInt: function(value) {
            return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
        },

        /**
         * Save record as image
         *
         * @param record
         */
        save: function(record) {
            // update modal with an image url
            var image_url = record.preview_url;
            var targetEl = $('.media-gallery-modal:has(#search_adobe_stock)').data('mageMediabrowser').getTargetElement();
            targetEl.val(image_url).trigger('change');
            // close insert image panel
            window.MediabrowserUtility.closeDialog();
            targetEl.focus();
            $(targetEl).change();
            // close adobe panel
            $("#adobe-stock-images-search-modal").trigger('closeModal');
        },

        download: function (record) {
            //@TODO add a logic for getting the target path
            var destinationPath = '';
            var postData = {
                'media_id': record.id,
                'destination_path': destinationPath
            };
            $('#' + record.id).text('');
            $.ajax({
                       type: "POST",
                       url: this.downloadImagePreviewUrl,
                       dataType: 'json',
                       data: postData,
                       success: function (response) {
                           var successMessage = '<div class="messages"><div class="message message-success success">' +
                                                response.message +
                                         '<div data-ui-id="messages-message-success"></div></div></div>';
                           $('#' + record.id).append(successMessage);
                       },
                       error: function (response) {
                           var errorMessage = '<div class="messages"><div class="message message-error error">' +
                                              response.responseJSON.error_message +
                                         '<div data-ui-id="messages-message-error"></div></div></div>';
                           $('#' + record.id).append(errorMessage);
                       }
                   });
        }
    });
});
