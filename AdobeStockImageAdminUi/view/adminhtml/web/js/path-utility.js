/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([], function () {
    'use strict';

    return {
        /**
         * Extracts image name from its path
         *
         * @param {String} path
         * @returns {String}
         */
        getImageNameFromPath: function (path) {
            var filePathArray = path.split('/'),
                imageIndex = filePathArray.length - 1;

            return filePathArray[imageIndex].substring(0, filePathArray[imageIndex].lastIndexOf('.'));
        },

        /**
         * Generate meaningful name image file,
         * allow only alphanumerics, dashes, and underscores
         *
         * @param {String} title
         * @param {Number} id
         * @return string
         */
        generateImageName: function (title, id) {
            var fileName = title.substring(0, 32)
                .replace(/[^a-zA-Z0-9_]/g, '-')
                .replace(/-{2,}/g, '-')
                .toLowerCase();

            /* If the filename does not contain latin chars, use ID as a filename */
            return fileName === '-' ? id : fileName;
        },

        /**
         * Get image file extension
         *
         * @param {String} contentType
         * @return string
         */
        getImageExtension: function (contentType) {
            return contentType.match(/[^/]{1,4}$/);
        },

        /**
         * Create path
         *
         * @param {String} directoryPath
         * @param {String} fileName
         * @param {String} contentType
         * @returns {String}
         */
        buildPath: function (directoryPath, fileName, contentType) {
            return directoryPath + '/' + fileName + '.' + this.getImageExtension(contentType);
        }
    };
});
