/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'jquery',
    'Magento_AdobeStockImageAdminUi/js/model/messages',
    'Magento_AdobeStockImageAdminUi/js/media-gallery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/prompt',
    'text!Magento_AdobeStockImageAdminUi/template/modal/adobe-modal-prompt-content.html'
], function (Component, $, messages, mediaGallery, confirmation, prompt, adobePromptContentTmpl) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/grid/column/preview/actions',
            loginProvider: 'name = adobe-login, ns = adobe-login',
            // eslint-disable-next-line max-len
            previewProvider: 'name = adobe_stock_images_listing.adobe_stock_images_listing.adobe_stock_images_columns.preview, ns = ${ $.ns }',
            mediaGallerySelector: '.media-gallery-modal:has(#search_adobe_stock)',
            adobeStockModalSelector: '#adobe-stock-images-search-modal',
            downloadImagePreviewUrl: 'adobe_stock/preview/download',
            licenseAndDownloadUrl: 'adobe_stock/license/license',
            confirmationUrl: 'adobe_stock/license/confirmation',
            buyCreditsUrl: 'https://stock.adobe.com/',
            messageDelay: 5,
            modules: {
                login: '${ $.loginProvider }',
                preview: '${ $.previewProvider }'
            }
        },

        /**
         * Returns is_downloaded flag as observable for given record
         *
         * @returns {observable}
         */
        isDownloaded: function () {
            return this.preview().displayedRecord().is_downloaded;
        },

        /**
         * Is asset licensed in adobe stock in context of currently logged in account
         *
         * @returns {observable}
         */
        isLicensed: function () {
            return this.preview().displayedRecord().is_licensed;
        },

        /**
         * Is licensed version of asset saved locally
         *
         * @returns {observable}
         */
        isLicensedLocally: function () {
            return this.preview().displayedRecord().is_licensed_locally;
        },

        /**
         * Locate downloaded image in media browser
         */
        locate: function () {
            $(this.preview().adobeStockModalSelector).trigger('closeModal');
            mediaGallery.locate(this.preview().displayedRecord().path);
        },

        /**
         * Save preview
         */
        savePreview: function () {
            this.getPrompt(
                {
                    'title': $.mage.__('Save Preview'),
                    'content': $.mage.__('File Name'),
                    'visible': true,
                    'actions': {
                        confirm: function (fileName) {
                            this.save(this.preview().displayedRecord(), fileName);
                        }.bind(this)
                    },
                    'buttons': [{
                        text: $.mage.__('Cancel'),
                        class: 'action-secondary action-dismiss',
                        click: function () {
                            this.closeModal();
                        }
                    }, {
                        text: $.mage.__('Confirm'),
                        class: 'action-primary action-accept'
                    }]

                }
            );
        },

        /**
         * Save record as image
         *
         * @param {Object} record
         * @param {String} fileName
         * @param {bool} license
         */
        save: function (record, fileName, license) {
            var mediaBrowser = $(this.preview().mediaGallerySelector).data('mageMediabrowser'),
                destinationPath = (mediaBrowser.activeNode.path || '') + '/' + fileName + '.' +
                                  this.getImageExtension(record);

            $.ajax({
                type: 'POST',
                url: license ? this.preview().licenseAndDownloadUrl : this.preview().downloadImagePreviewUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'media_id': record.id,
                    'destination_path': destinationPath
                },
                context: this,
                success: function () {
                    var displayedRecord = this.preview().displayedRecord();

                    displayedRecord.is_downloaded = 1;
                    displayedRecord.path = destinationPath;

                    if (license) {
                        displayedRecord.is_licensed = 1;
                        displayedRecord.is_licensed_locally = 1;
                    }
                    this.preview().displayedRecord(displayedRecord);
                    $(this.preview().adobeStockModalSelector).trigger('closeModal');
                    mediaBrowser.reload(true);
                },
                error: function (response) {
                    messages.add('error', response.message);
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
            return record.title.substring(0, 32).replace(/\s+/g, '-').toLowerCase();
        },

        /**
         * Get image file extension
         *
         * @param {Object} record
         * @return string
         */
        getImageExtension: function (record) {
            return record.content_type.match(/[^/]{1,4}$/);
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
         *s
         * @param {Object} record
         * @param fileName
         */
        licenseAndSave: function (record, fileName) {
            this.save(record, fileName, true);
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
                    url: this.preview().confirmationUrl,
                    dataType: 'json',
                    data: {
                        'media_id': record.id
                    },
                    context: this,
                    showLoader: true,

                    success: function (response) {
                        var confirmationContent = $.mage.__('License "' + record.title + '"'),
                            quotaMessage = response.result.message,
                            canPurchase = response.result.canLicense;

                        this.getPrompt(
                            {
                                'title': $.mage.__('License Adobe Stock Image?'),
                                'content': '<p>' + confirmationContent + '</p><p><b>' + quotaMessage + '</p><br>' +
                                           $.mage.__('File Name') + '</b>',
                                'visible': canPurchase,
                                'actions': {
                                    confirm: function (fileName) {
                                        canPurchase ?
                                            licenseAndSave(record, fileName) :
                                            window.open(this.preview().buyCreditsUrl);
                                    }
                                },
                                'buttons': [{
                                    text: $.mage.__('Cancel'),
                                    class: 'action-secondary action-dismiss',
                                    click: function () {
                                        this.closeModal();
                                    }
                                }, {
                                    text: canPurchase ? $.mage.__('Confirm') : $.mage.__('Buy Credits'),
                                    class: 'action-primary action-accept'
                                }]

                            }
                        );
                    },

                    error: function (response) {
                        messages.add('error', response.responseJSON.message);
                        messages.scheduleCleanup(3);
                    }
                }
            );
        },

        /**
         * Return configured  prompt with input field.
         */
        getPrompt: function (data) {
            prompt({
                title: data.title,
                content:  data.content,
                value: this.generateImageName(this.preview().displayedRecord()),
                imageExtension: this.getImageExtension(this.preview().displayedRecord()),
                visible: data.visible,
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
                actions: data.actions,
                buttons: data.buttons
            });
        },

        /**
         * Process of license
         */
        licenseProcess: function () {
            this.login().login()
                .then(function () {
                    this.showLicenseConfirmation(this.preview().displayedRecord());
                }.bind(this))
                .catch(function (error) {
                    messages.add('error', error.message);
                })
                .finally(function () {
                    messages.scheduleCleanup(this.messageDelay);
                }.bind(this));
        },

        /**
         * Returns license button title depending on the existing saved preview
         *
         * @returns {String}
         */
        getLicenseButtonTitle: function () {
            return this.isDownloaded() ?  $.mage.__('License') : $.mage.__('License and Save');
        }
    });
});
