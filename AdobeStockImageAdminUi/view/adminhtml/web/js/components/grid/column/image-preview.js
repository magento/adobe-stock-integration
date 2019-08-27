/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'jquery',
    'knockout',
    'Magento_Ui/js/grid/columns/column',
    'Magento_AdobeIms/js/action/authorization',
    'Magento_AdobeStockImageAdminUi/js/model/messages',
    'mage/translate',
    'Magento_AdobeUi/js/components/grid/column/image-preview',
], function (_, $, ko, Column, authorizationAction, messages, translate, imagePreview) {
    'use strict';

    return imagePreview.extend({
        defaults: {
            mediaGallerySelector: '.media-gallery-modal:has(#search_adobe_stock)',
            adobeStockModalSelector: '#adobe-stock-images-search-modal',
            modules: {
                thumbnailComponent: '${ $.parentName }.thumbnail_url'
            },
            keywordsLimit: 5,
            saveAvailable: true,
            statefull: {
                visible: true,
                sorting: true,
                lastOpenedImage: true
            },
            tracks: {
                lastOpenedImage: true,
            },
            downloadImagePreviewUrl: Column.downloadImagePreviewUrl,
            messageDelay: 5,
            authConfig: {
                url: '',
                isAuthorized: false,
                stopHandleTimeout: 10000,
                windowParams: {
                    width: 500,
                    height: 600,
                    top: 100,
                    left: 300
                },
                response: {
                    regexpPattern: /auth\[code=(success|error);message=(.+)\]/,
                    codeIndex: 1,
                    messageIndex: 2,
                    successCode: 'success',
                    errorCode: 'error'
                }
            }
        },

        /**
         * @inheritDoc
         */
        next: function (record){
            this._super();
            this.hideAllKeywords(record);
        },

        /**
         * @inheritDoc
         */
        prev: function (record){
            this._super();
            this.hideAllKeywords(record);
        },

        /**
         * @inheritDoc
         */
        hide: function (record) {
            this._super();
            this.hideAllKeywords(record);
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
            return record.thumbnail_500_url;
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
            return record.creator_name || 'Author';
        },

        /**
         * Returns attributes to display under the preview image
         *
         * @param record
         * @returns {*[]}
         */
        getDisplayAttributes: function(record) {
            return [
                {
                    name: 'Dimensions',
                    value: record.width + ' x ' + record.height + ' px'
                },
                {
                    name: 'File type',
                    value: record.content_type.toUpperCase()
                },
                {
                    name: 'Category',
                    value: record.category.name || 'None'
                },
                {
                    name: 'File #',
                    value: record.id
                }
            ];
        },

        /**
         * Returns keywords to display under the attributes image
         *
         * @param record
         * @returns {*[]}
         */
        getKeywords: function(record) {
            return record.keywords;
        },

        /**
         * Returns keywords limit to show no of keywords
         *
         * @param record
         * @returns {*}
         */
        getKeywordsLimit: function (record){
            if (!record.keywordsLimit) {
                record.keywordsLimit = ko.observable(this.keywordsLimit);
            }
            return record.keywordsLimit();
        },

        /**
         * Show all the related keywords
         *
         * @param record
         * @returns {*}
         */
        viewAllKeywords: function(record) {
            record.keywordsLimit(record.keywords.length);
        },

        /**
         * Hide all the related keywords
         *
         * @param record
         * @returns {*}
         */
        hideAllKeywords: function(record) {
            record.keywordsLimit(this.keywordsLimit);
            record.canViewMoreKeywords(true);
        },

        /**
         * Check if view all button is visible or not
         *
         * @param record
         * @returns {*}
         */
        canViewMoreKeywords: function(record) {
            if (!record.canViewMoreKeywords) {
                record.canViewMoreKeywords = ko.observable(true);
            }
            if (record.keywordsLimit() >= record.keywords.length) {
                record.canViewMoreKeywords(false);
            }
            return record.canViewMoreKeywords();
        },

        /**
         * Returns visibility for given record.
         *
         * @param {Object} record
         * @return {*|boolean}
         */
        isVisible: function (record) {
            if (this.lastOpenedImage === record._rowIndex &&
                (this.visibility()[record._rowIndex] === undefined || this.visibility()[record._rowIndex] === false)
            ) {
                this.show(record);
            }
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
         * Scroll to preview window
         */
        scrollToPreview: function () {
            $(this.previewImageSelector).get(0).scrollIntoView({
                behavior: "smooth",
                block: "center",
                inline: "nearest"
            });
        },

        /**
         * Save record as image
         *
         * @param record
         */
        save: function (record) {
            var mediaBrowser = $(this.mediaGallerySelector).data('mageMediabrowser');
            $(this.adobeStockModalSelector).trigger('processStart');
            $.ajax(
                {
                    type: 'POST',
                    url: this.downloadImagePreviewUrl,
                    dataType: 'json',
                    data: {
                       'media_id': record.id,
                       'destination_path': mediaBrowser.activeNode.path || ''
                    },
                    context: this,
                    success: function () {
                        $(this.adobeStockModalSelector).trigger('processStop');
                        $(this.adobeStockModalSelector).trigger('closeModal');
                        mediaBrowser.reload(true);
                    },
                    error: function (response) {
                        $(this.adobeStockModalSelector).trigger('processStop');
                        messages.add('error', response.responseJSON.message);
                        messages.scheduleCleanup(3);
                    }
               }
           );
        },

        /**
         * Get messages
         *
         * @return {Array}
         */
        getMessages: function () {
            return messages.get();
        },

        /**
         * License and save image
         *
         * @param {Object} record
         */
        licenseAndSave: function (record) {
            /** @todo add license functionality */
            console.warn('add license functionality');
            console.dir(record);
        },

        /**
         * Process of license
         *
         * @param {Object} record
         */
        licenseProcess: function (record) {
            if (this.authConfig.isAuthorized) {
                this.licenseAndSave(record);

                return;
            }

            /**
             * Opens authorization window of Adobe Stock
             * then starts the authorization process
             */
            authorizationAction(this.authConfig)
                .then(
                    function (authConfig) {
                        this.authConfig = _.extend(this.authConfig, authConfig);
                        this.licenseProcess(record);
                        messages.add('success', authConfig.lastAuthSuccessMessage);
                    }.bind(this)
                )
                .catch(
                    function (error) {
                        messages.add('error', error.message);
                    }.bind(this)
                )
                .finally((function () {
                    messages.scheduleCleanup(this.messageDelay);
                }).bind(this));
        }
    });
});
