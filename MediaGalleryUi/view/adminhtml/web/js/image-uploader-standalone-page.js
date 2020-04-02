/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_MediaGalleryUi/js/image-uploader',
    'underscore'
], function (ImageUploader, _) {
    'use strict';

    return ImageUploader.extend({
        defaults: {
            actionsPath: 'standalone_media_gallery_listing.standalone_media_gallery_listing.media_gallery_columns.thumbnail_url_actions',
            directoriesPath: 'standalone_media_gallery_listing.standalone_media_gallery_listing.media_gallery_directories'
        },
        modules: {
            actions: '${ $.actionsPath }',
            directories: '${ $.directoriesPath }'
        },

        /**
         * Gets Media Gallery selected folder
         *
         * @returns {String}
         */
        getTargetFolder: function () {

            if (_.isUndefined(this.directories().selectedFolder)) {
                return '/';
            }

            return selectedFolder();
        }

    });
});
