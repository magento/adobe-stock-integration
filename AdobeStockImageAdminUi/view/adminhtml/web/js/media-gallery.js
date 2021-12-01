/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global Base64 */
define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirm) {
    'use strict';

    return {
        jsTreeRootFolderId: 'root',
        jsTreeFolderNameMaxLength: 20,

        /**
         * Locate downloaded image in media browser
         *
         * @param {String} path
         */
        locate: function (path) {
            var imageFolder = this.selectFolder(path),
                imageFilename = path.substring(path.lastIndexOf('/') + 1),
                locatedImage;

            if (imageFolder.length) {
                locatedImage = $('div[data-row="file"]:has(img[alt=\"' + imageFilename + '\"])');

                return locatedImage.length ? locatedImage : false;
            }

            $.ajaxSetup({
                async: true
            });
        },

        /**
         * Select folder
         *
         * @param {String} path
         */
        selectFolder: function (path) {
            var imageFolder,
                pathId,
                imagePath = path.replace(/^\/+/, ''),
                folderPathParts = imagePath.split('/').slice(0, -1);

            $.ajaxSetup({
                async: false
            });

            if (folderPathParts.length > 1) {
                this.openFolderTree(folderPathParts);
            }

            pathId = Base64.idEncode(folderPathParts.join('/'));
            imageFolder = $('.jstree li[data-id="' + pathId + '"]').children('a');

            if (!imageFolder.length) {
                imageFolder = $('.jstree li[data-id="' + this.jsTreeRootFolderId + '"]')
                    .children('a');
            }

            if (imageFolder.length) {
                imageFolder[0].click();
            }

            return imageFolder;
        },

        /**
         * Show popup that image cannot be located
         */
        notLocated: function () {
            confirm({
                title: $.mage.__('The image cannot be located'),
                content: $.mage.__('We cannot find this image in the media gallery.'),
                buttons: [{
                    text: $.mage.__('Ok'),
                    class: 'action-primary',
                    attr: {},

                    /**
                     * Close modal on button click
                     */
                    click: function (event) {
                        this.closeModal(event);
                    }
                }]
            });
        },

        /**
         * Open folder Tree
         *
         * @param {Array} folderPathParts
         */
        openFolderTree: function (folderPathParts) {
            var i,
                pathId,
                openFolderButton,
                folderPath = '';

            for (i = 0; i < folderPathParts.length - 1; i++) {
                if (folderPath === '') {
                    folderPath = folderPathParts[i];
                } else {
                    folderPath = folderPath + '/' + folderPathParts[i];
                }
                pathId = Base64.idEncode(folderPath);
                openFolderButton = $('.jstree li[data-id="' + pathId + '"].jstree-closed').children('.jstree-icon');

                if (openFolderButton.length) {
                    openFolderButton.click();
                }
            }
        }
    };
});
