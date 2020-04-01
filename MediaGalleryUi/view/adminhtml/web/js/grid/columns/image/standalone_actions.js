/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_MediaGalleryUi/js/grid/columns/image/actions'
], function (Actions) {
    'use strict';

    return Actions.extend({
        defaults: {
            messagesStandalone: 'standalone_media_gallery_listing.standalone_media_gallery_listing.messages',
            modules: {
                messages: '${ $.messagesStandalone }'
            }
        }
    });
});
