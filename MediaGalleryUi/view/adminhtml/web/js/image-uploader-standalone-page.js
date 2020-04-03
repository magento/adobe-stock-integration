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
            // eslint-disable-next-line max-len
            actionsPath: 'standalone_media_gallery_listing.standalone_media_gallery_listing.media_gallery_columns.thumbnail_url_actions',
            // eslint-disable-next-line max-len
            directoriesPath: 'standalone_media_gallery_listing.standalone_media_gallery_listing.media_gallery_directories',
            messagesPath: 'standalone_media_gallery_listing.standalone_media_gallery_listing.messages'
        },
        modules: {
            actions: '${ $.actionsPath }',
            directories: '${ $.directoriesPath }',
            mediaGridMessages: '${ $.messagesPath }'
        },

        /**
         * Gets Media Gallery selected folder
         *
         * @returns {String}
         */
        getTargetFolder: function () {

            if (_.isUndefined(this.directories().activeNode()) ||
                _.isNull(this.directories().activeNode())) {
                return '/';
            }

            return this.directories().activeNode();
        }

    });
});
