/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/grid/masonry',
    'jquery'
], function (Masonry, $) {
    'use strict';

    return Masonry.extend({
        defaults: {
            modules: {
                provider: '${ $.provider }'
            }
        },

        /**
         * Init component
         *
         * @return {Object}
         */
        initialize: function () {
            this._super();
            this.initEvents();

            return this;
        },

        /**
         * Initialize events
         */
        initEvents: function () {
            $(window).on('enhanced_media_gallery_folder_deleted', this.reloadGrid.bind(this));
        },

        /**
         * Reload grid
         */
        reloadGrid: function () {
            var provider = this.provider(),
                dataStorage = provider.storage();

            dataStorage.clearRequests();
            provider.reload();
        }
    });
});
