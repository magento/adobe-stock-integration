/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'jquery',
    'jquery/file-uploader',
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            imageUploadInputSelector: '#image-uploader-input',
            directoryTreeComponent: 'media_gallery_listing.media_gallery_listing.media_gallery_directories',
            config: {
                imageUploadUrl: '',
                acceptFileTypes: '',
                allowedExtensions: '',
                maxFileSize: ''
            },
            modules: {
                directoryTree: '${ $.directoryTreeComponent }',
            }
        },

        /**
         * Init component
         *
         * @return {exports}
         */
        initialize: function () {
            this._super();
            this.initializeFileUpload();

            return this;
        },

        /**
         * Initializes file upload library
         */
        initializeFileUpload: function () {
            $(this.imageUploadInputSelector).fileupload({
                url: this.config.imageUploadUrl,
                dataType: 'json',
                formData: {
                    isAjax: 'true',
                    form_key: FORM_KEY,
                    path: '/var/www/html/pub/media/catalog'
                },
                sequentialUploads: true,
                acceptFileTypes: this.config.acceptFileTypes,
                allowedExtensions: this.config.allowedExtensions,
                maxFileSize: this.config.maxFileSize,
                add: function(e, data) {
                    data.submit();
                },
            });
        }
    });
});
