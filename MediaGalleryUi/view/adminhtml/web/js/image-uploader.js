/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'jquery',
    'Magento_Ui/js/lib/spinner',
    'mage/translate',
    'jquery/file-uploader',
], function (Component, $, loader) {
    'use strict';

    return Component.extend({
        defaults: {
            imageUploadInputSelector: '#image-uploader-form',
            directoriesComponentPath: 'media_gallery_listing.media_gallery_listing.media_gallery_directories_directories',
            actionsComponentPath: 'media_gallery_listing.media_gallery_listing.media_gallery_columns.thumbnail_url_actions',
            messagesComponentPath: 'media_gallery_listing.media_gallery_listing.messages',
            imageUploadUrl: '',
            acceptFileTypes: '',
            allowedExtensions: '',
            maxFileSize: '',
            loader: false,
            modules: {
                directories: '${ $.directoriesComponentPath }',
                actions: '${ $.actionsComponentPath }',
                mediaGridMessages: '${ $.messagesComponentPath }'
            }
        },

        /**
         * Init component
         *
         * @return {exports}
         */
        initialize: function () {
            this._super().observe(['loader']);

            return this;
        },

        /**
         * Initializes file upload library
         */
        initializeFileUpload: function () {
            $(this.imageUploadInputSelector).fileupload({
                url: this.imageUploadUrl,
                dataType: 'json',
                formData: function (form) {
                    return form.serializeArray().concat(
                        [{
                            name: 'isAjax',
                            value: true,
                        },
                        {
                            name: 'form_key',
                            value: window.FORM_KEY,
                        }]
                    );
                },
                sequentialUploads: true,
                acceptFileTypes: this.acceptFileTypes,
                allowedExtensions: this.allowedExtensions,
                maxFileSize: this.maxFileSize,
                add: function (e, data) {
                    this.showLoader();
                    data.submit();
                }.bind(this),
                done: function (e, data) {
                    this.hideLoader();
                    this.actions().reloadGrid();
                }.bind(this),
                fail: function (e, data) {
                    var responseData = data.jqXHR.responseJSON;
                    if (responseData !== undefined && responseData.message) {
                        this.mediaGridMessages().add('error', $.mage.__(responseData.message));
                        this.mediaGridMessages().scheduleCleanup();
                    }
                    this.hideLoader();
                }.bind(this)
            });
        },

        /**
         * Returns the selected folder
         *
         * @returns {*}
         */
        getSelectedFolder: function () {
            return this.directories().selectedFolder;
        },

        /**
         * Shows spinner loader
         */
        showLoader: function() {
            this.loader(true);
        },

        /**
         * Hides spinner loader
         */
        hideLoader: function () {
            this.loader(false);
        }
    });
});
