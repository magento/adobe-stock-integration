/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
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
            $.ajaxSetup({async: false});
            var imagePath = path.replace(/^\/+/, '');
            var imagePathParts = imagePath.split('/');
            var imageFilename = imagePath;
            var imageFolderName = this.jsTreeRootFolderName;

            if (imagePathParts.length > 1) {
                imageFilename = imagePathParts[imagePathParts.length - 1];
                imageFolderName = imagePathParts[imagePathParts.length - 2];

                for (var i = 0; i < imagePathParts.length - 2; i++) {
                    var folderName = imagePathParts[i];

                    /* folder name is being cut in file browser */
                    if (folderName.length > this.jsTreeFolderNameMaxLength) {
                        folderName = folderName.substring(0, this.jsTreeFolderNameMaxLength) + '...';
                    }

                    //var folderSelector = ".jstree a:contains('" + folderName + "')";
                    var openFolderChildrenButton = $(".jstree a:contains('" + folderName + "')").prev('.jstree-icon');
                    if (openFolderChildrenButton.length) {
                        openFolderChildrenButton.click();
                    }
                }
            }

            //select folder
            var imageFolder = $(".jstree a:contains('" + imageFolderName + "')");
            if (imageFolder.length) {
                imageFolder[0].click();
                //select image
                var locatedImage = $("div[data-row='file']:has(img[alt=\"" + imageFilename + "\"])");
                if (locatedImage.length) {
                    locatedImage.click();
                }
            }
            $.ajaxSetup({async: true});
        }
    }
});
