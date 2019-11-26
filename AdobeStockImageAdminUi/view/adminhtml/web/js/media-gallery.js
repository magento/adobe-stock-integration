/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirm) {
    'use strict';

    return {
        jsTreeRootFolderName: 'Storage Root',
        jsTreeFolderNameMaxLength: 20,

        /**
         * Locate downloaded image in media browser
         *
         * @param {String} path
         */
        locate: function (path) {
            var i,
                folderName,
                openFolderChildrenButton,
                imageFolder,
                locatedImage,
                imagePath = path.replace(/^\/+/, ''),
                imagePathParts = imagePath.split('/'),
                imageFilename = imagePath,
                imageFolderName = this.jsTreeRootFolderName;

            $.ajaxSetup({
                async: false
            });

            if (imagePathParts.length > 1) {
                imageFilename = imagePathParts[imagePathParts.length - 1];
                imageFolderName = imagePathParts[imagePathParts.length - 2];

                for (i = 0; i < imagePathParts.length - 2; i++) {
                    folderName = imagePathParts[i];

                    /* folder name is being cut in file browser */
                    // eslint-disable-next-line max-depth
                    if (folderName.length > this.jsTreeFolderNameMaxLength) {
                        folderName = folderName.substring(0, this.jsTreeFolderNameMaxLength) + '...';
                    }

                    //var folderSelector = ".jstree a:contains('" + folderName + "')";
                    openFolderChildrenButton = $('.jstree a:contains("' + folderName + '")').prev('.jstree-icon');

                    // eslint-disable-next-line max-depth
                    if (openFolderChildrenButton.length) {
                        openFolderChildrenButton.click();
                    }
                }
            }

            //select folder
            imageFolder = $('.jstree a:contains("' + imageFolderName + '")');

            if (imageFolder.length) {
                imageFolder[0].click();
                //select image
                locatedImage = $('div[data-row="file"]:has(img[alt=\"' + imageFilename + '\"])');

                return locatedImage.length ?  locatedImage : false;
            }

            $.ajaxSetup({
                async: true
            });
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
        }
    };
});
