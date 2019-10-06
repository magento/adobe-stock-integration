/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'jquery',
    'knockout',
    'mage/translate',
    'Magento_AdobeUi/js/components/grid/column/image-preview',
    'Magento_AdobeStockImageAdminUi/js/model/messages',
    'Magento_AdobeStockImageAdminUi/js/media-gallery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/prompt',
    'text!Magento_AdobeStockImageAdminUi/template/modal/adobe-modal-prompt-content.html',
    'Magento_AdobeStockAdminUi/js/config',
    'mage/backend/tabs'
], function (_, $, ko, translate, imagePreview, messages, mediaGallery, confirmation, prompt, adobePromptContentTmpl, config) {
    'use strict';

    return imagePreview.extend({
        defaults: {
            chipsProvider: 'componentType = filtersChips, ns = ${ $.ns }',
            searchChipsProvider: 'componentType = keyword_search, ns = ${ $.ns }',
            filterChipsProvider: 'componentType = filters, ns = ${ $.ns }',
            loginProvider: 'name = adobe-login, ns = adobe-login',
            inputValue: '',
            chipInputValue: '',
            serieFilterValue: '',
            modelFilterValue: '',
            defaultKeywordsLimit: 5,
            keywordsLimit: 5,
            canViewMoreKeywords: true,
            saveAvailable: true,
            searchValue: null,
            messageDelay: 5,
            displayedRecord: {},
            selectedRelatedType: null,
            previewStyles: {},
            statefull: {
                visible: true,
                sorting: true,
                lastOpenedImage: true,
                serieFilterValue: true,
                modelFilterValue: true
            },
            tracks: {
                lastOpenedImage: true
            },
            modules: {
                thumbnailComponent: '${ $.parentName }.thumbnail_url',
                chips: '${ $.chipsProvider }',
                searchChips: '${ $.searchChipsProvider }',
                filterChips: '${ $.filterChipsProvider }',
                login: '${ $.loginProvider }',
            },
            listens: {
                '${ $.provider }:params.filters': 'hide',
                '${ $.provider }:params.search': 'hide'
            },
            exports: {
                inputValue: '${ $.provider }:params.search',
                serieFilterValue: '${ $.provider }:params.filters.serie_id',
                modelFilterValue: '${ $.provider }:params.filters.model_id',
                chipInputValue: '${ $.searchChipsProvider }:value'
            }
        },

        /**
         * @param {Object} record
         * @private
         */
        _initRecord: function (record) {
            if (!record.model || !record.series) {
                record.series = ko.observable([]);
                record.model = ko.observable([]);
            }
        },

        /**
         * @inheritdoc
         */
        next: function (record) {
            if (this.selectedRelatedType()) {
                this.nextRelated(record);

                return;
            }
            this.hideAllKeywords();
            this._super(record);
        },

        /**
         * @inheritdoc
         */
        prev: function (record) {
            if (this.selectedRelatedType()) {
                this.prevRelated(record);

                return;
            }
            this.hideAllKeywords();
            this._super(record);
        },

        /**
         * @inheritdoc
         */
        show: function (record) {
            this.selectedRelatedType(null);
            this._initRecord(record);
            this.hideAllKeywords();
            this.displayedRecord(record);
            this._super(record);
            this.loadRelatedImages(record);
            this._updateHeight();
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
                    'inputValue',
                    'chipInputValue',
                    'serieFilterValue',
                    'modelFilterValue',
                    'displayedRecord',
                    'keywordsLimit',
                    'canViewMoreKeywords',
                    'selectedRelatedType',
                    'previewStyles'
                ]);
            this.height.subscribe(function () {
                this.thumbnailComponent().previewHeight(this.height());
            }, this);

            return this;
        },

        /**
         * Get image related image series.
         *
         * @param {Object} record
         */
        loadRelatedImages: function (record) {
            if (record.series && record.model
                && record.series() && record.model()
                && record.series().length && record.model().length) {
                return;
            }
            $.ajax({
                type: 'GET',
                url: config.relatedImagesUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'image_id': record.id,
                    'limit': 4
                }
            }).done(function (data) {
                record.series(data.result.same_series);
                record.model(data.result.same_model);
                this._updateHeight();
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
            this.filterChips().set('applied', {'serie_id' : record.id.toString()})
        },

        /**
         * Filter images from serie_id
         *
         * @param {Object} record
         */
        seeMoreFromModel: function(record) {
            this.modelFilterValue(record.id);
            this.filterChips().set('applied', {'model_id' : record.id.toString()})
        },

        /**
         * Returns keywords to display under the attributes image
         *
         * @returns {*[]}
         */
        getKeywords: function () {
            return this.displayedRecord().keywords;
        },

        /**
         * Returns keywords limit to show no of keywords
         */
        getKeywordsLimit: function () {
            return this.keywordsLimit();
        },

        /**
         * Show all the related keywords
         */
        viewAllKeywords: function () {
            this.keywordsLimit(this.displayedRecord().keywords.length);
            this.canViewMoreKeywords(false);
            this._updateHeight();
        },

        /**
         * Hide all the related keywords
         */
        hideAllKeywords: function () {
            this.keywordsLimit(this.defaultKeywordsLimit);
            this.canViewMoreKeywords(true);
        },

        /**
         * Check if view all button is visible or not
         *
         * @returns {boolean}
         */
        canViewMoreKeywords: function () {
            return this.canViewMoreKeywords();
        },

        /**
         * Drop all filters and initiate search on keyword click event
         */
        searchByKeyWord: function (keyword) {
            _.invoke(this.chips().elems(), 'clear');
            this.inputValue(keyword);
            this.chipInputValue(keyword);
        },

        /**
         * Returns is_downloaded flag as observable for given record
         *
         * @returns {observable}
         */
        isDownloaded: function () {
            return this.displayedRecord().is_downloaded;
        },

        /**
         * Get styles for preview
         *
         * @returns {Object}
         */
        getStyles: function () {
            this.previewStyles({'margin-top': '-' + this.height()});
            return this.previewStyles();
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
         * Locate downloaded image in media browser
         */
        locate: function () {
            $(config.adobeStockModalSelector).trigger('closeModal');
            mediaGallery.locate(this.displayedRecord().path);
        },

        /**
         * Save preview
         */
        savePreview: function () {
            prompt({
                title: 'Save Preview',
                content: 'File Name',
                value: this.generateImageName(this.displayedRecord()),
                imageExtension: this.getImageExtension(this.displayedRecord()),
                promptContentTmpl: adobePromptContentTmpl,
                modalClass: 'adobe-stock-save-preview-prompt',
                validation: true,
                promptField: '[data-role="promptField"]',
                validationRules: ['required-entry'],
                attributesForm: {
                    novalidate: 'novalidate',
                    action: '',
                    onkeydown: 'return event.key != \'Enter\';'
                },
                attributesField: {
                    name: 'name',
                    'data-validate': '{required:true}',
                    maxlength: '128'
                },
                context: this,
                actions: {
                    confirm: function (fileName) {
                        this.save(this.displayedRecord(), fileName, config.downloadPreviewUrl);
                    }.bind(this)
                },
                buttons: [{
                    text: $.mage.__('Cancel'),
                    class: 'action-secondary action-dismiss'
                }, {
                    text: $.mage.__('Confirm'),
                    class: 'action-primary action-accept'
                }]
            });
        },

        /**
         * Save record as image
         *
         * @param {Object} record
         * @param {String} fileName
         * @param {String} actionURI
         */
        save: function (record, fileName, actionURI) {
            var mediaBrowser = $(config.mediaGallerySelector).data('mageMediabrowser'),
                destinationPath = (mediaBrowser.activeNode.path || '') + '/' + fileName + '.' + this.getImageExtension(record);

            $.ajax({
                type: 'POST',
                url: actionURI,
                dataType: 'json',
                showLoader: true,
                data: {
                    'media_id': record.id,
                    'destination_path': destinationPath
                },
                context: this,
                success: function () {
                    var displayedRecord = this.displayedRecord();
                    displayedRecord.is_downloaded = 1;
                    displayedRecord.path = destinationPath;
                    this.displayedRecord(displayedRecord);
                    $(config.adobeStockModalSelector).trigger('closeModal');
                    mediaBrowser.reload(true);
                },
                error: function (response) {
                    messages.add('error', response.responseJSON.message);
                    messages.scheduleCleanup(3);
                }
            });
        },

        /**
         * Generate meaningful name image file
         *
         * @param {Object} record
         * @return string
         */
        generateImageName: function (record) {
            var imageName = record.title.substring(0, 32).replace(/\s+/g, '-').toLowerCase();
            return imageName;
        },

        /**
         * Get image file extension
         *
         * @param {Object} record
         * @return string
         */
        getImageExtension: function (record) {
            var imageType = record.content_type.match(/[^/]{1,4}$/);
            return imageType;
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
            this.save(record, this.generateImageName(record), config.licenseAndDownloadUrl);
        },

        /**
         * Shows license confirmation popup with information about current license quota
         *
         * @param {Object} record
         */
        showLicenseConfirmation: function (record) {
            var licenseAndSave = this.licenseAndSave.bind(this);
            $.ajax(
                {
                    type: 'POST',
                    url: config.quotaUrl,
                    dataType: 'json',
                    data: {
                        'media_id': record.id
                    },
                    context: this,
                    showLoader: true,

                    success: function (response) {
                        var quota = response.result.quota,
                            confirmationContent = $.mage.__('License "' + record.title + '"'),
                            quotaMessage = response.result.message;
                        confirmation({
                            title: $.mage.__('License Adobe Stock Image?'),
                            content: confirmationContent + '<p><b>' + quotaMessage + '</b></p>',
                            actions: {
                                confirm: function () {
                                    if (quota > 0) {
                                        licenseAndSave(record);
                                    } else {
                                        window.open(config.buyCreditsUrl);
                                    }
                                }
                            },
                            buttons: [{
                                text: $.mage.__('Cancel'),
                                class: 'action-secondary action-dismiss',
                                click: function () {
                                    this.closeModal();
                                }
                            }, {
                                text: quota > 0 ? $.mage.__('OK') : $.mage.__('Buy Credits'),
                                class: 'action-primary action-accept',
                                click: function () {
                                    this.closeModal();
                                    this.options.actions.confirm();
                                }
                            }]
                        })
                    },

                    error: function (response) {
                        messages.add('error', response.responseJSON.message);
                        messages.scheduleCleanup(3);
                    }
                }
            );
        },

        /**
         * Process of license
         *
         * @param {Object} record
         */
        licenseProcess: function () {
            this.login().login()
                .then(function () {
                    this.showLicenseConfirmation(this.displayedRecord());
                }.bind(this))
                .catch(function (error) {
                    messages.add('error', error.message);
                })
                .finally((function () {
                    messages.scheduleCleanup(this.messageDelay);
                }).bind(this));
        },

        /**
         * Show related image data in the preview section
         *
         * @param {Object} record
         */
        showRelated: function (record) {
            this.hideAllKeywords();
            this.displayedRecord(record);
            this._updateHeight();
        },

        /**
         * Next related image preview
         *
         * @param {Object} record
         */
        nextRelated: function (record) {
            var relatedList = this.selectedRelatedType() === 'series' ? record.series() : record.model(),
                nextRelatedIndex = _.findLastIndex(relatedList, {id: this.displayedRecord().id}) + 1,
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
                prevRelatedIndex = _.findLastIndex(relatedList, {id: this.displayedRecord().id}) - 1,
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
            if (!this.selectedRelatedType()) {
                return false;
            }
            var relatedList = this.selectedRelatedType() === 'series' ? record.series() : record.model(),
                prevRelatedIndex,
                prevRelated;

            prevRelatedIndex = _.findLastIndex(relatedList, {id: this.displayedRecord().id}) - 1;
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
            if (!this.selectedRelatedType()) {
                return false;
            }
            var relatedList = this.selectedRelatedType() === 'series' ? record.series() : record.model(),
                nextRelatedIndex,
                nextRelated;

            nextRelatedIndex = _.findLastIndex(relatedList, {id: this.displayedRecord().id}) + 1;
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
         * @param {Object} record
         */
        switchImagePreviewToRelatedImage: function (relatedImage, record) {
            if (!relatedImage) {
                this.selectedRelatedType(null);

                return;
            }

            if (this.displayedRecord().id === relatedImage.id) {
                return;
            }

            this.showRelated(relatedImage);
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
        },
    });
});
